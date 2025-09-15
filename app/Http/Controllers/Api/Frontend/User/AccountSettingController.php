<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Actions\Media\v1\MediaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Account\ChangePasswordRequest;
use App\Http\Requests\Api\User\Account\DeactivateAccountRequest;
use App\Http\Requests\Api\User\Account\UpdateSettingsRequest;
use App\Http\Requests\Api\User\Account\VerifyProfileRequest;
use App\Http\Resources\User\AccountDeactivateResource;
use App\Http\Resources\User\IdentityVerificationResource;
use App\Http\Resources\User\UserSettingsResource;
use App\Http\Responses\ApiResponse;
use App\Mail\BasicMail;
use App\Models\Backend\IdentityVerification;
use App\Models\Frontend\AccountDeactivate;
use App\Models\Frontend\UserSettings;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Membership\app\Models\BusinessHours;
use Modules\Membership\app\Models\UserMembership;

class AccountSettingController extends Controller
{
  public function __construct(
    private MediaHelper $mediaHelper
  ) {
  }

  public function getAccountSettings(): JsonResponse
  {
    try {
      $user = auth()->user();
      $accountInfo = AccountDeactivate::where('user_id', $user->id)->first();
      $verifyInfo = IdentityVerification::where('user_id', $user->id)->first();

      $businessHoursData = null;
      if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
        $userMembership = UserMembership::where('user_id', $user->id)->first();
        if ($userMembership && $userMembership->business_hour === 1) {
          $userBusinessHours = BusinessHours::where('user_id', $user->id)->first();
          if ($userBusinessHours) {
            $businessHoursData = json_decode($userBusinessHours->day_of_week, true);
          }
        }
      }

      $userSettings = UserSettings::firstOrCreate(
        ['user_id' => $user->id],
        [
          'show_phone_to_buyers' => true,
          'enable_location_tracking' => true,
          'share_usage_data' => false,
          'enable_all_notifications' => true,
          'new_message_notifications' => true,
          'listing_comment_notifications' => true,
          'weekly_email_summary' => false,
          'email_matching_listings' => false,
          'email_offers_promotions' => false,
          'sms_notifications' => false,
          'theme' => 'light',
          'language' => 'ar',
          'show_nearby_listings' => true,
          'show_currency_rates' => false,
          'enable_image_caching' => true,
          'disable_listing_comments' => false,
        ]
      );

      $data = [
        'account_info' => $accountInfo ? AccountDeactivateResource::make($accountInfo) : null,
        'verify_info' => $verifyInfo ? IdentityVerificationResource::make($verifyInfo) : null,
        'business_hours' => $businessHoursData,
        'user_settings' => UserSettingsResource::make($userSettings),
      ];

      return ApiResponse::success('Account settings retrieved successfully', $data);

    } catch (\Exception $e) {
      Log::error('Account settings fetch error: ' . $e->getMessage());
      return ApiResponse::error('Failed to retrieve account settings');
    }
  }

   public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $user->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now(),
            ]);

            return ApiResponse::success('Password updated successfully');

        } catch (\Exception $e) {
            Log::error('Password change error: ' . $e->getMessage());
            return ApiResponse::error('Failed to update password');
        }
    }

  public function getUserSettings(): JsonResponse
  {
    try {
      $user = auth()->user();

      $userSettings = UserSettings::firstOrCreate(
        ['user_id' => $user->id]
      );

      return ApiResponse::success(
        'User settings retrieved successfully',
        UserSettingsResource::make($userSettings)
      );

    } catch (\Exception $e) {
      Log::error('User settings fetch error: ' . $e->getMessage());
      return ApiResponse::error('Failed to retrieve user settings');
    }
  }

  public function updateUserSettings(UpdateSettingsRequest $request): JsonResponse
  {
    try {
      $user = auth()->user();

      $userSettings = UserSettings::firstOrCreate(['user_id' => $user->id]);

      $validatedData = $request->validated();
      $userSettings->update($validatedData);

      return ApiResponse::success(
        'Settings updated successfully',
        UserSettingsResource::make($userSettings->fresh())
      );

    } catch (\Exception $e) {
      Log::error('Settings update error: ' . $e->getMessage());
      return ApiResponse::error('Failed to update settings');
    }
  }

  public function updateSecuritySettings(Request $request): JsonResponse
  {
    try {
      $request->validate([
        'show_phone_to_buyers' => 'sometimes|boolean',
        'enable_location_tracking' => 'sometimes|boolean',
        'share_usage_data' => 'sometimes|boolean',
      ]);

      $user = auth()->user();
      $userSettings = UserSettings::firstOrCreate(['user_id' => $user->id]);

      $userSettings->update($request->only([
        'show_phone_to_buyers',
        'enable_location_tracking',
        'share_usage_data'
      ]));

      return ApiResponse::success(
        'Security settings updated successfully',
        UserSettingsResource::make($userSettings->fresh())
      );

    } catch (\Exception $e) {
      Log::error('Security settings update error: ' . $e->getMessage());
      return ApiResponse::error('Failed to update security settings');
    }
  }

  public function updateNotificationSettings(Request $request): JsonResponse
  {
    try {
      $request->validate([
        'enable_all_notifications' => 'sometimes|boolean',
        'new_message_notifications' => 'sometimes|boolean',
        'listing_comment_notifications' => 'sometimes|boolean',
        'weekly_email_summary' => 'sometimes|boolean',
        'email_matching_listings' => 'sometimes|boolean',
        'email_offers_promotions' => 'sometimes|boolean',
        'sms_notifications' => 'sometimes|boolean',
      ]);

      $user = auth()->user();
      $userSettings = UserSettings::firstOrCreate(['user_id' => $user->id]);

      $updateData = $request->only([
        'enable_all_notifications',
        'new_message_notifications',
        'listing_comment_notifications',
        'weekly_email_summary',
        'email_matching_listings',
        'email_offers_promotions',
        'sms_notifications'
      ]);

      if (isset($updateData['enable_all_notifications']) && !$updateData['enable_all_notifications']) {
        $updateData = array_merge($updateData, [
          'new_message_notifications' => false,
          'listing_comment_notifications' => false,
          'weekly_email_summary' => false,
          'email_matching_listings' => false,
          'email_offers_promotions' => false,
          'sms_notifications' => false,
        ]);
      }

      $userSettings->update($updateData);

      return ApiResponse::success(
        'Notification settings updated successfully',
        UserSettingsResource::make($userSettings->fresh())
      );

    } catch (\Exception $e) {
      Log::error('Notification settings update error: ' . $e->getMessage());
      return ApiResponse::error('Failed to update notification settings');
    }
  }

  public function updateGeneralSettings(Request $request): JsonResponse
  {
    try {
      $request->validate([
        'theme' => 'sometimes|in:light,dark',
        'language' => 'sometimes|in:ar,en',
        'show_nearby_listings' => 'sometimes|boolean',
        'show_currency_rates' => 'sometimes|boolean',
        'enable_image_caching' => 'sometimes|boolean',
        'disable_listing_comments' => 'sometimes|boolean',
      ]);

      $user = auth()->user();
      $userSettings = UserSettings::firstOrCreate(['user_id' => $user->id]);

      $userSettings->update($request->only([
        'theme',
        'language',
        'show_nearby_listings',
        'show_currency_rates',
        'enable_image_caching',
        'disable_listing_comments'
      ]));

      return ApiResponse::success(
        'General settings updated successfully',
        UserSettingsResource::make($userSettings->fresh())
      );

    } catch (\Exception $e) {
      Log::error('General settings update error: ' . $e->getMessage());
      return ApiResponse::error('Failed to update general settings');
    }
  }

  public function resetSettingsToDefault(): JsonResponse
  {
    try {
      $user = auth()->user();
      $userSettings = UserSettings::where('user_id', $user->id)->first();

      if ($userSettings) {
        $userSettings->update([
          'show_phone_to_buyers' => true,
          'enable_location_tracking' => true,
          'share_usage_data' => false,
          'enable_all_notifications' => true,
          'new_message_notifications' => true,
          'listing_comment_notifications' => true,
          'weekly_email_summary' => false,
          'email_matching_listings' => false,
          'email_offers_promotions' => false,
          'sms_notifications' => false,
          'theme' => 'light',
          'language' => 'ar',
          'show_nearby_listings' => true,
          'show_currency_rates' => false,
          'enable_image_caching' => true,
          'disable_listing_comments' => false,
        ]);
      } else {
        $userSettings = UserSettings::create([
          'user_id' => $user->id,
          'show_phone_to_buyers' => true,
          'enable_location_tracking' => true,
          'share_usage_data' => false,
          'enable_all_notifications' => true,
          'new_message_notifications' => true,
          'listing_comment_notifications' => true,
          'weekly_email_summary' => false,
          'email_matching_listings' => false,
          'email_offers_promotions' => false,
          'sms_notifications' => false,
          'theme' => 'light',
          'language' => 'ar',
          'show_nearby_listings' => true,
          'show_currency_rates' => false,
          'enable_image_caching' => true,
          'disable_listing_comments' => false,
        ]);
      }

      return ApiResponse::success(
        'Settings reset to default successfully',
        UserSettingsResource::make($userSettings->fresh())
      );

    } catch (\Exception $e) {
      Log::error('Settings reset error: ' . $e->getMessage());
      return ApiResponse::error('Failed to reset settings');
    }
  }

  public function deactivateAccount(DeactivateAccountRequest $request): JsonResponse
  {
    try {
      $user = auth()->user();

      $accountDeactivate = AccountDeactivate::create([
        'user_id' => $user->id,
        'reason' => $request->reason,
        'description' => $request->description,
        'status' => 0,
        'account_status' => 0,
      ]);

      return ApiResponse::success(
        'Account deactivated successfully',
        AccountDeactivateResource::make($accountDeactivate)
      );

    } catch (\Exception $e) {
      Log::error('Account deactivation error: ' . $e->getMessage());
      return ApiResponse::error('Failed to deactivate account');
    }
  }

  public function deleteAccount(DeactivateAccountRequest $request): JsonResponse
  {
    try {
      $user = auth()->user();

      AccountDeactivate::create([
        'user_id' => $user->id,
        'reason' => $request->reason,
        'description' => $request->description,
        'status' => 1,
        'account_status' => 1,
      ]);

      $user->tokens()->delete();

      return ApiResponse::success('Account deletion request submitted successfully');

    } catch (\Exception $e) {
      Log::error('Account deletion error: ' . $e->getMessage());
      return ApiResponse::error('Failed to process account deletion');
    }
  }

  public function cancelDeactivation(): JsonResponse
  {
    try {
      $user = auth()->user();
      $accountDetails = AccountDeactivate::where('user_id', $user->id)->first();

      if ($accountDetails) {
        $accountDetails->delete();
        return ApiResponse::success('Account reactivated successfully');
      }

      return ApiResponse::error('No deactivation request found', [], 404);

    } catch (\Exception $e) {
      Log::error('Account reactivation error: ' . $e->getMessage());
      return ApiResponse::error('Failed to reactivate account');
    }
  }

  public function verifyProfile(VerifyProfileRequest $request): JsonResponse
  {
    $user = auth()->user();
    $frontImageName = null;
    $backImageName = null;
    $verificationRecord = null;

    try {
      if ($request->hasFile('front_document')) {
        $frontImage = $this->mediaHelper->uploadMedia(
          $request->file('front_document'),
          'verification',
          $user->id
        );
        $frontImageName = $frontImage->id;
      }

      if ($request->hasFile('back_document')) {
        $backImage = $this->mediaHelper->uploadMedia(
          $request->file('back_document'),
          'verification',
          $user->id
        );
        $backImageName = $backImage->id;
      }

      $oldDocument = IdentityVerification::where('user_id', $user->id)->first();

      $verificationData = [
        'user_id' => $user->id,
        'identification_type' => $request->identification_type,
        'country_id' => $request->country_id,
        'state_id' => $request->state_id,
        'city_id' => $request->city_id,
        'zip_code' => $request->zip_code,
        'address' => $request->address,
        'identification_number' => $request->identification_number,
        'front_document' => $frontImageName ?? ($oldDocument->front_document ?? null),
        'back_document' => $backImageName ?? ($oldDocument->back_document ?? null),
      ];

      if (is_null($oldDocument)) {
        $verificationRecord = IdentityVerification::create($verificationData);
      } else {
        $verificationData['status'] = 0;
        $oldDocument->update($verificationData);
        $verificationRecord = $oldDocument->fresh();
      }

      $this->sendVerificationEmail();

      return ApiResponse::success(
        'Verification information submitted successfully',
        IdentityVerificationResource::make($verificationRecord->load(['user_country', 'user_state', 'user_city']))
      );

    } catch (\Exception $e) {
      $this->rollbackVerificationMedia($frontImageName, $backImageName);

      Log::error('Profile verification error: ' . $e->getMessage());
      return ApiResponse::error('Failed to submit verification information');
    }
  }

  public function getVerificationStatus(): JsonResponse
  {
    try {
      $user = auth()->user();
      $verification = IdentityVerification::where('user_id', $user->id)
        ->with(['user_country', 'user_state', 'user_city'])
        ->first();

      if (!$verification) {
        return ApiResponse::success('No verification record found', null);
      }

      return ApiResponse::success(
        'Verification status retrieved successfully',
        IdentityVerificationResource::make($verification)
      );

    } catch (\Exception $e) {
      Log::error('Verification status error: ' . $e->getMessage());
      return ApiResponse::error('Failed to retrieve verification status');
    }
  }

  private function sendVerificationEmail(): void
  {
    try {
      $subject = get_static_option('user_identity_verification_subject') ?? 'User Verification Request';
      $message = get_static_option('admin_user_identity_verification_message');

      Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
        'subject' => $subject,
        'message' => $message
      ]));
    } catch (\Exception $e) {
      Log::warning('Verification email failed: ' . $e->getMessage());
    }
  }

  private function rollbackVerificationMedia(?int $frontImageId, ?int $backImageId): void
  {
    try {
      if ($frontImageId) {
        $this->mediaHelper->deleteMediaImage($frontImageId, 'verification');
      }
      if ($backImageId) {
        $this->mediaHelper->deleteMediaImage($backImageId, 'verification');
      }
    } catch (\Exception $e) {
      Log::critical('Verification media rollback failed: ' . $e->getMessage());
    }
  }
}