@php($categoryData = $category ?? null)

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('finance::expense_category.fields.name') }}</label>
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
        <label for="slug" class="fs-6 fw-bold mt-2 mb-3">{{ __('finance::expense_category.fields.slug') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="slug" type="text" class="form-control form-control-solid @error('slug') is-invalid @enderror"
               name="slug" value="{{ old('slug', $categoryData?->slug) }}" maxlength="255"/>
        <div class="form-text">{{ __('finance::expense_category.hints.slug') }}</div>
        @error('slug')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

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
