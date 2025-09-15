<?php

namespace Modules\Chat\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageSendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "from_user" => "required",
            "message" => "nullable",
            "file" => "nullable|mimes:png,jpeg,jpg,webp,gif,pdf",
            "listing_id" => "nullable",
        ];
    }

    public function authorize(): bool {
        return false;
    }
}
