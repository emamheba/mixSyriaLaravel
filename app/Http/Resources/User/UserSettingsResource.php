<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'security' => [
                'show_phone_to_buyers' => $this->show_phone_to_buyers,
                'enable_location_tracking' => $this->enable_location_tracking,
                'share_usage_data' => $this->share_usage_data,
            ],
            'notifications' => [
                'enable_all_notifications' => $this->enable_all_notifications,
                'new_message_notifications' => $this->new_message_notifications,
                'listing_comment_notifications' => $this->listing_comment_notifications,
                'weekly_email_summary' => $this->weekly_email_summary,
                'email_matching_listings' => $this->email_matching_listings,
                'email_offers_promotions' => $this->email_offers_promotions,
                'sms_notifications' => $this->sms_notifications,
            ],
            'general' => [
                'theme' => $this->theme,
                'language' => $this->language,
                'show_nearby_listings' => $this->show_nearby_listings,
                'show_currency_rates' => $this->show_currency_rates,
                'enable_image_caching' => $this->enable_image_caching,
                'disable_listing_comments' => $this->disable_listing_comments,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}