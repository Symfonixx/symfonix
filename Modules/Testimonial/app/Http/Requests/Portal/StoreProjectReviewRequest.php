<?php

namespace Modules\Testimonial\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()?->isCustomer()
            && $project
            && $project->canBeReviewedBy($this->user());
    }

    public function rules(): array
    {
        return [
            'quote' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'quote.required' => __('user::portal.projects.review_quote_required'),
            'quote.min' => __('user::portal.projects.review_quote_min'),
            'quote.max' => __('user::portal.projects.review_quote_max'),
        ];
    }
}
