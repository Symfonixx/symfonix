<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessfulPasswordResetLinkRequestResponseContract;
use Symfony\Component\HttpFoundation\Response;

class SuccessfulPasswordResetLinkRequestResponse implements SuccessfulPasswordResetLinkRequestResponseContract
{
    public function __construct(protected string $status) {}

    public function toResponse($request): Response
    {
        $message = trans($this->status);

        if ($request->wantsJson()) {
            return new JsonResponse(['message' => $message], 200);
        }

        return back()
            ->with('status', $message)
            ->with('success', $message);
    }
}
