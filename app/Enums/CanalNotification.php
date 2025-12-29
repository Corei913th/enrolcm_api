<?php

namespace App\Enums;

class CanalNotification
{
    public const APP = 'app';
    public const EMAIL = 'email';
    public const SMS = 'sms';
    public const PUSH = 'push';
    
    public static function values(): array
    {
        return [
            self::APP,
            self::EMAIL,
            self::SMS,
            self::PUSH,
        ];
    }

    public static function label(string $value): string
    {
        $labels = [
            self::APP => 'Application',
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::PUSH => 'Notification push',
        ];

        return $labels[$value] ?? $value;
    }
}
