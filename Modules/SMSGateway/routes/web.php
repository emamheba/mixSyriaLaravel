<?php

use Illuminate\Support\Facades\Route;
use Modules\SMSGateway\app\Http\Controllers\Backend\SmsSettingsController;

/*------------------ SMS SETTINGS MANAGE --------------*/
Route::group(['prefix' => 'sms-gateway-settings', 'middleware' => ['auth:admin']], function () {
    Route::get('/view',[SmsSettingsController::class, 'sms_settings'])->name('admin.sms.gateway.settings')->permission('sms-gateway-settings');
    Route::post('/update',[SmsSettingsController::class, 'update_sms_settings'])->name('admin.sms.gateway.update');
    Route::match(['get', 'post'], '/update-status', [SmsSettingsController::class, 'update_status'])->name('admin.sms.status')->permission('sms-gateway-status-change');
    Route::get('/login-otp-status', [SmsSettingsController::class, 'login_otp_status'])->name('admin.sms.login.otp.status');
    // sms settings controller
    Route::match(['get', 'post'], '/sms-options', [SmsSettingsController::class, 'update_sms_option_settings'])->name('admin.sms.options')->permission('sms-options-settings');
    // test sms
    Route::post('/test/otp', [SmsSettingsController::class, 'send_test_sms'])->name('admin.sms.test');
});


// USER OTP LOGIN
Route::middleware(['globalVariable', 'maintains_mode'])
    ->controller(\Modules\SMSGateway\app\Http\Controllers\Frontend\UserFrontendController::class)->name('user.')->group(function () {
        Route::get('/login/otp', 'showOtpLoginForm')->name('login.otp');
        Route::post('/login/otp', 'sendOtp');
        Route::get('/login/otp/verification', 'showOtpVerificationForm')->name('login.otp.verification');
        Route::post('/login/otp/verification', 'verifyOtp');
        Route::get('/login/otp/resend', 'resendOtp')->name('login.otp.resend');
        // user phone number check
        Route::post('login/user-phone-number-check','userPhoneNumberCheck')->name('phone.number.check');
});
