<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('quiz.update');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'time_limit' => 'nullable|integer|min:1|max:480',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'status' => 'string|in:0,1,2',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'questions' => 'array',
            'questions.*' => 'exists:questions,id'
        ];
    }
}
