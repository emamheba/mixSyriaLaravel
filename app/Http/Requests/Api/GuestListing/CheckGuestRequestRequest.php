<?php

namespace App\Http\Requests\Api\GuestListing;

use Illuminate\Foundation\Http\FormRequest;

class CheckGuestRequestRequest extends FormRequest
{
    /**
     * التحقق من صلاحية الطلب
     * 
     * @return bool
     */
    public function authorize()
    {
        return true; // يمكن تعديل هذا المنطق للتحقق من الصلاحيات إذا لزم الأمر
    }

    /**
     * قواعد التحقق من البيانات
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'guest_email' => 'required|email',
            'guest_phone' => 'required',
            'guest_first_name' => 'required',
            'guest_last_name' => 'required',
            'guest_register_request' => 'nullable',
        ];
    }
}