<?php

namespace Modules\SMSGateway\app\Http\Controllers\Frontend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\SMSGateway\app\Actions\SmsSendAction;
use Modules\SMSGateway\app\Models\UserOtp;
use Modules\SMSGateway\app\Http\Traits\OtpGlobalTrait;
class UserFrontendController extends Controller
{
    use OtpGlobalTrait;
    public function __construct()
    {
        abort_if(empty(get_static_option('otp_login_status')), 404);
    }

    public function userPhoneNumberCheck(Request $request)
    {
        $phone = User::where('phone', $request->phone)->first();
        if(!empty($phone) && $phone->phone == $request->phone){
            $status = 'available';
            $msg = __('Phone number is correct');
        }else{
            $status = 'not_available';
            $msg = __('Sorry! Phone number is not correct');
        }
        return response()->json([
            'status'=>$status,
            'msg'=>$msg,
            'phone'=>$phone,
        ]);
    }

    // OTP Login
    public function showOtpLoginForm()
    {
        return view('smsgateway::user.login-otp');
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|regex:/^[0-9+]+$/|exists:users,phone',
            'remember' => 'nullable'
        ], ['phone.exists' => __('No record found for this phone number.')]);

        $sentOtp = null;

        try {
            $otp = $this->generateOtp($validated['phone']);
            $sentOtp = $this->sendSms([$validated['phone'], __('Your login OTP: ') . $otp->otp_code, $otp->otp_code], 'otp');
            session()->put('user-otp', $otp);
        } catch (\Exception $exception) {
            if ($exception->getCode() == 20003)
            {
                return back()->with(FlashMsg::item_delete(__('OTP login in unavailable right now.')));
            }
        }

        return $sentOtp ? to_route('user.login.otp.verification') : back()->with(FlashMsg::item_delete(__('OTP send failed')));
    }

    public function showOtpVerificationForm()
    {
        $userOtp = null;
        $auth_user_id = session('auth_user_id');
        $user = User::where('id', $auth_user_id)->first();


        if ($user){
            try {
                // if user otp is null for first time user register
                $user_otp_info = UserOtp::where('user_id', $user->id)->latest()->first();

                if(is_null($user_otp_info)){
                    $otp = $this->generateOtp($user->phone);
                    $userOtp = $this->sendSms([$user->phone, __('Your login OTP: ') . $otp->otp_code, $otp->otp_code], 'otp');
                    session()->put('user-otp', $otp);
                }
            } catch (\Exception $exception) {
                if ($exception->getCode() == 20003)
                {
                    return back()->with(FlashMsg::item_delete(__('OTP login in unavailable right now.')));
                }
            }
        }

        $userOtp = session('user-otp');

        return view('smsgateway::user.otp-verify', compact('userOtp'));
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|numeric|digits:6',
            'remember' => 'nullable'
        ]);


        $userOtp = UserOtp::where('otp_code', $validated['otp'])->select('user_id', 'expire_date')->first();

        if (empty($userOtp)) {
            return back()->with(FlashMsg::item_delete(__('The OTP code you have entered is not correct')));
        }

        $user = User::findOrFail($userOtp->user_id);

        if (!now()->isAfter($userOtp->expire_date)) {
            Auth::guard('web')->login($user, array_key_exists('remember', $validated));
            session()->forget('user-otp');
            session()->forget('auth_user_id');
            $user->update(['otp_verified' => 1]);

            return to_route('user.dashboard');
        } else {
            return back()->with(FlashMsg::item_delete(__('The OTP code is expired. Apply for new OTP code')));
        }
    }

    public function resendOtp()
    {
        $userOtp = session('user-otp');
        if (!empty($userOtp))
        {
            if (now()->isAfter($userOtp->expire_date)) {
                $number = $userOtp->user?->phone;
                $otp = $this->generateOtp($number);
                $this->sendSms([$number, 'Your login OTP: ' . $otp->otp_code]);
                session()->put('user-otp', $otp);
                return to_route('user.login.otp.verification')->with(FlashMsg::item_delete(__('OTP reset successful. A new code has been sent to your phone')));
            }
            else
            {
                return back()->with(FlashMsg::item_delete(__('You can request a new OTP after the countdown has finished.')));
            }
        }

        return back()->with(FlashMsg::item_delete('Something went wrong.'));
    }
}
