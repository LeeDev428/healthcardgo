<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseasePrediction extends Model
{
    protected $fillable = [
        'disease_type',
        'barangay_id',
        'prediction_date',
        'predicted_cases',
        'confidence_interval_lower',
        'confidence_interval_upper',
        'model_version',
        'accuracy_metrics',
    ];

    protected function casts(): array
    {
        return [
            'prediction_date' => 'date',
            'predicted_cases' => 'decimal:2',
            'confidence_interval_lower' => 'decimal:2',
            'confidence_interval_upper' => 'decimal:2',
            'accuracy_metrics' => 'array',
        ];
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
