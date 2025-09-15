<?php

namespace App\Http\Requests\Frontend\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Responses\ApiResponse;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            // 'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'city_id' => 'nullable|exists:cities,id',
            'state_id' => 'nullable|exists:states,id',
            'password' => 'required|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'first_name.max' => 'The first name must not exceed 50 characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.max' => 'The last name must not exceed 50 characters.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username has already been taken.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'The phone number has already been taken.',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city is invalid.',
            'state_id.required' => 'The state field is required.',
            'state_id.exists' => 'The selected state is invalid.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                'Validation failed',
                $validator->errors()->toArray(),
            )
        );
    }
}
