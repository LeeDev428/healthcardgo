<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'doctor_id',
        'service_id',
        'record_type',
        'category',
        'template_type',
        'title',
        'description',
        'diagnosis',
        'diagnosis_codes',
        'treatment',
        'notes',
        'record_data',
        'attachments',
        'is_encrypted',
        'recorded_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'diagnosis_codes' => 'array',
            'record_data' => 'array',
            'attachments' => 'array',
            'is_encrypted' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function diseases(): HasMany
    {
        return $this->hasMany(Disease::class);
    }

    /**
     * Get records by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get encrypted records
     */
    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }
}
