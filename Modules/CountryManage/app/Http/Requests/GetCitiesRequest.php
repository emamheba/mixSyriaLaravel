<?php
namespace Modules\CountryManage\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state_id' => 'required|exists:states,id'
        ];
    }

    public function messages(): array
    {
        return [
            'state_id.required' => __('State ID is required'),
            'state_id.exists' => __('Invalid State ID'),
        ];
    }
}