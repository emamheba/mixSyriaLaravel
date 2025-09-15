<?php

namespace App\Http\Requests\Api\User\Account;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
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
            'reason.required' => 'Reason for deletion is required',
            'reason.max' => 'Reason must not exceed 255 characters',
            'description.required' => 'Description is required',
            'description.max' => 'Description must not exceed 150 characters',
        ];
    }
}
