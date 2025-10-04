<?php

namespace App\Http\Enums;

enum ClientStatusEnum: string
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}
