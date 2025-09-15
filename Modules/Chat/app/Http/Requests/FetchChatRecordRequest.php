<?php

namespace Modules\Chat\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchChatRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user_id" => "required|exists:users,id",
            "member_id" => "required|exists:users,id",
            "listing_id" => "nullable|exists:listings,id",
            "from_user" => "required"
        ];
    }

    protected function prepareForValidation()
    {

        return $this->merge(
            $this->from_user == 1 ? ["user_id" => auth('web')->id()] : ["member_id" => auth('web')->id()]
                + ['from_user' => $this->from_user == 1 ? 1 : 2]
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
