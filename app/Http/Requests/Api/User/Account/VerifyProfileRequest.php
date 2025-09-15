<?php

namespace App\Http\Requests\Api\User\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Responses\ApiResponse;

class VerifyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'identification_type' => 'required|string|max:191',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'zip_code' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'identification_number' => 'required|string|max:100',
        ];

        if ($this->hasFile('front_document') || $this->hasFile('back_document')) {
            $rules['front_document'] = 'required|file|mimes:jpg,png,jpeg,webp,pdf|max:10240';
            $rules['back_document'] = 'required|file|mimes:jpg,png,jpeg,webp,pdf|max:10240';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'identification_type.required' => 'Identification type is required',
            'identification_type.max' => 'Identification type must not exceed 191 characters',
            'country_id.required' => 'Country is required',
            'country_id.exists' => 'Selected country is invalid',
            'state_id.required' => 'State is required',
            'state_id.exists' => 'Selected state is invalid',
            'city_id.required' => 'City is required',
            'city_id.exists' => 'Selected city is invalid',
            'zip_code.required' => 'Zip code is required',
            'address.required' => 'Address is required',
            'identification_number.required' => 'Identification number is required',
            'front_document.required' => 'Front document is required',
            'front_document.mimes' => 'Front document must be jpg, png, jpeg, webp, or pdf',
            'front_document.max' => 'Front document size must not exceed 10MB',
            'back_document.required' => 'Back document is required',
            'back_document.mimes' => 'Back document must be jpg, png, jpeg, webp, or pdf',
            'back_document.max' => 'Back document size must not exceed 10MB',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation failed', $validator->errors(), 422)
        );
    }
}