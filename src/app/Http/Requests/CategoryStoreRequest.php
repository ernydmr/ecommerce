<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // auth:api + admin middleware zaten var; burada true olsun
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ];
    }
}
