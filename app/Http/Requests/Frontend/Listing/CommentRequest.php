<?php

namespace App\Http\Requests\Frontend\Listing;


use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content' => 'required|string|max:500'
        ];
    }
}
