@props([
    'default' => true,
])

@php
    if (old()->has('auto_translate')) {
        $isChecked = filter_var(old('auto_translate'), FILTER_VALIDATE_BOOLEAN);
    } else {
        $isChecked = (bool) $default;
    }
@endphp

<div {{ $attributes->merge(['class' => 'row mb-8']) }}>
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">
            <i class="bi bi-translate text-primary mx-1"></i>{{ __('Auto translate to other languages') }}
        </div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid me-10">
            <input type="hidden" name="auto_translate" value="0">
            <input class="form-check-input h-30px w-50px"
                   type="checkbox"
                   name="auto_translate"
                   id="autoTranslateSwitch"
                   value="1"
                   @checked($isChecked)/>
        </div>
        <div class="form-text">
            {{ __('If enabled, content will be automatically translated into other languages when you save.') }}
        </div>
    </div>
</div>
