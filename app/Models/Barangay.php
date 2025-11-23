<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'latitude',
        'longitude',
        'population',
        'boundaries',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'boundaries' => 'array',
        ];
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
