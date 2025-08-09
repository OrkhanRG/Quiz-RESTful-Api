<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CreateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('category.create');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Kateqoriya adı mütləqdir',
            'name.max' => 'Kateqoriya adı maksimum 255 simvol ola bilər',
            'name.unique' => 'Bu adda kateqoriya artıq mövcuddur',
            'slug.unique' => 'Bu slug artıq istifadə edilir'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->slug) {
            $this->merge([
                'slug' => Str::slug($this->slug)
            ]);
        } elseif ($this->name) {
            $this->merge([
                'slug' => Str::slug($this->name)
            ]);
        }
    }
}
