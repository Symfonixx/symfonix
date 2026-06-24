@props([
    'currentImage' => null,
    'dimensions' => '500 × 500 px',
    'required' => true,
])

@php
    $imageUrl = $currentImage ?? asset('images/default.jpg');
@endphp

<div class="card mb-6 cms-aside-image-card">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-image text-primary me-2"></i>{{ __('Featured Image') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="image-input image-input-outline w-100" data-kt-image-input="true"
             style="background-image: url('{{ asset('images/default.jpg') }}')">
            <div class="image-input-wrapper bgi-position-center"
                 style="background-image: url('{{ $imageUrl }}')"></div>
            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change image') }}">
                <i class="bi bi-pencil-fill fs-7"></i>
                <input type="file" name="img" accept=".png, .jpg, .jpeg, .webp" @if($required && !$currentImage) required @endif/>
                <input type="hidden" name="avatar_remove"/>
            </label>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('Cancel') }}">
                <i class="bi bi-x fs-2"></i>
            </span>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                  data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('Remove') }}">
                <i class="bi bi-trash fs-7"></i>
            </span>
        </div>
        <div class="form-text mt-3">
            <span class="badge badge-light-primary fs-8">{{ $dimensions }}</span>
        </div>
    </div>
</div>
