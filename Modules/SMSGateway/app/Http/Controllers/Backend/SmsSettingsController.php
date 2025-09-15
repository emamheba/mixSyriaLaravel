<?php

namespace Modules\SMSGateway\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\SMSGateway\app\Models\SmsGateway;
use Modules\SMSGateway\app\Http\Traits\OtpGlobalTrait;

class SmsSettingsController extends Controller
{
    use OtpGlobalTrait;
    public function login_otp_status()
    {
        if (!get_static_option('otp_login_status'))
        {
            update_static_option('otp_login_status', 'on');
        } else {
            delete_static_option('otp_login_status');
        }

        return response()->json([
            'type' => 'success'
        ]);
    }

    public function sms_settings()
    {
        return view('smsgateway::backend.sms-gateway-settings');
    }

    public function update_sms_settings(Request $request)
    {
        abort_if($request->method() == 'GET', 404);

        $request->validate([
            'sms_gateway_name' => 'required|in:nexmo,twilio,msg91',
            'user_otp_expire_time' => 'required|numeric',
            'nexmo_api_key' => 'required_if:sms_gateway_name,nexmo',
            'nexmo_api_secret' => 'required_if:sms_gateway_name,nexmo',
            'twilio_sid' => 'required_if:sms_gateway_name,twilio',
            'twilio_auth_token' => 'required_if:sms_gateway_name,twilio',
            'msg91_auth_key' => 'required_if:sms_gateway_name,msg91',
            'msg91_otp_template_id' => 'required_if:sms_gateway_name,msg91',
            'msg91_notify_user_register_template_id' => 'nullable',
            'msg91_notify_admin_register_template_id' => 'nullable',
            'msg91_notify_user_order_template_id' => 'nullable',
            'msg91_notify_admin_order_template_id' => 'nullable',
        ]);

        $fields = [];
        foreach ($request->toArray() ?? [] as $key => $value)
        {
            $fields[$key] = $value;
        }

        unset($fields['_token'], $fields['sms_gateway_name'], $fields['user_otp_expire_time']);

        $gateway = SmsGateway::updateOrCreate(
            [
                'name' => $request->sms_gateway_name
            ],
            [
                'name' => $request->sms_gateway_name,
                'status' => SmsGateway::where('name', $request->sms_gateway_name)->first()?->status ?? true,
                'otp_expire_time' => $request->user_otp_expire_time,
                'credentials' => json_encode($fields)
            ]
        );

        SmsGateway::where('id', '!=', $gateway->id)->update(['status' => false]);

        return back()->with(['msg' => __('Settings updated'), 'type' => 'success']);
    }

    public function update_status(Request $request)
    {
        $validated = $request->validate([
            'option_name' => 'required'
        ]);

        SmsGateway::where('name', '!=', $validated['option_name'])->update(['status' => false]);

        $sms_gateway = SmsGateway::where('name', $validated['option_name'])->first();
        $sms_gateway?->update([
            'status' => !$sms_gateway->status
        ]);

        return response()->json([
            'type' => 'success'
        ]);
    }

    public function update_sms_option_settings(Request $request)
    {
        abort_if($request->method() == 'GET', 404);
            $validated = [
                'new_user_admin' => 'nullable',
                'new_user_user' => 'nullable',
                'new_order_admin' => 'nullable',
                'new_order_user' => 'nullable'
            ];

        $active_sms_gateway = SmsGateway::active()->first();
        if ($active_sms_gateway?->name === 'msg91')
        {
            $credentials = json_decode($active_sms_gateway->credentials);

            foreach ($request->except('_token', 'receiving_phone_number') ?? [] as $key => $item)
            {
                if (!empty($item))
                {
                    $index_name_array = explode('_', $key);
                    $condition = $index_name_array[1];
                    $needle = $index_name_array[2];

                    $condition = $condition === 'user' ? 'register' : 'order';
                    $query = "msg91_notify_{$needle}_{$condition}_template_id";

                    if (property_exists($credentials, $query) && empty($credentials->$query))
                    {
                        return back()->with(FlashMsg::item_update('danger', str_replace("_"," ", ucfirst($query))." can not be empty"));
                    }
                }
            }
        }

        $validated['receiving_phone_number'] = 'required|numeric|regex:/^[+].*/';

        $request->validate($validated);

        foreach ($validated ?? [] as $key => $value)
        {
            update_static_option($key, $request->$key);
        }

        return back()->with(FlashMsg::settings_update('SMS option settings updated successfully'));
    }

    public function send_test_sms(Request $request)
    {
        abort_if($request->method() == 'GET', 404);

        $request->validate([
            'test_phone_number' => 'required'
        ]);

        try {
            $this->sendSms([$request->test_phone_number, __('Test SMS From '.get_static_option('site_title'))], 'otp');
        } catch (\Exception $exception) {
            if ($exception->getCode() == 20003)
            {
                return back()->with(FlashMsg::item_delete('danger', __('Authentication failed, sms gateway access credentials are incorrect.')));
            }
        }

        return back()->with(FlashMsg::item_new('success', __('Test SMS has sent to your phone number.')));
    }
}
