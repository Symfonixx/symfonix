<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedPasswordResetResponse as FailedPasswordResetResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class FailedPasswordResetResponse implements FailedPasswordResetResponseContract
{
    public function __construct(protected string $status) {}

    /**
     * @throws ValidationException
     */
    public function toResponse($request): Response
    {
        $message = trans($this->status);

        if ($request instanceof Request && $request->hasSession()) {
            $request->session()->flash('error', $message);
        }

        throw ValidationException::withMessages([
            Fortify::email() => [$message],
        ])->redirectTo($this->redirectTo($request));
    }

    private function redirectTo(mixed $request): ?string
    {
        if (! $request instanceof Request) {
            return url()->previous();
        }

        $email = $request->input(Fortify::email());
        $token = $request->input('token');

        if (! is_string($token) || $token === '') {
            return url()->previous();
        }

        return route('password.reset', array_filter([
            'token' => $token,
            'email' => is_string($email) ? $email : null,
        ]));
    }
}
