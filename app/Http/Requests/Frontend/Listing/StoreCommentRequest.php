<?php

namespace App\Http\Requests\Frontend\Listing;

use Illuminate\Foundation\Http\FormRequest;


class StoreCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string|max:500'
        ];
    }
}
