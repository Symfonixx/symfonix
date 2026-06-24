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
    <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.contact_information') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::contact.sections.contact_information_hint') }}</p>
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
        <label for="email" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::contact.fields.email') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="email" type="email" class="form-control form-control-solid @error('email') is-invalid @enderror"
               name="email" value="{{ old('email', $contactData?->email) }}"
               placeholder="{{ __('crm::contact.placeholders.email') }}" maxlength="255" required/>
        @error('email')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="mobile" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.mobile') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="mobile" type="text" class="form-control form-control-solid @error('mobile') is-invalid @enderror"
               name="mobile" value="{{ old('mobile', $contactData?->mobile) }}"
               placeholder="{{ __('crm::contact.placeholders.mobile') }}" maxlength="255"/>
        @error('mobile')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="subject" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.subject') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input id="subject" type="text" class="form-control form-control-solid @error('subject') is-invalid @enderror"
               name="subject" value="{{ old('subject', $contactData?->subject) }}"
               placeholder="{{ __('crm::contact.placeholders.subject') }}" maxlength="255"/>
        @error('subject')
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

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.message') }}</h4>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="message" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::contact.fields.message') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="message" class="form-control form-control-solid @error('message') is-invalid @enderror"
                  name="message" rows="5" required
                  placeholder="{{ __('crm::contact.placeholders.message') }}">{{ old('message', $contactData?->message) }}</textarea>
        @error('message')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

@if($contactData)
    <div class="separator my-10"></div>

    <div class="mb-10">
        <h4 class="fw-bold mb-2">{{ __('crm::contact.sections.status') }}</h4>
    </div>

    <div class="row mb-8">
        <div class="col-xl-3">
            <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.blocked') }}</div>
        </div>
        <div class="col-xl-9 fv-row">
            <div class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="blocked" value="1" id="blocked"
                       @checked(old('blocked', $contactData?->blocked))/>
                <label class="form-check-label text-muted" for="blocked">
                    {{ __('crm::contact.hints.blocked') }}
                </label>
            </div>
            @error('blocked')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    @if($contactData->ip_address)
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::contact.fields.ip_address') }}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <span class="text-muted">{{ $contactData->ip_address }}</span>
            </div>
        </div>
    @endif
@endif
