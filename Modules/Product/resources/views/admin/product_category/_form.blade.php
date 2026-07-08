@php($categoryData = $category ?? null)

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::category.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $categoryData?->name) }}" maxlength="255" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="slug" class="fs-6 fw-bold mt-2 mb-3">{{ __('product::category.fields.slug') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="slug" type="text" class="form-control form-control-solid @error('slug') is-invalid @enderror"
               name="slug" value="{{ old('slug', $categoryData?->slug) }}" maxlength="255"/>
        <div class="form-text">{{ __('product::category.hints.slug') }}</div>
        @error('slug')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="description" class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1"></i>{{ __('product::category.fields.description') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                  name="description" rows="4">{{ old('description', $categoryData?->description) }}</textarea>
        @error('description')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<x-admin.auto-translate-checkbox :default="! $categoryData"/>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            if (!nameInput || !slugInput || slugInput.value) {
                return;
            }

            nameInput.addEventListener('input', function () {
                if (!slugInput.dataset.manual) {
                    slugInput.value = nameInput.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-')
                        .trim();
                }
            });

            slugInput.addEventListener('input', function () {
                slugInput.dataset.manual = slugInput.value ? '1' : '';
            });
        });
    </script>
@endpush
