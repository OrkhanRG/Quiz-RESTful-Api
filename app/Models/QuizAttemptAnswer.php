<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_options',
        'text_answer',
        'is_correct',
        'points_earned',
        'answered_at'
    ];

    protected $casts = [
        'selected_options' => 'array',
        'is_correct' => 'string',
        'answered_at' => 'datetime'
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function isCorrect(): bool
    {
        return $this->is_correct === '1';
    }
}

