<?php
namespace Modules\Brand\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBrandsByCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكنك تعديل هذا بناءً على صلاحيات المستخدم
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => __('Category ID is required'),
            'category_id.exists'   => __('The selected category ID is invalid'),
        ];
    }
}