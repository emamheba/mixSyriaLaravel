<?php

namespace App\Http\Controllers\Api\Frontend\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\Auth\LoginRequest;
use App\Http\Requests\Frontend\User\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Mail\BasicMail;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\app\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Membership\app\Http\Services\MembershipService;
use Carbon\Carbon;

class AuthController extends Controller
{
  protected $membershipService;

  const TOKEN_EXPIRY_MINUTES = 10;
  const MAX_DAILY_ATTEMPTS = 5;
  const RATE_LIMIT_MINUTES = 1;

  public function __construct()
  {
    if (moduleExists("Membership")) {
      if (membershipModuleExistsAndEnable('Membership')) {
        $this->membershipService = app()->make(MembershipService::class);
      }
    }
  }

  public function login(LoginRequest $request)
  {
    $credentials = $request->only('email', 'password');

    $user = User::where('email', $request->identifier)
      ->orWhere('phone', $request->identifier)
      ->orWhere('username', $request->identifier)
      ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return ApiResponse::error('بيانات الدخول غير صحيحة', [], 401);
    }

    return ApiResponse::success('succssfully', [
      'token' => $user->createToken('auth_token')->plainTextToken,
      'user' => new UserResource($user)
    ]);
  }

  public function register(RegisterRequest $request)
  {
    $data = $request->validated();
    $emailVerifyToken = sprintf("%d", random_int(123456, 999999));
    $tokenExpiresAt = Carbon::now()->addMinutes(self::TOKEN_EXPIRY_MINUTES);

    $user = User::create([
        ...$data,
        'username' => fake()->userName() ?? null,
        'password' => bcrypt($data['password']),
        'email_verify_token' => $emailVerifyToken,
        'email_verify_token_expires_at' => $tokenExpiresAt,
        'email_verified' => false,
        'email_verification_attempts' => 1,
        'last_verification_attempt_at' => Carbon::now(),
    ]);

    if (moduleExists("Wallet")) {
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'remaining_balance' => 0,
            'withdraw_amount' => 0,
            'status' => 1
        ]);
    }

    if (moduleExists("Membership")) {
        if (membershipModuleExistsAndEnable('Membership')) {
            $this->membershipService->createFreeMembership($user);
        }
    }

    $emailSent = false;
    $emailError = null;

    try {
        Mail::to($user->email)->send(new BasicMail([
            'subject' => __('Email Verification Code'),
            'message' => __('Your verification code is: ') . $emailVerifyToken .
            __('\nThis code will expire in ') . self::TOKEN_EXPIRY_MINUTES . __(' minutes.')
        ]));
        $emailSent = true;
    } catch (\Exception $e) {
        $emailError = $e->getMessage();
        \Log::error('Failed to send verification email: ' . $e->getMessage());
    }

    $responseData = [
        'user' => new UserResource($user),
        'email_verification_required' => true,
        'email_sent' => $emailSent,
        'token_expires_in_minutes' => self::TOKEN_EXPIRY_MINUTES,
        'remaining_attempts' => self::MAX_DAILY_ATTEMPTS - 1
    ];

    if (!$emailSent) {
        $responseData['email_error'] = 'فشل في إرسال رمز التحقق، يرجى المحاولة لاحقاً';
    }

    return ApiResponse::success(
        $emailSent
        ? 'تم إنشاء الحساب بنجاح، تحقق من بريدك الإلكتروني للتفعيل'
        : 'تم إنشاء الحساب بنجاح، ولكن فشل إرسال رمز التحقق',
        $responseData,
        201
    );
  }

  private function canSendVerificationCode($user)
  {
    $now = Carbon::now();

    $todayAttempts = $user->email_verification_attempts;
    $lastAttempt = $user->last_verification_attempt_at ? Carbon::parse($user->last_verification_attempt_at) : null;

    if ($lastAttempt && $lastAttempt->isSameDay($now) === false ) {
        $user->update([
            'email_verification_attempts' => 0,
            // 'last_verification_attempt_at' => null
        ]);
        $todayAttempts = 0;
    }


    if ($todayAttempts >= self::MAX_DAILY_ATTEMPTS) {
        return [
            'can_send' => false,
            'error' => 'تم تجاوز الحد الأقصى لعدد المحاولات اليومية (' . self::MAX_DAILY_ATTEMPTS . ' محاولات). حاول غداً.',
            'remaining_attempts' => 0
        ];
    }

    if ($user->last_verification_attempt_at) {
        $lastAttemptTime = Carbon::parse($user->last_verification_attempt_at);
        if ($now->diffInMinutes($lastAttemptTime) < self::RATE_LIMIT_MINUTES) {
            $waitTime = self::RATE_LIMIT_MINUTES - $now->diffInMinutes($lastAttemptTime);
            return [
                'can_send' => false,
                'error' => 'يجب الانتظار ' . $waitTime . ' دقيقة قبل إعادة الإرسال.',
                'wait_minutes' => $waitTime,
                'remaining_attempts' => self::MAX_DAILY_ATTEMPTS - $todayAttempts
            ];
        }
    }

    return [
        'can_send' => true,
        'remaining_attempts' => self::MAX_DAILY_ATTEMPTS - $todayAttempts -1
    ];
  }

  private function sendVerificationEmail($user, $subject, $messageTemplate)
  {
    $emailVerifyToken = sprintf("%d", random_int(123456, 999999));
    $tokenExpiresAt = Carbon::now()->addMinutes(self::TOKEN_EXPIRY_MINUTES);

    $user->update([
      'email_verify_token' => $emailVerifyToken,
      'email_verify_token_expires_at' => $tokenExpiresAt,
      'email_verification_attempts' => $user->email_verification_attempts + 1,
      'last_verification_attempt_at' => Carbon::now(),
    ]);

    try {
      Mail::to($user->email)->send(new BasicMail([
        'subject' => $subject,
        'message' => sprintf(
            $messageTemplate,
            $emailVerifyToken,
            self::TOKEN_EXPIRY_MINUTES
        )
      ]));
      return ['success' => true, 'token' => $emailVerifyToken];
    } catch (\Exception $e) {
      \Log::error('Failed to send email (' . $subject . '): ' . $e->getMessage());
      return ['success' => false, 'error' => $e->getMessage()];
    }
  }

  public function sendCode(Request $request)
  {
    $request->validate(['email' => 'required|email']);
    $user = User::where('email', $request->email)->firstOrFail();

    $canSend = $this->canSendVerificationCode($user);
    if (!$canSend['can_send']) {
      return ApiResponse::error($canSend['error'], [
        'remaining_attempts' => $canSend['remaining_attempts'],
        'wait_minutes' => $canSend['wait_minutes'] ?? null
      ], 429);
    }

    $sendResult = $this->sendVerificationEmail(
        $user,
        __('Email Verification Code'),
        __('Your verification code is: %s\nThis code will expire in %d minutes.')
    );

    if ($sendResult['success']) {
      return ApiResponse::success('تم إرسال رمز التحقق بنجاح', [
        'token_expires_in_minutes' => self::TOKEN_EXPIRY_MINUTES,
        'remaining_attempts' => $canSend['remaining_attempts']
      ]);
    } else {
      return ApiResponse::error('فشل في إرسال رمز التحقق، يرجى المحاولة لاحقاً', [], 500);
    }
  }

  public function sendPasswordResetCode(Request $request)
  {
      $request->validate(['email' => 'required|email']);
      $user = User::where('email', $request->email)->first();

      if (!$user) {
          return ApiResponse::success(
              'إذا كان هذا البريد الإلكتروني مسجلاً، فسيتم إرسال رمز إعادة تعيين كلمة المرور إليه.',
              ['token_expires_in_minutes' => self::TOKEN_EXPIRY_MINUTES]
          );
      }

      $canSend = $this->canSendVerificationCode($user);
      if (!$canSend['can_send']) {
          return ApiResponse::error($canSend['error'], [
              'remaining_attempts' => $canSend['remaining_attempts'],
              'wait_minutes' => $canSend['wait_minutes'] ?? null
          ], 429);
      }

      $sendResult = $this->sendVerificationEmail(
          $user,
          __('Password Reset Code'),
          __('Your password reset code is: %s.\nThis code will expire in %d minutes.\nIf you did not request a password reset, please ignore this email.')
      );

      if ($sendResult['success']) {
          return ApiResponse::success('تم إرسال رمز إعادة تعيين كلمة المرور بنجاح.', [
              'token_expires_in_minutes' => self::TOKEN_EXPIRY_MINUTES,
              'remaining_attempts' => $canSend['remaining_attempts']
          ]);
      } else {
          return ApiResponse::error('فشل في إرسال رمز إعادة تعيين كلمة المرور. يرجى المحاولة لاحقًا.', [], 500);
      }
  }


  public function resendVerificationCode(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)
        ->where('email_verified', false)
        ->first();

    if (!$user) {
        return ApiResponse::error('الحساب غير موجود أو مفعل بالفعل', [], 404);
    }

    $canSend = $this->canSendVerificationCode($user);

    if (!$canSend['can_send']) {
        return ApiResponse::error($canSend['error'], [
            'remaining_attempts' => $canSend['remaining_attempts'],
            'wait_minutes' => $canSend['wait_minutes'] ?? null
        ], 429);
    }

    $sendResult = $this->sendVerificationEmail(
        $user,
        __('Email Verification Code'),
        __('Your verification code is: %s\nThis code will expire in %d minutes.')
    );


    if ($sendResult['success']) {
        return ApiResponse::success('تم إرسال رمز التحقق بنجاح', [
            'token_expires_in_minutes' => self::TOKEN_EXPIRY_MINUTES,
            'remaining_attempts' => $canSend['remaining_attempts']
        ]);
    } else {
        return ApiResponse::error('فشل في إرسال رمز التحقق، يرجى المحاولة لاحقاً', [], 500);
    }
  }

  public function verifyEmail(Request $request)
  {
    $request->validate([
        'email' => 'required|email',
        'token' => 'required|string',
    ]);

    $user = User::where('email', $request->email)
        ->where('email_verify_token', $request->token)
        ->where('email_verified', false)
        ->first();

    if (!$user) {
        return ApiResponse::error('رمز التحقق غير صحيح', [], 400);
    }

    if ($user->email_verify_token_expires_at && Carbon::now()->gt($user->email_verify_token_expires_at)) {
        return ApiResponse::error('رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد.', [
            'expired' => true,
            'can_resend' => true
        ], 400);
    }

    $user->update([
        'email_verified' => true,
        'email_verify_token' => null,
        'email_verify_token_expires_at' => null,
        'email_verification_attempts' => 0,
        'last_verification_attempt_at' => null
    ]);

    return ApiResponse::success('تم تفعيل الحساب بنجاح', [
        'token' => $user->createToken('auth_token')->plainTextToken,
        'user' => new UserResource($user)
    ]);
  }

  public function logout(Request $request)
  {
    // Auth::logout();
    $request->user()->currentAccessToken()->delete();
    return ApiResponse::success('تم تسجيل الخروج بنجاح');
  }

  public function resetPassword(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'token' => 'required',
      'password' => 'required|confirmed|min:6',
    ]);

    $user = User::where('email', $request->email)
      ->where('email_verify_token', $request->token)
      ->first();

    if (!$user) {
      return ApiResponse::error('رمز خاطئ أو بريد إلكتروني غير موجود', [], 400);
    }

    if ($user->email_verify_token_expires_at && Carbon::now()->gt($user->email_verify_token_expires_at)) {
      return ApiResponse::error('رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد.', ['expired' => true], 400);
    }

    $user->update([
      'password' => bcrypt($request->password),
      'email_verify_token' => null,
      'email_verify_token_expires_at' => null,
      'email_verification_attempts' => 0,
      'last_verification_attempt_at' => null
    ]);
    return ApiResponse::success('تم تغيير كلمة المرور بنجاح.');
  }


  public function verifyCode(Request $request)
  {
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
    ]);

    $user = User::where('email', $request->email)
        ->where('email_verify_token', $request->token)
        ->first();

    if (!$user) {
        return ApiResponse::error('رمز غير صحيح أو بريد إلكتروني غير موجود', [], 400);
    }

    if ($user->email_verify_token_expires_at && Carbon::now()->gt($user->email_verify_token_expires_at)) {
        return ApiResponse::error('رمز التحقق منتهي الصلاحية', ['expired' => true], 400);
    }


    if (!$user->email_verified) {
        $user->update([
            'email_verified' => true,
        ]);
    }

    return ApiResponse::success('تم التحقق من الرمز بنجاح.');
  }
}
