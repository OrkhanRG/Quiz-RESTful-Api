<?php

namespace App\Http\Requests;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('question.create');
    }

    public function rules()
    {
        return [
            'question_text' => 'required|string',
            'type' => 'required|string|in:1,2,3,4',
            'points' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'difficulty' => 'required|string|in:1,2,3',
            'options' => 'required|array|min:1',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'question_text.required' => 'Sual mətni mütləqdir',
            'type.required' => 'Sual növü seçilməlidir',
            'type.in' => 'Yalnış sual növü',
            'points.required' => 'Xal sayı mütləqdir',
            'points.min' => 'Minimum 1 xal olmalıdır',
            'points.max' => 'Maksimum 100 xal ola bilər',
            'difficulty.required' => 'Çətinlik səviyyəsi seçilməlidir',
            'difficulty.in' => 'Yalnış çətinlik səviyyəsi',
            'options.required' => 'Ən azı bir seçim olmalıdır',
            'options.min' => 'Ən azı bir seçim olmalıdır',
            'options.*.text.required' => 'Seçim mətni mütləqdir',
            'options.*.is_correct.required' => 'Seçimin düzgünlüyü göstərilməlidir',
            'image.image' => 'Fayl şəkil formatında olmalıdır',
            'image.mimes' => 'Şəkil jpeg, png, jpg və ya gif formatında olmalıdır',
            'image.max' => 'Şəkil maksimum 2MB ola bilər'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->type == QuestionType::MULTIPLE_CHOICE || $this->type == QuestionType::TRUE_FALSE) {
                $correctOptions = collect($this->options)->where('is_correct', true)->count();

                if ($this->type == QuestionType::TRUE_FALSE && count($this->options) != 2) {
                    $validator->errors()->add('options', 'Doğru/Yanlış sualında yalnız 2 seçim olmalıdır');
                }

                if ($correctOptions == 0) {
                    $validator->errors()->add('options', 'Ən azı bir düzgün cavab olmalıdır');
                }

                if ($this->type == QuestionType::TRUE_FALSE && $correctOptions != 1) {
                    $validator->errors()->add('options', 'Doğru/Yanlış sualında yalnız bir düzgün cavab olmalıdır');
                }
            }
        });
    }
}
