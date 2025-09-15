<?php

namespace App\Http\Requests\Frontend\Listing;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:sub_categories,id',
            'child_category_id' => 'nullable|integer|exists:child_categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'district_id' => 'nullable|integer|exists:districts,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'condition' => 'nullable|in:new,used,refurbished',
            'listing_type' => 'nullable|in:sell,rent,wanted,buy,exchange,service,job',
            'featured' => 'nullable|in:true,false',
            'verified_user' => 'nullable|boolean',
            'with_images' => 'nullable|boolean',
            'sort' => 'nullable|in:newest,oldest,price_asc,price_desc,popular,created_at,updated_at,nearest',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'lat' => 'nullable|numeric|between:-90,90',
            'lon' => 'nullable|numeric|between:-180,180',
            'user_id' => 'nullable|string|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',


            'filter_lat' => 'nullable|numeric|between:-90,90',
            'filter_lon' => 'nullable|numeric|between:-180,180',

            'user_lat' => 'nullable|numeric|between:-90,90',
            'user_lon' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        $tags = $this->tags;
        if ($tags && is_string($tags)) {
            $tags = explode(',', $tags);
        }

        $this->merge([
            'radius' => $this->radius ?? 10,
            'tags' => $tags ?? [],
            'sort' => $this->sort ?? 'newest',
        ]);
    }
}
