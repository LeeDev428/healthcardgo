<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'fee',
        'category',
        'requirements',
        'preparation_instructions',
        'requires_appointment',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'requirements' => 'array',
            'preparation_instructions' => 'array',
            'requires_appointment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function getCategories(): array
    {
        return [
            'health_card' => 'Health Card Services',
            'hiv_testing' => 'HIV Testing & Counseling',
            'pregnancy_care' => 'Pregnancy Care & Monitoring',
            'vaccination' => 'Vaccination Services',
            'laboratory' => 'Laboratory Services',
            'health_education' => 'Health Education & Promotion',
            'emergency' => 'Emergency Response',
            'consultation' => 'Medical Consultation',
            'dental' => 'Dental Services',
            'pediatric' => 'Pediatric Care',
        ];
    }

    public function getCategoryNameAttribute(): string
    {
        return static::getCategories()[$this->category] ?? $this->category;
    }
}
