<?php

namespace App\Enums\Otp;

enum OTPTypeEnum: string
{
    case LOGIN = 'login';
    case REGISTER = 'register';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}