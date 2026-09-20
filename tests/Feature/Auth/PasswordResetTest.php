<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_page_receives_email_and_token(): void
    {
        config(['inertia.ssr.enabled' => false]);

        $user = User::factory()->create();

        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LocaleSessionRedirect::class,
        ])->get(route('password.reset', [
            'token' => 'reset-token-value',
            'email' => $user->email,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('User::Auth/ResetPassword', false)
                ->where('email', $user->email)
                ->where('token', 'reset-token-value')
            );
    }

    public function test_reset_password_fails_with_visible_email_error_for_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
        ]))->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('email')
            ->assertSessionHas('error');
    }

    public function test_reset_password_requires_token(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', [
            'token' => 'reset-token-value',
            'email' => $user->email,
        ]))->post(route('password.update'), [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('token');
    }

    public function test_reset_password_succeeds_and_flashes_status_on_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success')
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
