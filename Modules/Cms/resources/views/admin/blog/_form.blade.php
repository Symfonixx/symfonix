@php($blogData = $blog ?? null)

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

<div class="row g-6">
    <div class="col-xl-8">
        <x-admin.settings-section
            icon="bi-file-earmark-text"
            title='Content'
            description='Main blog content and SEO fields.'
        >
            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="category_id">
                        <i class="bi bi-folder text-primary me-1"></i>{{ __('Category') }}
                        <span class="text-danger">*</span>
                    </label>
                </div>
                <div class="col-lg-8">
                    <select id="category_id" name="category_id" class="form-select form-select-solid" required>
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $blogData?->category_id) === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="title">
                        <i class="bi bi-type text-primary me-1"></i>{{ __('Title') }}
                        <span class="text-danger">*</span>
                    </label>
                </div>
                <div class="col-lg-8">
                    <input id="title" type="text" class="form-control form-control-solid" name="title"
                           value="{{ old('title', $blogData?->title) }}" placeholder="{{ __('Title') }}" required autofocus/>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="gslug">
                        <i class="bi bi-link-45deg text-primary me-1"></i>{{ __('Url') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="settings-field-hint">{{ __('English slug used in the page URL.') }}</div>
                </div>
                <div class="col-lg-8">
                    <input type="text" id="gslug" name="gslug" class="form-control form-control-solid"
                           value="{{ old('slug', $blogData?->slug) }}" placeholder="my-blog-post"/>
                    <input type="hidden" name="slug" value="{{ old('slug', $blogData?->slug) }}" id="slug">
                    <div class="my-2 fs-7" id="link">{{ old('slug', $blogData?->slug) }}</div>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="description">
                        <i class="bi bi-card-text text-primary me-1"></i>{{ __('Short Description') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="settings-field-hint">{{ __('150–160 characters ideal for SEO.') }}</div>
                </div>
                <div class="col-lg-8">
                    <input id="description" type="text" class="form-control form-control-solid seo-counter-input"
                           name="description" maxlength="160" data-counter-target="blog-desc"
                           value="{{ old('description', $blogData?->description) }}"
                           placeholder="{{ __('Short Description') }}..."/>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted fs-8" id="blog-desc-hint"></span>
                        <span class="seo-char-counter fs-8" id="blog-desc-counter" data-max="160">0 / 160</span>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <div class="col-lg-4">
                    <label class="settings-field-label" for="kt_tagify_1">
                        <i class="bi bi-tags text-primary me-1"></i>{{ __('Keywords') }}
                        <span class="text-danger">*</span>
                    </label>
                </div>
                <div class="col-lg-8">
                    <input class="form-control form-control-solid" name="keywords" id="kt_tagify_1"
                           value="{{ old('keywords', $blogData?->keywords ?? 'Blog,') }}"/>
                </div>
            </div>

            @include('cms::admin.shared._editor', [
                'value' => old('content', $blogData?->content),
            ])
        </x-admin.settings-section>
    </div>

    <div class="col-xl-4">
        <div class="seo-preview-panel">
            @include('cms::admin.shared._image-aside', [
                'currentImage' => $blogData?->image_link ?? null,
                'dimensions' => '500 × 500 px',
                'required' => !$blogData,
            ])

            @include('cms::admin.shared._publish-aside', ['item' => $blogData])

            <div class="card mb-6">
                <div class="card-body">
                    <x-admin.auto-translate-checkbox :default="! $blogData" class="mb-0"/>
                </div>
            </div>

            @include('cms::admin.shared._seo-preview-aside', [
                'formId' => 'blog-form',
                'defaultImage' => $blogData?->image_link ?? asset('images/default.jpg'),
            ])
        </div>
    </div>
</div>

@if (! $blogData)
    @include('crm::admin.shared._send_as_marketing')
@endif

@include('cms::admin.shared._form-scripts')

@push('scripts')
<script>
    (function () {
        const descInput = document.getElementById('description');
        const counterEl = document.getElementById('blog-desc-counter');
        const hintEl = document.getElementById('blog-desc-hint');

        function updateDescCounter() {
            if (!descInput) return;
            const len = descInput.value.length;
            if (counterEl) {
                counterEl.textContent = len + ' / 160';
                counterEl.classList.toggle('text-danger', len > 144);
                counterEl.classList.toggle('text-warning', len > 120 && len <= 144);
                counterEl.classList.toggle('text-success', len <= 120);
            }
            if (hintEl) {
                hintEl.textContent = len > 160
                    ? @json(__('May be truncated in search results'))
                    : (len >= 120 ? @json(__('Good length')) : @json(__('Consider adding more detail')));
            }
        }

        descInput?.addEventListener('input', updateDescCounter);
        updateDescCounter();
    })();
</script>
@endpush
