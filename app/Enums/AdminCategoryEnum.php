<?php

namespace App\Enums;

enum AdminCategoryEnum: string
{
    case HealthCard = 'healthcard';
    case HIV = 'hiv';
    case Pregnancy = 'pregnancy';
    case MedicalRecords = 'medical_records';

    public function label(): string
    {
        return match ($this) {
            self::HealthCard => 'Health Card',
            self::HIV => 'HIV',
            self::Pregnancy => 'Pregnancy',
            self::MedicalRecords => 'Medical Records',
        };
    }
}
