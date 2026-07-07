@php
    use Modules\Base\Support\AdminBranding;

    $adminLogoUrl = AdminBranding::logoUrl();
@endphp
<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <title>{{ __('Two-Factor Authentication') }} - {{ __('Admin Panel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon/favicon-96x96.png') }}" sizes="96x96"/>

    @if(app()->getLocale() === 'ar')
        <link href="{{ asset('admin/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('admin/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
        <style>html, body { font-family: 'Tajawal', sans-serif !important; }</style>
    @else
        <link href="{{ asset('admin/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('admin/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    @endif

    <link href="{{ asset('admin/css/custom-admin.css') }}" rel="stylesheet" type="text/css"/>
</head>
<body class="app-blank" style="background-color: #f5f8fa;">
<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-10 p-lg-20">
            <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-500px p-10 p-lg-15 mx-2 mx-lg-0 shadow-sm">
                <div class="text-center mb-10">
                    <a href="{{ route('login') }}">
                        <img alt="Logo" src="{{ $adminLogoUrl }}" class="h-50px"/>
                    </a>
                </div>

                <div class="text-center mb-8">
                    <h1 class="text-dark fw-bolder mb-3">{{ __('Two-Factor Authentication') }}</h1>
                    <div class="text-gray-500 fw-semibold fs-6">
                        {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('two-factor.login.store') }}" id="two-factor-form">
                    @csrf

                    <div id="code-section">
                        <div class="fv-row mb-8">
                            <label class="form-label fw-semibold">{{ __('Authentication Code') }}</label>
                            <input
                                type="text"
                                name="code"
                                class="form-control form-control-lg"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                autofocus
                            >
                        </div>
                    </div>

                    <div id="recovery-section" class="d-none">
                        <div class="fv-row mb-8">
                            <label class="form-label fw-semibold">{{ __('Recovery Code') }}</label>
                            <input
                                type="text"
                                name="recovery_code"
                                class="form-control form-control-lg"
                                autocomplete="one-time-code"
                            >
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center pb-lg-0 mb-8">
                        <button type="button" id="toggle-recovery" class="btn btn-link">
                            {{ __('Use a recovery code') }}
                        </button>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">{{ __('Login') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggle-recovery');
        const codeSection = document.getElementById('code-section');
        const recoverySection = document.getElementById('recovery-section');
        const codeInput = document.querySelector('input[name="code"]');
        const recoveryInput = document.querySelector('input[name="recovery_code"]');
        let usingRecovery = false;

        toggleBtn.addEventListener('click', function () {
            usingRecovery = !usingRecovery;

            if (usingRecovery) {
                codeSection.classList.add('d-none');
                recoverySection.classList.remove('d-none');
                codeInput.value = '';
                recoveryInput.focus();
                toggleBtn.textContent = '{{ __('Use an authentication code') }}';
            } else {
                recoverySection.classList.add('d-none');
                codeSection.classList.remove('d-none');
                recoveryInput.value = '';
                codeInput.focus();
                toggleBtn.textContent = '{{ __('Use a recovery code') }}';
            }
        });
    });
</script>
</body>
</html>
