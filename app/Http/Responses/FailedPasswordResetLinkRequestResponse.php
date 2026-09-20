<?php

namespace App\Http\Responses;

use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class FailedPasswordResetLinkRequestResponse implements FailedPasswordResetLinkRequestResponseContract
{
    public function __construct(protected string $status) {}

    /**
     * @throws ValidationException
     */
    public function toResponse($request): Response
    {
        $message = trans($this->status);

        if (method_exists($request, 'hasSession') && $request->hasSession()) {
            $request->session()->flash('error', $message);
        }

        throw ValidationException::withMessages([
            Fortify::email() => [$message],
        ]);
    }
}
