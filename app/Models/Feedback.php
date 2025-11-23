<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'overall_rating',
        'doctor_rating',
        'facility_rating',
        'wait_time_rating',
        'would_recommend',
        'comments',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'would_recommend' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function averageRating(): float
    {
        return ($this->overall_rating + $this->doctor_rating + $this->facility_rating + $this->wait_time_rating) / 4;
    }
}
