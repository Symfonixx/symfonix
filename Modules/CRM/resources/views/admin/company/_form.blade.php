@php($companyData = $company ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::company.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::company.sections.basic_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::company.sections.basic_information_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="user_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::company.fields.customer') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="user_id" class="form-select form-select-solid @error('user_id') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::company.fields.select_customer') }}"
                name="user_id" required>
            <option value="">{{ __('crm::company.fields.select_customer') }}</option>
            @foreach(($customers ?? collect()) as $customer)
                <option value="{{ $customer->id }}" @selected((int) old('user_id', $companyData?->user_id ?? request('customer_id')) === $customer->id)>
                    {{ $customer->name }} ({{ $customer->email }})
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::company.hints.customer') }}</div>
        <button type="button" class="btn btn-sm btn-light-primary mt-3" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
            <i class="bi bi-person-plus me-1"></i>{{ __('crm::company.actions.add_customer') }}
        </button>
        @error('user_id')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::company.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $companyData?->name) }}"
               placeholder="{{ __('crm::company.placeholders.name') }}" maxlength="255" required autofocus/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="activity_type" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::company.fields.activity_type') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="activity_type" class="form-select form-select-solid @error('activity_type') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::company.fields.select_activity_type') }}"
                data-allow-clear="true" name="activity_type">
            <option value=""></option>
            @foreach(\Modules\CRM\Models\Company::ACTIVITY_TYPES as $type)
                <option value="{{ $type }}" @selected(old('activity_type', $companyData?->activity_type) === $type)>
                    {{ __('crm::company.activity_types.' . $type) }}
                </option>
            @endforeach
        </select>
        @error('activity_type')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::company.sections.contact_information') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('crm::company.fields.email') }}</label>
                <input id="email" type="email"
                       class="form-control form-control-solid @error('email') is-invalid @enderror" name="email"
                       value="{{ old('email', $companyData?->email) }}"
                       placeholder="{{ __('crm::company.placeholders.email') }}" maxlength="255"
                       autocomplete="email"/>
                @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">{{ __('crm::company.fields.phone') }}</label>
                <input id="phone" type="text"
                       class="form-control form-control-solid @error('phone') is-invalid @enderror" name="phone"
                       value="{{ old('phone', $companyData?->phone) }}"
                       placeholder="{{ __('crm::company.placeholders.phone') }}" maxlength="50" autocomplete="tel"/>
                @error('phone')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::company.sections.location') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::company.sections.location_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::company.fields.country') }} / {{ __('crm::company.fields.city') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="row g-6">
            <div class="col-md-6">
                <label for="country" class="form-label">{{ __('crm::company.fields.country') }}</label>
                <input id="country" type="text"
                       class="form-control form-control-solid @error('country') is-invalid @enderror" name="country"
                       value="{{ old('country', $companyData?->country) }}"
                       placeholder="{{ __('crm::company.placeholders.country') }}" maxlength="100"/>
                @error('country')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="city" class="form-label">{{ __('crm::company.fields.city') }}</label>
                <input id="city" type="text"
                       class="form-control form-control-solid @error('city') is-invalid @enderror" name="city"
                       value="{{ old('city', $companyData?->city) }}"
                       placeholder="{{ __('crm::company.placeholders.city') }}" maxlength="100"/>
                @error('city')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="address" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::company.fields.address') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="address" type="text"
               class="form-control form-control-solid @error('address') is-invalid @enderror" name="address"
               value="{{ old('address', $companyData?->address) }}"
               placeholder="{{ __('crm::company.placeholders.address') }}" maxlength="255"/>
        @error('address')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::company.sections.additional_details') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::company.sections.additional_details_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="notes" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::company.fields.notes') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="notes" class="form-control form-control-solid @error('notes') is-invalid @enderror" name="notes"
                  rows="4" placeholder="{{ __('crm::company.placeholders.notes') }}">{{ old('notes', $companyData?->notes) }}</textarea>
        @error('notes')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="row mb-0">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::company.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" class="form-select form-select-solid @error('status') is-invalid @enderror"
                name="status" required>
            <option value="active" @selected(old('status', $companyData?->status ?? 'active') === 'active')>
                {{ __('crm::company.status.active') }}
            </option>
            <option value="disabled" @selected(old('status', $companyData?->status ?? '') === 'disabled')>
                {{ __('crm::company.status.disabled') }}
            </option>
        </select>
        <div class="form-text">{{ __('crm::company.hints.status') }}</div>
        @error('status')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
