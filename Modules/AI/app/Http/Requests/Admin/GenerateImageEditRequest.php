<?php

namespace Modules\AI\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AI\Support\ImageEditTargets;

class GenerateImageEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        if (! $this->filled('target')) {
            $permissions = ImageEditTargets::anyPermissions();

            return $permissions === [] || $user->canany($permissions);
        }

        $entry = ImageEditTargets::get((string) $this->input('target'));

        if ($entry === null) {
            return true;
        }

        return $user->can($entry['permission']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'prompt' => ['required', 'string', 'max:500'],
            'use_brand_logo' => ['nullable', 'boolean'],
            'mode' => ['nullable', 'string', Rule::in(['create', 'edit'])],
        ];

        if ($this->filled('target')) {
            $entry = ImageEditTargets::get((string) $this->input('target'));
            $rules['target'] = ['required', 'string', Rule::in(ImageEditTargets::names())];
            $rules['id'] = ['required', 'integer', 'min:1'];
            $rules['field'] = ['required', 'string', Rule::in($entry['fields'] ?? [])];
        } elseif ($this->mode() === 'edit') {
            $rules['image'] = ['required', 'string', 'max:8000000'];
            $rules['mime_type'] = ['nullable', 'string', 'max:80'];
        }

        return $rules;
    }

    public function mode(): string
    {
        return $this->input('mode') === 'create' ? 'create' : 'edit';
    }

    public function prompt(): string
    {
        return (string) $this->validated('prompt');
    }

    public function usesBrandLogo(): bool
    {
        return $this->boolean('use_brand_logo', true);
    }

    public function isPersisted(): bool
    {
        return $this->filled('target');
    }

    public function target(): ?string
    {
        $target = $this->validated('target') ?? null;

        return is_string($target) ? $target : null;
    }

    public function recordId(): ?int
    {
        $id = $this->validated('id') ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    public function field(): ?string
    {
        $field = $this->validated('field') ?? null;

        return is_string($field) ? $field : null;
    }

    public function inlineImage(): ?string
    {
        $image = $this->validated('image') ?? null;

        return is_string($image) ? $image : null;
    }

    public function mimeType(): ?string
    {
        $mime = $this->validated('mime_type') ?? null;

        return is_string($mime) ? $mime : null;
    }
}
