@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start p-5 mb-10">
        <i class="bi bi-exclamation-triangle-fill fs-2hx text-danger me-4 mt-1"></i>
        <div>
            <h5 class="mb-2">{{ __('crm::marketing.validation.fix_errors') }}</h5>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::marketing.sections.compose') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::marketing.sections.compose_hint') }}</p>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="subject" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.subject') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="subject" class="form-control form-control-solid @error('subject') is-invalid @enderror"
                  name="subject" rows="3"
                  placeholder="{{ __('crm::marketing.placeholders.subject') }}" autofocus>{!! old('subject') !!}</textarea>
        @error('subject')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="body" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.body') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="body" class="form-control form-control-solid @error('body') is-invalid @enderror"
                  name="body" rows="8"
                  placeholder="{{ __('crm::marketing.placeholders.body') }}">{!! old('body') !!}</textarea>
        @error('body')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="separator my-10"></div>

<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::marketing.sections.recipients') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::marketing.sections.recipients_hint') }}</p>
</div>

@error('recipients')
<div class="alert alert-danger mb-8">{{ $message }}</div>
@enderror

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::marketing.fields.subscribers') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_subscribers" value="1" id="all_subscribers"
                   @checked(old('all_subscribers'))/>
            <label class="form-check-label" for="all_subscribers">
                <span class="fw-semibold">{{ __('crm::marketing.fields.all_subscribers') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::marketing.hints.all_subscribers') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="subscriber_ids" class="form-label fw-semibold">{{ __('crm::marketing.fields.select_subscribers') }}</label>
            <select id="subscriber_ids" name="subscriber_ids[]"
                    class="form-select form-select-solid @error('subscriber_ids') is-invalid @enderror"
                    multiple
                    data-control="select2"
                    data-placeholder="{{ __('crm::marketing.placeholders.select_subscribers') }}"
                    data-close-on-select="false">
                @foreach(($subscribers ?? collect()) as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(collect(old('subscriber_ids', []))->contains($subscriber->id))>
                        {{ $subscriber->email }}
                    </option>
                @endforeach
            </select>
            @error('subscriber_ids')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::marketing.fields.contacts') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_contacts" value="1" id="all_contacts"
                   @checked(old('all_contacts'))/>
            <label class="form-check-label" for="all_contacts">
                <span class="fw-semibold">{{ __('crm::marketing.fields.all_contacts') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::marketing.hints.all_contacts') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="contact_ids" class="form-label fw-semibold">{{ __('crm::marketing.fields.select_contacts') }}</label>
            <select id="contact_ids" name="contact_ids[]"
                    class="form-select form-select-solid @error('contact_ids') is-invalid @enderror"
                    multiple
                    data-control="select2"
                    data-placeholder="{{ __('crm::marketing.placeholders.select_contacts') }}"
                    data-close-on-select="false">
                @foreach(($contacts ?? collect()) as $contact)
                    <option value="{{ $contact->id }}" @selected(collect(old('contact_ids', []))->contains($contact->id))>
                        {{ $contact->name }} &lt;{{ $contact->email }}&gt;
                    </option>
                @endforeach
            </select>
            @error('contact_ids')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::marketing.fields.contact_forms') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_contact_forms" value="1" id="all_contact_forms"
                   @checked(old('all_contact_forms'))/>
            <label class="form-check-label" for="all_contact_forms">
                <span class="fw-semibold">{{ __('crm::marketing.fields.all_contact_forms') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::marketing.hints.all_contact_forms') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="contact_form_ids" class="form-label fw-semibold">{{ __('crm::marketing.fields.select_contact_forms') }}</label>
            <select id="contact_form_ids" name="contact_form_ids[]"
                    class="form-select form-select-solid @error('contact_form_ids') is-invalid @enderror"
                    multiple
                    data-control="select2"
                    data-placeholder="{{ __('crm::marketing.placeholders.select_contact_forms') }}"
                    data-close-on-select="false">
                @foreach(($contactForms ?? collect()) as $contactForm)
                    <option value="{{ $contactForm->id }}" @selected(collect(old('contact_form_ids', []))->contains($contactForm->id))>
                        {{ $contactForm->name }} &lt;{{ $contactForm->email }}&gt;
                        @if($contactForm->subject) — {{ Str::limit($contactForm->subject, 40) }} @endif
                    </option>
                @endforeach
            </select>
            @error('contact_form_ids')
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::marketing.fields.custom_emails') }}</h5>
    </div>
    <div class="card-body">
        <input id="custom_emails" class="form-control form-control-solid @error('custom_emails') is-invalid @enderror"
               name="custom_emails" value="{{ old('custom_emails') }}"
               placeholder="{{ __('crm::marketing.placeholders.custom_emails') }}"/>
        <div class="form-text">{{ __('crm::marketing.hints.custom_emails') }}</div>
        @error('custom_emails')
        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
