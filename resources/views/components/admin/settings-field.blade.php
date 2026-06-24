@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => null,
    'hint' => null,
    'icon' => null,
    'iconClass' => 'text-primary',
    'rows' => 4,
    'maxlength' => null,
    'counter' => false,
    'counterTarget' => null,
    'required' => false,
])

@php
    preg_match('/^data\[(.+)\]$/', $name, $matches);
    $fieldKey = $matches[1] ?? $name;
    $fieldValue = old('data.' . $fieldKey, $value);
    $inputId = 'field-' . str_replace(['[', ']'], ['-', ''], $name);
@endphp

<div class="row mb-6 settings-field">
    <div class="col-lg-4">
        <label class="settings-field-label" for="{{ $inputId }}">
            @if($icon)
                <i class="bi {{ $icon }} {{ $iconClass }} me-1"></i>
            @endif
            {{ __($label) }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
        @if($hint)
            <div class="settings-field-hint">{{ __($hint) }}</div>
        @endif
    </div>
    <div class="col-lg-8">
        @if($type === 'textarea')
            <textarea
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="form-control form-control-solid {{ $counter ? 'seo-counter-input' : '' }}"
                rows="{{ $rows }}"
                placeholder="{{ $placeholder ? __($placeholder) : '' }}"
                @if($maxlength) maxlength="{{ $maxlength }}" @endif
                @if($counter && $counterTarget) data-counter-target="{{ $counterTarget }}" @endif
            >{{ $fieldValue }}</textarea>
        @else
            <input
                type="{{ $type }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="form-control form-control-solid {{ $counter ? 'seo-counter-input' : '' }}"
                value="{{ $fieldValue }}"
                placeholder="{{ $placeholder ? __($placeholder) : '' }}"
                @if($maxlength) maxlength="{{ $maxlength }}" @endif
                @if($counter && $counterTarget) data-counter-target="{{ $counterTarget }}" @endif
            />
        @endif
        @if($counter && $counterTarget)
            <div class="d-flex justify-content-between mt-2">
                <span class="text-muted fs-8" id="{{ $counterTarget }}-hint"></span>
                <span class="seo-char-counter fs-8" id="{{ $counterTarget }}-counter" data-max="{{ $maxlength }}">0 / {{ $maxlength }}</span>
            </div>
        @endif
    </div>
</div>
