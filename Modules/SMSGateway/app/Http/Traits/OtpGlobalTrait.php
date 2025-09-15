<?php

namespace Modules\SMSGateway\app\Http\Traits;

use App\Helpers\FlashMsg;
use Exception;
use Illuminate\Support\Facades\Http;
use Modules\SMSGateway\app\Models\SmsGateway;
use Modules\SMSGateway\app\Models\UserOtp;
use Twilio\Rest\Client;

trait OtpGlobalTrait
{
    public string $twilio_number;
    public string|array|object $header;
    public string $headerType = 'OTP';
    public string $method = 'GET';
    public string $apiEndpoint = '';
    public string|array $body = [];
    public string $receiverNumber = '';
    public string $templateId = '';
    public string $user;

    public function sendSms($data, $type='notify', $sms_type='register', $user='user')
    {

        [$receiverNumber, $message] = $data;
        $client = $this->config();

        if($client['gateway'] === 'nexmo'){
            $nexmo_from = get_static_option('site_title');
            try {
                $sms_gateway = $this->activeSmsGateway();

                $credentials = json_decode($sms_gateway->credentials);
                $nexmo_api_key = $credentials->nexmo_api_key;
                $nexmo_api_secret = $credentials->nexmo_api_secret;

                $basic  = new \Nexmo\Client\Credentials\Basic($nexmo_api_key, $nexmo_api_secret);
                $client = new \Nexmo\Client($basic);

                $message = $client->message()->send([
                    'to' => $receiverNumber,
                    'from' => $nexmo_from,
                    'text' => $message
                ]);

                // Check if the response indicates success
                if ($message['status'] == 0) {
                    FlashMsg::error(__('SMS sent successfully'));
                } else {
                    FlashMsg::error(__('Failed to send SMS'));
                }

            } catch (\Exception $e) {
                info("Error: ". $e->getMessage());
            }

        }elseif($client['gateway'] === 'twilio'){
            try {
            $client['client']->messages->create($receiverNumber, [
                'from' => $this->twilio_number,
                'body' => $message
            ]);
            } catch (\Exception $e) {
                info("Error: ". $e->getMessage());
            }

        }else {
            $this->user = $user;
            // type  otp or notify
            // sms_type : register or order
            $otp_or_order_id = count($data) === 3 ? $data[count($data)-1] : 000000;
            $client = $this->otherOperator($client['gateway'], $type,'POST')
            ->setBody(str_replace('+','',$receiverNumber), $sms_type, $otp_or_order_id)->match();

            if ($client->ok())
            {
                return true;
            }
            return false;
        }


        return true;
    }

    public function generateOtp($phone_number, $model_name = 'User', $column_name = 'phone', $relation_name = 'otpInfo', $is_module = false, $module_name = null)
    {
        $user = $this->getModel($model_name, $is_module, $module_name)::select('id')->where($column_name, $phone_number)->first();
        $userOtp = $user->$relation_name;

        if ($user) {
            $gateway = $this->activeSmsGateway();
            $now = now();

            $otp_expire_time = $gateway->otp_expire_time ?? 1;
            $times = $otp_expire_time == 30 ? 'addSeconds' : 'addMinutes';

            if ($userOtp) {
                $userOtp->update([
                    'user_id' => $user->id,
                    'otp_code' => rand(111111, 999999),
                    'expire_date' => $now->$times($otp_expire_time)
                ]);
            } else {
                $userOtp = UserOtp::create([
                    'user_id' => $user->id,
                    'otp_code' => rand(111111, 999999),
                    'expire_date' => $now->$times($otp_expire_time)
                ]);
            }
        }

        return $userOtp;
    }

    private function getModel($model_name = 'User', $is_module = false, $module_name = null): string
    {
        // Use CamelCase for Model and Module Name
        $model_path = '';
        if ($is_module) {
            $model_path = 'Modules\\' . ucwords($module_name) . '\app\Http\\' . ucwords($model_name);
        } else {
            $model_path = '\App\Models\\' . ucwords($model_name);
        }

        return $model_path;
    }

    private function activeSmsGateway()
    {
        return SMSGateway::active()->first();
    }

    private function config()
    {
        $sms_gateway = $this->activeSmsGateway();
        $client = '';

        try {
            retry(2, function () use ($sms_gateway, &$client) {
                $client = null;

                if ($sms_gateway->name == 'nexmo') {
                    $credentials = $this->nexmoConfig($sms_gateway);
                    $basic  = new \Nexmo\Client\Credentials\Basic($credentials[0], $credentials[1]);
                    $client = new \Nexmo\Client($basic);
                }

                if ($sms_gateway->name == 'twilio') {
                    $credentials = $this->twilioConfig($sms_gateway);
                    $client = new Client(...$credentials);
                }
            }, 1000);
        } catch (Exception $e) {
            info("error: " . $e->getMessage());
        }

        return [
                'gateway' => $sms_gateway?->name ?? '',
                'client' => $client ?? []
            ];
    }

    private function otherOperator($sms_gateway, $type, $method)
    {
        $this->headerType = $type;
        $headerFunction = $sms_gateway.'Header';
        $header = $this->$headerFunction();
        $this->header = Http::withHeaders($header);
        $this->method = $method;
        return $this;
    }

    private function setBody($receiverNumber, $sms_type, $otp_or_order_id = 000000)
    {
        $this->setActiveTemplate($sms_type);
        $this->receiverNumber = $receiverNumber;

        if (strtolower($this->headerType) === 'otp')
        {
            $this->body = [
                "template_id" => $this->templateId,
                "recipients" => [
                    [
                        "mobiles" => $this->receiverNumber,
                        "CODE" => $otp_or_order_id,
                        "NAME" => "user",
                        "SITENAME" => get_static_option('site_title')
                    ]
                ]
            ];
        } else {
            if (strtolower($sms_type) === 'register')
            {
                $this->body = [
                    "template_id" => $this->templateId,
                    "recipients" => [
                        [
                            "mobiles" => $this->receiverNumber,
                            "SITENAME" => get_static_option('site_title')
                        ]
                    ]
                ];
            } else {
                $this->body = [
                    "template_id" => $this->templateId,
                    "recipients" => [
                        [
                            "mobiles" => $this->receiverNumber,
                            "ORDER" => $otp_or_order_id,
                            "SITENAME" => get_static_option('site_title')
                        ]
                    ]
                ];
            }
        }

        return $this;
    }

    private function match()
    {
        if (in_array(strtolower($this->method), ['get','post']))
        {
            $method = strtolower($this->method);

            if (!empty($this->body))
            {
                return $this->header->$method($this->apiEndpoint, $this->body);
            }

            return $this->header->$method($this->apiEndpoint);
        }

        return response("Invalid http type exception", 400);
    }

    private function msg91Header()
    {
        $config = $this->msg91Config();
        $this->apiEndpoint = 'https://control.msg91.com/api/v5/flow/';

        return [
            'accept' => 'application/json',
            'authkey' => $config['auth_key'],
            'content-type' => 'application/json'
        ];
    }

    private function nexmoConfig($sms_gateway): array
    {
        $credentials = json_decode($sms_gateway->credentials);
        $nexmo_api_key = $credentials->nexmo_api_key;
        $nexmo_api_secret = $credentials->nexmo_api_secret;

        return [$nexmo_api_key, $nexmo_api_secret];
    }

    private function twilioConfig($sms_gateway): array
    {
        $credentials = json_decode($sms_gateway->credentials);
        $account_sid = $credentials->twilio_sid;
        $auth_token = $credentials->twilio_auth_token;
        $this->twilio_number = $credentials->twilio_number;

        return [$account_sid, $auth_token];
    }

    private function msg91Config(): array
    {
        $sms_gateway = $this->activeSmsGateway();
        $credentials = json_decode($sms_gateway->credentials);
        $auth_key = $credentials->msg91_auth_key;
        $msg91_otp_template_id = $credentials->msg91_otp_template_id;
        $msg91_notify_user_register_template_id = $credentials->msg91_notify_user_register_template_id;

        return [
            'auth_key' => $auth_key,
            'opt_template_id' => $msg91_otp_template_id,
            'notify_user_register_template_id' => $msg91_notify_user_register_template_id,
        ];
    }

    private function setActiveTemplate($sms_type)
    {
        $config = $this->msg91Config();

        $template_id = '';
        if (strtolower($this->headerType) === 'otp')
        {
            $template_id = $config['opt_template_id'];
        } else {
            if (strtolower($sms_type) === 'register')
            {
                $template_id = $config["notify_".$this->user."_register_template_id"];
            } else {
                $template_id = $config["notify_".$this->user."_order_template_id"];
            }
        }

        $this->templateId = $template_id;
    }
}
