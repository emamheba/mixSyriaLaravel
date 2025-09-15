<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditListingRequest extends FormRequest
{
        public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $listingId = $this->route('id');

        return [
            'listing_type' => 'required|in:sell,rent,job,service',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'child_category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'negotiable' => 'boolean',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string',
            'phone_hidden' => 'boolean',
            'product_condition' => 'nullable|in:new,used',
            'image' => 'required|image',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
        ];
    }


    public function messages(): array
    {
        return [
            'category_id.required'       => __('The category field is required.'),
            'category_id.exists'         => __('The selected category is invalid.'),

            'sub_category_id.exists'     => __('The selected sub category is invalid.'),
            'child_category_id.exists'   => __('The selected child category is invalid.'),

            'country_id.exists'          => __('The selected country is invalid.'),
            'state_id.exists'            => __('The selected state is invalid.'),
            'city_id.exists'             => __('The selected city is invalid.'),

            'brand_id.exists'            => __('The selected brand is invalid.'),

            'title.required'             => __('The title field is required.'),
            'title.max'                  => __('The title must not exceed 191 characters.'),

            'slug.required'              => __('The slug field is required.'),
            'slug.unique'                => __('The slug has already been taken.'),

            'description.required'       => __('The description field is required.'),
            'description.min'            => __('The description must be at least 150 characters.'),

            'price.required'             => __('The price field is required.'),
            'price.numeric'              => __('The price must be a numeric value.'),

            'negotiable.boolean'         => __('The negotiable field must be true or false.'),
            'condition.max'              => __('The condition must not exceed 50 characters.'),
            'authenticity.max'           => __('The authenticity must not exceed 50 characters.'),

            'phone.max'                  => __('The phone number must not exceed 20 characters.'),
            'country_code.max'           => __('The country code must not exceed 5 characters.'),
            'phone_hidden.boolean'       => __('The phone hidden field must be true or false.'),

            'image.url'                  => __('The image must be a valid URL.'),
            'gallery_images.array'       => __('The gallery images must be an array.'),
            'gallery_images.*.url'       => __('Each gallery image must be a valid URL.'),
            'video_url.url'              => __('The video URL must be a valid URL.'),

            'address.max'                => __('The address must not exceed 255 characters.'),
            'latitude.numeric'           => __('The latitude must be numeric.'),
            'longitude.numeric'          => __('The longitude must be numeric.'),

            'is_featured.boolean'        => __('The is featured field must be true or false.'),

            'tags.array'                 => __('The tags must be an array.'),
            'tags.*.exists'              => __('One or more selected tags are invalid.'),

            'attributes_title.array'         => __('The attributes title must be an array.'),
            'attributes_title.*.max'           => __('Each attribute title must not exceed 255 characters.'),
            'attributes_description.array'   => __('The attributes description must be an array.'),
            'attributes_description.*.max'     => __('Each attribute description must not exceed 1000 characters.'),
        ];
    }
}
