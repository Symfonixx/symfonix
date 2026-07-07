<div class="row">
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('support::ticket.category.fields.name_en') }}</label>
        <input type="text" name="name[en]" value="{{ old('name.en', $category->getTranslation('name', 'en') ?? '') }}"
               class="form-control form-control-solid @error('name.en') is-invalid @enderror" required>
        @error('name.en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('support::ticket.category.fields.name_ar') }}</label>
        <input type="text" name="name[ar]" value="{{ old('name.ar', $category->getTranslation('name', 'ar') ?? '') }}"
               class="form-control form-control-solid @error('name.ar') is-invalid @enderror">
        @error('name.ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('support::ticket.category.fields.name_tr') }}</label>
        <input type="text" name="name[tr]" value="{{ old('name.tr', $category->getTranslation('name', 'tr') ?? '') }}"
               class="form-control form-control-solid @error('name.tr') is-invalid @enderror">
        @error('name.tr')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('support::ticket.category.fields.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
               class="form-control form-control-solid @error('sort_order') is-invalid @enderror">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('Status') }}</label>
        <div class="form-check form-switch form-check-custom form-check-solid mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="is_active" @checked(old('is_active', $category->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
        </div>
    </div>
</div>
