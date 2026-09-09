@section('title', __('base::integrations.title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Settings', 'url' => route('admin.settings.index')],
            ['label' => __('base::integrations.title')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('base::integrations.title')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('base::integrations.page_description')"
    />
@endsection

<x-admin-layout>
    @php
        $mailMailer = old('data.mail_mailer', $mailStored['mail_mailer'] ?: '');
        $mailEncryption = old('data.mail_encryption', $mailStored['mail_encryption'] ?: '');
    @endphp

    <x-admin.create-card
        title="base::integrations.title"
        :formUrl="route('admin.integrations.store')"
        :description="__('base::integrations.form_description')"
        id="integrations-form"
    >
        <div class="sys-status-grid mb-8">
            <div class="sys-status-card {{ $mailConfigured ? 'is-ok' : 'is-warn' }}">
                <div class="sys-status-icon">
                    <i class="bi {{ $mailConfigured ? 'bi-envelope-check' : 'bi-envelope-exclamation' }}"></i>
                </div>
                <div>
                    <div class="sys-status-label">{{ __('base::integrations.status.email') }}</div>
                    <div class="sys-status-value">
                        {{ $mailConfigured ? __('base::integrations.status.configured') : __('base::integrations.status.missing') }}
                    </div>
                </div>
            </div>
            <div class="sys-status-card {{ $whatsappConfigured ? 'is-ok' : 'is-warn' }}">
                <div class="sys-status-icon">
                    <i class="bi {{ $whatsappConfigured ? 'bi-whatsapp' : 'bi-exclamation-triangle' }}"></i>
                </div>
                <div>
                    <div class="sys-status-label">{{ __('base::integrations.status.whatsapp') }}</div>
                    <div class="sys-status-value">
                        {{ $whatsappConfigured ? __('base::integrations.status.configured') : __('base::integrations.status.missing') }}
                    </div>
                </div>
            </div>
            <div class="sys-status-card is-info">
                <div class="sys-status-icon">
                    <i class="bi bi-gear"></i>
                </div>
                <div>
                    <div class="sys-status-label">{{ __('base::integrations.status.system') }}</div>
                    <div class="sys-status-value">
                        <a href="{{ route('admin.system-configurations.index') }}" class="text-primary text-hover-primary fw-semibold">
                            {{ __('base::integrations.status.system_link') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x settings-tabs mb-8 fs-6 fw-semibold flex-nowrap overflow-auto" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-nowrap" data-bs-toggle="tab" href="#tab-int-email">
                    <i class="bi bi-envelope-at me-2"></i>{{ __('base::integrations.tabs.email') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" data-bs-toggle="tab" href="#tab-int-whatsapp">
                    <i class="bi bi-whatsapp me-2"></i>{{ __('base::integrations.tabs.whatsapp') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Email --}}
            <div class="tab-pane fade show active" id="tab-int-email">
                <x-admin.settings-section
                    icon="bi-envelope-at"
                    :title="__('base::integrations.email.title')"
                    :description="__('base::integrations.email.description')"
                >
                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-mail-mailer">
                                <i class="bi bi-mailbox text-primary me-1"></i>
                                {{ __('base::integrations.email.mailer') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::integrations.email.mailer_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <select id="field-mail-mailer" name="data[mail_mailer]" class="form-select form-select-solid">
                                @foreach(['smtp' => 'SMTP', 'log' => 'Log', 'sendmail' => 'Sendmail', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'resend' => 'Resend', 'array' => 'Array'] as $value => $label)
                                    <option value="{{ $value }}" @selected($mailMailer === $value || ($mailMailer === '' && ($mailEnvPlaceholders['mail_mailer'] ?: 'smtp') === $value))>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-admin.settings-field
                        :label="__('base::integrations.email.host')"
                        name="data[mail_host]"
                        :value="$mailStored['mail_host']"
                        :placeholder="$mailEnvPlaceholders['mail_host'] ?: 'smtp.example.com'"
                        icon="bi-hdd-network"
                        :hint="__('base::integrations.email.env_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::integrations.email.port')"
                        name="data[mail_port]"
                        type="number"
                        :value="$mailStored['mail_port']"
                        :placeholder="$mailEnvPlaceholders['mail_port'] ?: '587'"
                        icon="bi-hash"
                    />
                    <x-admin.settings-field
                        :label="__('base::integrations.email.username')"
                        name="data[mail_username]"
                        :value="$mailStored['mail_username']"
                        :placeholder="$mailEnvPlaceholders['mail_username'] ?: 'user@example.com'"
                        icon="bi-person"
                    />

                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-mail-password">
                                <i class="bi bi-key text-primary me-1"></i>
                                {{ __('base::integrations.email.password') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::integrations.email.password_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group input-group-solid">
                                <input
                                    type="password"
                                    id="field-mail-password"
                                    name="data[mail_password]"
                                    class="form-control form-control-solid"
                                    value=""
                                    placeholder="{{ filled($mailStored['mail_password']) || filled($mailEnvPlaceholders['mail_password']) ? '••••••••••••••••' : '' }}"
                                    autocomplete="new-password"
                                />
                                <button type="button" class="btn btn-light toggle-secret" data-target="field-mail-password" title="{{ __('Show / hide') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-mail-encryption">
                                <i class="bi bi-shield-lock text-primary me-1"></i>
                                {{ __('base::integrations.email.encryption') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::integrations.email.encryption_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <select id="field-mail-encryption" name="data[mail_encryption]" class="form-select form-select-solid">
                                @php
                                    $effectiveEncryption = $mailEncryption !== ''
                                        ? $mailEncryption
                                        : ($mailEnvPlaceholders['mail_encryption'] ?: 'tls');
                                @endphp
                                <option value="tls" @selected($effectiveEncryption === 'tls')>TLS</option>
                                <option value="ssl" @selected($effectiveEncryption === 'ssl')>SSL</option>
                                <option value="null" @selected($effectiveEncryption === '' || $effectiveEncryption === 'null')>{{ __('None') }}</option>
                            </select>
                        </div>
                    </div>

                    <x-admin.settings-field
                        :label="__('base::integrations.email.from_address')"
                        name="data[mail_from_address]"
                        type="email"
                        :value="$mailStored['mail_from_address']"
                        :placeholder="$mailEnvPlaceholders['mail_from_address'] ?: 'noreply@example.com'"
                        icon="bi-envelope"
                    />
                    <x-admin.settings-field
                        :label="__('base::integrations.email.from_name')"
                        name="data[mail_from_name]"
                        :value="$mailStored['mail_from_name']"
                        :placeholder="$mailEnvPlaceholders['mail_from_name'] ?: config('app.name')"
                        icon="bi-type"
                    />
                </x-admin.settings-section>
            </div>

            {{-- WhatsApp --}}
            <div class="tab-pane fade" id="tab-int-whatsapp">
                <x-admin.settings-section
                    icon="bi-whatsapp"
                    :title="__('base::integrations.whatsapp.title')"
                    :description="__('base::integrations.whatsapp.description')"
                >
                    <div class="row mb-6 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-whatsapp-api-token">
                                <i class="bi bi-shield-lock text-primary me-1"></i>
                                {{ __('base::integrations.whatsapp.api_token') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::integrations.whatsapp.api_token_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group input-group-solid">
                                <input
                                    type="password"
                                    id="field-whatsapp-api-token"
                                    name="data[whatsapp_api_token]"
                                    class="form-control form-control-solid"
                                    value=""
                                    placeholder="{{ filled($whatsappStored['whatsapp_api_token']) || filled($whatsappEnvPlaceholders['whatsapp_api_token']) ? '••••••••••••••••' : '' }}"
                                    autocomplete="off"
                                />
                                <button type="button" class="btn btn-light toggle-secret" data-target="field-whatsapp-api-token" title="{{ __('Show / hide') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <x-admin.settings-field
                        :label="__('base::integrations.whatsapp.phone_number_id')"
                        name="data[whatsapp_phone_number_id]"
                        :value="$whatsappStored['whatsapp_phone_number_id']"
                        :placeholder="$whatsappEnvPlaceholders['whatsapp_phone_number_id'] ?: '123456789012345'"
                        icon="bi-telephone"
                        :hint="__('base::integrations.whatsapp.env_hint')"
                    />
                    <x-admin.settings-field
                        :label="__('base::integrations.whatsapp.business_account_id')"
                        name="data[whatsapp_business_account_id]"
                        :value="$whatsappStored['whatsapp_business_account_id']"
                        :placeholder="$whatsappEnvPlaceholders['whatsapp_business_account_id'] ?: ''"
                        icon="bi-building"
                    />
                    <x-admin.settings-field
                        :label="__('base::integrations.whatsapp.api_version')"
                        name="data[whatsapp_api_version]"
                        :value="$whatsappStored['whatsapp_api_version']"
                        :placeholder="$whatsappEnvPlaceholders['whatsapp_api_version'] ?: 'v21.0'"
                        icon="bi-code-slash"
                    />

                    <div class="row mb-2 settings-field">
                        <div class="col-lg-4">
                            <label class="settings-field-label" for="field-whatsapp-webhook-verify-token">
                                <i class="bi bi-key text-primary me-1"></i>
                                {{ __('base::integrations.whatsapp.webhook_verify_token') }}
                            </label>
                            <div class="settings-field-hint">{{ __('base::integrations.whatsapp.webhook_verify_token_hint') }}</div>
                        </div>
                        <div class="col-lg-8">
                            <div class="input-group input-group-solid">
                                <input
                                    type="password"
                                    id="field-whatsapp-webhook-verify-token"
                                    name="data[whatsapp_webhook_verify_token]"
                                    class="form-control form-control-solid"
                                    value=""
                                    placeholder="{{ filled($whatsappStored['whatsapp_webhook_verify_token']) || filled($whatsappEnvPlaceholders['whatsapp_webhook_verify_token']) ? '••••••••••••••••' : '' }}"
                                    autocomplete="off"
                                />
                                <button type="button" class="btn btn-light toggle-secret" data-target="field-whatsapp-webhook-verify-token" title="{{ __('Show / hide') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </x-admin.settings-section>
            </div>
        </div>
    </x-admin.create-card>

    @push('scripts')
    <script>
        (function () {
            const form = document.getElementById('integrations-form');
            if (!form) return;

            form.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', function (e) {
                    localStorage.setItem('integrations_active_tab', e.target.getAttribute('href'));
                });
            });

            const savedTab = localStorage.getItem('integrations_active_tab');
            if (savedTab) {
                const tabEl = form.querySelector('[href="' + savedTab + '"]');
                if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
            }

            form.querySelectorAll('.toggle-secret').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.getAttribute('data-target'));
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    btn.innerHTML = isPassword
                        ? '<i class="bi bi-eye-slash"></i>'
                        : '<i class="bi bi-eye"></i>';
                });
            });
        })();
    </script>
    @endpush
</x-admin-layout>
