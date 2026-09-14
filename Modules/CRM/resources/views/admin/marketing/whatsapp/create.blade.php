@section('title', __('crm::whatsapp.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => __('crm::whatsapp.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::whatsapp.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.crm.marketing.index', ['channel' => 'whatsapp']) }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.querySelector('#custom_phones');
            if (input && typeof Tagify !== 'undefined') {
                new Tagify(input, {
                    pattern: /^\+?[0-9\s\-()]{8,20}$/,
                    delimiters: ',| ',
                    dropdown: {enabled: 0},
                    editTags: {clicks: 1, keepInvalid: false},
                });
            }

            var wizard = document.getElementById('whatsapp-wizard');
            if (!wizard) return;

            var steps = wizard.querySelectorAll('.wizard-step');
            var stepIndicators = wizard.querySelectorAll('.wizard-step-indicator');
            var btnPrev = document.getElementById('wizard-prev');
            var btnNext = document.getElementById('wizard-next');
            var btnSubmit = document.getElementById('wizard-submit');
            var currentStep = 0;

            var templateSelect = document.getElementById('whatsapp_template_id');
            var paramsContainer = document.getElementById('template-parameters-container');
            var previewContainer = document.getElementById('message-preview');
            var variablesUrl = @json(route('admin.crm.marketing.whatsapp.templates.variables', ['template' => '__ID__']));

            function showStep(index) {
                steps.forEach(function (step, i) {
                    step.classList.toggle('d-none', i !== index);
                });
                stepIndicators.forEach(function (indicator, i) {
                    indicator.classList.toggle('active', i <= index);
                    indicator.classList.toggle('completed', i < index);
                });
                btnPrev.classList.toggle('d-none', index === 0);
                btnNext.classList.toggle('d-none', index === steps.length - 1);
                btnSubmit.classList.toggle('d-none', index !== steps.length - 1);
                currentStep = index;
                if (index === 3) updatePreview();
            }

            function validateStep(index) {
                var step = steps[index];
                var inputs = step.querySelectorAll('[required]');
                var valid = true;
                inputs.forEach(function (input) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        valid = false;
                    }
                });
                if (index === 0) {
                    var hasRecipient = ['all_leads','all_contacts','all_deals','all_contact_forms'].some(function (name) {
                        var el = step.querySelector('[name="'+name+'"]');
                        return el && el.checked;
                    }) || ['lead_ids','contact_ids','deal_ids','contact_form_ids'].some(function (name) {
                        var el = step.querySelector('[name="'+name+'[]"]');
                        return el && el.selectedOptions && el.selectedOptions.length > 0;
                    }) || (document.getElementById('custom_phones') && document.getElementById('custom_phones').value.trim() !== '');
                    if (!hasRecipient) {
                        alert(@json(__('crm::whatsapp.validation.select_recipients')));
                        valid = false;
                    }
                }
                return valid;
            }

            function loadTemplateVariables(templateId) {
                if (!templateId) {
                    paramsContainer.innerHTML = '<p class="text-muted">' + @json(__('crm::whatsapp.hints.select_template_first')) + '</p>';
                    return;
                }
                var url = variablesUrl.replace('__ID__', templateId);
                fetch(url, {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}})
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        paramsContainer.innerHTML = '';
                        if (!data.variables || data.variables.length === 0) {
                            paramsContainer.innerHTML = '<p class="text-muted">' + @json(__('crm::whatsapp.hints.no_variables')) + '</p>';
                            return;
                        }
                        data.variables.forEach(function (v) {
                            var oldVal = @json(old('template_parameters', []));
                            var val = oldVal[v.index] || '';
                            var row = document.createElement('div');
                            row.className = 'row mb-6';
                            row.innerHTML = '<div class="col-xl-3"><label class="fs-6 fw-bold mt-2 mb-3 required">' +
                                @json(__('crm::whatsapp.fields.variable')) + ' ' + v.placeholder + '</label></div>' +
                                '<div class="col-xl-9 fv-row"><input type="text" name="template_parameters[' + v.index + ']" ' +
                                'class="form-control form-control-solid template-param-input" required ' +
                                'data-index="' + v.index + '" value="' + val.replace(/"/g, '&quot;') + '" ' +
                                'placeholder="' + @json(__('crm::whatsapp.placeholders.variable_value')) + ' ' + v.index + '"></div>';
                            paramsContainer.appendChild(row);
                        });
                        wizard.dataset.templateBody = data.body;
                        wizard.dataset.templateHeader = data.header_content || '';
                        wizard.dataset.templateFooter = data.footer || '';
                    });
            }

            function updatePreview() {
                var body = wizard.dataset.templateBody || '';
                var header = wizard.dataset.templateHeader || '';
                var footer = wizard.dataset.templateFooter || '';
                var preview = '';
                if (header) preview += header + '\n\n';
                document.querySelectorAll('.template-param-input').forEach(function (input) {
                    var idx = input.dataset.index;
                    body = body.split('{{' + idx + '}}').join(input.value || '{{' + idx + '}}');
                    if (header) header = header.split('{{' + idx + '}}').join(input.value || '{{' + idx + '}}');
                });
                preview = (header ? header + '\n\n' : '') + body;
                if (footer) preview += '\n\n' + footer;
                previewContainer.textContent = preview;
            }

            btnPrev.addEventListener('click', function () { if (currentStep > 0) showStep(currentStep - 1); });
            btnNext.addEventListener('click', function () {
                if (validateStep(currentStep)) {
                    if (currentStep === 1 && templateSelect.value) loadTemplateVariables(templateSelect.value);
                    showStep(currentStep + 1);
                }
            });

            templateSelect.addEventListener('change', function () {
                loadTemplateVariables(this.value);
            });

            if (templateSelect.value) loadTemplateVariables(templateSelect.value);

            showStep(0);
        });
    </script>
@endsection

<x-admin-layout>
    @if(!$whatsappConfigured)
        <div class="alert alert-warning d-flex align-items-center p-5 mb-8">
            <i class="bi bi-exclamation-triangle-fill fs-2hx text-warning me-4"></i>
            <div>
                <h5 class="mb-1">{{ __('crm::whatsapp.messages.not_configured') }}</h5>
                <p class="mb-0">{{ __('crm::whatsapp.hints.configure_whatsapp') }}
                    <a href="{{ route('admin.integrations.index') }}">{{ __('crm::whatsapp.actions.open_integrations') }}</a>
                </p>
            </div>
        </div>
    @endif

    <div class="card settings-form-card" id="whatsapp-wizard">
        <div class="card-header border-0 pt-6">
            <div class="card-title d-flex align-items-center gap-3">
                <span class="sx-form-icon bg-light-success text-success">
                    <i class="bi bi-whatsapp"></i>
                </span>
                <div>
                    <h2 class="fw-bold mb-1">{{ __('crm::whatsapp.pages.create_title') }}</h2>
                    <span class="text-muted fs-7 fw-semibold">{{ __('crm::whatsapp.sections.compose_hint') }}</span>
                </div>
            </div>
        </div>

        <div class="card-body pt-2">
            <div class="d-flex justify-content-between mb-10 px-4">
                @foreach([
                    __('crm::whatsapp.steps.recipients'),
                    __('crm::whatsapp.steps.template'),
                    __('crm::whatsapp.steps.parameters'),
                    __('crm::whatsapp.steps.preview'),
                ] as $i => $label)
                    <div class="wizard-step-indicator text-center flex-fill {{ $i === 0 ? 'active' : '' }}" data-step="{{ $i }}">
                        <div class="rounded-circle bg-light-primary text-primary fw-bold d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:36px;height:36px;">{{ $i + 1 }}</div>
                        <div class="fs-7 fw-semibold text-muted">{{ $label }}</div>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.crm.marketing.whatsapp.store') }}" id="whatsapp-campaign-form">
                @csrf

                <div class="wizard-step">
                    @include('crm::admin.marketing.whatsapp._recipients_form')
                </div>

                <div class="wizard-step d-none">
                    @include('crm::admin.marketing.whatsapp._template_select')
                </div>

                <div class="wizard-step d-none">
                    <div class="mb-10">
                        <h4 class="fw-bold mb-2">{{ __('crm::whatsapp.sections.parameters') }}</h4>
                        <p class="text-muted mb-0">{{ __('crm::whatsapp.sections.parameters_hint') }}</p>
                    </div>
                    <div id="template-parameters-container">
                        <p class="text-muted">{{ __('crm::whatsapp.hints.select_template_first') }}</p>
                    </div>
                </div>

                <div class="wizard-step d-none">
                    <div class="mb-10">
                        <h4 class="fw-bold mb-2">{{ __('crm::whatsapp.sections.preview') }}</h4>
                        <p class="text-muted mb-0">{{ __('crm::whatsapp.sections.preview_hint') }}</p>
                    </div>
                    <div class="bg-light-success bg-opacity-10 border border-success border-dashed rounded p-6">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-whatsapp text-success fs-2"></i>
                            <pre id="message-preview" class="mb-0 fs-6 text-gray-800" style="white-space:pre-wrap;font-family:inherit;"></pre>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-footer settings-form-footer d-flex justify-content-between align-items-center py-5 px-9">
            <span class="text-muted fs-7">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ __('crm::whatsapp.hints.send_warning') }}
            </span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.crm.marketing.index', ['channel' => 'whatsapp']) }}" class="btn btn-light btn-active-light-primary">
                    <i class="bi bi-x-lg me-1"></i>{{ __('Discard') }}
                </a>
                <button type="button" id="wizard-prev" class="btn btn-light btn-active-light-primary d-none">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('crm::whatsapp.actions.previous') }}
                </button>
                <button type="button" id="wizard-next" class="btn btn-primary">
                    {{ __('crm::whatsapp.actions.next') }} <i class="bi bi-arrow-right ms-1"></i>
                </button>
                <button type="submit" form="whatsapp-campaign-form" id="wizard-submit" class="btn btn-success d-none" {{ !$whatsappConfigured ? 'disabled' : '' }}>
                    <i class="bi bi-send me-1"></i>{{ __('crm::whatsapp.actions.send_campaign') }}
                </button>
            </div>
        </div>
    </div>
</x-admin-layout>
