@php
    $selectedColor = old('color', $tag->color ?? 'primary');
@endphp

<div class="row">
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.name_en') }}</label>
        <input type="text" name="name[en]" value="{{ old('name.en', $tag->getTranslation('name', 'en') ?? '') }}"
               class="form-control form-control-solid @error('name.en') is-invalid @enderror" required>
        @error('name.en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.name_ar') }}</label>
        <input type="text" name="name[ar]" value="{{ old('name.ar', $tag->getTranslation('name', 'ar') ?? '') }}"
               class="form-control form-control-solid @error('name.ar') is-invalid @enderror">
        @error('name.ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.name_de') }}</label>
        <input type="text" name="name[de]" value="{{ old('name.de', $tag->getTranslation('name', 'de') ?? '') }}"
               class="form-control form-control-solid @error('name.de') is-invalid @enderror">
        @error('name.de')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.name_tr') }}</label>
        <input type="text" name="name[tr]" value="{{ old('name.tr', $tag->getTranslation('name', 'tr') ?? '') }}"
               class="form-control form-control-solid @error('name.tr') is-invalid @enderror">
        @error('name.tr')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.color') }}</label>
        <select name="color" id="lead_tag_color"
                class="form-select form-select-solid @error('color') is-invalid @enderror"
                data-control="select2" required>
            @foreach(\Modules\CRM\Models\LeadTag::COLORS as $color)
                <option value="{{ $color }}" @selected($selectedColor === $color)>
                    {{ __('crm::lead_tag.colors.' . $color) }}
                </option>
            @endforeach
        </select>
        <div class="mt-3">
            <span id="lead_tag_color_preview" class="badge badge-light-{{ $selectedColor }}">
                {{ __('crm::lead_tag.fields.preview') }}
            </span>
        </div>
        @error('color')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::lead_tag.fields.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $tag->sort_order ?? 0) }}"
               class="form-control form-control-solid @error('sort_order') is-invalid @enderror">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('Status') }}</label>
        <div class="form-check form-switch form-check-custom form-check-solid mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                   id="is_active" @checked(old('is_active', $tag->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('lead_tag_color');
        const preview = document.getElementById('lead_tag_color_preview');
        if (!select || !preview) return;

        function updatePreview() {
            preview.className = 'badge badge-light-' + select.value;
        }

        select.addEventListener('change', updatePreview);
        $(select).on('change.select2', updatePreview);
    })();
</script>
@endpush
