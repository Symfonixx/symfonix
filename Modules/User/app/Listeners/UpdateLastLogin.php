<?php

namespace Modules\User\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Update last login timestamp
        $user->forceFill([
            'last_login' => now(),
        ])->save();
    }
}







