<?php

namespace App\Services;

use App\Enums\AdminCategoryEnum;
use App\Models\Appointment;
use App\Models\Disease;
use App\Models\Feedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * Build an appointments report dataset.
     *
     * @param  array{from?:string|null,to?:string|null,status?:string|null,doctor_id?:int|null,service_category?:string|null}  $filters
     */
    public function getAppointmentsReport(array $filters, ?User $viewer = null): array
    {
        $from = isset($filters['from']) && $filters['from'] ? Carbon::parse($filters['from'])->startOfDay() : now()->startOfMonth();
        $to = isset($filters['to']) && $filters['to'] ? Carbon::parse($filters['to'])->endOfDay() : now()->endOfDay();

        $query = Appointment::query()
            ->with(['patient.user', 'doctor.user', 'service'])
            ->whereBetween('scheduled_at', [$from, $to]);

        // Restrict healthcare admins to their category when applicable
        $query = $this->applyHealthcareAdminScopeToAppointments($query, $viewer);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', (int) $filters['doctor_id']);
        }

        if (! empty($filters['service_category'])) {
            $categories = $this->serviceSynonyms($filters['service_category']);
            $query->whereHas('service', function (Builder $q) use ($categories) {
                $q->whereIn('category', $categories);
            });
        }

        $list = (clone $query)
            ->latest('scheduled_at')
            ->paginate(20);

        $total = (clone $query)->count();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byService = (clone $query)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->selectRaw('services.category as category, COUNT(*) as count')
            ->groupBy('services.category')
            ->pluck('count', 'category')
            ->toArray();

        $daily = (clone $query)
            ->selectRaw('DATE(scheduled_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return [
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'total' => $total,
            ],
            'breakdown' => [
                'status' => $byStatus,
                'service' => $byService,
                'daily' => $daily,
            ],
            'list' => $this->simplifyAppointments($list),
            'pagination' => $this->paginationMeta($list),
        ];
    }

    /**
     * Build a diseases report dataset.
     *
     * @param  array{from?:string|null,to?:string|null,disease_type?:string|null,barangay_id?:int|null}  $filters
     */
    public function getDiseasesReport(array $filters, ?User $viewer = null): array
    {
        $from = isset($filters['from']) && $filters['from'] ? Carbon::parse($filters['from'])->startOfDay() : now()->startOfMonth();
        $to = isset($filters['to']) && $filters['to'] ? Carbon::parse($filters['to'])->endOfDay() : now()->endOfDay();

        $query = Disease::query()
            ->with(['patient.user', 'barangay'])
            ->whereBetween('diagnosis_date', [$from, $to]);

        if (! empty($filters['disease_type'])) {
            $query->where('disease_type', $filters['disease_type']);
        }

        if (! empty($filters['barangay_id'])) {
            $query->where('barangay_id', (int) $filters['barangay_id']);
        }

        // Optional restriction by healthcare admin category for HIV / Pregnancy
        if ($viewer && $viewer->role?->name === 'healthcare_admin') {
            $adminCat = $viewer->admin_category?->value;
            if ($adminCat === AdminCategoryEnum::HIV->value) {
                $query->whereIn('disease_type', ['hiv', 'hiv/aids', 'hiv_aids']);
            } elseif ($adminCat === AdminCategoryEnum::Pregnancy->value) {
                $query->whereIn('disease_type', ['pregnancy_complications', 'pregnancy']);
            }
        }

        $list = (clone $query)
            ->latest('diagnosis_date')
            ->paginate(20);

        $total = (clone $query)->count();

        $byType = (clone $query)
            ->selectRaw('disease_type, COUNT(*) as count')
            ->groupBy('disease_type')
            ->pluck('count', 'disease_type')
            ->toArray();

        $byBarangay = (clone $query)
            ->selectRaw('barangay_id, COUNT(*) as count')
            ->groupBy('barangay_id')
            ->with('barangay')
            ->get()
            ->mapWithKeys(fn ($row) => [optional($row->barangay)->name ?? 'Unknown' => (int) $row->count])
            ->toArray();

        $daily = (clone $query)
            ->selectRaw('DATE(diagnosis_date) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return [
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'total' => $total,
            ],
            'breakdown' => [
                'type' => $byType,
                'barangay' => $byBarangay,
                'daily' => $daily,
            ],
            'list' => $list->through(fn ($d) => [
                'id' => $d->id,
                'disease_type' => $d->disease_type,
                'patient' => $d->patient?->user?->name ?? 'N/A',
                'barangay' => $d->barangay?->name ?? 'Unknown',
                'diagnosis_date' => optional($d->diagnosis_date)?->toDateString(),
                'status' => $d->status,
            ]),
            'pagination' => $this->paginationMeta($list),
        ];
    }

    /**
     * Build a feedback report dataset.
     *
     * @param  array{from?:string|null,to?:string|null}  $filters
     */
    public function getFeedbackReport(array $filters, ?User $viewer = null): array
    {
        $from = isset($filters['from']) && $filters['from'] ? Carbon::parse($filters['from'])->startOfDay() : now()->startOfMonth();
        $to = isset($filters['to']) && $filters['to'] ? Carbon::parse($filters['to'])->endOfDay() : now()->endOfDay();

        $query = Feedback::query()
            ->with(['patient.user', 'appointment.service'])
            ->whereBetween('created_at', [$from, $to]);

        // If healthcare admin is category-specific, restrict to appointment's service category
        if ($viewer && $viewer->role?->name === 'healthcare_admin') {
            $category = $viewer->admin_category?->value;
            if ($category && $category !== AdminCategoryEnum::MedicalRecords->value) {
                $serviceCategories = $this->mapAdminCategoryToServiceCategories($viewer->admin_category);
                if ($serviceCategories) {
                    $query->whereHas('appointment.service', fn (Builder $q) => $q->whereIn('category', $serviceCategories));
                }
            }
        }

        $list = (clone $query)->latest()->paginate(20);

        $total = (clone $query)->count();

        $avg = (clone $query)
            ->selectRaw('AVG(overall_rating) as overall, AVG(doctor_rating) as doctor, AVG(facility_rating) as facility, AVG(wait_time_rating) as wait_time')
            ->first();

        $ratingDist = (clone $query)
            ->selectRaw('overall_rating as rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'total' => $total,
            ],
            'averages' => [
                'overall' => round((float) ($avg->overall ?? 0), 2),
                'doctor' => round((float) ($avg->doctor ?? 0), 2),
                'facility' => round((float) ($avg->facility ?? 0), 2),
                'wait_time' => round((float) ($avg->wait_time ?? 0), 2),
            ],
            'distribution' => $ratingDist,
            'list' => $list->through(fn ($f) => [
                'id' => $f->id,
                'patient' => $f->patient?->user?->name ?? 'N/A',
                'appointment' => $f->appointment?->appointment_number ?? 'N/A',
                'overall_rating' => (int) $f->overall_rating,
                'comments' => $f->comments,
                'created_at' => optional($f->created_at)?->toDateTimeString(),
            ]),
            'pagination' => $this->paginationMeta($list),
        ];
    }

    /**
     * Restrict appointments query by healthcare admin category if applicable.
     */
    protected function applyHealthcareAdminScopeToAppointments(Builder $query, ?User $viewer): Builder
    {
        if (! $viewer || $viewer->role?->name !== 'healthcare_admin') {
            return $query;
        }

        $adminCategory = $viewer->admin_category;
        if (! $adminCategory || $adminCategory === AdminCategoryEnum::MedicalRecords) {
            return $query;
        }

        $serviceCategories = $this->mapAdminCategoryToServiceCategories($adminCategory);
        if (! empty($serviceCategories)) {
            $query->whereHas('service', fn (Builder $q) => $q->whereIn('category', $serviceCategories));
        }

        return $query;
    }

    protected function mapAdminCategoryToServiceCategories(?AdminCategoryEnum $adminCategory): array
    {
        return match ($adminCategory) {
            AdminCategoryEnum::HealthCard => ['health_card', 'healthcard'],
            AdminCategoryEnum::HIV => ['hiv_testing', 'hiv'],
            AdminCategoryEnum::Pregnancy => ['pregnancy_care', 'pregnancy'],
            default => [],
        };
    }

    protected function serviceSynonyms(?string $category): array
    {
        return match ($category) {
            'health_card', 'healthcard' => ['health_card', 'healthcard'],
            'hiv_testing', 'hiv' => ['hiv_testing', 'hiv'],
            'pregnancy_care', 'pregnancy' => ['pregnancy_care', 'pregnancy'],
            default => $category ? [$category] : [],
        };
    }

    /**
     * Simplify appointments list and include related info.
     */
    protected function simplifyAppointments(LengthAwarePaginator $paginator)
    {
        return $paginator->through(function ($a) {
            return [
                'id' => $a->id,
                'number' => $a->appointment_number,
                'scheduled_at' => optional($a->scheduled_at)?->toDateTimeString(),
                'status' => $a->status,
                'patient' => $a->patient?->user?->name ?? 'N/A',
                'doctor' => $a->doctor?->user?->name ?? 'Unassigned',
                'service' => $a->service?->name ?? 'N/A',
                'service_category' => $a->service?->category ?? null,
            ];
        });
    }

    /**
     * Extract minimal pagination metadata.
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ];
    }
}
