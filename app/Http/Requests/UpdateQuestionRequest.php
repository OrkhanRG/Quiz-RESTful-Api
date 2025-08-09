<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends CreateQuestionRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('question.update');
    }
}
