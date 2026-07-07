@section('title', __('base::system.title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Settings', 'url' => route('admin.settings.index')],
            ['label' => __('base::system.title')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('base::system.title')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('base::system.page_description')"
    />
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="base::system.title"
        :formUrl="route('admin.system-configurations.store')"
        :description="__('base::system.form_description')"
        id="system-config-form"
    >
        <x-admin.settings-section
            icon="bi-layout-sidebar"
            :title="__('base::system.admin_branding.title')"
            :description="__('base::system.admin_branding.description')"
        >
            <div class="row g-5">
                <div class="col-md-6">
                    <x-admin.settings-image
                        :label="__('base::system.admin_branding.logo')"
                        name="logo"
                        :current="$settings->get('logo') ?: ''"
                        :imageUrl="\Modules\Base\Support\AdminBranding::logoUrl()"
                        dimensions="245 × 45 px"
                        :hint="__('base::system.admin_branding.logo_hint')"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.settings-image
                        :label="__('base::system.admin_branding.min_logo')"
                        name="min_logo"
                        :current="$settings->get('min_logo') ?: ''"
                        :imageUrl="\Modules\Base\Support\AdminBranding::minLogoUrl()"
                        dimensions="50 × 50 px"
                        :hint="__('base::system.admin_branding.min_logo_hint')"
                    />
                </div>
            </div>
        </x-admin.settings-section>

        <x-admin.settings-section
            icon="bi-building-gear"
            :title="__('base::system.company_details.title')"
            :description="__('base::system.company_details.description')"
        >
            <x-admin.settings-field
                :label="__('base::system.company_details.phone')"
                name="data[company_phone]"
                :value="$settings->get('company_phone')"
                placeholder="00905234***"
                icon="bi-phone"
                :hint="__('Include country code without + or spaces.')"
            />
            <x-admin.settings-field
                :label="__('base::system.company_details.email')"
                name="data[company_email]"
                type="email"
                :value="$settings->get('company_email')"
                placeholder="billing@example.com"
                icon="bi-envelope"
            />
            <x-admin.settings-field
                :label="__('base::system.company_details.address')"
                name="data[company_address]"
                :value="$settings->get('company_address')"
                placeholder="California, TX 70240"
                icon="bi-geo-alt"
            />
            <div class="row g-5">
                <div class="col-md-6">
                    <x-admin.settings-image
                        :label="__('base::system.company_details.sign_image')"
                        name="company_sign_image"
                        :current="$settings->get('company_sign_image', 'default.jpg')"
                        dimensions="200 × 80 px"
                        :hint="__('base::system.company_details.sign_image_hint')"
                    />
                </div>
            </div>
        </x-admin.settings-section>

        <x-admin.settings-section
            icon="bi-envelope-at"
            :title="__('base::system.admin_notifications.title')"
            :description="__('base::system.admin_notifications.description')"
        >
            <x-admin.settings-field
                :label="__('base::system.admin_notifications.email')"
                name="data[admin_email]"
                type="email"
                :value="$settings->get('admin_email')"
                :placeholder="env('ADMIN_EMAIL', 'admin@example.com')"
                icon="bi-envelope-check"
                :hint="__('base::system.admin_notifications.email_hint')"
            />
        </x-admin.settings-section>
    </x-admin.create-card>
</x-admin-layout>
