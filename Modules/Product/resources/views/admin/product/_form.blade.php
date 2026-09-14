@php($productData = $product ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('Please fix the following errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $productData?->name) }}" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="product_category_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.category') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="product_category_id" class="form-select form-select-solid @error('product_category_id') is-invalid @enderror"
                data-control="select2" name="product_category_id" required>
            <option value="">{{ __('product::product.fields.select_category') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('product_category_id', $productData?->product_category_id) === $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('product_category_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="sku" class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.sku') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="sku" type="text" class="form-control form-control-solid @error('sku') is-invalid @enderror"
               name="sku" value="{{ old('sku', $productData?->sku) }}"/>
        @error('sku')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="price" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.price') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-4">
            <div class="col-md-6">
                <input id="price" type="number" step="0.01" min="0"
                       class="form-control form-control-solid @error('price') is-invalid @enderror"
                       name="price" value="{{ old('price', $productData?->price) }}" required/>
                @error('price')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-3">
                <select id="currency" class="form-select form-select-solid @error('currency') is-invalid @enderror" name="currency">
                    @foreach(['USD', 'EUR', 'GBP', 'TRY'] as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', $productData?->currency ?? 'USD') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="billing_type" class="form-select form-select-solid @error('billing_type') is-invalid @enderror" name="billing_type" required>
                    @foreach(['one_time', 'monthly', 'quarterly', 'yearly'] as $billingType)
                        <option value="{{ $billingType }}" @selected(old('billing_type', $productData?->billing_type ?? 'one_time') === $billingType)>
                            {{ __('product::product.billing.'.$billingType) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-4 mt-2">
            <div class="col-md-12">
                <label for="product_tax_rate_id" class="form-label">{{ __('tax::tax_rate.fields.name') }}</label>
                <x-tax::tax-rate-select
                    name="tax_rate_id"
                    id="product_tax_rate_id"
                    :selected="$productData?->tax_rate_id"
                    :tax-rates="$taxRates ?? []"
                />
                @error('tax_rate_id')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('product::product.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" class="form-select form-select-solid @error('status') is-invalid @enderror" name="status" required>
            <option value="active" @selected(old('status', $productData?->status ?? 'active') === 'active')>{{ __('product::product.status.active') }}</option>
            <option value="archived" @selected(old('status', $productData?->status) === 'archived')>{{ __('product::product.status.archived') }}</option>
        </select>
        @error('status')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.featured') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                   id="is_featured" @checked(old('is_featured', $productData?->is_featured))/>
            <label class="form-check-label" for="is_featured">{{ __('product::product.fields.featured_help') }}</label>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.is_published') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_published" value="1"
                   id="is_published" @checked(old('is_published', $productData?->is_published))/>
            <label class="form-check-label" for="is_published">{{ __('product::product.fields.is_published_help') }}</label>
        </div>
    </div>
</div>

<div class="card mb-8">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-image text-primary me-2"></i>{{ __('product::product.sections.media') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="row mb-0">
            <div class="col-xl-3">
                <label class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.main_image') }}</label>
                <div class="text-muted fs-7">{{ __('product::product.fields.main_image_help') }}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="image-input image-input-outline" data-kt-image-input="true"
                     style="background-image: url('{{ $productData?->main_image_link ?? asset('images/default.jpg') }}')">
                    <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                         style="background-size: cover; background-image: url({{ $productData?->main_image_link ?? asset('images/default.jpg') }})"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                           data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change image') }}">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="main_image" accept=".png,.jpg,.jpeg,.webp"/>
                    </label>
                </div>
                @error('main_image')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-8">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-file-earmark-richtext text-primary me-2"></i>{{ __('product::product.sections.content') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="row mb-8">
            <div class="col-xl-3">
                <label for="short_description" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.short_description') }}</label>
                <div class="text-muted fs-7">{{ __('product::product.fields.short_description_help') }}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea id="short_description" class="form-control form-control-solid seo-counter-input @error('short_description') is-invalid @enderror"
                          name="short_description" rows="3" maxlength="500" data-counter-target="product-short-desc">{{ old('short_description', $productData?->short_description) }}</textarea>
                <div class="d-flex justify-content-end mt-2">
                    <span class="seo-char-counter fs-8" id="product-short-desc-counter" data-max="500">0 / 500</span>
                </div>
                @error('short_description')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="row mb-0">
            <div class="col-xl-3">
                <label for="product-description-editor" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.description') }}</label>
                <div class="text-muted fs-7">{{ __('product::product.fields.description_help') }}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea
                    id="product-description-editor"
                    name="description"
                    class="form-control form-control-solid cms-tinymce-editor @error('description') is-invalid @enderror"
                    rows="16"
                >{!! old('description', $productData?->getTranslation('description', app()->getLocale(), false)) !!}</textarea>
                @error('description')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-0">
    <div class="card-header border-0 pt-6 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#product-seo-collapse" aria-expanded="false">
        <h3 class="card-title fw-bold fs-5 mb-0">
            <i class="bi bi-search text-primary me-2"></i>{{ __('product::product.sections.seo') }}
            <i class="bi bi-chevron-down ms-2 fs-7"></i>
        </h3>
    </div>
    <div id="product-seo-collapse" class="collapse">
        <div class="card-body pt-0">
            <div class="row mb-8">
                <div class="col-xl-3">
                    <label for="seo_title" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.seo_title') }}</label>
                    <div class="text-muted fs-7">{{ __('product::product.fields.seo_title_help') }}</div>
                </div>
                <div class="col-xl-9 fv-row">
                    <input id="seo_title" type="text" class="form-control form-control-solid seo-counter-input @error('seo_title') is-invalid @enderror"
                           name="seo_title" maxlength="70" data-counter-target="product-seo-title"
                           value="{{ old('seo_title', $productData?->seo_title) }}" placeholder="{{ $productData?->name }}"/>
                    <div class="d-flex justify-content-end mt-2">
                        <span class="seo-char-counter fs-8" id="product-seo-title-counter" data-max="70">0 / 70</span>
                    </div>
                    @error('seo_title')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <label for="seo_description" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.seo_description') }}</label>
                    <div class="text-muted fs-7">{{ __('product::product.fields.seo_description_help') }}</div>
                </div>
                <div class="col-xl-9 fv-row">
                    <textarea id="seo_description" class="form-control form-control-solid seo-counter-input @error('seo_description') is-invalid @enderror"
                              name="seo_description" rows="3" maxlength="320" data-counter-target="product-seo-desc">{{ old('seo_description', $productData?->seo_description) }}</textarea>
                    <div class="d-flex justify-content-end mt-2">
                        <span class="seo-char-counter fs-8" id="product-seo-desc-counter" data-max="160">0 / 160</span>
                    </div>
                    @error('seo_description')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <label for="seo_keywords" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::product.fields.seo_keywords') }}</label>
                </div>
                <div class="col-xl-9 fv-row">
                    <input id="seo_keywords" type="text" class="form-control form-control-solid @error('seo_keywords') is-invalid @enderror"
                           name="seo_keywords" value="{{ old('seo_keywords', $productData?->seo_keywords) }}"
                           placeholder="{{ __('product::product.fields.seo_keywords_placeholder') }}"/>
                    @error('seo_keywords')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-xl-3">
                    <label class="fs-6 fw-bold mt-2 mb-3">{{ __('product::product.fields.seo_meta_img') }}</label>
                    <div class="text-muted fs-7">{{ __('product::product.fields.seo_meta_img_help') }}</div>
                </div>
                <div class="col-xl-9 fv-row">
                    <div class="image-input image-input-outline" data-kt-image-input="true"
                         style="background-image: url('{{ $productData?->seo_meta_img ? asset('storage/'.$productData->seo_meta_img) : asset('images/default.jpg') }}')">
                        <div class="image-input-wrapper w-200px h-120px bgi-position-center"
                             style="background-size: cover; background-image: url({{ $productData?->seo_meta_img ? asset('storage/'.$productData->seo_meta_img) : asset('images/default.jpg') }})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change image') }}">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="seo_meta_img" accept=".png,.jpg,.jpeg,.webp"/>
                        </label>
                    </div>
                    @error('seo_meta_img')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<x-admin.auto-translate-checkbox :default="! $productData"/>

@if (! $productData)
    @include('crm::admin.shared._send_as_marketing')
@endif

@push('scripts')
<script>
    (function () {
        function bindCounter(inputId, counterId, idealMax) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            if (!input || !counter) return;

            const max = parseInt(counter.dataset.max || idealMax, 10);

            function update() {
                const len = input.value.length;
                counter.textContent = len + ' / ' + max;
                counter.classList.toggle('text-danger', len > idealMax);
                counter.classList.toggle('text-warning', len > idealMax * 0.75 && len <= idealMax);
                counter.classList.toggle('text-success', len <= idealMax * 0.75);
            }

            input.addEventListener('input', update);
            update();
        }

        bindCounter('short_description', 'product-short-desc-counter', 500);
        bindCounter('seo_title', 'product-seo-title-counter', 60);
        bindCounter('seo_description', 'product-seo-desc-counter', 160);
    })();
</script>
@endpush
