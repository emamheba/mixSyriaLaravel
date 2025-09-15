<?php

namespace Modules\SMSGateway\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Http\Client\Exception;
use Illuminate\Http\Request;
use Modules\SMSGateway\app\Models\SmsGateway;
use Twilio\Rest\Client;

class SMSGatewayController extends Controller
{


    public function smsTestSent(Request $request)
    {

        $request->validate([
            'phone_number' => 'required|max:50'
        ]);

        $phoneNumber = $request->phone_number;
        $message = __('Test SMS Successfully send.');

        // if twilio enable
        if(!empty(get_static_option('sms_gateway_enable_disable'))){
            try {
                $accountSid = get_static_option('twilio_sid');
                $authToken = get_static_option('twilio_auth_token');
                $twilioNumber = get_static_option('twilio_number');

                $client = new Client($accountSid, $authToken);

                // Sending SMS
                $client->messages->create($phoneNumber, [
                    'from' => $twilioNumber,
                    'body' => $message
                ]);

                info(__('SMS Send Successfully.'));

                return redirect()->back()->with(FlashMsg::item_new(__('Send Success')));

            } catch (\Twilio\Exceptions\TwilioException $e) {
                info("Twilio Error: " . $e->getMessage());
                // Handle Twilio-specific errors
                return redirect()->back()->with(FlashMsg::error(__('Failed to send SMS. Twilio Error:') . $e->getMessage()));
            } catch (\Exception $e) {
                info("General Error: " . $e->getMessage());
                // Handle general errors
                return redirect()->back()->with(FlashMsg::error(__('Failed to send SMS. Please try again later.')));
            }
        }
        return redirect()->back()->with(FlashMsg::error(__('Enable SMS Gateway')));
    }



    public function smsGatewayUpdate(Request $request)
    {
        // Check if SMS gateway with the given name already exists
        $find_sms_gateway = SmsGateway::where('name', $request->sms_gateway_name)->first();
        if (!empty($request->sms_gateway_enable_disable)){
            $status = 1;
        }else{
            $status = 0;
        }

        if(empty($find_sms_gateway)){
            // If the SMS gateway doesn't exist, create a new one
            SmsGateway::create([
                'name' => $request->sms_gateway_name,
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'token' => $request->token,
                'from_number' => $request->from_number,
                'otp_template_id' => $request->otp_template_id,
                'status' => $status
            ]);
        } else {
            // If the SMS gateway already exists, update its fields
            $find_sms_gateway->update([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'token' => $request->token,
                'from_number' => $request->from_number,
                'otp_template_id' => $request->otp_template_id,
                'status' => $status
            ]);
        }
        return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
    }

}
