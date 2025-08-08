<?php

namespace App\Models;

use App\Enums\QuizAttemptStatus;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'completed_at',
        'score',
        'total_questions',
        'correct_answers',
        'answers',
        'status'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'status' => 'string'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function attemptAnswers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }

    public function isInProgress(): bool
    {
        return $this->status === QuizAttemptStatus::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === QuizAttemptStatus::COMPLETED;
    }

    public function isAbandoned(): bool
    {
        return $this->status === QuizAttemptStatus::ABANDONED;
    }

    public function isExpired(): bool
    {
        return $this->status === QuizAttemptStatus::EXPIRED;
    }

    public function getStatusLabelAttribute(): string
    {
        return QuizAttemptStatus::getLabel($this->status);
    }

    public function getPercentageAttribute(): float
    {
        return $this->total_questions > 0
            ? round(($this->correct_answers / $this->total_questions) * 100, 2)
            : 0;
    }
}
