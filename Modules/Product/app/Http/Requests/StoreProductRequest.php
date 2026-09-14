<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Services\Marketing\ContentMarketingEmailSender;
use Modules\Product\Enums\ProductBillingType;
use Modules\Product\Enums\ProductStatus;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product.catalog.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            'seo_meta_img' => ['nullable', 'image', 'max:5120'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'tax_rate_id' => ['nullable', 'integer', 'exists:tax_rates,id'],
            'billing_type' => ['required', Rule::in(ProductBillingType::values())],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'auto_translate' => ['nullable', 'boolean'],
            ...app(ContentMarketingEmailSender::class)->validationRules(),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            'auto_translate' => $this->boolean('auto_translate'),
            'send_as_marketing' => $this->boolean('send_as_marketing'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty() || ! $this->boolean('send_as_marketing')) {
                return;
            }

            if (! $this->user()?->can('marketing.email.send')) {
                $validator->errors()->add('send_as_marketing', __('crm::marketing.validation.permission_required'));
            }
        });
    }
}
