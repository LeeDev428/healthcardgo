<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricalHealthCardData extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_date',
        'issued_count',
        'notes',
        'data_source',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'issued_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }
}
