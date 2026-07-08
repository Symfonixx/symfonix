@section('title', __('Seo Configurations'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Settings', 'url' => route('admin.settings.index')],
            ['label' => 'Seo Configurations'],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('Seo Configurations')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('Optimize how your website appears in search engines and social shares.')"
    />
@endsection

<x-admin-layout>
    <div class="row g-6">
        {{-- Form column --}}
        <div class="col-xl-7">
            <x-admin.create-card
                title="Seo Configurations"
                :formUrl="route('admin.seo.store')"
                :description="__('Set default meta tags used when pages do not define their own SEO.')"
                id="seo-form"
            >
                <x-admin.settings-section
                    icon="bi-badge-ad"
                    :title="__('Brand & Identity')"
                    :description="__('Core naming used in browser tabs, search results, and social cards.')"
                >
                    <x-admin.settings-field
                        :label="__('Website Name')"
                        name="data[website_name]"
                        :value="$seo->get('website_name')"
                        icon="bi-globe2"
                        :hint="__('Short brand name, e.g. Symfonix')"
                        maxlength="60"
                        :counter="true"
                        counterTarget="seo-name"
                    />
                    <x-admin.settings-field
                        :label="__('Website Main Title')"
                        name="data[main_title]"
                        :value="$seo->get('main_title')"
                        icon="bi-type"
                        :hint="__('Homepage title shown in Google results (50–60 chars ideal)')"
                        maxlength="70"
                        :counter="true"
                        counterTarget="seo-title"
                    />
                </x-admin.settings-section>

                <x-admin.settings-section
                    icon="bi-search"
                    :title="__('Meta Tags')"
                    :description="__('Default description and keywords for search engine indexing.')"
                >
                    <x-admin.settings-field
                        :label="__('Website Description')"
                        name="data[website_desc]"
                        type="textarea"
                        :rows="3"
                        :value="$seo->get('website_desc')"
                        icon="bi-card-text"
                        :hint="__('Summarize your site in 150–160 characters for best results.')"
                        maxlength="160"
                        :counter="true"
                        counterTarget="seo-desc"
                    />
                    <x-admin.settings-field
                        :label="__('Website Keywords')"
                        name="data[website_keywords]"
                        :value="$seo->get('website_keywords')"
                        icon="bi-tags"
                        :hint="__('Comma-separated keywords, e.g. web development, mobile apps')"
                        maxlength="255"
                    />
                </x-admin.settings-section>

                <x-admin.settings-section
                    icon="bi-info-circle"
                    :title="__('About')"
                    :description="__('Brief company description used in structured data and about sections.')"
                >
                    <x-admin.settings-field
                        :label="__('About Us')"
                        name="data[about_us]"
                        type="textarea"
                        :rows="4"
                        :value="$seo->get('about_us')"
                        icon="bi-building"
                        :hint="__('A concise paragraph about your company and services.')"
                        maxlength="500"
                        :counter="true"
                        counterTarget="seo-about"
                    />
                </x-admin.settings-section>

                <x-admin.settings-section
                    icon="bi-translate"
                    :title="__('Translation')"
                    :description="__('Choose whether to update all language versions when saving.')"
                >
                    <x-admin.auto-translate-checkbox :default="false" class="mb-0"/>
                </x-admin.settings-section>
            </x-admin.create-card>
        </div>

        {{-- Preview column --}}
        <div class="col-xl-5">
            <div class="seo-preview-panel">
                <div class="card mb-6">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title fw-bold fs-5">
                            <i class="bi bi-google text-primary me-2"></i>{{ __('Search Preview') }}
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="seo-google-preview">
                            <div class="seo-google-url" id="preview-url">{{ config('app.url') }}</div>
                            <div class="seo-google-title" id="preview-title">{{ $seo->get('main_title') ?: $seo->get('website_name') ?: __('Your Page Title') }}</div>
                            <div class="seo-google-desc" id="preview-desc">{{ $seo->get('website_desc') ?: __('Your meta description will appear here. Write a compelling summary to improve click-through rates.') }}</div>
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
                                <img src="{{ asset('storage/' . $metaImage) }}" alt="" id="preview-social-img"
                                     onerror="this.src='{{ asset('images/admin_logo.png') }}'">
                            </div>
                            <div class="seo-social-body">
                                <div class="seo-social-site" id="preview-social-site">{{ config('app.url') }}</div>
                                <div class="seo-social-title" id="preview-social-title">{{ $seo->get('main_title') ?: $seo->get('website_name') ?: __('Your Page Title') }}</div>
                                <div class="seo-social-desc" id="preview-social-desc">{{ Str::limit($seo->get('website_desc'), 100) ?: __('Meta description preview') }}</div>
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
                            <li>{{ __('Ensure your meta image is at least 600×600 px.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const form = document.getElementById('seo-form');
            if (!form) return;

            const titleInput = form.querySelector('[name="data[main_title]"]');
            const nameInput = form.querySelector('[name="data[website_name]"]');
            const descInput = form.querySelector('[name="data[website_desc]"]');

            const previewTitle = document.getElementById('preview-title');
            const previewDesc = document.getElementById('preview-desc');
            const previewSocialTitle = document.getElementById('preview-social-title');
            const previewSocialDesc = document.getElementById('preview-social-desc');
            const previewSocialSite = document.getElementById('preview-social-site');

            function updatePreview() {
                const title = titleInput?.value.trim() || nameInput?.value.trim() || @json(__('Your Page Title'));
                const desc = descInput?.value.trim() || @json(__('Your meta description will appear here. Write a compelling summary to improve click-through rates.'));

                if (previewTitle) previewTitle.textContent = title;
                if (previewDesc) previewDesc.textContent = desc;
                if (previewSocialTitle) previewSocialTitle.textContent = title;
                if (previewSocialDesc) previewSocialDesc.textContent = desc.length > 100 ? desc.substring(0, 100) + '…' : desc;
                if (previewSocialSite) previewSocialSite.textContent = nameInput?.value.trim() || @json(parse_url(config('app.url'), PHP_URL_HOST));
            }

            [titleInput, nameInput, descInput].forEach(function (el) {
                el?.addEventListener('input', updatePreview);
            });

            form.querySelectorAll('.seo-counter-input').forEach(function (input) {
                const target = input.dataset.counterTarget;
                const counterEl = document.getElementById(target + '-counter');
                const hintEl = document.getElementById(target + '-hint');
                const max = parseInt(counterEl?.dataset.max || input.maxLength || 0, 10);

                function updateCounter() {
                    const len = input.value.length;
                    if (counterEl) {
                        counterEl.textContent = len + ' / ' + max;
                        counterEl.classList.toggle('text-danger', len > max * 0.9);
                        counterEl.classList.toggle('text-warning', len > max * 0.75 && len <= max * 0.9);
                        counterEl.classList.toggle('text-success', len <= max * 0.75);
                    }
                    if (hintEl && target === 'seo-title') {
                        hintEl.textContent = len > 60 ? @json(__('May be truncated in search results')) : @json(__('Good length for search results'));
                    }
                    if (hintEl && target === 'seo-desc') {
                        hintEl.textContent = len > 160 ? @json(__('May be truncated in search results')) : (len >= 120 ? @json(__('Good length')) : @json(__('Consider adding more detail')));
                    }
                }

                input.addEventListener('input', updateCounter);
                updateCounter();
            });

            updatePreview();
        })();
    </script>
    @endpush
</x-admin-layout>
