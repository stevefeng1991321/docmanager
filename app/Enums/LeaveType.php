<?php

namespace App\Enums;

enum LeaveType: string
{
    case Annual    = 'annual';
    case Sick      = 'sick';
    case Personal  = 'personal';
    case Unpaid    = 'unpaid';
    case Maternity = 'maternity';
    case Paternity = 'paternity';

    public function label(): string
    {
        return match($this) {
            self::Annual    => 'Annual Leave',
            self::Sick      => 'Sick Leave',
            self::Personal  => 'Personal Leave',
            self::Unpaid    => 'Unpaid Leave',
            self::Maternity => 'Maternity Leave',
            self::Paternity => 'Paternity Leave',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
