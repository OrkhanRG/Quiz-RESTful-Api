<?php

namespace App\Enums;

class QuizStatus
{
    const DRAFT = '0';
    const ACTIVE = '1';
    const ARCHIVED = '2';

    public static function getLabels(): array
    {
        return [
            self::DRAFT => 'Qaralama',
            self::ACTIVE => 'Aktiv',
            self::ARCHIVED => 'Arxivləşdirilmiş',
        ];
    }

    public static function getLabel(string $status): string
    {
        return self::getLabels()[$status] ?? 'Unknown';
    }
}
