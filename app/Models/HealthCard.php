<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'card_number',
        'issue_date',
        'expiry_date',
        'qr_code',
        'status',
        'medical_data',
        'last_renewed_at',
    ];

    protected function casts()
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'last_renewed_at' => 'datetime',
            'medical_data' => 'array',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && now()->lessThanOrEqualTo($this->expiry_date);
    }

    public function isExpired()
    {
        return $this->status === 'expired' || now()->greaterThan($this->expiry_date);
    }

    public static function generateCardNumber(): string
    {
        do {
            $number = 'HC'.date('Y').str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('card_number', $number)->exists());

        return $number;
    }

    public function generateQrCode(): string
    {
        return base64_encode("{$this->card_number}|{$this->patient_id}|{$this->issue_date}");
    }
}
