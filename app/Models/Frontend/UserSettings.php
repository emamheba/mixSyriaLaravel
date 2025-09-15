<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'show_phone_to_buyers',
        'enable_location_tracking',
        'share_usage_data',
        'enable_all_notifications',
        'new_message_notifications',
        'listing_comment_notifications',
        'weekly_email_summary',
        'email_matching_listings',
        'email_offers_promotions',
        'sms_notifications',
        'theme',
        'language',
        'show_nearby_listings',
        'show_currency_rates',
        'enable_image_caching',
        'disable_listing_comments',
    ];

    protected $casts = [
        'show_phone_to_buyers' => 'boolean',
        'enable_location_tracking' => 'boolean',
        'share_usage_data' => 'boolean',
        'enable_all_notifications' => 'boolean',
        'new_message_notifications' => 'boolean',
        'listing_comment_notifications' => 'boolean',
        'weekly_email_summary' => 'boolean',
        'email_matching_listings' => 'boolean',
        'email_offers_promotions' => 'boolean',
        'sms_notifications' => 'boolean',
        'show_nearby_listings' => 'boolean',
        'show_currency_rates' => 'boolean',
        'enable_image_caching' => 'boolean',
        'disable_listing_comments' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

  
    public function getGroupedSettings(): array
    {
        return [
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
        ];
    }
}