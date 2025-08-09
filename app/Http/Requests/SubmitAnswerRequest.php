<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('quiz.take');
    }

    public function rules()
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'selected_options' => 'nullable|array',
            'selected_options.*' => 'exists:question_options,id',
            'text_answer' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'question_id.required' => 'Sual ID-si mütləqdir',
            'question_id.exists' => 'Sual mövcud deyil',
            'selected_options.array' => 'Seçimlər array formatında olmalıdır',
            'selected_options.*.exists' => 'Seçilən variant mövcud deyil',
            'text_answer.max' => 'Mətn cavabı maksimum 1000 simvol ola bilər'
        ];
    }
}
