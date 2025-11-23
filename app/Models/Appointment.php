<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory; // Enable model factories

    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'service_id',
        'scheduled_at',
        'queue_number',
        'check_in_at',
        'started_at',
        'completed_at',
        'status',
        'notes',
        'health_card_purpose',
        'cancellation_reason',
        'reminder_sent',
        'fee',
        // Files generated for QR and digital copy
        'qr_code_path',
        'digital_copy_path',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'check_in_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'queue_number' => 'integer',
            'reminder_sent' => 'array',
            'fee' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->appointment_number)) {
                $appointment->appointment_number = 'A'.date('Y').str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
            $this->scheduled_at > now()->addHours(24);
    }

    public function canCheckIn(): bool
    {
        return $this->status === 'confirmed' &&
            now()->diffInMinutes($this->scheduled_at, false) <= 30;
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => 'checked_in',
            'check_in_at' => now(),
        ]);
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(?string $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    public function markAsNoShow(): void
    {
        $this->update([
            'status' => 'no_show',
        ]);
    }
}
