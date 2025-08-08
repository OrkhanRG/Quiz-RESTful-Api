<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order'
    ];

    protected $casts = [
        'is_correct' => 'string'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function isCorrect(): bool
    {
        return $this->is_correct === '1';
    }
}
