<?php

namespace App\Enums;

enum CanalNotification: string
{
    case APP = 'app';
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::APP => 'Application',
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
            self::PUSH => 'Notification push',
        };
    }
}
