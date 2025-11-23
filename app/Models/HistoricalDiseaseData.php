<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricalDiseaseData extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_type',
        'barangay_id',
        'record_date',
        'case_count',
        'notes',
        'data_source',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'case_count' => 'integer',
        ];
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByDiseaseType($query, string $diseaseType)
    {
        return $query->where('disease_type', $diseaseType);
    }

    public function scopeByBarangay($query, int $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }
}
