<?php

namespace Modules\User\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use Modules\Base\Models\Seo;
use Modules\Base\Support\Meta;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        Fortify::loginView(function () {
            $siteName = Seo::get('website_name', config('app.name'));
            $meta = (new Meta)
                ->title(__('Login').' | '.$siteName)
                ->description(__('Log in to manage your account and services.'))
                ->keywords(__('login, sign in, account access'))
                ->ogImage()
                ->twitterImage()
                ->toArray();
            $meta['robots'] = 'noindex, nofollow';

            return Inertia::render('User::Auth/Login', ['meta' => $meta]);
        });

        Fortify::registerView(function () {
            $siteName = Seo::get('website_name', config('app.name'));
            $meta = (new Meta)
                ->title(__('Register').' | '.$siteName)
                ->description(__('Create a new account to access our services.'))
                ->keywords(__('register, sign up, create account'))
                ->ogImage()
                ->twitterImage()
                ->toArray();
            $meta['robots'] = 'noindex, nofollow';

            return Inertia::render('User::Auth/Register', ['meta' => $meta]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            $siteName = Seo::get('website_name', config('app.name'));
            $meta = (new Meta)
                ->title(__('Forgot Password').' | '.$siteName)
                ->description(__('Request a password reset link to regain access to your account.'))
                ->keywords(__('forgot password, reset password, account recovery'))
                ->ogImage()
                ->twitterImage()
                ->toArray();
            $meta['robots'] = 'noindex, nofollow';

            return Inertia::render('User::Auth/ForgotPassword', ['meta' => $meta]);
        });

        Fortify::resetPasswordView(function () {
            $siteName = Seo::get('website_name', config('app.name'));
            $meta = (new Meta)
                ->title(__('Reset Password').' | '.$siteName)
                ->description(__('Set a new password to secure your account.'))
                ->keywords(__('reset password, account security, set new password'))
                ->ogImage()
                ->twitterImage()
                ->toArray();
            $meta['robots'] = 'noindex, nofollow';

            return Inertia::render('User::Auth/ResetPassword', ['meta' => $meta]);
        });

        Fortify::confirmPasswordView(function () {
            $user = auth()->user();

            if ($user?->isCustomer()) {
                return Inertia::render('User::Portal/ConfirmPassword');
            }

            return view('auth.confirm-password');
        });

        Fortify::twoFactorChallengeView(function () {
            $loginId = session('login.id');
            $user = $loginId ? User::find($loginId) : null;

            if ($user?->isAdmin()) {
                return view('auth.two-factor-challenge');
            }

            $siteName = Seo::get('website_name', config('app.name'));
            $meta = (new Meta)
                ->title(__('Two-Factor Authentication').' | '.$siteName)
                ->description(__('Please confirm access to your account by entering the authentication code provided by your authenticator application.'))
                ->keywords(__('two factor authentication, login security'))
                ->ogImage()
                ->twitterImage()
                ->toArray();
            $meta['robots'] = 'noindex, nofollow';

            return Inertia::render('User::Auth/TwoFactorChallenge', ['meta' => $meta]);
        });

    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
