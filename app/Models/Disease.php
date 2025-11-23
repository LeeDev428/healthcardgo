<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_record_id',
        'disease_type',
        'case_number',
        'status',
        'onset_date',
        'reported_date',
        'confirmed_date',
        'diagnosis_date',
        'barangay_id',
        'symptoms',
        'risk_factors',
        'treatment_notes',
        'severity',
        'reported_by',
    ];

    protected function casts(): array
    {
        return [
            'onset_date' => 'date',
            'reported_date' => 'date',
            'confirmed_date' => 'date',
            'diagnosis_date' => 'date',
            'symptoms' => 'array',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get confirmed cases only
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Get cases by disease type
     */
    public function scopeByDiseaseType($query, string $diseaseType)
    {
        return $query->where('disease_type', $diseaseType);
    }

    /**
     * Get cases by barangay
     */
    public function scopeByBarangay($query, int $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    /**
     * Get cases within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('diagnosis_date', [$startDate, $endDate]);
    }

    /**
     * Get cases between dates (alias for compatibility)
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('diagnosis_date', [$startDate, $endDate]);
    }

    /**
     * Get cases of specific type (alias for compatibility)
     */
    public function scopeOfType($query, string $diseaseType)
    {
        return $query->where('disease_type', $diseaseType);
    }

    /**
     * Get cases in specific barangay (alias for compatibility)
     */
    public function scopeInBarangay($query, int $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }
}
