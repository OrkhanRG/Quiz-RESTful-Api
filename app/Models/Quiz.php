<?php

namespace App\Models;

use App\Enums\QuizStatus;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'teacher_id',
        'time_limit',
        'max_attempts',
        'shuffle_questions',
        'show_results_immediately',
        'status',
        'starts_at',
        'ends_at'
    ];

    protected $casts = [
        'shuffle_questions' => 'string',
        'show_results_immediately' => 'string',
        'status' => 'string',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot('order')
            ->orderBy('quiz_questions.order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isActive(): bool
    {
        return $this->status === QuizStatus::ACTIVE &&
            (!$this->starts_at || $this->starts_at <= now()) &&
            (!$this->ends_at || $this->ends_at >= now());
    }

    public function isDraft(): bool
    {
        return $this->status === QuizStatus::DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === QuizStatus::ARCHIVED;
    }

    public function getStatusLabelAttribute(): string
    {
        return QuizStatus::getLabel($this->status);
    }

    public function getShuffleQuestionsLabelAttribute(): string
    {
        return $this->shuffle_questions === '1' ? 'Bəli' : 'Xeyr';
    }
}
