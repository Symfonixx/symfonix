<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetResponse implements PasswordResetResponseContract
{
    public function __construct(protected string $status) {}

    public function toResponse($request): Response
    {
        $message = trans($this->status);

        return redirect()
            ->route('login')
            ->with('status', $message)
            ->with('success', $message);
    }
}
