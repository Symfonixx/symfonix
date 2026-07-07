<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        $redirect = $user?->type === User::TYPE_ADMIN
            ? route('admin.dashboard.index')
            : route('portal.dashboard');

        if ($request->wantsJson()) {
            return new JsonResponse([
                'two_factor' => false,
                'redirect' => $redirect,
            ], 200);
        }

        return redirect()->intended($redirect);
    }
}
