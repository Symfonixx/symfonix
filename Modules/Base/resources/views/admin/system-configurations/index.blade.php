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
    @php
        $selectedCurrency = old('data.default_currency', $defaultCurrency);
        $backupEnabled = old('data.auto_backup_enabled', $settings->get('auto_backup_enabled', '0')) == '1';
        $backupLastRun = $settings->get('auto_backup_last_run');
        $fingerprintEnabled = old('data.fingerprint_enabled', $settings->get('fingerprint_enabled', '0')) == '1';
        $fingerprintLastSync = $settings->get('fingerprint_last_sync_at');
        $currencyNames = [
            'USD' => __('base::system.currency.names.USD'),
            'EUR' => __('base::system.currency.names.EUR'),
            'GBP' => __('base::system.currency.names.GBP'),
            'TRY' => __('base::system.currency.names.TRY'),
        ];
    @endphp

    <x-admin.create-card
        title="base::system.title"
        :formUrl="route('admin.system-configurations.store')"
        :description="__('base::system.form_description')"
        id="system-config-form"
    >
        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x settings-tabs mb-8 fs-6 fw-semibold flex-nowrap overflow-auto" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-nowrap" data-bs-toggle="tab" href="#tab-sys-branding">
                    <i class="bi bi-layout-sidebar me-2"></i>{{ __('base::system.tabs.branding') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-sys-company">
                    <i class="bi bi-building-gear me-2"></i>{{ __('base::system.tabs.company') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-sys-finance">
                    <i class="bi bi-currency-exchange me-2"></i>{{ __('base::system.tabs.finance') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-sys-notifications">
                    <i class="bi bi-envelope-at me-2"></i>{{ __('base::system.tabs.notifications') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-sys-fingerprint">
                    <i class="bi bi-fingerprint me-2"></i>{{ __('base::system.tabs.fingerprint') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-sys-backup">
                    <i class="bi bi-database-down me-2"></i>{{ __('base::system.tabs.backup') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Branding --}}
            <div class="tab-pane fade show active" id="tab-sys-branding">
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
            </div>

            {{-- Company --}}
            <div class="tab-pane fade" id="tab-sys-company">
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
            </div>

            {{-- Finance --}}
            <div class="tab-pane fade" id="tab-sys-finance">
                <div class="sys-status-grid mb-8">
                    <div class="sys-status-card {{ $hasFixerKey ? 'is-ok' : 'is-warn' }}">
                        <div class="sys-status-icon">
                            <i class="bi {{ $hasFixerKey ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.api_configs.status_title') }}</div>
                            <div class="sys-status-value">
                                {{ $hasFixerKey ? __('base::system.api_configs.key_configured') : __('base::system.api_configs.key_missing') }}
                            </div>
                        </div>
                    </div>
                    <div class="sys-status-card {{ $latestRateFetchedAt ? 'is-ok' : 'is-muted' }}">
                        <div class="sys-status-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.api_configs.rates_updated') }}</div>
                            <div class="sys-status-value">
                                @if($latestRateFetchedAt)
                                    {{ \Illuminate\Support\Carbon::parse($latestRateFetchedAt)->diffForHumans() }}
                                @else
                                    {{ __('base::system.api_configs.rates_never') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="sys-status-card is-info">
                        <div class="sys-status-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.currency.default') }}</div>
                            <div class="sys-status-value">{{ $selectedCurrency }}</div>
                        </div>
                    </div>
                </div>

                <x-admin.settings-section
                    icon="bi-currency-exchange"
                    :title="__('base::system.currency.title')"
                    :description="__('base::system.currency.description')"
                >
                    <div class="row mb-2 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label">
                                <i class="bi bi-cash-coin text-primary me-1"></i>
                                {{ __('base::system.currency.default') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::system.currency.default_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <input type="hidden" name="data[default_currency]" id="field-default-currency" value="{{ $selectedCurrency }}">
                            <div class="currency-chip-group" role="radiogroup" aria-label="{{ __('base::system.currency.default') }}">
                                @foreach($supportedCurrencies as $code)
                                    <button
                                        type="button"
                                        class="currency-chip {{ $selectedCurrency === $code ? 'is-active' : '' }}"
                                        data-currency="{{ $code }}"
                                        aria-pressed="{{ $selectedCurrency === $code ? 'true' : 'false' }}"
                                    >
                                        <span class="currency-chip-code">{{ $code }}</span>
                                        <span class="currency-chip-name">{{ $currencyNames[$code] ?? $code }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-admin.settings-section>

                <x-admin.settings-section
                    icon="bi-key"
                    :title="__('base::system.api_configs.title')"
                    :description="__('base::system.api_configs.description')"
                >
                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-fixer-api-key">
                                <i class="bi bi-shield-lock text-primary me-1"></i>
                                {{ __('base::system.api_configs.fixer_api_key') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::system.api_configs.fixer_api_key_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group input-group-solid">
                                <input
                                    type="password"
                                    id="field-fixer-api-key"
                                    name="data[fixer_api_key]"
                                    class="form-control form-control-solid"
                                    value="{{ old('data.fixer_api_key', $settings->get('fixer_api_key')) }}"
                                    placeholder="••••••••••••••••"
                                    autocomplete="off"
                                />
                                <button
                                    type="button"
                                    class="btn btn-light"
                                    id="toggle-fixer-key"
                                    title="{{ __('Show / hide') }}"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            <button
                                type="submit"
                                formaction="{{ route('admin.system-configurations.fetch-rates') }}"
                                class="btn btn-light-primary"
                            >
                                <i class="bi bi-arrow-repeat me-1"></i>
                                {{ __('base::system.api_configs.fetch_rates') }}
                            </button>
                        </div>
                    </div>
                </x-admin.settings-section>
            </div>

            {{-- Notifications --}}
            <div class="tab-pane fade" id="tab-sys-notifications">
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
                    <div class="row mb-2">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            <a href="{{ route('admin.integrations.index') }}" class="btn btn-light-primary">
                                <i class="bi bi-plug me-1"></i>
                                {{ __('base::integrations.title') }}
                            </a>
                        </div>
                    </div>
                </x-admin.settings-section>
            </div>

            {{-- Fingerprint --}}
            <div class="tab-pane fade" id="tab-sys-fingerprint">
                <div class="sys-status-grid mb-8">
                    <div class="sys-status-card {{ $fingerprintEnabled ? 'is-ok' : 'is-muted' }}">
                        <div class="sys-status-icon">
                            <i class="bi {{ $fingerprintEnabled ? 'bi-fingerprint' : 'bi-fingerprint' }}"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.fingerprint.status_title') }}</div>
                            <div class="sys-status-value">
                                {{ $fingerprintEnabled ? __('base::system.fingerprint.enabled_on') : __('base::system.fingerprint.enabled_off') }}
                            </div>
                        </div>
                    </div>
                    <div class="sys-status-card {{ $fingerprintConfigured ? 'is-ok' : 'is-warn' }}">
                        <div class="sys-status-icon">
                            <i class="bi {{ $fingerprintConfigured ? 'bi-hdd-network' : 'bi-hdd-network-fill' }}"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.fingerprint.connection_title') }}</div>
                            <div class="sys-status-value">
                                {{ $fingerprintConfigured ? __('base::system.fingerprint.connection_ready') : __('base::system.fingerprint.connection_missing') }}
                            </div>
                        </div>
                    </div>
                    <div class="sys-status-card {{ $fingerprintLastSync ? 'is-ok' : 'is-muted' }}">
                        <div class="sys-status-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="sys-status-label">{{ __('base::system.fingerprint.last_sync') }}</div>
                            <div class="sys-status-value">
                                @if($fingerprintLastSync)
                                    {{ \Illuminate\Support\Carbon::parse($fingerprintLastSync)->diffForHumans() }}
                                @else
                                    {{ __('base::system.fingerprint.never_synced') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <x-admin.settings-section
                    icon="bi-fingerprint"
                    :title="__('base::system.fingerprint.title')"
                    :description="__('base::system.fingerprint.description')"
                >
                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-fingerprint-enabled">
                                <i class="bi bi-toggle-on text-primary me-1"></i>
                                {{ __('base::system.fingerprint.enabled') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::system.fingerprint.enabled_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <input type="hidden" name="data[fingerprint_enabled]" value="0">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="field-fingerprint-enabled"
                                    name="data[fingerprint_enabled]"
                                    value="1"
                                    @checked($fingerprintEnabled)
                                />
                                <label class="form-check-label" for="field-fingerprint-enabled">
                                    {{ __('base::system.fingerprint.enabled_label') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <x-admin.settings-field
                        :label="__('base::system.fingerprint.host')"
                        name="data[fingerprint_host]"
                        :value="$settings->get('fingerprint_host')"
                        placeholder="192.168.1.201"
                        icon="bi-hdd-network"
                        :hint="__('base::system.fingerprint.host_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::system.fingerprint.port')"
                        name="data[fingerprint_port]"
                        type="number"
                        :value="$settings->get('fingerprint_port', '4370')"
                        placeholder="4370"
                        icon="bi-ethernet"
                        :hint="__('base::system.fingerprint.port_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::system.fingerprint.comm_key')"
                        name="data[fingerprint_comm_key]"
                        type="number"
                        :value="$settings->get('fingerprint_comm_key', '0')"
                        placeholder="0"
                        icon="bi-key"
                        :hint="__('base::system.fingerprint.comm_key_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::system.fingerprint.timeout')"
                        name="data[fingerprint_timeout]"
                        type="number"
                        :value="$settings->get('fingerprint_timeout', '10')"
                        placeholder="10"
                        icon="bi-hourglass-split"
                        :hint="__('base::system.fingerprint.timeout_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::system.fingerprint.name_encoding')"
                        name="data[fingerprint_name_encoding]"
                        :value="$settings->get('fingerprint_name_encoding', 'UTF-8')"
                        placeholder="UTF-8"
                        icon="bi-translate"
                        :hint="__('base::system.fingerprint.name_encoding_hint')"
                    />
                    <div class="row mb-2">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8 d-flex gap-2 flex-wrap">
                            <button
                                type="submit"
                                formaction="{{ route('admin.system-configurations.test-fingerprint') }}"
                                class="btn btn-light-primary"
                            >
                                <i class="bi bi-plug me-1"></i>
                                {{ __('base::system.fingerprint.test_connection') }}
                            </button>
                            @can('settings.system.edit')
                                <a href="{{ route('admin.fingerprint.index') }}" class="btn btn-light-info">
                                    <i class="bi bi-people me-1"></i>
                                    {{ __('base::system.fingerprint.manage_sync') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </x-admin.settings-section>
            </div>

            {{-- Backup --}}
            <div class="tab-pane fade" id="tab-sys-backup">
                <div class="sys-backup-banner {{ $backupEnabled ? 'is-on' : 'is-off' }} mb-8">
                    <div class="d-flex align-items-start gap-3 flex-grow-1">
                        <div class="sys-status-icon">
                            <i class="bi {{ $backupEnabled ? 'bi-cloud-check' : 'bi-cloud-slash' }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold mb-1">
                                {{ $backupEnabled ? __('base::system.backup.status_on') : __('base::system.backup.status_off') }}
                            </div>
                            <div class="text-muted fs-7">
                                {{ __('base::system.backup.last_run') }}:
                                <span class="fw-semibold text-gray-800">
                                    {{ $backupLastRun ?: __('base::system.backup.never_run') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.backups.index') }}" class="btn btn-sm btn-light-primary flex-shrink-0">
                        <i class="bi bi-folder2-open me-1"></i>
                        {{ __('base::system.backup.manage_link') }}
                    </a>
                </div>

                <x-admin.settings-section
                    icon="bi-database-down"
                    :title="__('base::system.backup.title')"
                    :description="__('base::system.backup.description')"
                >
                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-auto-backup-enabled">
                                <i class="bi bi-arrow-repeat text-primary me-1"></i>
                                {{ __('base::system.backup.enabled') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::system.backup.enabled_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <input type="hidden" name="data[auto_backup_enabled]" value="0">
                            <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="field-auto-backup-enabled"
                                    name="data[auto_backup_enabled]"
                                    value="1"
                                    @checked($backupEnabled)
                                />
                                <label class="form-check-label" for="field-auto-backup-enabled">
                                    {{ __('base::system.backup.enabled_label') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <x-admin.settings-field
                        :label="__('base::system.backup.interval_days')"
                        name="data[auto_backup_interval_days]"
                        type="number"
                        :value="$settings->get('auto_backup_interval_days', '7')"
                        placeholder="7"
                        icon="bi-calendar3"
                        :hint="__('base::system.backup.interval_days_hint')"
                    />
                </x-admin.settings-section>
            </div>
        </div>
    </x-admin.create-card>

    @push('scripts')
    <script>
        (function () {
            const form = document.getElementById('system-config-form');
            if (!form) return;

            form.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', function (e) {
                    localStorage.setItem('system_config_active_tab', e.target.getAttribute('href'));
                });
            });

            const savedTab = localStorage.getItem('system_config_active_tab');
            if (savedTab) {
                const tabEl = form.querySelector('[href="' + savedTab + '"]');
                if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
            }

            const currencyInput = document.getElementById('field-default-currency');
            form.querySelectorAll('.currency-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    const code = chip.getAttribute('data-currency');
                    currencyInput.value = code;
                    form.querySelectorAll('.currency-chip').forEach(function (el) {
                        el.classList.toggle('is-active', el === chip);
                        el.setAttribute('aria-pressed', el === chip ? 'true' : 'false');
                    });
                });
            });

            const keyInput = document.getElementById('field-fixer-api-key');
            const toggleBtn = document.getElementById('toggle-fixer-key');
            if (keyInput && toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = keyInput.type === 'password';
                    keyInput.type = isPassword ? 'text' : 'password';
                    toggleBtn.innerHTML = isPassword
                        ? '<i class="bi bi-eye-slash"></i>'
                        : '<i class="bi bi-eye"></i>';
                });
            }
        })();
    </script>
    @endpush
</x-admin-layout>
