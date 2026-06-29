<?php

namespace App\Enums;

enum UserStatus: string
{
    case Pending  = 'pending';
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Pending',
            self::Active   => 'Active',
            self::Inactive => 'Inactive',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Pending  => 'bg-yellow-100 text-yellow-700',
            self::Active   => 'bg-green-100 text-green-700',
            self::Inactive => 'bg-gray-100 text-gray-500',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
