<?php

namespace App\Enums;

enum PlanTaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
            self::Cancelled  => 'Cancelled',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Pending    => 'bg-gray-100 text-gray-600',
            self::InProgress => 'bg-blue-100 text-blue-700',
            self::Completed  => 'bg-green-100 text-green-700',
            self::Cancelled  => 'bg-red-100 text-red-700',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending    => 'gray',
            self::InProgress => 'blue',
            self::Completed  => 'green',
            self::Cancelled  => 'red',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
