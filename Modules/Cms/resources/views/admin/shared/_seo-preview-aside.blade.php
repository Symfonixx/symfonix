@props([
    'formId' => 'cms-form',
    'titleField' => 'title',
    'descField' => 'description',
    'imagePreviewId' => 'preview-social-img',
    'defaultImage' => null,
])

<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-google text-primary me-2"></i>{{ __('Search Preview') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="seo-google-preview">
            <div class="seo-google-url" id="preview-url">{{ config('app.url') }}</div>
            <div class="seo-google-title" id="preview-title">{{ __('Your Page Title') }}</div>
            <div class="seo-google-desc" id="preview-desc">{{ __('Your meta description will appear here. Write a compelling summary to improve click-through rates.') }}</div>
        </div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-share text-info me-2"></i>{{ __('Social Share Preview') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="seo-social-preview">
            <div class="seo-social-image">
                <img src="{{ $defaultImage ?? asset('images/default.jpg') }}" alt="" id="{{ $imagePreviewId }}"
                     onerror="this.src='{{ asset('images/default.jpg') }}'">
            </div>
            <div class="seo-social-body">
                <div class="seo-social-site" id="preview-social-site">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
                <div class="seo-social-title" id="preview-social-title">{{ __('Your Page Title') }}</div>
                <div class="seo-social-desc" id="preview-social-desc">{{ __('Meta description preview') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="fw-bold fs-6 mb-4"><i class="bi bi-lightbulb text-warning me-2"></i>{{ __('SEO Tips') }}</h4>
        <ul class="seo-tips-list mb-0">
            <li>{{ __('Keep titles under 60 characters to avoid truncation.') }}</li>
            <li>{{ __('Write unique descriptions for each major page.') }}</li>
            <li>{{ __('Use keywords naturally — avoid keyword stuffing.') }}</li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const form = document.getElementById(@json($formId));
        if (!form) return;

        const titleInput = form.querySelector('[name="{{ $titleField }}"]');
        const descInput = form.querySelector('[name="{{ $descField }}"]');
        const slugInput = form.querySelector('#slug');

        const previewTitle = document.getElementById('preview-title');
        const previewDesc = document.getElementById('preview-desc');
        const previewUrl = document.getElementById('preview-url');
        const previewSocialTitle = document.getElementById('preview-social-title');
        const previewSocialDesc = document.getElementById('preview-social-desc');
        const previewSocialImg = document.getElementById(@json($imagePreviewId));

        function updatePreview() {
            const title = titleInput?.value.trim() || @json(__('Your Page Title'));
            const desc = descInput?.value.trim() || @json(__('Your meta description will appear here. Write a compelling summary to improve click-through rates.'));
            const slug = slugInput?.value.trim();

            if (previewTitle) previewTitle.textContent = title;
            if (previewDesc) previewDesc.textContent = desc;
            if (previewSocialTitle) previewSocialTitle.textContent = title;
            if (previewSocialDesc) previewSocialDesc.textContent = desc.length > 100 ? desc.substring(0, 100) + '…' : desc;
            if (previewUrl && slug) previewUrl.textContent = @json(config('app.url')) + '/' + slug;
        }

        [titleInput, descInput, slugInput].forEach(function (el) {
            el?.addEventListener('input', updatePreview);
        });

        const imageInput = form.querySelector('input[name="img"]');
        imageInput?.addEventListener('change', function () {
            const file = this.files?.[0];
            if (file && previewSocialImg) {
                previewSocialImg.src = URL.createObjectURL(file);
            }
        });

        updatePreview();
    })();
</script>
@endpush
