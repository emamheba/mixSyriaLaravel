<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        config([
            'services.google.client_id' => get_static_option('google_client_id'),
            'services.google.client_secret' => get_static_option('google_client_secret'),
            'services.google.redirect' => get_static_option('google_callback_url'),

            'services.facebook.client_id' => get_static_option('facebook_client_id'),
            'services.facebook.client_secret' => get_static_option('facebook_client_secret'),
            'services.facebook.redirect' => get_static_option('facebook_callback_url'),
        ]);
    }
}
