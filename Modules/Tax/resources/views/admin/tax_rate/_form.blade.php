@php($taxRateData = $taxRate ?? null)

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('tax::tax_rate.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $taxRateData?->name) }}" maxlength="255" required autofocus/>
        @error('name')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="percentage" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('tax::tax_rate.fields.percentage') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="input-group">
            <input id="percentage" type="number" step="0.0001" min="0" max="100"
                   class="form-control form-control-solid @error('percentage') is-invalid @enderror"
                   name="percentage" value="{{ old('percentage', $taxRateData?->percentage) }}" required/>
            <span class="input-group-text">%</span>
        </div>
        @error('percentage')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="type" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('tax::tax_rate.fields.type') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="type" name="type" class="form-select form-select-solid @error('type') is-invalid @enderror" required>
            @foreach(['exclusive', 'inclusive'] as $type)
                <option value="{{ $type }}" @selected(old('type', $taxRateData?->type ?? 'exclusive') === $type)>
                    {{ __('tax::tax_rate.types.'.$type) }}
                </option>
            @endforeach
        </select>
        @error('type')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="region_code" class="fs-6 fw-bold mt-2 mb-3">{{ __('tax::tax_rate.fields.region_code') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="region_code" type="text" class="form-control form-control-solid @error('region_code') is-invalid @enderror"
               name="region_code" value="{{ old('region_code', $taxRateData?->region_code) }}" maxlength="10"/>
        <div class="form-text">{{ __('tax::tax_rate.hints.region_code') }}</div>
        @error('region_code')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('tax::tax_rate.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" name="status" class="form-select form-select-solid @error('status') is-invalid @enderror" required>
            @foreach(['active', 'inactive'] as $status)
                <option value="{{ $status }}" @selected(old('status', $taxRateData?->status ?? 'active') === $status)>
                    {{ __('tax::tax_rate.statuses.'.$status) }}
                </option>
            @endforeach
        </select>
        @error('status')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label class="fs-6 fw-bold mt-2 mb-3">{{ __('tax::tax_rate.fields.is_default') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input type="hidden" name="is_default" value="0"/>
            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default"
                   @checked(old('is_default', $taxRateData?->is_default))/>
            <label class="form-check-label" for="is_default">{{ __('tax::tax_rate.hints.is_default') }}</label>
        </div>
        @error('is_default')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>
