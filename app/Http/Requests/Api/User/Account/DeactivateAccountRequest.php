<?php

namespace App\Http\Requests\Api\User\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Responses\ApiResponse;

class DeactivateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Reason is required',
            'reason.max' => 'Reason must not exceed 255 characters',
            'description.required' => 'Description is required',
            'description.max' => 'Description must not exceed 150 characters',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation failed', $validator->errors(), 422)
        );
    }
}