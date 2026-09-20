<?php

namespace Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\AI\Services\GeminiImageService;
use Modules\AI\Support\CompanyContentProfile;

class ImageEditController extends Controller
{
    public function __construct(private readonly GeminiImageService $geminiImageService) {}

    /**
     * Generate an AI-produced image, either by editing an existing one or by
     * creating a brand new one from a text prompt alone.
     *
     * `mode` controls the source image behaviour:
     * - `edit` (default): needs a source image, either read from disk
     *   (persisted mode, `target`/`id`/`field` present) or supplied inline as
     *   base64 (ad-hoc mode, `image` present) for images that only exist
     *   locally in the browser.
     * - `create`: no source image is read or required at all — Gemini
     *   generates a new image purely from the `prompt`.
     *
     * Independently of `mode`, when `target`/`id`/`field` are present the
     * result is also persisted to disk and an encrypted token is returned so
     * it can later be applied via {@see apply()}. Without a target, nothing
     * is persisted here — the caller applies the result client-side by
     * re-staging the relevant file input.
     */
    public function generate(Request $request): JsonResponse
    {
        $targets = config('ai.editable_targets', []);
        $isPersisted = $request->filled('target');
        $mode = $request->input('mode') === 'create' ? 'create' : 'edit';

        $rules = [
            'prompt' => ['required', 'string', 'max:500'],
            'use_brand_logo' => ['nullable', 'boolean'],
        ];

        if ($isPersisted) {
            $rules['target'] = ['required', 'string', Rule::in(array_keys($targets))];
            $rules['id'] = ['required', 'integer', 'min:1'];
            $rules['field'] = ['required', 'string'];
        } elseif ($mode === 'edit') {
            $rules['image'] = ['required', 'string'];
            $rules['mime_type'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $disk = 'public';
        $entry = null;
        $binary = null;
        $mimeType = null;

        if ($isPersisted) {
            $entry = $targets[$validated['target']];

            if (! in_array($validated['field'], $entry['fields'], true)) {
                return response()->json([
                    'success' => false,
                    'error' => __('ai::image_edit.messages.invalid_target'),
                ], 422);
            }

            if (! $request->user()?->can($entry['permission'])) {
                abort(403);
            }

            $model = $entry['model']::query()->findOrFail($validated['id']);
            $disk = $entry['disk'];

            if ($mode === 'edit') {
                $currentPath = $model->{$validated['field']};

                if (empty($currentPath) || ! Storage::disk($disk)->exists($currentPath)) {
                    return response()->json([
                        'success' => false,
                        'error' => __('ai::image_edit.messages.source_image_missing'),
                    ], 422);
                }

                $binary = Storage::disk($disk)->get($currentPath);
                $mimeType = Storage::disk($disk)->mimeType($currentPath) ?: 'image/png';
            }
        } elseif ($mode === 'edit') {
            if (! $request->user()) {
                abort(403);
            }

            [$mimeType, $binary] = $this->decodeInlineImage($validated['image'], $validated['mime_type'] ?? null);

            if ($binary === null) {
                return response()->json([
                    'success' => false,
                    'error' => __('ai::image_edit.messages.source_image_missing'),
                ], 422);
            }
        } elseif (! $request->user()) {
            abort(403);
        }

        $brandLogo = $request->boolean('use_brand_logo', true)
            ? CompanyContentProfile::logoReference()
            : null;

        $result = $mode === 'create'
            ? $this->geminiImageService->generateImage($validated['prompt'], $brandLogo)
            : $this->geminiImageService->editImage($binary, $mimeType, $validated['prompt'], $brandLogo);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        $response = [
            'success' => true,
            'mode' => $isPersisted ? 'persisted' : 'adhoc',
            'previewUrl' => 'data:'.$result['mime_type'].';base64,'.base64_encode($result['binary']),
        ];

        if ($isPersisted) {
            $extension = $this->extensionForMimeType($result['mime_type']);
            $newPath = sprintf(
                'ai-edits/%s/%s/%s.%s',
                $validated['target'],
                $validated['id'],
                (string) Str::uuid(),
                $extension,
            );

            Storage::disk($disk)->put($newPath, $result['binary']);

            $response['token'] = Crypt::encrypt([
                'path' => $newPath,
                'disk' => $disk,
                'target' => $validated['target'],
                'id' => $validated['id'],
                'field' => $validated['field'],
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);
        }

        return response()->json($response);
    }

    /**
     * Decode a raw base64 string or a `data:<mime>;base64,<data>` URI into
     * [mimeType, binary]. Returns a null binary when decoding fails.
     *
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

    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'action' => ['required', Rule::in(['replace', 'new_version'])],
        ]);

        try {
            $payload = Crypt::decrypt($validated['token']);
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'error' => __('ai::image_edit.messages.invalid_or_expired'),
            ], 422);
        }

        if (! is_array($payload) || ($payload['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json([
                'success' => false,
                'error' => __('ai::image_edit.messages.invalid_or_expired'),
            ], 422);
        }

        $targets = config('ai.editable_targets', []);
        $entry = $targets[$payload['target']] ?? null;

        if ($entry === null || ! in_array($payload['field'], $entry['fields'], true)) {
            return response()->json([
                'success' => false,
                'error' => __('ai::image_edit.messages.invalid_target'),
            ], 422);
        }

        if (! $request->user()?->can($entry['permission'])) {
            abort(403);
        }

        $disk = $payload['disk'];

        if (! Storage::disk($disk)->exists($payload['path'])) {
            return response()->json([
                'success' => false,
                'error' => __('ai::image_edit.messages.invalid_or_expired'),
            ], 422);
        }

        $model = $entry['model']::query()->findOrFail($payload['id']);
        $oldPath = $model->{$payload['field']};

        if ($validated['action'] === 'replace' && filled($oldPath) && $oldPath !== $payload['path']) {
            Storage::disk($disk)->delete($oldPath);
        }

        $model->{$payload['field']} = $payload['path'];
        $model->save();

        return response()->json([
            'success' => true,
            'url' => Storage::disk($disk)->url($payload['path']),
            'message' => __('ai::image_edit.messages.applied'),
        ]);
    }

    private function extensionForMimeType(?string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
    }
}
