<div class="mb-10">
    <h4 class="fw-bold mb-2">{{ __('crm::whatsapp.sections.template') }}</h4>
    <p class="text-muted mb-0">{{ __('crm::whatsapp.sections.template_hint') }}</p>
</div>

@if(($templates ?? collect())->isEmpty())
    <div class="alert alert-info d-flex align-items-center p-5">
        <i class="bi bi-info-circle fs-2hx text-info me-4"></i>
        <div>
            <p class="mb-2">{{ __('crm::whatsapp.messages.no_templates') }}</p>
            <x-can perform="marketing.whatsapp_templates.create">
                <a href="{{ route('admin.crm.marketing.whatsapp-templates.create') }}" class="btn btn-sm btn-primary">
                    {{ __('crm::whatsapp.actions.create_template') }}
                </a>
            </x-can>
        </div>
    </div>
@else
    <div class="row mb-8">
        <div class="col-xl-3">
            <label for="whatsapp_template_id" class="fs-6 fw-bold mt-2 mb-3 required">{{ __('crm::whatsapp.fields.template') }}</label>
        </div>
        <div class="col-xl-9 fv-row">
            <select id="whatsapp_template_id" name="whatsapp_template_id" required
                    class="form-select form-select-solid @error('whatsapp_template_id') is-invalid @enderror"
                    data-control="select2"
                    data-placeholder="{{ __('crm::whatsapp.placeholders.select_template') }}">
                <option value=""></option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('whatsapp_template_id') == $template->id)>
                        {{ $template->displayName() }} — {{ __('crm::whatsapp.categories.'.$template->category) }}
                    </option>
                @endforeach
            </select>
            @error('whatsapp_template_id')
            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
            @enderror
            <div class="form-text">
                {{ __('crm::whatsapp.hints.manage_templates') }}
                <a href="{{ route('admin.crm.marketing.whatsapp-templates.index') }}">{{ __('crm::whatsapp.actions.manage_templates') }}</a>
            </div>
        </div>
    </div>
@endif
