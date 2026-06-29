<?php

namespace App\Enums;

enum PlanPriority: string
{
    case Low      = 'low';
    case Medium   = 'medium';
    case High     = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match($this) {
            self::Low      => 'Low',
            self::Medium   => 'Medium',
            self::High     => 'High',
            self::Critical => 'Critical',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Low      => 'bg-green-100 text-green-700',
            self::Medium   => 'bg-blue-100 text-blue-700',
            self::High     => 'bg-orange-100 text-orange-700',
            self::Critical => 'bg-red-100 text-red-600',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Low      => 'green',
            self::Medium   => 'blue',
            self::High     => 'orange',
            self::Critical => 'red',
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
