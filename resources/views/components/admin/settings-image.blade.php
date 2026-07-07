@props([
    'label',
    'name',
    'current' => 'default.jpg',
    'dimensions' => null,
    'hint' => null,
    'imageUrl' => null,
    'fallbackAsset' => null,
])

@php
    $hasStoredImage = $current && $current !== 'default.jpg';
    if (! $imageUrl) {
        $imageUrl = ($fallbackAsset && ! $hasStoredImage)
            ? asset($fallbackAsset)
            : asset('storage/' . $current);
    }
@endphp

<div class="settings-image-card">
    <div class="settings-image-preview" style="background-image: url('{{ $imageUrl }}')"></div>
    <div class="settings-image-body">
        <div class="fw-bold text-gray-800 mb-1">{{ __($label) }}</div>
        @if($dimensions)
            <span class="badge badge-light-primary fs-8 mb-2">{{ $dimensions }}</span>
        @endif
        @if($hint)
            <p class="text-muted fs-8 mb-3">{{ __($hint) }}</p>
        @endif
        <div class="image-input image-input-outline settings-image-input" data-kt-image-input="true">
            <div class="image-input-wrapper w-100px h-100px bgi-position-center"
                 style="background-size: contain; background-image: url('{{ $imageUrl }}')"></div>
            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                   data-kt-image-input-action="change" data-bs-toggle="tooltip"
                   title="{{ __('Change image') }}">
                <i class="bi bi-pencil-fill fs-7"></i>
                <input type="file" name="imgs[{{ $name }}]" accept=".png, .jpg, .jpeg, .webp"/>
                <input type="hidden" name="avatar_remove"/>
            </label>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                  title="{{ __('Cancel') }}">
                <i class="bi bi-x fs-2"></i>
            </span>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                  data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                  title="{{ __('Remove') }}">
                <i class="bi bi-trash fs-7"></i>
            </span>
        </div>
    </div>
</div>
