@props([
    'prefix',
])

@php
    $logo = \Modules\AI\Support\CompanyContentProfile::logoReference();
@endphp

@if($logo)
    <div class="d-flex align-items-center gap-3 mb-6 p-3 border rounded bg-light">
        <img src="{{ $logo['url'] }}" alt="{{ $logo['name'] }}" style="height:32px; max-width:88px; object-fit:contain;">
        <div class="flex-grow-1">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="{{ $prefix }}-use-brand" checked>
                <label class="form-check-label fw-semibold" for="{{ $prefix }}-use-brand">
                    {{ __('Match our brand') }}
                </label>
            </div>
            <div class="text-muted fs-8">{{ __('Sends the site logo from Settings so Gemini follows your colors and identity.') }}</div>
        </div>
    </div>
@else
    <div class="alert alert-light mb-6 py-2 px-3 fs-8 text-muted">
        {{ __('Add a site logo under Settings › Branding to make generated images match your brand.') }}
    </div>
@endif
