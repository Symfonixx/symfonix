@props([
    'default' => true,
])

@php
    $oldAutoTranslate = old('auto_translate');
    $isChecked = $oldAutoTranslate !== null
        ? filter_var($oldAutoTranslate, FILTER_VALIDATE_BOOLEAN)
        : (bool) $default;
@endphp

<div {{ $attributes->merge(['class' => 'cms-aside-option align-items-start mb-8']) }}>
    <div class="flex-grow-1 pe-3">
        <div class="cms-aside-option-label">
            <i class="bi bi-translate text-primary me-1"></i>{{ __('Auto translate to other languages') }}
        </div>
        <div class="cms-aside-option-hint">
            {{ __('If enabled, content will be automatically translated into other languages when you save.') }}
        </div>
    </div>
    <div class="form-check form-switch form-check-custom form-check-solid flex-shrink-0 pt-1">
        <input type="hidden" name="auto_translate" value="0">
        <input class="form-check-input h-30px w-50px"
               type="checkbox"
               name="auto_translate"
               id="autoTranslateSwitch"
               value="1"
               @checked($isChecked)/>
    </div>
</div>
