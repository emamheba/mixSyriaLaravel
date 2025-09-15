<?php

namespace Modules\SMSGateway\app\Http\Services;

use Modules\SMSGateway\App\Http\Traits\OtpGlobalTrait;

class OtpTraitService
{
    use OtpGlobalTrait;

    public static function gateways(): array
    {
        return [
            'nexmo' => __('Nexmo'),
            'twilio' => __('Twilio'),
            'msg91' => __('MSG91')
        ];
    }

    public function send($data, $type='notify', $sms_type='register', $user='user')
    {
        return $this->sendSms($data, $type, $sms_type, $user);
    }
}
