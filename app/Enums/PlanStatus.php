<?php

namespace App\Enums;

enum PlanStatus: string
{
    case Draft      = 'draft';
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case OnHold     = 'on_hold';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';
    case Archived   = 'archived';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Draft',
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::OnHold     => 'On Hold',
            self::Completed  => 'Completed',
            self::Cancelled  => 'Cancelled',
            self::Archived   => 'Archived',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Draft      => 'bg-gray-100 text-gray-600',
            self::Pending    => 'bg-yellow-100 text-yellow-700',
            self::InProgress => 'bg-blue-100 text-blue-700',
            self::OnHold     => 'bg-orange-100 text-orange-700',
            self::Completed  => 'bg-green-100 text-green-700',
            self::Cancelled  => 'bg-red-100 text-red-700',
            self::Archived   => 'bg-purple-100 text-purple-700',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft      => 'gray',
            self::Pending    => 'yellow',
            self::InProgress => 'blue',
            self::OnHold     => 'orange',
            self::Completed  => 'green',
            self::Cancelled  => 'red',
            self::Archived   => 'purple',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled, self::Archived]);
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
