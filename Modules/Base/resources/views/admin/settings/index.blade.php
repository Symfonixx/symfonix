@section('title', __('Website Configurations'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Settings', 'url' => route('admin.settings.index')],
            ['label' => 'Website Configurations'],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('Website Configurations')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('Manage your site branding, contact details, scripts, and social links.')"
    />
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Website Configurations"
        :formUrl="route('admin.settings.store')"
        :description="__('Configure global website settings used across the frontend.')"
        id="settings-form"
    >
        {{-- Tab navigation --}}
        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x settings-tabs mb-8 fs-6 fw-semibold" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-branding">
                    <i class="bi bi-image me-2"></i>{{ __('Branding') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-contact">
                    <i class="bi bi-telephone me-2"></i>{{ __('Contact') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-scripts">
                    <i class="bi bi-code-slash me-2"></i>{{ __('Scripts') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-social">
                    <i class="bi bi-share me-2"></i>{{ __('Social Media') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Branding --}}
            <div class="tab-pane fade show active" id="tab-branding">
                <x-admin.settings-section
                    icon="bi-palette"
                    :title="__('Brand Assets')"
                    :description="__('Upload your site logo and default social sharing image.')"
                >
                    <div class="row g-5">
                        <div class="col-md-6">
                            <x-admin.settings-image
                                :label="__('Site Logo')"
                                name="site_logo"
                                :current="$settings->get('site_logo', 'default.jpg')"
                                dimensions="185 × 35 px"
                                :hint="__('Displayed in the site header and admin panel.')"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-admin.settings-image
                                :label="__('Meta Image')"
                                name="meta_img"
                                :current="$settings->get('meta_img', 'default.jpg')"
                                dimensions="600 × 600 px"
                                :hint="__('Default Open Graph image when pages do not specify one.')"
                            />
                        </div>
                    </div>
                </x-admin.settings-section>
            </div>

            {{-- Contact --}}
            <div class="tab-pane fade" id="tab-contact">
                <x-admin.settings-section
                    icon="bi-building"
                    :title="__('Contact Information')"
                    :description="__('Public contact details shown on the website footer and contact page.')"
                >
                    <x-admin.settings-field
                        :label="__('Website Phone')"
                        name="data[phone]"
                        :value="$settings->get('phone')"
                        placeholder="00905234***"
                        icon="bi-phone"
                        :hint="__('Include country code without + or spaces.')"
                    />
                    <x-admin.settings-field
                        :label="__('Website Email')"
                        name="data[email]"
                        type="email"
                        :value="$settings->get('email')"
                        placeholder="support@example.com"
                        icon="bi-envelope"
                    />
                    <x-admin.settings-field
                        :label="__('Website Address')"
                        name="data[address]"
                        :value="$settings->get('address')"
                        placeholder="California, TX 70240"
                        icon="bi-geo-alt"
                    />
                </x-admin.settings-section>
            </div>

            {{-- Scripts --}}
            <div class="tab-pane fade" id="tab-scripts">
                <x-admin.settings-section
                    icon="bi-braces"
                    :title="__('Custom Scripts')"
                    :description="__('Inject analytics, chat widgets, or tracking codes. Use with caution.')"
                >
                    <div class="alert alert-warning d-flex align-items-center mb-6">
                        <i class="bi bi-exclamation-triangle fs-3 me-3"></i>
                        <span class="fs-7">{{ __('Only paste code from trusted sources. Invalid scripts can break your site.') }}</span>
                    </div>
                    <x-admin.settings-field
                        :label="__('Header Scripts')"
                        name="data[header_scripts]"
                        type="textarea"
                        :rows="8"
                        :value="$settings->get('header_scripts')"
                        icon="bi-code-slash"
                        :hint="__('Inserted inside the <head> tag on every page.')"
                    />
                    <x-admin.settings-field
                        :label="__('Body Scripts')"
                        name="data[body_scripts]"
                        type="textarea"
                        :rows="8"
                        :value="$settings->get('body_scripts')"
                        icon="bi-code-slash"
                        :hint="__('Inserted before the closing </body> tag.')"
                    />
                </x-admin.settings-section>
            </div>

            {{-- Social --}}
            <div class="tab-pane fade" id="tab-social">
                <x-admin.settings-section
                    icon="bi-share"
                    :title="__('Social Media Links')"
                    :description="__('Links to your social profiles. Leave blank to hide from the website.')"
                >
                    <div class="row g-4">
                        @php
                            $socialFields = [
                                ['key' => 'whatsapp', 'label' => 'Whatsapp', 'icon' => 'bi-whatsapp', 'class' => 'text-success', 'placeholder' => '90564xxxxxxx', 'type' => 'text'],
                                ['key' => 'facebook', 'label' => 'Facebook', 'icon' => 'bi-facebook', 'class' => 'text-primary', 'placeholder' => 'https://www.facebook.com/xxxx'],
                                ['key' => 'instagram', 'label' => 'Instagram', 'icon' => 'bi-instagram', 'class' => 'text-danger', 'placeholder' => 'https://www.instagram.com/xxxx'],
                                ['key' => 'twitter', 'label' => 'Twitter', 'icon' => 'bi-twitter-x', 'class' => 'text-dark', 'placeholder' => 'https://www.twitter.com/xxxx'],
                                ['key' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'bi-linkedin', 'class' => 'text-primary', 'placeholder' => 'https://www.linkedin.com/xxxx'],
                                ['key' => 'github', 'label' => 'Github', 'icon' => 'bi-github', 'class' => 'text-dark', 'placeholder' => 'https://www.github.com/xxxx'],
                            ];
                        @endphp
                        @foreach($socialFields as $social)
                            <div class="col-md-6">
                                <div class="settings-social-field">
                                    <label class="settings-social-label" for="social-{{ $social['key'] }}">
                                        <span class="settings-social-icon {{ $social['class'] }}">
                                            <i class="bi {{ $social['icon'] }}"></i>
                                        </span>
                                        {{ __($social['label']) }}
                                    </label>
                                    <input
                                        type="text"
                                        id="social-{{ $social['key'] }}"
                                        name="data[{{ $social['key'] }}]"
                                        class="form-control form-control-solid"
                                        value="{{ $settings->get($social['key']) }}"
                                        placeholder="{{ $social['placeholder'] }}"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-admin.settings-section>
            </div>
        </div>
    </x-admin.create-card>

    @push('scripts')
    <script>
        document.querySelectorAll('#settings-form [data-bs-toggle="tab"]').forEach(function (tab) {
            tab.addEventListener('shown.bs.tab', function (e) {
                localStorage.setItem('settings_active_tab', e.target.getAttribute('href'));
            });
        });
        const savedTab = localStorage.getItem('settings_active_tab');
        if (savedTab) {
            const tabEl = document.querySelector('#settings-form [href="' + savedTab + '"]');
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }
    </script>
    @endpush
</x-admin-layout>
