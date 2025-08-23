<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['required','string','min:3'],
            'description'    => ['nullable','string'],
            'price'          => ['required','numeric','min:0.01'],
            'stock_quantity' => ['required','integer','min:0'],
            'category_id'    => ['required','integer','exists:categories,id'],
        ];
    }
}
