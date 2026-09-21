@props([
    'lead',
])

@if (\Illuminate\Support\Facades\Route::has('admin.ai.leads.follow-up') && auth()->user()?->can('crm.activities.create'))
    <button type="button"
            class="btn btn-sm btn-light-info ai-lead-follow-up-trigger"
            data-ai-lead-id="{{ $lead->getKey() }}"
            title="{{ __('crm::lead.follow_up.tooltip') }}">
        <i class="bi bi-stars me-1"></i>{{ __('crm::lead.follow_up.button') }}
    </button>

    @once
        @push('scripts')
            <style>
                #ai-lead-follow-up-modal { z-index: 200000; }
                .modal-backdrop.ai-follow-up-backdrop { z-index: 199990; }
            </style>
            <div class="modal fade" id="ai-lead-follow-up-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-stars text-info me-2"></i>{{ __('crm::lead.follow_up.modal_title') }}
                            </h5>
                            <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                        </div>
                        <div class="modal-body">
                            <div id="ai-lead-follow-up-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                            <p class="text-muted fs-7 mb-4">{{ __('crm::lead.follow_up.hint') }}</p>
                            <div class="mb-5">
                                <label class="form-label fw-semibold" for="ai-lead-follow-up-prompt">{{ __('crm::lead.follow_up.prompt_label') }}</label>
                                <textarea id="ai-lead-follow-up-prompt" class="form-control form-control-solid" rows="3"
                                          placeholder="{{ __('crm::lead.follow_up.prompt_placeholder') }}"></textarea>
                            </div>
                            <div id="ai-lead-follow-up-loading" class="d-none text-muted fs-7 mb-4">
                                <span class="spinner-border spinner-border-sm text-info me-2"></span>{{ __('crm::lead.follow_up.generating') }}
                            </div>
                            <div id="ai-lead-follow-up-preview" class="d-none border border-dashed rounded p-5">
                                <div class="text-muted fs-8 text-uppercase mb-3">{{ __('crm::lead.follow_up.preview_title') }}</div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <span class="badge badge-light-info" id="ai-lead-follow-up-type-label"></span>
                                    <span class="text-muted fs-7" id="ai-lead-follow-up-scheduled-label"></span>
                                </div>
                                <div class="fw-bold text-gray-900 mb-2" id="ai-lead-follow-up-title"></div>
                                <div class="text-gray-700" id="ai-lead-follow-up-body" style="white-space: pre-wrap;"></div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('Cancel') }}</button>
                            <div class="d-flex gap-2">
                                <button type="button" id="ai-lead-follow-up-generate-btn" class="btn btn-info" data-action="ai">
                                    <i class="bi bi-stars me-1"></i>{{ __('crm::lead.follow_up.generate') }}
                                </button>
                                <button type="button" id="ai-lead-follow-up-fill-btn" class="btn btn-success d-none" data-action="create">
                                    {{ __('crm::lead.follow_up.fill_form') }}
                                </button>
                                <button type="button" id="ai-lead-follow-up-create-btn" class="btn btn-success d-none" data-action="create">
                                    <i class="bi bi-plus-lg me-1"></i>{{ __('crm::lead.follow_up.create') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function () {
                    var generateUrlTemplate = @json(url(route('admin.ai.leads.follow-up', ['lead' => '__ID__'], false)));
                    var storeUrl = @json(route('admin.activities.store'));
                    var locale = @json(app()->getLocale());
                    var typeLabels = @json(collect(\Modules\CRM\Models\CrmActivity::TYPES)->mapWithKeys(fn ($type) => [$type => __('crm::timeline.activity_types.'.$type)])->all());
                    var i18n = {
                        genericError: @json(__('The AI content generation request failed. Please try again.')),
                        notAvailable: @json(__('The AI content generator is not available right now.')),
                        applied: @json(__('crm::lead.follow_up.applied')),
                        created: @json(__('crm::lead.follow_up.created')),
                        scheduled: @json(__('crm::timeline.fields.scheduled_at')),
                    };
                    var modal = null;
                    var currentLeadId = null;
                    var currentFields = null;

                    function csrfToken() {
                        var meta = document.querySelector('meta[name="csrf-token"]');
                        return meta ? meta.getAttribute('content') : '';
                    }

                    function getModal() {
                        var el = document.getElementById('ai-lead-follow-up-modal');
                        if (!el) {
                            return null;
                        }

                        if (!modal) {
                            if (el.parentElement !== document.body) {
                                document.body.appendChild(el);
                            }

                            el.addEventListener('shown.bs.modal', function () {
                                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                                    backdrop.classList.add('ai-follow-up-backdrop');
                                });
                            });

                            modal = bootstrap.Modal.getOrCreateInstance(el);
                        }

                        return modal;
                    }

                    function showError(message) {
                        var alertEl = document.getElementById('ai-lead-follow-up-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = message || i18n.genericError;
                        alertEl.classList.remove('d-none');
                    }

                    function hideError() {
                        var alertEl = document.getElementById('ai-lead-follow-up-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = '';
                        alertEl.classList.add('d-none');
                    }

                    function activityForm() {
                        return document.querySelector('.js-crm-activity-form');
                    }

                    function resetPreview() {
                        currentFields = null;
                        document.getElementById('ai-lead-follow-up-preview').classList.add('d-none');
                        document.getElementById('ai-lead-follow-up-fill-btn').classList.add('d-none');
                        document.getElementById('ai-lead-follow-up-create-btn').classList.add('d-none');
                    }

                    function showPreview(fields) {
                        currentFields = fields;
                        document.getElementById('ai-lead-follow-up-type-label').textContent = typeLabels[fields.type] || fields.type;
                        document.getElementById('ai-lead-follow-up-scheduled-label').textContent = fields.scheduled_at
                            ? (i18n.scheduled + ': ' + fields.scheduled_at.replace('T', ' '))
                            : '';
                        document.getElementById('ai-lead-follow-up-title').textContent = fields.title || '';
                        document.getElementById('ai-lead-follow-up-body').textContent = fields.body || '';
                        document.getElementById('ai-lead-follow-up-preview').classList.remove('d-none');
                        document.getElementById('ai-lead-follow-up-create-btn').classList.remove('d-none');
                        if (activityForm()) {
                            document.getElementById('ai-lead-follow-up-fill-btn').classList.remove('d-none');
                        }
                    }

                    function fillForm(fields) {
                        var form = activityForm();
                        if (!form || !fields) {
                            return false;
                        }

                        var type = form.querySelector('[name="type"]');
                        var title = form.querySelector('[name="title"]');
                        var body = form.querySelector('[name="body"]');
                        var scheduled = form.querySelector('[name="scheduled_at"]');

                        if (type) type.value = fields.type || 'task';
                        if (title) title.value = fields.title || '';
                        if (body) body.value = fields.body || '';
                        if (scheduled) scheduled.value = fields.scheduled_at || '';

                        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return true;
                    }

                    function generateFollowUp() {
                        if (!currentLeadId) {
                            showError(i18n.notAvailable);
                            return;
                        }

                        hideError();
                        resetPreview();
                        document.getElementById('ai-lead-follow-up-loading').classList.remove('d-none');
                        document.getElementById('ai-lead-follow-up-generate-btn').disabled = true;

                        fetch(generateUrlTemplate.replace('__ID__', encodeURIComponent(currentLeadId)), {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                prompt: (document.getElementById('ai-lead-follow-up-prompt').value || '').trim(),
                                locale: locale,
                            }),
                        })
                            .then(function (response) {
                                return response.json().then(function (payload) {
                                    return { ok: response.ok, payload: payload };
                                });
                            })
                            .then(function (result) {
                                document.getElementById('ai-lead-follow-up-loading').classList.add('d-none');
                                document.getElementById('ai-lead-follow-up-generate-btn').disabled = false;

                                if (!result.ok || !result.payload || !result.payload.success) {
                                    showError(result.payload && (result.payload.error
                                        || (result.payload.errors && Object.values(result.payload.errors)[0][0])));
                                    return;
                                }

                                showPreview(result.payload.fields || {});
                            })
                            .catch(function () {
                                document.getElementById('ai-lead-follow-up-loading').classList.add('d-none');
                                document.getElementById('ai-lead-follow-up-generate-btn').disabled = false;
                                showError(i18n.genericError);
                            });
                    }

                    document.addEventListener('click', function (event) {
                        var trigger = event.target.closest('.ai-lead-follow-up-trigger');
                        if (trigger) {
                            currentLeadId = trigger.getAttribute('data-ai-lead-id');
                            hideError();
                            resetPreview();
                            document.getElementById('ai-lead-follow-up-prompt').value = '';
                            document.getElementById('ai-lead-follow-up-loading').classList.add('d-none');
                            document.getElementById('ai-lead-follow-up-generate-btn').disabled = false;
                            var instance = getModal();
                            if (instance) {
                                instance.show();
                            }
                            generateFollowUp();
                            return;
                        }

                        if (event.target.closest('#ai-lead-follow-up-generate-btn')) {
                            generateFollowUp();
                            return;
                        }

                        if (event.target.closest('#ai-lead-follow-up-fill-btn')) {
                            if (fillForm(currentFields) && typeof toastr !== 'undefined') {
                                toastr.success(i18n.applied);
                            }
                            if (modal) {
                                modal.hide();
                            }
                            return;
                        }

                        if (event.target.closest('#ai-lead-follow-up-create-btn')) {
                            if (!currentFields || !currentLeadId) {
                                return;
                            }

                            var createBtn = document.getElementById('ai-lead-follow-up-create-btn');
                            createBtn.disabled = true;
                            hideError();

                            var body = new FormData();
                            body.append('subject_type', 'lead');
                            body.append('subject_id', currentLeadId);
                            body.append('type', currentFields.type || 'task');
                            body.append('title', currentFields.title || '');
                            body.append('body', currentFields.body || '');
                            if (currentFields.scheduled_at) {
                                body.append('scheduled_at', currentFields.scheduled_at);
                            }

                            fetch(storeUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken(),
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: body,
                            })
                                .then(function (response) {
                                    if (!response.ok) {
                                        return response.json().then(function (payload) {
                                            throw new Error(payload.error || i18n.genericError);
                                        }).catch(function (error) {
                                            if (error instanceof Error && error.message !== i18n.genericError) {
                                                throw error;
                                            }
                                            throw new Error(i18n.genericError);
                                        });
                                    }

                                    if (typeof toastr !== 'undefined') {
                                        toastr.success(i18n.created);
                                    }
                                    window.location.reload();
                                })
                                .catch(function (error) {
                                    createBtn.disabled = false;
                                    showError(error.message || i18n.genericError);
                                });
                        }
                    });
                })();
            </script>
        @endpush
    @endonce
@endif
