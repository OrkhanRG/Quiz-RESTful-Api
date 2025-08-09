<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserStatusRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('user.manage');
    }

    public function rules()
    {
        return [
            'status' => 'required|string|in:0,1,2'
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Status mütləqdir',
            'status.in' => 'Yalnış status dəyəri'
        ];
    }
}
