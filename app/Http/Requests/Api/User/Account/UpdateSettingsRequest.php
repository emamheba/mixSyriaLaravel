<?php

namespace App\Http\Requests\Api\User\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'show_phone_to_buyers' => 'sometimes|boolean',
            'enable_location_tracking' => 'sometimes|boolean',
            'share_usage_data' => 'sometimes|boolean',
            
            'enable_all_notifications' => 'sometimes|boolean',
            'new_message_notifications' => 'sometimes|boolean',
            'listing_comment_notifications' => 'sometimes|boolean',
            'weekly_email_summary' => 'sometimes|boolean',
            'email_matching_listings' => 'sometimes|boolean',
            'email_offers_promotions' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            
            'theme' => 'sometimes|in:light,dark',
            'language' => 'sometimes|in:ar,en',
            'show_nearby_listings' => 'sometimes|boolean',
            'show_currency_rates' => 'sometimes|boolean',
            'enable_image_caching' => 'sometimes|boolean',
            'disable_listing_comments' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'theme.in' => 'المظهر يجب أن يكون فاتح أو داكن',
            'language.in' => 'اللغة يجب أن تكون عربية أو إنجليزية',
            '*.boolean' => 'القيمة يجب أن تكون صحيحة أو خاطئة',
        ];
    }
}