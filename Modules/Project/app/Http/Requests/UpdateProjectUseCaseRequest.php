<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectUseCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('project.use_cases.edit') ?? false;
    }

    public function rules(): array
    {
        $useCaseId = $this->route('project_use_case')?->id;

        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('project_use_cases', 'slug')->ignore($useCaseId)],
            'client_name' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'challenge' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'results' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'technologies' => ['nullable', 'string'],
            'category_tag' => ['nullable', 'string', 'max:100'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'completed_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'featured' => ['nullable', 'boolean'],
            'publish' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'auto_translate' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'publish' => $this->boolean('publish'),
            'auto_translate' => $this->boolean('auto_translate'),
        ]);
    }
}
