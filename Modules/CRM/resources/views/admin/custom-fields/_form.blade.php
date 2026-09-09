@php
    $selectedType = old('type', $field->type ?? \Modules\CRM\Models\LeadCustomField::TYPE_TEXT);
    $optionsText = old('options_text', implode("\n", $field->options ?? []));
@endphp

<div class="row">
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.label_en') }}</label>
        <input type="text" name="label[en]" value="{{ old('label.en', $field->getTranslation('label', 'en') ?? '') }}"
               class="form-control form-control-solid @error('label.en') is-invalid @enderror" required>
        @error('label.en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.label_ar') }}</label>
        <input type="text" name="label[ar]" value="{{ old('label.ar', $field->getTranslation('label', 'ar') ?? '') }}"
               class="form-control form-control-solid @error('label.ar') is-invalid @enderror">
        @error('label.ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.label_de') }}</label>
        <input type="text" name="label[de]" value="{{ old('label.de', $field->getTranslation('label', 'de') ?? '') }}"
               class="form-control form-control-solid @error('label.de') is-invalid @enderror">
        @error('label.de')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.label_tr') }}</label>
        <input type="text" name="label[tr]" value="{{ old('label.tr', $field->getTranslation('label', 'tr') ?? '') }}"
               class="form-control form-control-solid @error('label.tr') is-invalid @enderror">
        @error('label.tr')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@if($field->exists)
    <div class="row mt-6">
        <div class="col-md-6">
            <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.key') }}</label>
            <input type="text" class="form-control form-control-solid" value="{{ $field->key }}" disabled>
            <div class="form-text">{{ __('crm::custom_field.hints.key_locked') }}</div>
        </div>
    </div>
@endif

<div class="row mt-6">
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.type') }}</label>
        <select name="type" id="custom_field_type"
                class="form-select form-select-solid @error('type') is-invalid @enderror"
                data-control="select2" required>
            @foreach(\Modules\CRM\Models\LeadCustomField::TYPES as $type)
                <option value="{{ $type }}" @selected($selectedType === $type)>
                    {{ __('crm::custom_field.types.' . $type) }}
                </option>
            @endforeach
        </select>
        @error('type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $field->sort_order ?? 0) }}"
               class="form-control form-control-solid @error('sort_order') is-invalid @enderror">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-2">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.is_required') }}</label>
        <div class="form-check form-switch form-check-custom form-check-solid mt-4">
            <input class="form-check-input" type="checkbox" name="is_required" value="1"
                   id="is_required" @checked(old('is_required', $field->is_required ?? false))>
            <label class="form-check-label" for="is_required">{{ __('Yes') }}</label>
        </div>
    </div>
    <div class="col-md-2">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('Status') }}</label>
        <div class="form-check form-switch form-check-custom form-check-solid mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="is_active" @checked(old('is_active', $field->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
        </div>
    </div>
</div>

<div class="row mt-6" id="custom_field_options_row" style="{{ $selectedType === \Modules\CRM\Models\LeadCustomField::TYPE_SELECT ? '' : 'display:none;' }}">
    <div class="col-md-8">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::custom_field.fields.options') }}</label>
        <textarea name="options_text" rows="5"
                  class="form-control form-control-solid @error('options_text') is-invalid @enderror"
                  placeholder="Option 1&#10;Option 2&#10;Option 3">{{ $optionsText }}</textarea>
        <div class="form-text">{{ __('crm::custom_field.fields.options_hint') }}</div>
        @error('options_text')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('custom_field_type');
        const optionsRow = document.getElementById('custom_field_options_row');
        if (!typeSelect || !optionsRow) return;

        function toggleOptions() {
            optionsRow.style.display = typeSelect.value === 'select' ? '' : 'none';
        }

        typeSelect.addEventListener('change', toggleOptions);
        $(typeSelect).on('change.select2', toggleOptions);
    })();
</script>
@endpush
