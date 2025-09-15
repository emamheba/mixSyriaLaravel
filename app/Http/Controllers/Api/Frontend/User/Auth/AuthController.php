<?php

namespace App\Http\Controllers\Api\Frontend\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\User\Auth\LoginRequest;
use App\Http\Requests\Frontend\User\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\SmsService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Membership\app\Http\Services\MembershipService;
use Carbon\Carbon;
use Modules\SMSGateway\app\Models\UserOtp; 
use Modules\Wallet\app\Models\Wallet;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str; // <--- أضف هذا السطر هنا

class AuthController extends Controller
{
    protected $membershipService;
    protected $smsService; 

    const OTP_EXPIRY_MINUTES = 10;
    const MAX_DAILY_ATTEMPTS = 10;
    const RATE_LIMIT_MINUTES = 1;

    public function __construct(SmsService $smsService) 
    {
        $this->smsService = $smsService;

        if (moduleExists("Membership")) {
            if (membershipModuleExistsAndEnable('Membership')) {
                $this->membershipService = app()->make(MembershipService::class);
            }
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        if (!$user) {
            return ApiResponse::error('الحساب غير موجود. يرجى التحقق من البيانات المدخلة.', [], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse::error('كلمة المرور التي أدخلتها غير صحيحة.', [], 401);
        }

        if (!$user->otp_verified) {
            return ApiResponse::error(
                'يجب تفعيل الحساب أولاً. تم إرسال رمز التفعيل إلى هاتفك عند التسجيل.',
                [
                    'phone_verification_required' => true,
                    'user_phone' => $user->phone
                ],
                403 
            );
        }

        if (!$user->status) { 
            return ApiResponse::error('حسابك غير نشط حالياً. يرجى التواصل مع الإدارة.', [], 403);
        }

        if ($user->is_suspend) {
            return ApiResponse::error('تم تعليق حسابك. يرجى التواصل مع الإدارة.', [], 403);
        }

        return ApiResponse::success('تم تسجيل الدخول بنجاح', [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $user = User::create([
                ...$data,
                'username' => fake()->userName() ?? uniqid('user_'),
                'password' => bcrypt($data['password']),
                'otp_verified' => false,
                'last_verification_attempt_at' => Carbon::now(),
            ]);

            Log::info("User registered successfully with ID: {$user->id}");

            if (moduleExists("Wallet")) {
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'remaining_balance' => 0,
                    'withdraw_amount' => 0,
                    'status' => 1
                ]);
                Log::info("Wallet created for user ID: {$user->id}");
            }

            if (moduleExists("Membership") && membershipModuleExistsAndEnable('Membership')) {
                if (!isset($this->membershipService)) {
                     $this->membershipService = app()->make(MembershipService::class);
                }
                $this->membershipService->createFreeMembership($user);
                Log::info("Free membership created for user ID: {$user->id}");
            }

            $otpSent = $this->sendAndStoreOtp($user);

            $finalResponseData = [
                'user' => new UserResource($user),
                'phone_verification_required' => true,
                'otp_sent' => $otpSent,
                'otp_expires_in_minutes' => self::OTP_EXPIRY_MINUTES,
            ];

            $message = $otpSent
                ? 'تم إنشاء الحساب بنجاح، تحقق من هاتفك للحصول على رمز التفعيل'
                : 'تم إنشاء الحساب بنجاح، ولكن فشل إرسال رمز التحقق';
            
            return ApiResponse::success($message, $finalResponseData, 201);

        } catch (\Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ApiResponse::error('حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقًا.', [], 500);
        }
    }
    
    /**
     * Helper function to generate, store, and send OTP.
     * @param User $user
     * @return bool
     */
    private function sendAndStoreOtp(User $user): bool
    {
        $traceId = uniqid(); 
        
        Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Starting process for user ID {$user->id}.");

        try {
            $otpCode = random_int(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES);
            Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Generated OTP {$otpCode}, expires at {$expiresAt}.");

            $deletedCount = UserOtp::where('user_id', $user->id)->delete();
            Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Deleted {$deletedCount} old OTPs for user ID {$user->id}.");

            UserOtp::create([
                'user_id' => $user->id,
                'otp_code' => $otpCode,
                'expire_date' => $expiresAt,
            ]);
            Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Successfully created new OTP in database.");

            $user->update([
                'last_verification_attempt_at' => Carbon::now(),
            ]);
            Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Successfully updated user's last attempt time.");

            Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - Attempting to send OTP via SmsService to phone number {$user->phone}.");
            $smsSent = $this->smsService->sendOtp($user->phone, (string)$otpCode);

            if ($smsSent) {
                Log::info("sendAndStoreOtp: [TraceID: {$traceId}] - SmsService reported SUCCESS.");
                return true;
            } else {
                Log::error("sendAndStoreOtp: [TraceID: {$traceId}] - SmsService reported FAILURE.");
                return false;
            }

        } catch (\Exception $e) {
            Log::error("sendAndStoreOtp: [TraceID: {$traceId}] - An exception occurred for user ID {$user->id}.");
            Log::error("sendAndStoreOtp: [TraceID: {$traceId}] - Error Message: " . $e->getMessage());
            Log::error("sendAndStoreOtp: [TraceID: {$traceId}] - Stack Trace: " . $e->getTraceAsString());
            
            return false; 
        }
    }


    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('otp_verified', false)
            ->first();

        if (!$user) {
            return ApiResponse::error('رقم الهاتف غير صحيح أو الحساب مفعل بالفعل', [], 400);
        }

        $userOtp = UserOtp::where('user_id', $user->id)
                           ->where('otp_code', $request->otp_code)
                           ->first();
                           
        if (!$userOtp) {
            return ApiResponse::error('رمز التحقق غير صحيح', [], 400);
        }

        if (Carbon::now()->gt($userOtp->expire_date)) {
            $userOtp->delete(); 
            return ApiResponse::error('رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد.', [
                'expired' => true,
                'can_resend' => true
            ], 400);
        }

        $user->update([
            'otp_verified' => true,
            'email_verification_attempts' => 0, 
            'last_verification_attempt_at' => null
        ]);

        $userOtp->delete(); 

        return ApiResponse::success('تم تفعيل الحساب بنجاح', [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }

    public function resendVerificationOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $user = User::where('phone', $request->phone)
            ->where('otp_verified', false)
            ->first();

        if (!$user) {
            return ApiResponse::error('الحساب غير موجود أو مفعل بالفعل', [], 404);
        }

        $canSend = $this->canSendVerificationCode($user); 

        if (!$canSend['can_send']) {
            return ApiResponse::error($canSend['error'], [
                // 'remaining_attempts' => $canSend['remaining_attempts'],
                'wait_minutes' => $canSend['wait_minutes'] ?? null
            ], 429);
        }

        $otpSent = $this->sendAndStoreOtp($user);

        if ($otpSent) {
            return ApiResponse::success('تم إرسال رمز التحقق بنجاح', [
                'otp_expires_in_minutes' => self::OTP_EXPIRY_MINUTES,
                // 'remaining_attempts' => $canSend['remaining_attempts']
            ]);
        } else {
            return ApiResponse::error('فشل في إرسال رمز التحقق، يرجى المحاولة لاحقاً', [], 500);
        }
    }


    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return ApiResponse::success(
                'إذا كان هذا الرقم مسجلاً، فسيتم إرسال رمز إعادة تعيين كلمة المرور إليه.',
                ['otp_expires_in_minutes' => self::OTP_EXPIRY_MINUTES]
            );
        }

        $canSend = $this->canSendVerificationCode($user);
        if (!$canSend['can_send']) {
            return ApiResponse::error($canSend['error'], [
                // 'remaining_attempts' => $canSend['remaining_attempts'],
                'wait_minutes' => $canSend['wait_minutes'] ?? null
            ], 429);
        }

        $otpSent = $this->sendAndStoreOtp($user);

        if ($otpSent) {
            return ApiResponse::success('تم إرسال رمز إعادة تعيين كلمة المرور بنجاح.', [
                'otp_expires_in_minutes' => self::OTP_EXPIRY_MINUTES,
                // 'remaining_attempts' => $canSend['remaining_attempts']
            ]);
        } else {
            return ApiResponse::error('فشل في إرسال رمز إعادة تعيين كلمة المرور. يرجى المحاولة لاحقًا.', [], 500);
        }
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp_code' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return ApiResponse::error('رقم هاتف غير موجود', [], 400);
        }

        $userOtp = UserOtp::where('user_id', $user->id)
            ->where('otp_code', $request->otp_code)
            ->first();
            
        if (!$userOtp) {
            return ApiResponse::error('رمز التحقق غير صحيح', [], 400);
        }

        if (Carbon::now()->gt($userOtp->expire_date)) {
            $userOtp->delete();
            return ApiResponse::error('رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد.', ['expired' => true], 400);
        }
        
        $user->update([
            'password' => bcrypt($request->password),
            'email_verification_attempts' => 0,
            'last_verification_attempt_at' => null
        ]);

        $userOtp->delete();
        return ApiResponse::success('تم تغيير كلمة المرور بنجاح.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success('تم تسجيل الخروج بنجاح');
    }

  private function canSendVerificationCode($user)
{
    $now = Carbon::now();

    if ($user->last_verification_attempt_at) {
        $lastAttemptTime = Carbon::parse($user->last_verification_attempt_at);
        if ($now->diffInMinutes($lastAttemptTime) < self::RATE_LIMIT_MINUTES) {
            $waitTime = self::RATE_LIMIT_MINUTES - $now->diffInMinutes($lastAttemptTime);
            return [
                'can_send' => false,
                'error' => 'يجب الانتظار ' . $waitTime . ' دقيقة قبل إعادة الإرسال.',
                'wait_minutes' => $waitTime,
            ];
        }
    }
    
    return [
        'can_send' => true,
    ];
}
}