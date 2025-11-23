<?php

namespace App\Services;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AppointmentService
{
    public function __construct(public NotificationService $notifications) {}

    /**
     * Get the next available queue number for a given date and service
     */
    public function getNextQueueNumber(Carbon $date, int $serviceId): int
    {
        $maxQueue = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->where('service_id', $serviceId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->max('queue_number');

        $nextQueue = ($maxQueue ?? 0) + 1;

        return $nextQueue <= 100 ? $nextQueue : throw new \Exception('Queue is full for this date.');
    }

    /**
     * Enforce a 7-day lead time: date must be at least 7 days from today.
     */
    public function isWithinBookingWindow(Carbon $date): bool
    {
        $today = now()->startOfDay();
        $minDate = $today->copy()->addDays(7); // Minimum 7 days advance booking

        return $date->greaterThanOrEqualTo($minDate);
    }

    /**
     * Get the minimum bookable date (7 days from now)
     */
    public function getMinimumBookableDate(): Carbon
    {
        return now()->addDays(7)->startOfDay();
    }

    /**
     * Check if total daily appointments for a given service would exceed 100
     */
    public function isDailyCapacityAvailable(Carbon $date, int $serviceId): bool
    {
        $totalAppointments = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->where('service_id', $serviceId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->count();

        return $totalAppointments < 100;
    }

    /**
     * Get daily appointment statistics
     */
    public function getDailyStats(Carbon $date): array
    {
        $totalAppointments = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->count();

        return [
            'total_appointments' => $totalAppointments,
            'available_slots' => max(0, 100 - $totalAppointments),
            'is_full' => $totalAppointments >= 100,
            'capacity_percentage' => ($totalAppointments / 100) * 100,
        ];
    }

    /**
     * Book an appointment with queue management and validations
     */
    public function bookAppointment(array $data): Appointment
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);

        // Validate 7-day minimum lead time
        if (! $this->isWithinBookingWindow($scheduledAt)) {
            throw new \Exception('Appointments must be booked at least 7 days in advance.');
        }

        // Validate daily capacity (100 appointments per day per service)
        if (! $this->isDailyCapacityAvailable($scheduledAt, (int) $data['service_id'])) {
            throw new \Exception('Queue is full for this service on the selected date.');
        }

        // Check if patient already has an active appointment for the same service
        $existingAppointment = Appointment::where('patient_id', $data['patient_id'])
            ->where('service_id', $data['service_id'])
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->exists();

        if ($existingAppointment) {
            throw new \Exception('You already have an active appointment for this service. Please cancel or complete it before booking a new one.');
        }

        return DB::transaction(function () use ($data, $scheduledAt) {
            $queueNumber = $this->getNextQueueNumber($scheduledAt, $data['service_id']);

            $appointment = Appointment::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'service_id' => $data['service_id'],
                'scheduled_at' => $scheduledAt,
                'queue_number' => $queueNumber,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'health_card_purpose' => $data['health_card_purpose'] ?? null,
                'fee' => $data['fee'] ?? 0,
            ]);

            if ($appointment->wasRecentlyCreated) {
                $appointment->loadMissing(['service', 'patient.user']);

                // Notify patient that appointment is pending approval
                $this->notifications->sendAppointmentPending($appointment);

                // Notify super admins of new appointment
                $this->notifications->sendNewAppointmentToAdmin($appointment);

                // Notify healthcare admins for review and approval
                $this->notifications->sendNewAppointmentToHealthcareAdmins($appointment);
            }

            // Generate the digital copy PDF and QR image assets
            $this->ensureAssetsGenerated($appointment);

            return $appointment;
        });
    }

    /**
     * Ensure appointment has generated assets (PDF and QR). If either is missing, (re)generate.
     */
    public function ensureAssetsGenerated(Appointment $appointment): void
    {
        $needsPdf = empty($appointment->digital_copy_path);
        $needsQr = empty($appointment->qr_code_path);

        if (! $needsPdf && ! $needsQr) {
            return;
        }

        $this->generateAssets($appointment, $needsPdf, $needsQr);
    }

    /**
     * Generate appointment assets (PDF and/or QR) and persist paths on the record.
     */
    private function generateAssets(Appointment $appointment, bool $generatePdf = true, bool $generateQr = true): void
    {
        $updates = [];

        // Build a signed URL for the appointment's public digital copy
        $signedUrl = null;
        try {
            $signedUrl = URL::temporarySignedRoute(
                'appointments.digital',
                now()->addDays(7),
                ['appointment' => $appointment->id]
            );
        } catch (\Throwable $e) {
            // If URL generation fails, we can still generate PDF without QR; skip QR if no URL.
        }

        // Generate PDF (independent of QR generation)
        if ($generatePdf) {
            try {
                $pdf = Pdf::loadView('appointments.digital_copy', ['appointment' => $appointment]);
                $pdfPath = 'appointments/'.strtolower($appointment->appointment_number).'.pdf';
                Storage::disk('public')->put($pdfPath, $pdf->output());
                $updates['digital_copy_path'] = $pdfPath;
            } catch (\Throwable $e) {
                // Swallow to avoid breaking critical flows
            }
        }

        // Generate QR (requires a signed URL)
        if ($generateQr && $signedUrl) {
            try {
                $qrPath = 'appointments/'.strtolower($appointment->appointment_number).'_qr.png';
                $builder = new Builder(
                    writer: new PngWriter,
                    data: $signedUrl,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::High,
                    size: 300,
                    margin: 10,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                );

                $result = $builder->build();
                Storage::disk('public')->put($qrPath, $result->getString());
                $updates['qr_code_path'] = $qrPath;
            } catch (\Throwable $e) {
                // Swallow to avoid breaking critical flows
            }
        }

        if (! empty($updates)) {
            $appointment->update($updates);
        }
    }

    /**
     * Cancel an appointment and recalculate queue numbers
     *
     * @param  bool  $notifyPatient  Whether to send a cancellation notification to the patient (false if patient initiated)
     */
    public function cancelAppointment(Appointment $appointment, string $reason, bool $notifyPatient = true): bool
    {
        if (! $appointment->canBeCancelled()) {
            throw new \Exception('Appointment cannot be cancelled (must be 24+ hours before scheduled time).');
        }

        return DB::transaction(function () use ($appointment, $reason, $notifyPatient) {
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            // Notify patient of cancellation (only if cancelled by admin/staff, not by patient themselves)
            if ($notifyPatient) {
                $appointment->loadMissing(['patient', 'service']);
                $this->notifications->sendAppointmentCancellation($appointment, $reason);
            }

            // Notify healthcare admins of cancellation
            $appointment->loadMissing(['service']);
            $this->notifications->sendAppointmentCancellationToHealthcareAdmins($appointment, $reason);

            // Recalculate queue numbers for appointments after this one
            $this->recalculateQueueNumbers(
                $appointment->scheduled_at,
                $appointment->service_id,
                $appointment->queue_number
            );

            return true;
        });
    }

    /**
     * Recalculate queue numbers after a cancellation
     */
    protected function recalculateQueueNumbers(Carbon $date, int $serviceId, int $cancelledQueue): void
    {
        $appointments = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->where('service_id', $serviceId)
            ->where('queue_number', '>', $cancelledQueue)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->orderBy('queue_number')
            ->get();

        foreach ($appointments as $apt) {
            $apt->update(['queue_number' => $apt->queue_number - 1]);
        }
    }

    /**
     * Get available time slots for a given date and service
     */
    public function getAvailableSlots(Carbon $date, int $serviceId): array
    {
        $bookedCount = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->where('service_id', $serviceId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->count();

        return [
            'available_slots' => max(0, 100 - $bookedCount),
            'booked_slots' => $bookedCount,
            'is_full' => $bookedCount >= 100,
        ];
    }

    /**
     * Check in a patient for their appointment
     */
    public function checkIn(Appointment $appointment): bool
    {
        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            throw new \Exception('Only pending or confirmed appointments can be checked in.');
        }

        $updated = $appointment->update([
            'status' => 'checked_in',
            'check_in_at' => now(),
        ]);

        if ($updated) {
            $appointment->loadMissing(['patient']);
            $this->notifications->sendPatientCheckedIn($appointment);
        }

        return $updated;
    }

    /**
     * Start an appointment (doctor begins consultation)
     */
    public function startAppointment(Appointment $appointment): bool
    {
        if ($appointment->status !== 'checked_in') {
            throw new \Exception('Patient must be checked in before starting appointment.');
        }

        return $appointment->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Complete an appointment
     */
    public function completeAppointment(Appointment $appointment): bool
    {
        if ($appointment->status !== 'in_progress') {
            throw new \Exception('Only in-progress appointments can be completed.');
        }

        return $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark appointments as no-show (called by scheduled job)
     */
    public function markNoShows(): int
    {
        $thirtyMinutesAgo = now()->subMinutes(30);

        $noShowAppointments = Appointment::where('status', 'confirmed')
            ->where('scheduled_at', '<', $thirtyMinutesAgo)
            ->whereNull('check_in_at')
            ->get();

        foreach ($noShowAppointments as $appointment) {
            $appointment->update(['status' => 'no_show']);
        }

        return $noShowAppointments->count();
    }

    /**
     * Get current queue status for a date
     */
    public function getQueueStatus(Carbon $date): array
    {
        $appointments = Appointment::whereDate('scheduled_at', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress', 'completed'])
            ->orderBy('queue_number')
            ->with(['patient.user', 'service'])
            ->get();

        $currentQueue = $appointments->where('status', 'in_progress')->first();
        $waitingCount = $appointments->whereIn('status', ['pending', 'confirmed', 'checked_in'])->count();
        $completedCount = $appointments->where('status', 'completed')->count();

        return [
            'current_queue_number' => $currentQueue?->queue_number,
            'current_patient' => $currentQueue?->patient,
            'waiting_count' => $waitingCount,
            'completed_count' => $completedCount,
            'total_count' => $appointments->count(),
            'appointments' => $appointments,
        ];
    }
}
