<?php

namespace Modules\AI\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\AI\Http\Requests\Admin\GenerateImageEditRequest;
use Modules\AI\Support\CompanyContentProfile;
use Modules\AI\Support\ImageEditTargets;

class ImageEditService
{
    public function __construct(private readonly GeminiImageService $geminiImageService) {}

    /**
     * @return array{success: bool, error?: string, mode?: string, previewUrl?: string, token?: string}
     */
    public function generate(GenerateImageEditRequest $request, User $user): array
    {
        $mode = $request->mode();
        $disk = 'public';
        $binary = null;
        $mimeType = null;
        $entry = null;

        if ($request->isPersisted()) {
            $target = (string) $request->target();
            $field = (string) $request->field();
            $entry = ImageEditTargets::get($target);

            if ($entry === null || ! ImageEditTargets::allowsField($target, $field)) {
                return $this->failure(__('ai::image_edit.messages.invalid_target'));
            }

            if (! $user->can($entry['permission'])) {
                abort(403);
            }

            $model = $entry['model']::query()->findOrFail($request->recordId());
            $disk = $entry['disk'];

            if ($mode === 'edit') {
                $currentPath = $model->{$field};

                if (empty($currentPath) || ! Storage::disk($disk)->exists($currentPath)) {
                    return $this->failure(__('ai::image_edit.messages.source_image_missing'));
                }

                $binary = Storage::disk($disk)->get($currentPath);
                $mimeType = Storage::disk($disk)->mimeType($currentPath) ?: 'image/png';
            }
        } elseif ($mode === 'edit') {
            [$mimeType, $binary] = $this->decodeInlineImage((string) $request->inlineImage(), $request->mimeType());

            if ($binary === null) {
                return $this->failure(__('ai::image_edit.messages.source_image_missing'));
            }
        }

        $brandLogo = $request->usesBrandLogo()
            ? CompanyContentProfile::logoReference()
            : null;

        $result = $mode === 'create'
            ? $this->geminiImageService->generateImage($request->prompt(), $brandLogo)
            : $this->geminiImageService->editImage((string) $binary, (string) $mimeType, $request->prompt(), $brandLogo);

        if (! $result['success'] || ! is_string($result['binary'])) {
            return $this->failure($result['error'] ?? __('ai::image_edit.messages.request_failed'));
        }

        $payload = [
            'success' => true,
            'mode' => $request->isPersisted() ? 'persisted' : 'adhoc',
            'previewUrl' => 'data:'.$result['mime_type'].';base64,'.base64_encode($result['binary']),
        ];

        if ($request->isPersisted() && $entry !== null) {
            $extension = $this->extensionForMimeType($result['mime_type']);
            $newPath = sprintf(
                'ai-edits/%s/%s/%s.%s',
                $request->target(),
                $request->recordId(),
                (string) Str::uuid(),
                $extension,
            );

            Storage::disk($disk)->put($newPath, $result['binary']);

            $payload['token'] = Crypt::encrypt([
                'path' => $newPath,
                'disk' => $disk,
                'target' => $request->target(),
                'id' => $request->recordId(),
                'field' => $request->field(),
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);
        }

        return $payload;
    }

    /**
     * @return array{success: bool, error?: string, url?: string, message?: string}
     */
    public function apply(string $token, string $action, User $user): array
    {
        try {
            $payload = Crypt::decrypt($token);
        } catch (\Throwable) {
            return $this->failure(__('ai::image_edit.messages.invalid_or_expired'));
        }

        if (! is_array($payload) || ($payload['expires_at'] ?? 0) < now()->timestamp) {
            return $this->failure(__('ai::image_edit.messages.invalid_or_expired'));
        }

        $target = (string) ($payload['target'] ?? '');
        $field = (string) ($payload['field'] ?? '');
        $entry = ImageEditTargets::get($target);

        if ($entry === null || ! ImageEditTargets::allowsField($target, $field)) {
            return $this->failure(__('ai::image_edit.messages.invalid_target'));
        }

        if (! $user->can($entry['permission'])) {
            abort(403);
        }

        $disk = $payload['disk'] ?? $entry['disk'];

        if (! Storage::disk($disk)->exists($payload['path'])) {
            return $this->failure(__('ai::image_edit.messages.invalid_or_expired'));
        }

        $model = $entry['model']::query()->findOrFail($payload['id']);
        $oldPath = $model->{$field};

        if ($action === 'replace' && filled($oldPath) && $oldPath !== $payload['path']) {
            Storage::disk($disk)->delete($oldPath);
        }

        $model->{$field} = $payload['path'];
        $model->save();

        return [
            'success' => true,
            'url' => Storage::disk($disk)->url($payload['path']),
            'message' => __('ai::image_edit.messages.applied'),
        ];
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    private function decodeInlineImage(string $image, ?string $mimeTypeHint): array
    {
        $mimeType = $mimeTypeHint ?: 'image/png';

        if (str_starts_with($image, 'data:') && preg_match('/^data:([^;]+);base64,(.*)$/s', $image, $matches)) {
            $mimeType = $matches[1];
            $image = $matches[2];
        }

        return [$mimeType, base64_decode($image, true) ?: null];
    }

    private function extensionForMimeType(?string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
    }

    /**
     * @return array{success: bool, error: string}
     */
    private function failure(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
        ];
    }
}
