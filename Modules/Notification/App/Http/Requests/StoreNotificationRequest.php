<?php

namespace Modules\Notification\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize()
    {
        // Typically only admins or system should create notifications
        return $this->user()->can('create', Notification::class);
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|exists:notification_types,slug',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array'
        ];
    }
}