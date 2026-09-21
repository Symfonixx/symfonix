@props([
    'type',
    'banner' => false,
    'hint' => null,
    'optimize' => false,
])

@php
    $mode = $optimize ? 'optimize' : 'create';
    $buttonLabel = $optimize
        ? __('ai::content_generation.form.optimize_button')
        : __('ai::content_generation.form.button');
    $buttonTooltip = $optimize
        ? __('ai::content_generation.form.optimize_tooltip')
        : __('ai::content_generation.form.tooltip');
    $bannerTitle = $optimize
        ? __('ai::content_generation.form.optimize_banner_title')
        : __('ai::content_generation.form.banner_title');
    $bannerHint = $hint ?? ($optimize
        ? __('ai::content_generation.form.optimize_banner_hint')
        : __('ai::content_generation.form.banner_hint'));
@endphp

@if (\Illuminate\Support\Facades\Route::has('admin.ai.content.generate-form'))
    @if ($banner)
        <div class="alert alert-light-primary d-flex flex-wrap align-items-center justify-content-between gap-3 mb-8">
            <div>
                <div class="fw-bold">{{ $bannerTitle }}</div>
                <div class="text-muted fs-7">{{ $bannerHint }}</div>
            </div>
            <button type="button"
                    class="btn btn-sm btn-info ai-generate-form-trigger"
                    data-ai-form-type="{{ $type }}"
                    data-ai-mode="{{ $mode }}"
                    title="{{ $buttonTooltip }}" data-action="ai">
                <i class="bi bi-stars me-1"></i>{{ $buttonLabel }}
            </button>
        </div>
    @else
        <button type="button"
                class="btn btn-sm btn-info ai-generate-form-trigger"
                data-ai-form-type="{{ $type }}"
                data-ai-mode="{{ $mode }}"
                title="{{ $buttonTooltip }}" data-action="ai">
            <i class="bi bi-stars me-1"></i>{{ $buttonLabel }}
        </button>
    @endif

    @once
        @push('scripts')
            <style>
                #ai-form-modal { z-index: 200000; }
                .modal-backdrop.ai-form-backdrop { z-index: 199990; }
            </style>
            <div class="modal fade" id="ai-form-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-stars text-primary me-2"></i><span id="ai-form-modal-title">{{ __('ai::content_generation.form.modal_title') }}</span>
                            </h5>
                            <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                        </div>
                        <div class="modal-body">
                            <div id="ai-form-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold" for="ai-form-prompt" id="ai-form-prompt-label">{{ __('ai::content_generation.form.prompt_label') }}</label>
                                <textarea id="ai-form-prompt" class="form-control form-control-solid" rows="4" placeholder="{{ __('ai::content_generation.form.prompt_placeholder') }}"></textarea>
                            </div>
                            <div id="ai-form-loading" class="d-none text-muted fs-7 mt-4">
                                <span class="spinner-border spinner-border-sm text-primary me-2"></span><span id="ai-form-loading-text">{{ __('ai::content_generation.form.generating') }}</span>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('ai::content_generation.modal.close') }}</button>
                            <button type="button" id="ai-form-generate-btn" class="btn btn-info" data-action="ai">
                                <i class="bi bi-stars me-1"></i><span id="ai-form-generate-label">{{ __('ai::content_generation.form.generate') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.SymfonixAIFormFill = (function () {
                    var routeUrl = @json(route('admin.ai.content.generate-form'));
                    var locale = @json(app()->getLocale());
                    var i18n = {
                        emptyPrompt: @json(__('ai::content_generation.messages.empty_prompt')),
                        emptyContent: @json(__('ai::content_generation.messages.empty_content')),
                        genericError: @json(__('ai::content_generation.messages.request_failed')),
                        notAvailable: @json(__('ai::content_generation.messages.not_available')),
                        applied: @json(__('ai::content_generation.form.applied')),
                        productApplied: @json(__('ai::content_generation.form.product_applied')),
                        overwrite: @json(__('ai::content_generation.form.overwrite_confirm')),
                        create: {
                            title: @json(__('ai::content_generation.form.modal_title')),
                            promptLabel: @json(__('ai::content_generation.form.prompt_label')),
                            placeholder: @json(__('ai::content_generation.form.prompt_placeholder')),
                            generate: @json(__('ai::content_generation.form.generate')),
                            generating: @json(__('ai::content_generation.form.generating')),
                            applied: @json(__('ai::content_generation.form.applied')),
                        },
                        optimize: {
                            title: @json(__('ai::content_generation.form.optimize_modal_title')),
                            promptLabel: @json(__('ai::content_generation.form.optimize_prompt_label')),
                            placeholder: @json(__('ai::content_generation.form.optimize_prompt_placeholder')),
                            generate: @json(__('ai::content_generation.form.optimize_generate')),
                            generating: @json(__('ai::content_generation.form.optimize_generating')),
                            applied: @json(__('ai::content_generation.form.optimize_applied')),
                        },
                    };
                    var cmsFields = {
                        title: { name: 'title' },
                        slug: { type: 'slug' },
                        description: { name: 'description' },
                        keywords: { type: 'tagify', selector: '#kt_tagify_1' },
                        content: { type: 'tinymce', selector: '#tinymce' }
                    };
                    var fieldMaps = {
                        cms_blog: cmsFields,
                        cms_page: cmsFields,
                        service: cmsFields,
                        product: {
                            name: { name: 'name' },
                            short_description: { name: 'short_description' },
                            description: { type: 'tinymce', selector: '#product-description-editor' },
                            seo_title: { name: 'seo_title' },
                            seo_description: { name: 'seo_description' },
                            seo_keywords: { name: 'seo_keywords' }
                        },
                        use_case: {
                            title: { name: 'title' },
                            slug: { type: 'slug' },
                            client_name: { name: 'client_name' },
                            summary: { name: 'summary' },
                            challenge: { name: 'challenge' },
                            solution: { name: 'solution' },
                            results: { name: 'results' },
                            content: { type: 'tinymce', selector: '#tinymce' },
                            technologies: { type: 'tagify', selector: '#kt_tagify_tech' },
                            category_tag: { name: 'category_tag' }
                        }
                    };
                    var modal = null;
                    var activeForm = null;
                    var activeType = null;
                    var activeMode = 'create';

                    function csrfToken() {
                        var meta = document.querySelector('meta[name="csrf-token"]');

                        return meta ? meta.getAttribute('content') : '';
                    }

                    function getModal() {
                        var el = document.getElementById('ai-form-modal');

                        if (!el) {
                            return null;
                        }

                        if (!modal) {
                            if (el.parentElement !== document.body) {
                                document.body.appendChild(el);
                            }

                            el.addEventListener('shown.bs.modal', function () {
                                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                                    backdrop.classList.add('ai-form-backdrop');
                                });
                                var prompt = document.getElementById('ai-form-prompt');
                                if (prompt) {
                                    prompt.focus();
                                }
                            });

                            modal = bootstrap.Modal.getOrCreateInstance(el);
                        }

                        return modal;
                    }

                    function showError(message) {
                        var alertEl = document.getElementById('ai-form-alert');
                        if (!alertEl) {
                            return;
                        }

                        alertEl.textContent = message || i18n.genericError;
                        alertEl.classList.remove('d-none');
                    }

                    function hideError() {
                        var alertEl = document.getElementById('ai-form-alert');
                        if (!alertEl) {
                            return;
                        }

                        alertEl.textContent = '';
                        alertEl.classList.add('d-none');
                    }

                    function setBusy(busy) {
                        var loading = document.getElementById('ai-form-loading');
                        var button = document.getElementById('ai-form-generate-btn');

                        if (loading) {
                            loading.classList.toggle('d-none', !busy);
                        }

                        if (button) {
                            button.disabled = !!busy;
                        }
                    }

                    function fieldValue(form, spec, asHtml) {
                        if (!spec) {
                            return '';
                        }

                        if (spec.name) {
                            var input = form.querySelector('[name="' + spec.name + '"]');
                            return input && input.value ? String(input.value).trim() : '';
                        }

                        if (spec.type === 'slug') {
                            var slug = form.querySelector('#slug') || form.querySelector('#gslug');
                            return slug && slug.value ? String(slug.value).trim() : '';
                        }

                        if (spec.type === 'tagify' && spec.selector) {
                            var tagInput = form.querySelector(spec.selector);
                            if (!tagInput) {
                                return '';
                            }

                            var tagify = tagInput._tagify || tagInput.tagify;
                            if (tagify && typeof tagify.value !== 'undefined') {
                                return tagify.value.map(function (tag) {
                                    return tag.value;
                                }).join(', ');
                            }

                            return String(tagInput.value || '').trim();
                        }

                        if (spec.type === 'tinymce' && spec.selector) {
                            var editorId = spec.selector.replace(/^#/, '');
                            var editor = (typeof tinymce !== 'undefined') ? tinymce.get(editorId) : null;
                            if (editor) {
                                return String(editor.getContent({ format: asHtml ? 'html' : 'text' }) || '').trim();
                            }

                            var textarea = document.querySelector(spec.selector);
                            return textarea && textarea.value ? String(textarea.value).trim() : '';
                        }

                        return '';
                    }

                    function formHasContent(form, type) {
                        var map = fieldMaps[type] || {};

                        return Object.keys(map).some(function (key) {
                            var spec = map[key];
                            var isPrimary = spec.name === 'title' || spec.name === 'name' || spec.type === 'tinymce';

                            return isPrimary && fieldValue(form, spec) !== '';
                        });
                    }

                    function collectExisting(form, type, forOptimize) {
                        var map = fieldMaps[type] || {};
                        var existing = {};

                        Object.keys(map).forEach(function (key) {
                            var spec = map[key];
                            if (spec.type === 'tinymce' && !forOptimize) {
                                return;
                            }

                            var value = fieldValue(form, spec, spec.type === 'tinymce');
                            if (value) {
                                existing[key] = value.substring(0, spec.type === 'tinymce' ? 40000 : (forOptimize ? 4000 : 500));
                            }
                        });

                        var category = form.querySelector('[name="category_id"], [name="service_category_id"], [name="product_category_id"]');
                        if (category && category.value && category.selectedOptions && category.selectedOptions[0]) {
                            existing.category = String(category.selectedOptions[0].text || '').trim();
                        }

                        return existing;
                    }

                    function applyModeUi(mode) {
                        var copy = i18n[mode] || i18n.create;
                        var titleEl = document.getElementById('ai-form-modal-title');
                        var labelEl = document.getElementById('ai-form-prompt-label');
                        var promptEl = document.getElementById('ai-form-prompt');
                        var generateLabel = document.getElementById('ai-form-generate-label');
                        var loadingText = document.getElementById('ai-form-loading-text');

                        if (titleEl) titleEl.textContent = copy.title;
                        if (labelEl) labelEl.textContent = copy.promptLabel;
                        if (promptEl) promptEl.setAttribute('placeholder', copy.placeholder);
                        if (generateLabel) generateLabel.textContent = copy.generate;
                        if (loadingText) loadingText.textContent = copy.generating;
                    }

                    function dispatchInput(el) {
                        if (!el) {
                            return;
                        }

                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    function fillText(form, name, value) {
                        var el = form.querySelector('[name="' + name + '"]');
                        if (!el) {
                            return;
                        }

                        el.value = value;
                        dispatchInput(el);
                    }

                    function fillSlug(form, slug) {
                        var gslug = form.querySelector('#gslug');
                        var hidden = form.querySelector('#slug');

                        if (gslug) {
                            gslug.value = slug;
                        }

                        if (hidden) {
                            hidden.value = slug;
                            dispatchInput(hidden);
                        }

                        var link = form.querySelector('#link');
                        if (link && slug) {
                            link.classList.remove('text-danger');
                            link.classList.add('text-primary');
                            link.style.textDecoration = 'underline';
                            link.textContent = 'https://domain.com/' + slug;
                        }
                    }

                    function fillTagify(form, selector, value) {
                        var input = form.querySelector(selector);
                        if (!input) {
                            return;
                        }

                        var tags = String(value).split(',').map(function (tag) {
                            return tag.trim();
                        }).filter(Boolean);
                        var instance = input._tagify || input.tagify;

                        if (instance) {
                            instance.removeAllTags();
                            instance.addTags(tags);
                            return;
                        }

                        input.value = tags.join(', ');
                        dispatchInput(input);
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
                            dispatchInput(textarea);
                        }
                    }

                    function applyFields(form, type, fields) {
                        var map = fieldMaps[type] || {};

                        Object.keys(map).forEach(function (key) {
                            if (typeof fields[key] === 'undefined') {
                                return;
                            }

                            var spec = map[key];
                            var value = fields[key];

                            if (spec.type === 'slug') {
                                if (activeMode !== 'optimize') {
                                    fillSlug(form, value);
                                }
                                return;
                            }

                            if (spec.type === 'tagify') {
                                fillTagify(form, spec.selector, value);
                                return;
                            }

                            if (spec.type === 'tinymce') {
                                fillTinyMce(spec.selector, value);
                                return;
                            }

                            if (spec.name) {
                                fillText(form, spec.name, value);
                            }
                        });

                        if (type === 'product' && activeMode !== 'optimize') {
                            var published = form.querySelector('#is_published');
                            if (published) {
                                published.checked = true;
                                dispatchInput(published);
                            }

                            var seoCollapse = document.getElementById('product-seo-collapse');
                            if (seoCollapse && window.bootstrap && bootstrap.Collapse) {
                                bootstrap.Collapse.getOrCreateInstance(seoCollapse, { toggle: false }).show();
                            }
                        }
                    }

                    function open(trigger) {
                        activeForm = trigger.closest('form');
                        activeType = trigger.getAttribute('data-ai-form-type');
                        activeMode = trigger.getAttribute('data-ai-mode') === 'optimize' ? 'optimize' : 'create';

                        var instance = getModal();
                        if (!instance) {
                            return;
                        }

                        hideError();
                        applyModeUi(activeMode);
                        setBusy(false);

                        var prompt = document.getElementById('ai-form-prompt');
                        if (prompt) {
                            prompt.value = '';
                            if (activeMode === 'create' && activeForm && activeType) {
                                var existingTitle = fieldValue(activeForm, (fieldMaps[activeType] || {}).title || (fieldMaps[activeType] || {}).name);
                                if (existingTitle) {
                                    prompt.value = existingTitle;
                                }
                            }
                        }

                        if (!routeUrl) {
                            showError(i18n.notAvailable);
                        }

                        instance.show();
                    }

                    document.addEventListener('click', function (event) {
                        var trigger = event.target.closest('.ai-generate-form-trigger');
                        if (trigger) {
                            open(trigger);
                            return;
                        }

                        if (!event.target.closest('#ai-form-generate-btn')) {
                            return;
                        }

                        var prompt = (document.getElementById('ai-form-prompt').value || '').trim();
                        var isOptimize = activeMode === 'optimize';

                        if (!isOptimize && !prompt) {
                            showError(i18n.emptyPrompt);
                            return;
                        }

                        if (!routeUrl || !activeForm || !activeType) {
                            showError(i18n.notAvailable);
                            return;
                        }

                        var existing = collectExisting(activeForm, activeType, isOptimize);
                        var hasExisting = Object.keys(existing).some(function (key) {
                            return key !== 'category' && existing[key];
                        });

                        if (isOptimize && !hasExisting) {
                            showError(i18n.emptyContent);
                            return;
                        }

                        if (!isOptimize && formHasContent(activeForm, activeType) && !window.confirm(i18n.overwrite)) {
                            return;
                        }

                        hideError();
                        setBusy(true);

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
                                prompt: prompt,
                                form_type: activeType,
                                mode: activeMode,
                                locale: locale,
                                existing: existing,
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
                                setBusy(false);

                                var json = result && result.json;
                                if (!result || !result.ok || !json || !json.success || !json.fields) {
                                    var message = (json && (json.error || (json.errors && Object.values(json.errors)[0][0]))) || i18n.genericError;
                                    showError(message);
                                    return;
                                }

                                applyFields(activeForm, activeType, json.fields);
                                getModal().hide();

                                if (window.toastr && typeof window.toastr.success === 'function') {
                                    var appliedMessage = activeMode === 'optimize'
                                        ? i18n.optimize.applied
                                        : (activeType === 'product' ? i18n.productApplied : i18n.applied);
                                    window.toastr.success(appliedMessage);
                                }
                            })
                            .catch(function () {
                                setBusy(false);
                                showError(i18n.genericError);
                            });
                    });

                    return { open: open };
                })();
            </script>
        @endpush
    @endonce
@endif
