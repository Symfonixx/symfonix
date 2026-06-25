@php($statusData = $status ?? null)

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::status.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $statusData?->name) }}" maxlength="255" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="color_code" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::status.fields.color_code') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="d-flex align-items-center gap-3">
            <input id="color_code" type="color"
                   class="form-control form-control-color @error('color_code') is-invalid @enderror"
                   name="color_code" value="{{ old('color_code', $statusData?->color_code ?? '#6c757d') }}"/>
            <input type="text" class="form-control form-control-solid w-150px" id="color_code_text"
                   value="{{ old('color_code', $statusData?->color_code ?? '#6c757d') }}" readonly/>
        </div>
        <div class="form-text">{{ __('project::status.hints.color_code') }}</div>
        @error('color_code')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="sort_order" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('project::status.fields.sort_order') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="sort_order" type="number" min="0"
               class="form-control form-control-solid @error('sort_order') is-invalid @enderror"
               name="sort_order" value="{{ old('sort_order', $statusData?->sort_order ?? $nextSortOrder ?? 0) }}"
               required/>
        <div class="form-text">{{ __('project::status.hints.sort_order') }}</div>
        @error('sort_order')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const colorInput = document.getElementById('color_code');
            const colorText = document.getElementById('color_code_text');

            if (!colorInput || !colorText) {
                return;
            }

            colorInput.addEventListener('input', () => {
                colorText.value = colorInput.value;
            });
        });
    </script>
@endpush
