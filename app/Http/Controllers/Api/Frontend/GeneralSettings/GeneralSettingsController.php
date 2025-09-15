<?php

namespace App\Http\Controllers\Api\Frontend\GeneralSettings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\GeneralSettingsResource;
use App\Http\Resources\Settings\ListingSettingsResource;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\CustomFont;
use App\Models\Backend\Language;
use App\Models\Backend\StaticOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralSettingsController extends Controller
{

    public function siteIdentity(): JsonResponse
    {
        try {
            $siteLogoId = get_static_option('site_logo');
            $siteWhiteLogoId = get_static_option('site_white_logo');
            $siteFaviconId = get_static_option('site_favicon');
            $identitySettings = [
                'site_logo' => !empty($siteLogoId) ? get_image_url_id_wise($siteLogoId) : null,
                'site_white_logo' => !empty($siteWhiteLogoId) ? get_image_url_id_wise($siteWhiteLogoId) : null,
                'site_favicon' => !empty($siteFaviconId) ? get_image_url_id_wise($siteFaviconId) : null,
            ];
            return ApiResponse::success('Site identity retrieved successfully', $identitySettings);
        } catch (\Exception $e) {
            Log::error('Site identity fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve site identity settings');
        }
    }

    public function basicSettings(): JsonResponse
    {
        try {
            $basicSettings = [
                'site_title' => get_static_option('site_title'),
                'site_tag_line' => get_static_option('site_tag_line'),
                'site_footer_copyright' => get_static_option('site_footer_copyright'),
                // 'language_select_option' => get_static_option('language_select_option'),
                'user_email_verify_enable_disable' => get_static_option('user_email_verify_enable_disable'),
                'user_otp_verify_enable_disable' => get_static_option('user_otp_verify_enable_disable'),
                // 'site_main_color' => get_static_option('site_main_color'),
                // 'site_secondary_color' => get_static_option('site_secondary_color'),
                // 'site_maintenance_mode' => get_static_option('site_maintenance_mode'),
                // 'admin_loader_animation' => get_static_option('admin_loader_animation'),
                // 'site_loader_animation' => get_static_option('site_loader_animation'),
                // 'admin_panel_rtl_status' => get_static_option('admin_panel_rtl_status'),
                // 'site_force_ssl_redirection' => get_static_option('site_force_ssl_redirection'),
                // 'site_canonical_url_type' => get_static_option('site_canonical_url_type'),
            ];

            return ApiResponse::success('Basic settings retrieved successfully', $basicSettings);
        } catch (\Exception $e) {
            Log::error('Basic settings fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve basic settings');
        }
    }

    public function globalVariantNavbar(): JsonResponse
    {
        try {
            $navbarVariant = [
                'global_navbar_variant' => get_static_option('global_navbar_variant'),
            ];

            return ApiResponse::success('Navbar variant retrieved successfully', $navbarVariant);
        } catch (\Exception $e) {
            Log::error('Navbar variant fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve navbar variant');
        }
    }


    public function globalVariantFooter(): JsonResponse
    {
        try {
            $footerVariant = [
                'global_footer_variant' => get_static_option('global_footer_variant'),
            ];

            return ApiResponse::success('Footer variant retrieved successfully', $footerVariant);
        } catch (\Exception $e) {
            Log::error('Footer variant fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve footer variant');
        }
    }


    public function colorSettings(): JsonResponse
    {
        try {
            $colorSettings = [
                'site_main_color_one' => get_static_option('site_main_color_one'),
                'site_main_color_two' => get_static_option('site_main_color_two'),
                'site_main_color_three' => get_static_option('site_main_color_three'),
                'heading_color' => get_static_option('heading_color'),
                'light_color' => get_static_option('light_color'),
                'extra_light_color' => get_static_option('extra_light_color'),
            ];

            return ApiResponse::success('Color settings retrieved successfully', $colorSettings);
        } catch (\Exception $e) {
            Log::error('Color settings fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve color settings');
        }
    }


    public function seoSettings(): JsonResponse
    {
        try {
            $seoSettings = [
                'site_meta_tags' => get_static_option('site_meta_tags'),
                'site_meta_description' => get_static_option('site_meta_description'),
                'og_meta_title' => get_static_option('og_meta_title'),
                'og_meta_description' => get_static_option('og_meta_description'),
                'og_meta_site_name' => get_static_option('og_meta_site_name'),
                'og_meta_url' => get_static_option('og_meta_url'),
                'og_meta_image' => get_static_option('og_meta_image'),
            ];

            return ApiResponse::success('SEO settings retrieved successfully', $seoSettings);
        } catch (\Exception $e) {
            Log::error('SEO settings fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve SEO settings');
        }
    }


    public function allStaticOptions(): JsonResponse
    {
        try {
            $staticOptions = StaticOption::all();
            return ApiResponse::success(
                'All static options retrieved successfully',
                GeneralSettingsResource::collection($staticOptions)
            );
        } catch (\Exception $e) {
            Log::error('Static options fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve static options');
        }
    }


    public function languages(): JsonResponse
    {
        try {
            $languages = Language::select('id', 'name', 'slug', 'direction', 'status')->get();
            return ApiResponse::success('Languages retrieved successfully', $languages);
        } catch (\Exception $e) {
            Log::error('Languages fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve languages');
        }
    }

    public function getListingCreateSettings(): JsonResponse
    {
        try {
            $settings = [
                'listing_create_settings' => get_static_option('listing_create_settings'),
                'listing_create_status_settings' => get_static_option('listing_create_status_settings')
            ];

            return ApiResponse::success(
                'Listing create settings retrieved successfully',
                ListingSettingsResource::make($settings)
            );
        } catch (\Exception $e) {
            Log::error('Listing create settings fetch error: ' . $e->getMessage());
            return ApiResponse::error('Failed to retrieve listing create settings');
        }
    }
}
