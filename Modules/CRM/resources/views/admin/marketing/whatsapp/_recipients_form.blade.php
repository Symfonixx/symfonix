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
    <h4 class="fw-bold mb-2">{{ __('crm::whatsapp.sections.recipients') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::whatsapp.sections.recipients_hint') }}</p>
</div>

@error('recipients')
<div class="alert alert-danger mb-8">{{ $message }}</div>
@enderror

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::whatsapp.fields.leads') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_leads" value="1" id="all_leads" @checked(old('all_leads'))/>
            <label class="form-check-label" for="all_leads">
                <span class="fw-semibold">{{ __('crm::whatsapp.fields.all_leads') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::whatsapp.hints.all_leads') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="lead_ids" class="form-label fw-semibold">{{ __('crm::whatsapp.fields.select_leads') }}</label>
            <select id="lead_ids" name="lead_ids[]"
                    class="form-select form-select-solid @error('lead_ids') is-invalid @enderror"
                    multiple data-control="select2"
                    data-placeholder="{{ __('crm::whatsapp.placeholders.select_leads') }}"
                    data-close-on-select="false">
                @foreach(($leads ?? collect()) as $lead)
                    <option value="{{ $lead->id }}" @selected(collect(old('lead_ids', []))->contains($lead->id))>
                        {{ $lead->name }} — {{ $lead->phone }}
                    </option>
                @endforeach
            </select>
            @error('lead_ids')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::whatsapp.fields.contacts') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_contacts" value="1" id="all_contacts" @checked(old('all_contacts'))/>
            <label class="form-check-label" for="all_contacts">
                <span class="fw-semibold">{{ __('crm::whatsapp.fields.all_contacts') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::whatsapp.hints.all_contacts') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="contact_ids" class="form-label fw-semibold">{{ __('crm::whatsapp.fields.select_contacts') }}</label>
            <select id="contact_ids" name="contact_ids[]"
                    class="form-select form-select-solid @error('contact_ids') is-invalid @enderror"
                    multiple data-control="select2"
                    data-placeholder="{{ __('crm::whatsapp.placeholders.select_contacts') }}"
                    data-close-on-select="false">
                @foreach(($contacts ?? collect()) as $contact)
                    <option value="{{ $contact->id }}" @selected(collect(old('contact_ids', []))->contains($contact->id))>
                        {{ $contact->name }} — {{ $contact->phone ?? $contact->phone2 }}
                    </option>
                @endforeach
            </select>
            @error('contact_ids')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::whatsapp.fields.deals') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_deals" value="1" id="all_deals" @checked(old('all_deals'))/>
            <label class="form-check-label" for="all_deals">
                <span class="fw-semibold">{{ __('crm::whatsapp.fields.all_deals') }}</span>
                <span class="d-block text-muted fs-7">{{ __('crm::whatsapp.hints.all_deals') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="deal_ids" class="form-label fw-semibold">{{ __('crm::whatsapp.fields.select_deals') }}</label>
            <select id="deal_ids" name="deal_ids[]"
                    class="form-select form-select-solid @error('deal_ids') is-invalid @enderror"
                    multiple data-control="select2"
                    data-placeholder="{{ __('crm::whatsapp.placeholders.select_deals') }}"
                    data-close-on-select="false">
                @foreach(($deals ?? collect()) as $deal)
                    @php $phone = $deal->lead?->phone ?? $deal->company?->phone; @endphp
                    @if($phone)
                        <option value="{{ $deal->id }}" @selected(collect(old('deal_ids', []))->contains($deal->id))>
                            {{ $deal->title }} — {{ $phone }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('deal_ids')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::whatsapp.fields.contact_forms') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-check-custom form-check-solid mb-5">
            <input class="form-check-input" type="checkbox" name="all_contact_forms" value="1" id="all_contact_forms" @checked(old('all_contact_forms'))/>
            <label class="form-check-label" for="all_contact_forms">
                <span class="fw-semibold">{{ __('crm::whatsapp.fields.all_contact_forms') }}</span>
            </label>
        </div>
        <div class="fv-row">
            <label for="contact_form_ids" class="form-label fw-semibold">{{ __('crm::whatsapp.fields.select_contact_forms') }}</label>
            <select id="contact_form_ids" name="contact_form_ids[]"
                    class="form-select form-select-solid"
                    multiple data-control="select2"
                    data-placeholder="{{ __('crm::whatsapp.placeholders.select_contact_forms') }}"
                    data-close-on-select="false">
                @foreach(($contactForms ?? collect()) as $form)
                    <option value="{{ $form->id }}" @selected(collect(old('contact_form_ids', []))->contains($form->id))>
                        {{ $form->name }} — {{ $form->mobile }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="card card-bordered mb-8">
    <div class="card-header min-h-50px">
        <h5 class="card-title mb-0">{{ __('crm::whatsapp.fields.custom_phones') }}</h5>
    </div>
    <div class="card-body">
        <input id="custom_phones" class="form-control form-control-solid @error('custom_phones') is-invalid @enderror"
               name="custom_phones" value="{{ old('custom_phones') }}"
               placeholder="{{ __('crm::whatsapp.placeholders.custom_phones') }}"/>
        <div class="form-text">{{ __('crm::whatsapp.hints.custom_phones') }}</div>
        @error('custom_phones')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>
