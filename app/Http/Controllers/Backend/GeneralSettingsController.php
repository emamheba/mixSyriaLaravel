<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Language;
use Illuminate\Http\Request;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Support\Facades\Validator;

class GeneralSettingsController extends Controller
{

  protected $mediaHelper;

  public function __construct(MediaHelper $mediaHelper)
  {
    $this->mediaHelper = $mediaHelper;
  }

  public function siteIdentity()
  {
    return view('backend.general-settings.site-identity');
  }

  public function updateSiteIdentity(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'site_white_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
      'site_logo_id' => 'nullable|integer',
      'site_white_logo_id' => 'nullable|integer',
      'site_favicon_id' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', __('Please fix the validation errors.'));
    }

    try {
      $uploadedImages = [];
      $imageFields = [
        'site_logo' => 'site_logo_id',
        'site_white_logo' => 'site_white_logo_id',
        'site_favicon' => 'site_favicon_id'
      ];

      foreach ($imageFields as $fileField => $idField) {
        $oldImageId = null;

        if ($request->hasFile($fileField)) {
          $oldImageId = get_static_option($fileField);

          $uploadedImage = $this->mediaHelper->uploadMedia($request->file($fileField), 'admin');

          if ($uploadedImage) {
            update_static_option($fileField, $uploadedImage->id);
            $uploadedImages[] = $fileField;

            if ($oldImageId && $oldImageId != $uploadedImage->id) {
              try {
                $this->mediaHelper->deleteMediaImage($oldImageId, 'admin');
              } catch (\Exception $e) {
                \Log::warning("Failed to delete old image: " . $e->getMessage());
              }
            }
          } else {
            return redirect()->back()
              ->with('error', __('Failed to upload :field. Please try again.', ['field' => $fileField]))
              ->withInput();
          }
        } elseif ($request->has($idField) && $request->input($idField)) {
          update_static_option($fileField, $request->input($idField));
        }
      }

      $message = count($uploadedImages) > 0
        ? __('Site identity updated successfully! :count images uploaded.', ['count' => count($uploadedImages)])
        : __('Site identity settings saved successfully!');

      return redirect()->back()->with('success', $message);

    } catch (\Exception $e) {
      \Log::error('Site Identity Update Error: ' . $e->getMessage());

      return redirect()->back()
        ->with('error', __('An error occurred while updating site identity. Please try again.'))
        ->withInput();
    }
  }


  public function basicSettings()
  {
    $all_languages = Language::all();
    return view('backend.general-settings.basic')->with(['all_languages' => $all_languages]);
  }
  public function updateBasicSettings(Request $request)
  {
    $this->validate($request, [
      'language_select_option' => 'nullable|string',
      'user_email_verify_enable_disable' => 'nullable|string',
      'user_otp_verify_enable_disable' => 'nullable|string',
      'site_main_color' => 'nullable|string',
      'site_secondary_color' => 'nullable|string',
      'site_maintenance_mode' => 'nullable|string',
      'admin_loader_animation' => 'nullable|string',
      'site_loader_animation' => 'nullable|string',
      'site_force_ssl_redirection' => 'nullable|string',
      'admin_panel_rtl_status' => 'nullable|string',
      'site_title' => 'nullable|string',
      'site_tag_line' => 'nullable|string',
      'site_footer_copyright' => 'nullable|string',
    ]);

    $this->validate($request, [
      'site_title' => 'nullable|string',
      'site_tag_line' => 'nullable|string',
      'site_footer_copyright' => 'nullable|string',
    ]);
    $_title = 'site_title';
    $_tag_line = 'site_tag_line';
    $_footer_copyright = 'site_footer_copyright';
    update_static_option($_title, $request->$_title);
    update_static_option($_tag_line, $request->$_tag_line);
    update_static_option($_footer_copyright, $request->$_footer_copyright);


    $all_fields = [
      'language_select_option',
      'user_email_verify_enable_disable',
      'user_otp_verify_enable_disable',
      'site_main_color',
      'site_secondary_color',
      'site_maintenance_mode',
      'admin_loader_animation',
      'site_loader_animation',
      'admin_panel_rtl_status',
      'site_force_ssl_redirection',
      'site_canonical_url_type'
    ];
    foreach ($all_fields as $field) {
      update_static_option($field, $request->$field);
    }
    return redirect()->back()->with(FlashMsg::settings_update());
  }



  public function colorSettings()
  {
    return view('backend.general-settings.color-settings');
  }

  public function updateColorSettings(Request $request)
  {
    $this->validate($request, [
      'site_main_color_one' => 'nullable|string',
      'site_main_color_two' => 'nullable|string',
      'site_main_color_three' => 'nullable|string',
    ]);

    $all_fields = [
      'site_main_color_one',
      'site_main_color_two',
      'site_main_color_three',
      'heading_color',
      'light_color',
      'extra_light_color',
    ];

    foreach ($all_fields as $field) {
      update_static_option($field, $request->$field);
    }
    return redirect()->back()->with(FlashMsg::settings_update());
  }

  public function seoSettings()
  {
    $all_languages = Language::all();
    return view('backend.general-settings.seo')->with(['all_languages' => $all_languages]);
  }
  public function updateSeoSettings(Request $request)
  {
    $all_languages = Language::all();
    foreach ($all_languages as $lang) {
      $this->validate($request, [
        'site_meta_tags' => 'nullable|string',
        'site_meta_description' => 'nullable|string',
        'og_meta_title' => 'nullable|string',
        'og_meta_description' => 'nullable|string',
        'og_meta_site_name' => 'nullable|string',
        'og_meta_url' => 'nullable|string',
        'og_meta_image' => 'nullable|string',
      ]);
      $fields = [
        'site_meta_tags',
        'site_meta_description',
        'og_meta_title',
        'og_meta_description',
        'og_meta_site_name',
        'og_meta_url',
        'og_meta_image'
      ];
      foreach ($fields as $field) {
        if ($request->has($field)) {
          update_static_option($field, $request->$field);
        }
      }
    }
    return redirect()->back()->with(FlashMsg::settings_update());
  }



  public function loginRegisterPageSettings(Request $request)
  {
    if ($request->isMethod('post')) {

      $validator = Validator::make($request->all(), [
        'login_form_title' => 'nullable|string|max:255',
        'register_page_title' => 'nullable|string|max:255',
        'select_terms_condition_page' => 'nullable|string|max:255',
        'register_page_description' => 'nullable|string|max:500',
        'register_page_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'register_page_image_id' => 'nullable|integer',
        'register_page_image_removed' => 'nullable|boolean',
      ]);

      if ($validator->fails()) {
        return redirect()->back()
          ->withErrors($validator)
          ->withInput()
          ->with('error', __('Please fix the validation errors.'));
      }

      try {
        $currentRegisterImageId = get_static_option('register_page_image');

        if ($request->hasFile('register_page_image')) {
          $uploadedImage = $this->mediaHelper->uploadMedia($request->file('register_page_image'), 'admin');

          if ($uploadedImage) {
            update_static_option('register_page_image', $uploadedImage->id);

            if ($currentRegisterImageId && $currentRegisterImageId != $uploadedImage->id) {
              try {
                $this->mediaHelper->deleteMediaImage($currentRegisterImageId, 'admin');
              } catch (\Exception $e) {
                \Log::warning("Failed to delete old register page image (ID: {$currentRegisterImageId}): " . $e->getMessage());
              }
            }
          } else {
            return redirect()->back()
              ->with('error', __('Failed to upload register page image. Please try again.'))
              ->withInput();
          }
        } elseif ($request->has('register_page_image_id') && $request->input('register_page_image_id')) {
          update_static_option('register_page_image', $request->input('register_page_image_id'));
        } elseif ($request->input('register_page_image_removed') == 1) {
          if ($currentRegisterImageId) {
            try {
              $this->mediaHelper->deleteMediaImage($currentRegisterImageId, 'admin');
            } catch (\Exception $e) {
              \Log::warning("Failed to delete removed register page image (ID: {$currentRegisterImageId}): " . $e->getMessage());
            }
          }
          update_static_option('register_page_image', null);
        }

        $standardFields = [
          'login_form_title',
          'register_page_title',
          'register_page_description',
          'select_terms_condition_page',
          'register_page_social_login_show_hide',
        ];

        foreach ($standardFields as $field) {
          if ($field === 'register_page_social_login_show_hide') {
            update_static_option($field, $request->has($field) ? 1 : null);
          } else {
            update_static_option($field, $request->input($field));
          }
        }

        return redirect()->back()->with('success', __('Login and Register settings updated successfully!'));

      } catch (\Exception $e) {
        \Log::error('Login/Register Settings Update Error: ' . $e->getMessage());

        return redirect()->back()
          ->with('error', __('An error occurred while updating login and register settings. Please try again.'))
          ->withInput();
      }
    }

    return view('backend.general-settings.login-register-settings');
  }

  public function listingCreateSettings(Request $request)
  {
    if ($request->isMethod('post')) {

      $this->validate($request, [
        'listing_create_settings' => 'nullable|string',
        'listing_create_status_settings' => 'nullable|string'
      ]);

      $all_fields = [
        'listing_create_settings',
        'listing_create_status_settings'
      ];
      foreach ($all_fields as $field) {
        update_static_option($field, $request->$field);
      }
      return redirect()->back()->with(FlashMsg::settings_update());
    }

    return view('backend.general-settings.listing-create-page-settings');
  }
  public function scriptsSettings()
  {
    return view('backend.general-settings.thid-party');
  }

  public function updateScriptsSettings(Request $request)
  {

    $this->validate($request, [
      'tawk_api_key' => 'nullable|string',
      'google_adsense_id' => 'nullable|string',
      'site_third_party_tracking_code' => 'nullable|string',
      'site_google_analytics' => 'nullable|string',
    ]);

    update_static_option('site_disqus_key', $request->site_disqus_key);
    update_static_option('site_google_analytics', $request->site_google_analytics);
    update_static_option('tawk_api_key', $request->tawk_api_key);
    update_static_option('site_third_party_tracking_code', $request->site_third_party_tracking_code);
    update_static_option('facebook_client_id', $request->facebook_client_id);
    update_static_option('facebook_client_secret', $request->facebook_client_secret);
    update_static_option('facebook_callback_url', $request->facebook_callback_url);
    update_static_option('google_adsense_publisher_id', $request->google_adsense_publisher_id);
    update_static_option('google_adsense_customer_id', $request->google_adsense_customer_id);
    update_static_option('google_client_id', $request->google_client_id);
    update_static_option('google_client_secret', $request->google_client_secret);
    update_static_option('google_callback_url', $request->google_callback_url);

    $fields = [
      'site_third_party_tracking_code',
      'site_google_analytics',
      'tawk_api_key',
      'enable_google_login',
      'google_client_id',
      'google_client_secret',
      'enable_facebook_login',
      'facebook_client_id',
      'facebook_client_secret',
      'google_adsense_publisher_id',
      'google_adsense_customer_id',
      'enable_google_adsense',
      'instagram_access_token',
    ];
    foreach ($fields as $field) {
      update_static_option($field, $request->$field);
    }

    return redirect()->back()->with(['msg' => __('Third Party Scripts Settings Updated..'), 'type' => 'success']);
  }



}
