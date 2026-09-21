@if (\Illuminate\Support\Facades\Route::has('admin.ai.content.generate-quote'))
    <div class="alert alert-light-primary d-flex flex-wrap align-items-center justify-content-between gap-3 mb-8">
        <div>
            <div class="fw-bold">{{ __('crm::quote.ai.banner_title') }}</div>
            <div class="text-muted fs-7">{{ __('crm::quote.ai.banner_hint') }}</div>
        </div>
        <button type="button"
                class="btn btn-sm btn-info ai-generate-quote-trigger"
                title="{{ __('crm::quote.ai.tooltip') }}" data-action="ai">
            <i class="bi bi-stars me-1"></i>{{ __('crm::quote.ai.button') }}
        </button>
    </div>

    @once
        @push('scripts')
            <style>
                #ai-quote-modal { z-index: 200000; }
                .modal-backdrop.ai-quote-backdrop { z-index: 199990; }
            </style>
            <div class="modal fade" id="ai-quote-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-stars text-primary me-2"></i>{{ __('crm::quote.ai.modal_title') }}
                            </h5>
                            <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                        </div>
                        <div class="modal-body">
                            <div id="ai-quote-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                            <div id="ai-quote-context" class="alert alert-light-info d-none mb-4 fs-7"></div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold" for="ai-quote-prompt">{{ __('crm::quote.ai.prompt_label') }}</label>
                                <textarea id="ai-quote-prompt" class="form-control form-control-solid" rows="4"
                                          placeholder="{{ __('crm::quote.ai.prompt_placeholder') }}"></textarea>
                            </div>
                            <div id="ai-quote-loading" class="d-none text-muted fs-7 mt-4">
                                <span class="spinner-border spinner-border-sm text-primary me-2"></span>{{ __('crm::quote.ai.generating') }}
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('Close') }}</button>
                            <button type="button" id="ai-quote-generate-btn" class="btn btn-info" data-action="ai">
                                <i class="bi bi-stars me-1"></i>{{ __('crm::quote.ai.generate') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.SymfonixAIQuoteFill = (function () {
                    var routeUrl = @json(route('admin.ai.content.generate-quote'));
                    var locale = @json(app()->getLocale());
                    var i18n = {
                        genericError: @json(__('The AI content generation request failed. Please try again.')),
                        notAvailable: @json(__('The AI content generator is not available right now.')),
                        applied: @json(__('crm::quote.ai.applied')),
                        overwrite: @json(__('crm::quote.ai.overwrite_confirm')),
                        companyRequired: @json(__('crm::quote.ai.company_required')),
                        dealRequired: @json(__('crm::quote.ai.deal_required')),
                        contextLabel: @json(__('crm::quote.ai.context_label')),
                    };
                    var modal = null;

                    function csrfToken() {
                        var meta = document.querySelector('meta[name="csrf-token"]');
                        return meta ? meta.getAttribute('content') : '';
                    }

                    function quoteForm() {
                        var company = document.getElementById('quote-company');
                        return company ? company.closest('form') : null;
                    }

                    function selectedLabel(select) {
                        if (!select || !select.value) {
                            return '';
                        }
                        var option = select.selectedOptions && select.selectedOptions[0];
                        return option ? String(option.textContent || '').trim() : '';
                    }

                    function getModal() {
                        var el = document.getElementById('ai-quote-modal');
                        if (!el) {
                            return null;
                        }

                        if (!modal) {
                            if (el.parentElement !== document.body) {
                                document.body.appendChild(el);
                            }

                            el.addEventListener('shown.bs.modal', function () {
                                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                                    backdrop.classList.add('ai-quote-backdrop');
                                });
                                var prompt = document.getElementById('ai-quote-prompt');
                                if (prompt) {
                                    prompt.focus();
                                }
                            });

                            modal = bootstrap.Modal.getOrCreateInstance(el);
                        }

                        return modal;
                    }

                    function showError(message) {
                        var alertEl = document.getElementById('ai-quote-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = message || i18n.genericError;
                        alertEl.classList.remove('d-none');
                    }

                    function hideError() {
                        var alertEl = document.getElementById('ai-quote-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = '';
                        alertEl.classList.add('d-none');
                    }

                    function formHasContent(form) {
                        var terms = form.querySelector('[name="terms"]');
                        var notes = form.querySelector('[name="notes"]');
                        if ((terms && terms.value.trim()) || (notes && notes.value.trim())) {
                            return true;
                        }

                        return Array.from(form.querySelectorAll('.quote-line-row')).some(function (row) {
                            var service = row.querySelector('.quote-service-select');
                            var product = row.querySelector('.quote-product-select');
                            var price = row.querySelector('.quote-unit-price');
                            var description = row.querySelector('[name*="[description]"]');

                            return (service && service.value) || (product && product.value)
                                || (price && String(price.value).trim() !== '')
                                || (description && description.value.trim());
                        });
                    }

                    function open() {
                        var instance = getModal();
                        if (!instance) {
                            return;
                        }

                        hideError();
                        document.getElementById('ai-quote-loading').classList.add('d-none');
                        document.getElementById('ai-quote-generate-btn').disabled = false;

                        var company = document.getElementById('quote-company');
                        var deal = document.getElementById('quote-deal');
                        var context = document.getElementById('ai-quote-context');
                        var companyLabel = selectedLabel(company);
                        var dealLabel = selectedLabel(deal);

                        if (context) {
                            if (companyLabel || dealLabel) {
                                context.textContent = i18n.contextLabel + ': '
                                    + [companyLabel, dealLabel].filter(Boolean).join(' · ');
                                context.classList.remove('d-none');
                            } else {
                                context.textContent = '';
                                context.classList.add('d-none');
                            }
                        }

                        if (!routeUrl) {
                            showError(i18n.notAvailable);
                        }

                        instance.show();
                    }

                    document.addEventListener('click', function (event) {
                        var trigger = event.target.closest('.ai-generate-quote-trigger');
                        if (trigger) {
                            open();
                            return;
                        }

                        if (!event.target.closest('#ai-quote-generate-btn')) {
                            return;
                        }

                        var form = quoteForm();
                        var company = document.getElementById('quote-company');
                        var deal = document.getElementById('quote-deal');

                        if (!routeUrl || !form) {
                            showError(i18n.notAvailable);
                            return;
                        }

                        if (!company || !company.value) {
                            showError(i18n.companyRequired);
                            return;
                        }

                        if (!deal || !deal.value) {
                            showError(i18n.dealRequired);
                            return;
                        }

                        if (formHasContent(form) && !window.confirm(i18n.overwrite)) {
                            return;
                        }

                        hideError();
                        document.getElementById('ai-quote-loading').classList.remove('d-none');
                        document.getElementById('ai-quote-generate-btn').disabled = true;

                        fetch(routeUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                company_id: Number(company.value),
                                deal_id: Number(deal.value),
                                prompt: (document.getElementById('ai-quote-prompt').value || '').trim(),
                                locale: locale,
                            }),
                        })
                            .then(function (response) {
                                return response.json().catch(function () {
                                    return null;
                                }).then(function (json) {
                                    return { ok: response.ok, json: json };
                                });
                            })
                            .then(function (result) {
                                document.getElementById('ai-quote-loading').classList.add('d-none');
                                document.getElementById('ai-quote-generate-btn').disabled = false;

                                var json = result && result.json;
                                if (!result || !result.ok || !json || !json.success || !json.fields) {
                                    var message = (json && (json.error || (json.errors && Object.values(json.errors)[0][0]))) || i18n.genericError;
                                    showError(message);
                                    return;
                                }

                                if (window.SymfonixQuoteForm && typeof window.SymfonixQuoteForm.applyAiQuote === 'function') {
                                    window.SymfonixQuoteForm.applyAiQuote(json.fields);
                                }

                                getModal().hide();

                                if (window.toastr && typeof window.toastr.success === 'function') {
                                    window.toastr.success(i18n.applied);
                                }
                            })
                            .catch(function () {
                                document.getElementById('ai-quote-loading').classList.add('d-none');
                                document.getElementById('ai-quote-generate-btn').disabled = false;
                                showError(i18n.genericError);
                            });
                    });

                    return { open: open };
                })();
            </script>
        @endpush
    @endonce
@endif
