<?php

namespace Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Modules\AI\Http\Requests\Admin\ApplyImageEditRequest;
use Modules\AI\Http\Requests\Admin\GenerateImageEditRequest;
use Modules\AI\Services\ImageEditService;

class ImageEditController extends Controller
{
    public function __construct(private readonly ImageEditService $imageEditService) {}

    public function generate(GenerateImageEditRequest $request): JsonResponse
    {
        return $this->jsonResult(
            $this->imageEditService->generate($request, $this->actor($request->user())),
        );
    }

    public function apply(ApplyImageEditRequest $request): JsonResponse
    {
        return $this->jsonResult(
            $this->imageEditService->apply($request->token(), $request->action(), $this->actor($request->user())),
        );
    }

    private function actor(mixed $user): User
    {
        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }

    /**
     * @param  array{success: bool, error?: string}  $result
     */
    private function jsonResult(array $result): JsonResponse
    {
        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? __('ai::image_edit.messages.request_failed'),
            ], 422);
        }

        unset($result['error']);

        return response()->json($result);
    }
}
