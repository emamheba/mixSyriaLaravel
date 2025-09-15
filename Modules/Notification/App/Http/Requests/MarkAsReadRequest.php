<?php

namespace Modules\Notification\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkAsReadRequest extends FormRequest
{
    public function authorize()
    {
        $notification = $this->route('notification');
        return $this->user()->id === $notification->user_id;
    }

    public function rules()
    {
        return [];
    }
}