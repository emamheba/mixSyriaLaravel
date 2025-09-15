<?php

namespace App\Http\Requests\Api\GuestListing;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuestListingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id'              => 'required|exists:categories,id',
            'sub_category_id'          => 'nullable|exists:sub_categories,id',
            'child_category_id'        => 'nullable|exists:child_categories,id',
            'brand_id'                 => 'nullable|exists:brands,id',
            'country_id'               => 'nullable|exists:countries,id',
            'state_id'                 => 'nullable|exists:states,id',
            'city_id'                  => 'nullable|exists:cities,id',
            'terms_conditions'         => 'required|in:1',
            'title'                    => 'required|string|max:191',
            'description'              => 'required|string|min:150',
            'slug'                     => 'required|unique:listings,slug',
            'price'                    => 'required|numeric',
            'guest_first_name'         => 'required|string|max:191',
            'guest_last_name'          => 'required|string|max:191',
            'guest_email'              => 'required|email|unique:users,email|max:191',
            'guest_phone'              => 'required|unique:users,phone|max:191',
            'negotiable'               => 'nullable|in:0,1',
            'phone_hidden'             => 'nullable|in:0,1',
            'condition'                => 'nullable|in:new,used',
            'authenticity'             => 'nullable|in:original,refurbished',
            'image'                    => 'nullable|integer',
            'gallery_images'           => 'nullable|string',
            'video_url'                => 'nullable|url',
            'address'                  => 'nullable|string|max:191',
            'latitude'                 => 'nullable|string',
            'longitude'                => 'nullable|string',
            'is_featured'              => 'nullable|in:0,1',
            'attributes_title'         => 'nullable|array',
            'attributes_title.*'       => 'nullable|string|max:255',
            'attributes_description'   => 'nullable|array',
            'attributes_description.*' => 'nullable|string|max:1000',
            'tags'                     => 'nullable|array',
            'tags.*'                   => 'exists:tags,id',
            'guest_register_request'   => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required'            => 'The title field is required.',
            'description.required'      => 'The description field is required.',
            'description.min'           => 'The description must be at least 150 characters.',
            'slug.required'             => 'The slug field is required.',
            'slug.unique'               => 'The slug has already been taken.',
            'price.required'            => 'The price field is required.',
            'price.numeric'             => 'The price must be a numeric value.',
            'guest_first_name.required' => 'The first name field is required.',
            'guest_last_name.required'  => 'The last name field is required.',
            'guest_email.required'      => 'The email field is required.',
            'guest_email.email'         => 'The email format is invalid.',
            'guest_phone.required'      => 'The phone field is required.',
            'category_id.required'      => 'A main category must be selected.',
            'terms_conditions.in'       => 'You must agree to the terms and conditions.',
        ];
    }
}
