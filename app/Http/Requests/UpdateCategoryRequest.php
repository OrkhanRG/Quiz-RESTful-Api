<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasPermission('category.update');
    }

    public function rules()
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId
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
