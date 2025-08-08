<?php

namespace App\Enums;

class QuestionType
{
    const MULTIPLE_CHOICE = '1';
    const TRUE_FALSE = '2';
    const TEXT = '3';
    const ESSAY = '4';

    public static function getLabels(): array
    {
        return [
            self::MULTIPLE_CHOICE => 'Çoxseçimli',
            self::TRUE_FALSE => 'Doğru/Yanlış',
            self::TEXT => 'Mətn',
            self::ESSAY => 'Esse',
        ];
    }

    public static function getLabel(string $type): string
    {
        return self::getLabels()[$type] ?? 'Unknown';
    }
}
