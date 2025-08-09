<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('role.manage');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Rol adı mütləqdir',
            'name.unique' => 'Bu adda rol artıq mövcuddur',
            'display_name.required' => 'Göstəriş adı mütləqdir',
            'permissions.array' => 'İcazələr array formatında olmalıdır',
            'permissions.*.exists' => 'Seçilən icazələrdən biri mövcud deyil'
        ];
    }
}
