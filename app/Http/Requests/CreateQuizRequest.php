<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuizRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('quiz.create');
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
            'starts_at' => 'nullable|date|after:now',
            'ends_at' => 'nullable|date|after:starts_at',
            'questions' => 'array',
            'questions.*' => 'exists:questions,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Test başlığı mütləqdir',
            'title.max' => 'Test başlığı maksimum 255 simvol ola bilər',
            'category_id.required' => 'Kateqoriya seçilməlidir',
            'category_id.exists' => 'Seçilən kateqoriya mövcud deyil',
            'time_limit.min' => 'Vaxt limiti minimum 1 dəqiqə olmalıdır',
            'time_limit.max' => 'Vaxt limiti maksimum 480 dəqiqə (8 saat) ola bilər',
            'max_attempts.required' => 'Maksimum cəhd sayı mütləqdir',
            'max_attempts.min' => 'Minimum 1 cəhd olmalıdır',
            'max_attempts.max' => 'Maksimum 10 cəhd ola bilər',
            'starts_at.after' => 'Başlama tarixi indiki vaxtdan sonra olmalıdır',
            'ends_at.after' => 'Bitmə tarixi başlama tarixindən sonra olmalıdır',
            'questions.*.exists' => 'Seçilən suallardan biri mövcud deyil'
        ];
    }
}
