<?php

namespace App\Enums;

class UserStatus
{
    const INACTIVE = '0';
    const ACTIVE = '1';
    const SUSPENDED = '2';

    public static function getLabels(): array
    {
        return [
            self::INACTIVE => 'Qeyri-aktiv',
            self::ACTIVE => 'Aktiv',
            self::SUSPENDED => 'Dayandırılmış',
        ];
    }

    public static function getLabel(string $status): string
    {
        return self::getLabels()[$status] ?? 'Unknown';
    }
}
