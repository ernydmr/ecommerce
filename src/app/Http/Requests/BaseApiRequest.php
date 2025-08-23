<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Support\ApiResponse;

abstract class BaseApiRequest extends FormRequest
{
    // Varsayılan: herkes çağırabilir.
    public function authorize(): bool
    {
        return true;
    }

    // Tüm validasyon hatalarında standart JSON döndür
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation failed', $validator->errors()->toArray(), 422)
        );
    }
}
