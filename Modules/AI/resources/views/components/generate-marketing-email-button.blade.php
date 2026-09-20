@if (\Illuminate\Support\Facades\Route::has('admin.ai.content.generate-marketing-email') && auth()->user()?->can('marketing.email.send'))
    <div class="alert alert-light-primary d-flex flex-wrap align-items-center justify-content-between gap-3 mb-8">
        <div>
            <div class="fw-bold">{{ __('crm::marketing.ai.banner_title') }}</div>
            <div class="text-muted fs-7">{{ __('crm::marketing.ai.banner_hint') }}</div>
        </div>
        <button type="button"
                class="btn btn-sm btn-info ai-generate-marketing-email-trigger"
                title="{{ __('crm::marketing.ai.tooltip') }}" data-action="ai">
            <i class="bi bi-stars me-1"></i>{{ __('crm::marketing.ai.button') }}
        </button>
    </div>

    @once
        @push('scripts')
            <style>
                #ai-marketing-email-modal { z-index: 200000; }
                .modal-backdrop.ai-marketing-email-backdrop { z-index: 199990; }
            </style>
            <div class="modal fade" id="ai-marketing-email-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-stars text-primary me-2"></i>{{ __('crm::marketing.ai.modal_title') }}
                            </h5>
                            <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                        </div>
                        <div class="modal-body">
                            <div id="ai-marketing-email-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                            <div id="ai-marketing-email-context" class="alert alert-light-info d-none mb-4 fs-7"></div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold" for="ai-marketing-email-prompt">{{ __('crm::marketing.ai.prompt_label') }}</label>
                                <textarea id="ai-marketing-email-prompt" class="form-control form-control-solid" rows="4"
                                          placeholder="{{ __('crm::marketing.ai.prompt_placeholder') }}"></textarea>
                            </div>
                            <div id="ai-marketing-email-loading" class="d-none text-muted fs-7 mt-4">
                                <span class="spinner-border spinner-border-sm text-primary me-2"></span>{{ __('crm::marketing.ai.generating') }}
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('ai::content_generation.modal.close') }}</button>
                            <button type="button" id="ai-marketing-email-generate-btn" class="btn btn-info" data-action="ai">
                                <i class="bi bi-stars me-1"></i>{{ __('crm::marketing.ai.generate') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.SymfonixAIMarketingEmailFill = (function () {
                    var routeUrl = @json(route('admin.ai.content.generate-marketing-email'));
                    var locale = @json(app()->getLocale());
                    var i18n = {
                        genericError: @json(__('ai::content_generation.messages.request_failed')),
                        notAvailable: @json(__('ai::content_generation.messages.not_available')),
                        applied: @json(__('crm::marketing.ai.applied')),
                        overwrite: @json(__('crm::marketing.ai.overwrite')),
                        groupRequired: @json(__('crm::marketing.ai.group_required')),
                        goalRequired: @json(__('crm::marketing.ai.goal_required')),
                        contextLabel: @json(__('crm::marketing.fields.campaign')),
                    };
                    var modal = null;

                    function csrfToken() {
                        var meta = document.querySelector('meta[name="csrf-token"]');
                        return meta ? meta.getAttribute('content') : '';
                    }

                    function helpers() {
                        return window.SymfonixMarketingGroupFields || null;
                    }

                    function campaignForm() {
                        var subject = document.getElementById('subject');
                        return subject ? subject.closest('form') : null;
                    }

                    function getModal() {
                        var el = document.getElementById('ai-marketing-email-modal');
                        if (!el) {
                            return null;
                        }

                        if (!modal) {
                            if (el.parentElement !== document.body) {
                                document.body.appendChild(el);
                            }

                            el.addEventListener('shown.bs.modal', function () {
                                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                                    backdrop.classList.add('ai-marketing-email-backdrop');
                                });
                                var prompt = document.getElementById('ai-marketing-email-prompt');
                                if (prompt) {
                                    prompt.focus();
                                }
                            });

                            modal = bootstrap.Modal.getOrCreateInstance(el);
                        }

                        return modal;
                    }

                    function showError(message) {
                        var alertEl = document.getElementById('ai-marketing-email-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = message || i18n.genericError;
                        alertEl.classList.remove('d-none');
                    }

                    function hideError() {
                        var alertEl = document.getElementById('ai-marketing-email-alert');
                        if (!alertEl) {
                            return;
                        }
                        alertEl.textContent = '';
                        alertEl.classList.add('d-none');
                    }

                    function editorText(id) {
                        if (typeof tinymce !== 'undefined') {
                            var editor = tinymce.get(id);
                            if (editor) {
                                return String(editor.getContent({ format: 'text' }) || '').trim();
                            }
                        }

                        var el = document.getElementById(id);
                        return el ? String(el.value || '').trim() : '';
                    }

                    function fillTinyMce(selector, html) {
                        var textarea = document.querySelector(selector);
                        var editorId = selector.replace(/^#/, '');

                        if (typeof tinymce !== 'undefined') {
                            var editor = tinymce.get(editorId);
                            if (editor) {
                                editor.setContent(html || '');
                                editor.fire('change');
                                editor.nodeChanged();
                                editor.save();
                                if (typeof tinymce.triggerSave === 'function') {
                                    tinymce.triggerSave();
                                }
                                return;
                            }
                        }

                        if (textarea) {
                            textarea.value = html || '';
                            textarea.dispatchEvent(new Event('input', { bubbles: true }));
                            textarea.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }

                    function formHasContent() {
                        return editorText('subject') !== '' || editorText('body') !== '';
                    }

                    function campaignContext() {
                        var fields = helpers();
                        var title = fields && typeof fields.title === 'function' ? fields.title() : '';
                        var goal = fields && typeof fields.goal === 'function' ? fields.goal() : '';
                        var groupId = fields && typeof fields.groupId === 'function' ? fields.groupId() : null;

                        return { title: title, goal: goal, groupId: groupId };
                    }

                    function open() {
                        var instance = getModal();
                        if (!instance) {
                            return;
                        }

                        hideError();
                        document.getElementById('ai-marketing-email-loading').classList.add('d-none');
                        document.getElementById('ai-marketing-email-generate-btn').disabled = false;

                        var campaign = campaignContext();
                        var context = document.getElementById('ai-marketing-email-context');
                        if (context) {
                            if (campaign.title || campaign.goal) {
                                context.textContent = i18n.contextLabel + ': '
                                    + [campaign.title, campaign.goal].filter(Boolean).join(' — ');
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
                        var trigger = event.target.closest('.ai-generate-marketing-email-trigger');
                        if (trigger) {
                            open();
                            return;
                        }

                        if (!event.target.closest('#ai-marketing-email-generate-btn')) {
                            return;
                        }

                        var form = campaignForm();
                        var campaign = campaignContext();

                        if (!routeUrl || !form) {
                            showError(i18n.notAvailable);
                            return;
                        }

                        if (!campaign.groupId && (!campaign.title || !campaign.goal)) {
                            showError(i18n.groupRequired);
                            return;
                        }

                        if (!campaign.goal) {
                            showError(i18n.goalRequired);
                            return;
                        }

                        if (formHasContent() && !window.confirm(i18n.overwrite)) {
                            return;
                        }

                        hideError();
                        document.getElementById('ai-marketing-email-loading').classList.remove('d-none');
                        document.getElementById('ai-marketing-email-generate-btn').disabled = true;

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
                                marketing_group_id: campaign.groupId || null,
                                title: campaign.title,
                                goal: campaign.goal,
                                prompt: (document.getElementById('ai-marketing-email-prompt').value || '').trim(),
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
                                document.getElementById('ai-marketing-email-loading').classList.add('d-none');
                                document.getElementById('ai-marketing-email-generate-btn').disabled = false;

                                var json = result && result.json;
                                if (!result || !result.ok || !json || !json.success || !json.fields) {
                                    var message = (json && (json.error || (json.errors && Object.values(json.errors)[0][0]))) || i18n.genericError;
                                    showError(message);
                                    return;
                                }

                                fillTinyMce('#subject', json.fields.subject || '');
                                fillTinyMce('#body', json.fields.body || '');
                                getModal().hide();

                                if (window.toastr && typeof window.toastr.success === 'function') {
                                    window.toastr.success(i18n.applied);
                                }
                            })
                            .catch(function () {
                                document.getElementById('ai-marketing-email-loading').classList.add('d-none');
                                document.getElementById('ai-marketing-email-generate-btn').disabled = false;
                                showError(i18n.genericError);
                            });
                    });

                    return { open: open };
                })();
            </script>
        @endpush
    @endonce
@endif
