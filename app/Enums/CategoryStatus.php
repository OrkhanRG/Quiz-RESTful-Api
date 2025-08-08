<?php

namespace App\Enums;

class CategoryStatus
{
    const DEACTIVATE = '0';
    const ACTIVE = '1';

    public static function getLabels(): array
    {
        return [
            self::ACTIVE => 'Aktiv',
            self::DEACTIVATE => 'Qeyri-aktiv',
        ];
    }

    public static function getLabel(string $status): string
    {
        return self::getLabels()[$status] ?? 'Unknown';
    }
}
