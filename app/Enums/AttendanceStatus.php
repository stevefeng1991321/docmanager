<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent  = 'absent';
    case Late    = 'late';
    case OnLeave = 'on_leave';
    case Holiday = 'holiday';
    case HalfDay = 'half_day';

    public function label(): string
    {
        return match($this) {
            self::Present => 'Present',
            self::Absent  => 'Absent',
            self::Late    => 'Late',
            self::OnLeave => 'On Leave',
            self::Holiday => 'Holiday',
            self::HalfDay => 'Half Day',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Present => 'bg-green-100 text-green-700',
            self::Absent  => 'bg-red-100 text-red-700',
            self::Late    => 'bg-yellow-100 text-yellow-700',
            self::OnLeave => 'bg-blue-100 text-blue-700',
            self::Holiday => 'bg-purple-100 text-purple-700',
            self::HalfDay => 'bg-orange-100 text-orange-700',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Present => 'green',
            self::Absent  => 'red',
            self::Late    => 'yellow',
            self::OnLeave => 'blue',
            self::Holiday => 'purple',
            self::HalfDay => 'orange',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
