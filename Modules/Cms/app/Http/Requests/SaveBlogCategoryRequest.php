<?php

namespace Modules\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class SaveBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        $slug = ['required', 'string', 'min:2', 'max:255', 'alpha_dash'];

        if ($this->isMethod('POST')) {
            $slug[] = Rule::unique('blog_categories', 'slug');
        }

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => $slug,
            'auto_translate' => ['sometimes', 'boolean'],
        ];
    }
}
