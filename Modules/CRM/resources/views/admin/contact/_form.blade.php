@php($contactData = $contact ?? null)

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::contact.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.basic_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::contact.sections.basic_information_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::contact.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="name" type="text" class="form-control form-control-solid @error('name') is-invalid @enderror"
               name="name" value="{{ old('name', $contactData?->name) }}"
               placeholder="{{ __('crm::contact.placeholders.name') }}" maxlength="255" autofocus required/>
        @error('name')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="email" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.email') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="email" type="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
               name="email" value="{{ old('email', $contactData?->email) }}"
               placeholder="{{ __('crm::contact.placeholders.email') }}" maxlength="255"/>
        @error('email')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="phone" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.phone') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="phone" type="tel" inputmode="tel" pattern="[0-9+\-\s()]+"
               class="form-control form-control-solid @error('phone') is-invalid @enderror"
               name="phone" value="{{ old('phone', $contactData?->phone) }}"
               placeholder="{{ __('crm::contact.placeholders.phone') }}" maxlength="50"/>
        @error('phone')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="phone2" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.phone2') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="phone2" type="tel" inputmode="tel" pattern="[0-9+\-\s()]+"
               class="form-control form-control-solid @error('phone2') is-invalid @enderror"
               name="phone2" value="{{ old('phone2', $contactData?->phone2) }}"
               placeholder="{{ __('crm::contact.placeholders.phone') }}" maxlength="50"/>
        @error('phone2')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="source" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.source') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="source" class="form-select form-select-solid @error('source') is-invalid @enderror"
                data-control="select2" data-placeholder="{{ __('crm::contact.fields.select_source') }}"
                data-allow-clear="true" name="source">
            <option value=""></option>
            @foreach(\Modules\CRM\Models\Contact::SOURCES as $source)
                <option value="{{ $source }}" @selected(old('source', $contactData?->source) === $source)>
                    {{ __('crm::lead.sources.' . $source) }}
                </option>
            @endforeach
        </select>
        @error('source')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="job_title" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.job_title') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="job_title" type="text" class="form-control form-control-solid @error('job_title') is-invalid @enderror"
               name="job_title" value="{{ old('job_title', $contactData?->job_title) }}"
               placeholder="{{ __('crm::contact.placeholders.job_title') }}" maxlength="100"/>
        @error('job_title')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.company') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::contact.sections.company_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="company_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.company') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="company_id" class="form-select form-select-solid @error('company_id') is-invalid @enderror"
                data-control="select2"
                data-placeholder="{{ __('crm::contact.fields.select_company') }}"
                data-allow-clear="true"
                name="company_id">
            <option value=""></option>
            @foreach(($companies ?? collect()) as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $contactData?->company_id ?? request('company_id')) === $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::contact.hints.company') }}</div>
        @error('company_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="user_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.customer') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="user_id" class="form-select form-select-solid @error('user_id') is-invalid @enderror"
                data-control="select2"
                data-placeholder="{{ __('crm::contact.fields.select_customer') }}"
                data-allow-clear="true"
                name="user_id">
            <option value=""></option>
            @foreach(($customers ?? collect()) as $customer)
                <option value="{{ $customer->id }}" @selected((int) old('user_id', $contactData?->user_id) === $customer->id)>
                    {{ $customer->name }}@if($customer->email) ({{ $customer->email }})@endif
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('crm::contact.hints.customer') }}</div>
        @error('user_id')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.additional') }}</h4>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="notes" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.notes') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="notes" class="form-control form-control-solid @error('notes') is-invalid @enderror"
                  name="notes" rows="4"
                  placeholder="{{ __('crm::contact.placeholders.notes') }}">{{ old('notes', $contactData?->notes) }}</textarea>
        @error('notes')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.is_primary') }}</div>
    </div>
    <div class="col-xl-9 fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="is_primary"
                   @checked(old('is_primary', $contactData?->is_primary))/>
            <label class="form-check-label text-muted" for="is_primary">
                {{ __('crm::contact.hints.is_primary') }}
            </label>
        </div>
        @error('is_primary')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
