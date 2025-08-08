<?php

namespace App\Enums;

class QuizAttemptStatus
{
    const IN_PROGRESS = '1';
    const COMPLETED = '2';
    const ABANDONED = '3';
    const EXPIRED = '4';

    public static function getLabels(): array
    {
        return [
            self::IN_PROGRESS => 'Davam edir',
            self::COMPLETED => 'Tamamlanmış',
            self::ABANDONED => 'Tərk edilmiş',
            self::EXPIRED => 'Vaxtı keçmiş',
        ];
    }

    public static function getLabel(string $status): string
    {
        return self::getLabels()[$status] ?? 'Unknown';
    }
}
