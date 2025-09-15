<?php

namespace Modules\Notification\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize()
    {
        $notification = $this->route('notification');
        return $this->user()->can('update', $notification);
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'data' => 'nullable|array'
        ];
    }
}