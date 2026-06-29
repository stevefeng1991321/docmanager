<?php

namespace App\Enums;

enum LeaveRequestStatus: string
{
    case Pending   = 'pending';
    case Approved  = 'approved';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Approved  => 'Approved',
            self::Rejected  => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Pending   => 'bg-yellow-100 text-yellow-700',
            self::Approved  => 'bg-green-100 text-green-700',
            self::Rejected  => 'bg-red-100 text-red-700',
            self::Cancelled => 'bg-gray-100 text-gray-500',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => 'yellow',
            self::Approved  => 'green',
            self::Rejected  => 'red',
            self::Cancelled => 'gray',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
