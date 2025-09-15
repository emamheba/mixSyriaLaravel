<?php

namespace App\Http\Requests\Api\User\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Responses\ApiResponse;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'listing_type' => 'required|in:sell,rent,job,service',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'negotiable' => 'nullable|boolean',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'address' => 'nullable|string',
            'phone_hidden' => 'nullable|boolean',
            'condition' => 'nullable|in:new,used',
            'image' => 'nullable|image',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image',
            'deleted_images' => 'nullable|array',
            'lat' => 'nullable|numeric|between:-90,90',
            'lon' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 50 characters.',
            'description.required' => 'The description field is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a numeric value.',
            'district_id.required' => 'The district field is required.',
            'image.max' => 'Main image size should not exceed 5MB.',
            'gallery_images.max' => 'Gallery images cannot exceed 10 images.',
            'gallery_images.*.image' => 'Each gallery image must be a valid image file.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                'Validation failed',
                $validator->errors()->getMessages(),
            )
        );
    }
}
