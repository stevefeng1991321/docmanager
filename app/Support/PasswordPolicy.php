<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    /**
     * All available complexity levels in ascending order.
     * Each entry defines the rules that Laravel's Password rule will enforce.
     */
    public const LEVELS = [
        'basic' => [
            'label'       => 'Basic',
            'min'         => 6,
            'mixedCase'   => false,
            'numbers'     => false,
            'symbols'     => false,
            'description' => 'At least 6 characters.',
            'badge'       => 'bg-gray-100 text-gray-600',
        ],
        'standard' => [
            'label'       => 'Standard',
            'min'         => 8,
            'mixedCase'   => false,
            'numbers'     => false,
            'symbols'     => false,
            'description' => 'At least 8 characters.',
            'badge'       => 'bg-blue-100 text-blue-700',
        ],
        'strong' => [
            'label'       => 'Strong',
            'min'         => 10,
            'mixedCase'   => false,
            'numbers'     => true,
            'symbols'     => false,
            'description' => 'At least 10 characters, must include letters and numbers.',
            'badge'       => 'bg-yellow-100 text-yellow-700',
        ],
        'very_strong' => [
            'label'       => 'Very Strong',
            'min'         => 12,
            'mixedCase'   => true,
            'numbers'     => true,
            'symbols'     => false,
            'description' => 'At least 12 characters, must include uppercase, lowercase, and numbers.',
            'badge'       => 'bg-orange-100 text-orange-700',
        ],
        'high_security' => [
            'label'       => 'High Security',
            'min'         => 16,
            'mixedCase'   => true,
            'numbers'     => true,
            'symbols'     => true,
            'description' => 'At least 16 characters, must include uppercase, lowercase, numbers, and symbols.',
            'badge'       => 'bg-red-100 text-red-700',
        ],
    ];

    public static function currentLevelKey(): string
    {
        return Setting::get('password_complexity', 'standard');
    }

    public static function currentLevel(): array
    {
        return self::LEVELS[self::currentLevelKey()] ?? self::LEVELS['standard'];
    }

    /**
     * Returns a Laravel Password rule configured for the active complexity level.
     * Used as the Password::defaults() factory and wherever passwords are validated.
     */
    public static function rule(): Password
    {
        $level = self::currentLevel();

        $rule = Password::min($level['min']);

        if ($level['mixedCase']) $rule = $rule->mixedCase();
        if ($level['numbers'])   $rule = $rule->numbers();
        if ($level['symbols'])   $rule = $rule->symbols();

        return $rule;
    }

    public static function description(): string
    {
        return self::currentLevel()['description'];
    }
}
