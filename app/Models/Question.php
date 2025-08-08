<?php

namespace App\Models;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question_text',
        'type',
        'points',
        'explanation',
        'image',
        'difficulty',
        'status',
        'created_by'
    ];

    protected $casts = [
        'type' => 'string',
        'difficulty' => 'string',
        'status' => 'string'
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
            ->withPivot('order');
    }

    public function getCorrectOptionsAttribute()
    {
        return $this->options()->where('is_correct', '1')->get();
    }

    public function isActive(): bool
    {
        return $this->status === '1';
    }

    public function getTypeLabelAttribute(): string
    {
        return QuestionType::getLabel($this->type);
    }

    public function getDifficultyLabelAttribute(): string
    {
        return QuestionDifficulty::getLabel($this->difficulty);
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === QuestionType::MULTIPLE_CHOICE;
    }

    public function isTrueFalse(): bool
    {
        return $this->type === QuestionType::TRUE_FALSE;
    }

    public function isText(): bool
    {
        return $this->type === QuestionType::TEXT;
    }

    public function isEssay(): bool
    {
        return $this->type === QuestionType::ESSAY;
    }
}
