<?php

namespace Tests\Unit\Support;

use App\Models\Setting;
use App\Support\PasswordPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    // ── Level metadata ────────────────────────────────────────────────────────

    public function test_all_five_levels_are_defined(): void
    {
        $this->assertCount(5, PasswordPolicy::LEVELS);
        $this->assertArrayHasKey('basic',         PasswordPolicy::LEVELS);
        $this->assertArrayHasKey('standard',      PasswordPolicy::LEVELS);
        $this->assertArrayHasKey('strong',        PasswordPolicy::LEVELS);
        $this->assertArrayHasKey('very_strong',   PasswordPolicy::LEVELS);
        $this->assertArrayHasKey('high_security', PasswordPolicy::LEVELS);
    }

    public function test_levels_are_in_ascending_min_length_order(): void
    {
        $mins = array_column(PasswordPolicy::LEVELS, 'min');
        $sorted = $mins;
        sort($sorted);
        $this->assertSame($sorted, $mins, 'Levels should be ordered by ascending minimum length');
    }

    // ── currentLevelKey / description ────────────────────────────────────────

    public function test_default_level_is_standard(): void
    {
        $this->assertSame('standard', PasswordPolicy::currentLevelKey());
    }

    public function test_current_level_reflects_saved_setting(): void
    {
        Setting::set('password_complexity', 'strong');

        $this->assertSame('strong', PasswordPolicy::currentLevelKey());
    }

    public function test_description_returns_current_level_description(): void
    {
        Setting::set('password_complexity', 'basic');

        $this->assertStringContainsString('6', PasswordPolicy::description());
    }

    // ── rule() returns a Password instance ───────────────────────────────────

    public function test_rule_returns_password_instance(): void
    {
        $this->assertInstanceOf(Password::class, PasswordPolicy::rule());
    }

    // ── Validation enforcement per level ─────────────────────────────────────

    private function validate(string $level, string $password): bool
    {
        Setting::set('password_complexity', $level);
        $v = Validator::make(
            ['password' => $password],
            ['password' => PasswordPolicy::rule()]
        );
        return $v->passes();
    }

    #[DataProvider('levelProvider')]
    public function test_password_passes_for_valid_input(string $level, string $validPassword): void
    {
        $this->assertTrue(
            $this->validate($level, $validPassword),
            "Password '{$validPassword}' should pass level '{$level}'"
        );
    }

    #[DataProvider('levelFailProvider')]
    public function test_password_fails_for_insufficient_input(string $level, string $weakPassword): void
    {
        $this->assertFalse(
            $this->validate($level, $weakPassword),
            "Password '{$weakPassword}' should fail level '{$level}'"
        );
    }

    public static function levelProvider(): array
    {
        return [
            'basic passes 6 chars'              => ['basic',         'abc123'],
            'standard passes 8 chars'           => ['standard',      'abcdefgh'],
            'strong passes 10 chars with nums'  => ['strong',        'abcdefgh12'],
            'very_strong passes 12 mixed+nums'  => ['very_strong',   'Abcdefghij12'],
            'high_security passes 16 all types' => ['high_security', 'Abcdefghij12!@#$'],
        ];
    }

    public static function levelFailProvider(): array
    {
        return [
            'basic fails 5 chars'                    => ['basic',         'ab1de'],
            'standard fails 7 chars'                 => ['standard',      'abcdefg'],
            'strong fails without numbers'           => ['strong',        'abcdefghij'],
            'very_strong fails without uppercase'    => ['very_strong',   'abcdefghij12'],
            'high_security fails without symbols'    => ['high_security', 'Abcdefghijkl1'],
        ];
    }

    // ── Settings page saves and enforces new level ────────────────────────────

    public function test_saving_high_security_level_blocks_weak_registration(): void
    {
        Setting::set('password_complexity', 'high_security');

        $response = $this->post(route('register'), [
            'username'              => 'testuser1',
            'name'                  => 'Test User',
            'password'              => 'password123',   // no uppercase, no symbols, too short
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_basic_level_allows_short_simple_password_on_registration(): void
    {
        Setting::set('password_complexity', 'basic');

        $response = $this->post(route('register'), [
            'username'              => 'simpleuser',
            'name'                  => 'Simple User',
            'password'              => 'abc123',
            'password_confirmation' => 'abc123',
        ]);

        // Should pass complexity — either redirect to login or have no password error
        $response->assertSessionDoesntHaveErrors('password');
    }
}
