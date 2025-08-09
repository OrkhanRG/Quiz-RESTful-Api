<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('user.manage');
    }

    public function rules()
    {
        return [
            'role' => 'required|string|exists:roles,name'
        ];
    }

    public function messages()
    {
        return [
            'role.required' => 'Rol mütləqdir',
            'role.exists' => 'Seçilən rol mövcud deyil'
        ];
    }
}
