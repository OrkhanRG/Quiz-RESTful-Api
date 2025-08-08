<?php

namespace App\Enums;

class QuestionDifficulty
{
    const EASY = '1';
    const MEDIUM = '2';
    const HARD = '3';

    public static function getLabels(): array
    {
        return [
            self::EASY => 'Asan',
            self::MEDIUM => 'Orta',
            self::HARD => 'Çətin',
        ];
    }

    public static function getLabel(string $difficulty): string
    {
        return self::getLabels()[$difficulty] ?? 'Unknown';
    }
}
