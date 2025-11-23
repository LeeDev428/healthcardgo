<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'contact_number',
        'date_of_birth',
        'gender',
        'barangay_id',
        'patient_number',
        'philhealth_number',
        'medical_history',
        'allergies',
        'current_medications',
        'insurance_info',
        'emergency_contact',
        'accessibility_requirements',
        'photo_path',
        'blood_type',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'medical_history' => 'array',
            'allergies' => 'array',
            'current_medications' => 'array',
            'insurance_info' => 'array',
            'emergency_contact' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->patient_number)) {
                $patient->patient_number = static::generatePatientNumber();
            }
        });
    }

    public static function generatePatientNumber(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return 'P'.$year.str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function upcomingAppointments()
    {
        return $this->appointments()
            ->where('scheduled_at', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('scheduled_at');
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return null;
        }

        return Carbon::parse($this->date_of_birth)->diffInYears(now());
    }

    public function getFullNameAttribute(): string
    {
        // Prefer patient-specific full name, fallback to linked user name, else empty string
        return $this->attributes['full_name']
            ?? ($this->user?->name ?? '');
    }

    public function healthCards()
    {
        return $this->hasMany(HealthCard::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
