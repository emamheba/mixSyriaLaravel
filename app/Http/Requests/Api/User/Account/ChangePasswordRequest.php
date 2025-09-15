<?php

namespace App\Http\Requests\Api\User\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Responses\ApiResponse;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Current password is required',
            'current_password.min' => 'Current password must be at least 6 characters',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'New password must be at least 6 characters',
            'new_password.confirmed' => 'New password confirmation does not match',
            'new_password_confirmation.required' => 'Password confirmation is required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation failed', $validator->errors(), 422)
        );
    }
}
