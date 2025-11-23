<?php

namespace App\Enums;

enum HealthCardPurposeEnum: string
{
    case Food = 'food';
    case NonFood = 'non_food';

    public function label(): string
    {
        return match ($this) {
            self::Food => 'Food Handler',
            self::NonFood => 'Non-Food Handler',
        };
    }
}
