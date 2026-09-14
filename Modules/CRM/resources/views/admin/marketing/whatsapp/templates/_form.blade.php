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

@php $tpl = $template ?? null; @endphp

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="name" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.name') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="name" name="name" required
               class="form-control form-control-solid @error('name') is-invalid @enderror"
               value="{{ old('name', $tpl?->name) }}"
               placeholder="{{ __('crm::whatsapp.placeholders.template_name') }}"/>
        <div class="form-text">{{ __('crm::whatsapp.hints.template_name') }}</div>
        @error('name')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="language" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.language') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="language" name="language" required maxlength="10"
               class="form-control form-control-solid @error('language') is-invalid @enderror"
               value="{{ old('language', $tpl?->language ?? 'en') }}"
               placeholder="en"/>
        @error('language')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="category" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.category') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="category" name="category" required class="form-select form-select-solid">
            @foreach(['MARKETING', 'UTILITY', 'AUTHENTICATION'] as $cat)
                <option value="{{ $cat }}" @selected(old('category', $tpl?->category ?? 'MARKETING') === $cat)>
                    {{ __('crm::whatsapp.categories.'.$cat) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="status" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::marketing.fields.status') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="status" name="status" required class="form-select form-select-solid">
            @foreach(['approved', 'pending', 'rejected', 'draft'] as $st)
                <option value="{{ $st }}" @selected(old('status', $tpl?->status ?? 'approved') === $st)>
                    {{ __('crm::whatsapp.template_status.'.$st) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="separator my-10"></div>
<h4 class="fw-bold mb-6">{{ __('crm::whatsapp.sections.header') }}</h4>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="header_type" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.header_type') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <select id="header_type" name="header_type" required class="form-select form-select-solid">
            @foreach(['none', 'text', 'image', 'video', 'document'] as $ht)
                <option value="{{ $ht }}" @selected(old('header_type', $tpl?->header_type ?? 'none') === $ht)>
                    {{ __('crm::whatsapp.header_types.'.$ht) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="header_content" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::whatsapp.fields.header_content') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="header_content" name="header_content"
               class="form-control form-control-solid"
               value="{{ old('header_content', $tpl?->header_content) }}"
               placeholder="{{ __('crm::whatsapp.placeholders.header_content') }}"/>
        <div class="form-text">{{ __('crm::whatsapp.hints.header_content') }}</div>
    </div>
</div>

<div class="separator my-10"></div>
<h4 class="fw-bold mb-6">{{ __('crm::whatsapp.sections.body') }}</h4>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="body" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.body') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <textarea id="body" name="body" required rows="6"
                  class="form-control form-control-solid @error('body') is-invalid @enderror"
                  placeholder="{{ __('crm::whatsapp.placeholders.body') }}">{{ old('body', $tpl?->body) }}</textarea>
        <div class="form-text">{{ __('crm::whatsapp.hints.body_variables') }}</div>
        @error('body')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="footer" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::whatsapp.fields.footer') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="footer" name="footer" maxlength="60"
               class="form-control form-control-solid"
               value="{{ old('footer', $tpl?->footer) }}"
               placeholder="{{ __('crm::whatsapp.placeholders.footer') }}"/>
    </div>
</div>

<div class="separator my-10"></div>
<h4 class="fw-bold mb-6">{{ __('crm::whatsapp.sections.buttons') }}</h4>

<div id="buttons-container">
    @php
        $buttons = old('buttons', $tpl?->buttons ?? []);
        if (empty($buttons)) $buttons = [['type' => 'QUICK_REPLY', 'text' => '']];
    @endphp
    @foreach($buttons as $i => $button)
        <div class="card card-bordered mb-4 button-row">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('crm::whatsapp.fields.button_type') }}</label>
                        <select name="buttons[{{ $i }}][type]" class="form-select form-select-solid">
                            @foreach(['QUICK_REPLY', 'URL', 'PHONE_NUMBER'] as $bt)
                                <option value="{{ $bt }}" @selected(($button['type'] ?? '') === $bt)>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('crm::whatsapp.fields.button_text') }}</label>
                        <input type="text" name="buttons[{{ $i }}][text]" maxlength="25"
                               class="form-control form-control-solid"
                               value="{{ $button['text'] ?? '' }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('crm::whatsapp.fields.button_url') }}</label>
                        <input type="text" name="buttons[{{ $i }}][url]"
                               class="form-control form-control-solid"
                               value="{{ $button['url'] ?? '' }}"/>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row mb-8">
    <div class="col-xl-3"></div>
    <div class="col-xl-9">
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   @checked(old('is_active', $tpl?->is_active ?? true))/>
            <label class="form-check-label" for="is_active">{{ __('crm::whatsapp.fields.is_active') }}</label>
        </div>
    </div>
</div>

<div class="row mb-8">
    <div class="col-xl-3">
        <label for="meta_template_id" class="fs-6 fw-bold mt-2 mb-3">{{ __('crm::whatsapp.fields.meta_template_id') }}</label>
    </div>
    <div class="col-xl-9 fv-row">
        <input type="text" id="meta_template_id" name="meta_template_id"
               class="form-control form-control-solid"
               value="{{ old('meta_template_id', $tpl?->meta_template_id) }}"
               placeholder="{{ __('crm::whatsapp.placeholders.meta_template_id') }}"/>
    </div>
</div>
