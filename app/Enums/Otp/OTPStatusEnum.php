<?php

namespace App\Enums\Otp;

enum OTPStatusEnum: string
{
    case ACTIVE = 'active';
    case USED = 'used';
    case EXPIRED = 'expired';

    /**
     * Get all the values of the enum.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}