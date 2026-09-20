@php
    $editorSelector = $selector ?? '#tinymce';
    $editorHeight = $height ?? 500;
    $aiAssistantEnabled = $aiAssistant ?? true;
    $aiButton = $aiAssistantEnabled ? 'aiassistant | ' : '';

    if (isset($toolbar)) {
        $editorToolbar = ($aiAssistantEnabled && ! str_contains($toolbar, 'aiassistant'))
            ? $aiButton.$toolbar
            : $toolbar;
    } else {
        $editorToolbar = [
            $aiButton.'undo redo | blocks fontsize | bold italic underline strikethrough | link image media table',
            'align lineheight | numlist bullist indent outdent | emoticons charmap | code removeformat',
        ];
    }

    $editorPlugins = $plugins ?? 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount';
    $lfmUrl = route('admin.unisharp.lfm.show');
    $aiGenerateUrl = \Illuminate\Support\Facades\Route::has('admin.ai.content.generate') ? route('admin.ai.content.generate') : null;
@endphp

@once('tinymce-cdn')
    @push('scripts')
        <script src="https://cdn.tiny.cloud/1/{{ Config::get('core.tinymce_key') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush
@endonce

@once('tinymce-ai-assistant')
    @push('scripts')
        <style>
            #ai-content-modal { z-index: 200000; }
            .modal-backdrop.ai-content-backdrop { z-index: 199990; }
        </style>
        <div class="modal fade" id="ai-content-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-stars text-primary me-2"></i>{{ __('ai::content_generation.modal.title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="ai-content-alert" class="alert alert-danger d-none mb-4" role="alert"></div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="ai-content-prompt">{{ __('ai::content_generation.modal.prompt_label') }}</label>
                            <textarea id="ai-content-prompt" class="form-control form-control-solid" rows="4" placeholder="{{ __('ai::content_generation.modal.prompt_placeholder') }}"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="ai-content-mode">{{ __('ai::content_generation.modal.insert_mode') }}</label>
                            <select id="ai-content-mode" class="form-select form-select-solid">
                                <option value="insert">{{ __('ai::content_generation.modal.insert_at_cursor') }}</option>
                                <option value="replace">{{ __('ai::content_generation.modal.replace_selected') }}</option>
                            </select>
                        </div>

                        <div id="ai-content-loading" class="d-none text-muted fs-7 mb-4">
                            <span class="spinner-border spinner-border-sm text-primary me-2"></span>{{ __('ai::content_generation.modal.generating') }}
                        </div>

                        <div id="ai-content-preview-wrap" class="d-none">
                            <div class="text-muted fs-8 mb-2 text-uppercase">{{ __('ai::content_generation.modal.result_title') }}</div>
                            <div id="ai-content-preview" class="border rounded p-4 bg-light" style="min-height:120px; max-height:280px; overflow:auto;"></div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('ai::content_generation.modal.close') }}</button>
                        <div class="d-flex gap-2">
                            <button type="button" id="ai-content-generate-btn" class="btn btn-primary">
                                <i class="bi bi-stars me-1"></i>{{ __('ai::content_generation.modal.generate') }}
                            </button>
                            <button type="button" id="ai-content-insert-btn" class="btn btn-success d-none">
                                {{ __('ai::content_generation.modal.insert') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.SymfonixAIAssistant = (function () {
                var routeUrl = @json($aiGenerateUrl);
                var i18n = {
                    buttonLabel: @json(__('ai::content_generation.button.label')),
                    buttonTooltip: @json(__('ai::content_generation.button.tooltip')),
                    emptyPrompt: @json(__('ai::content_generation.messages.empty_prompt')),
                    genericError: @json(__('ai::content_generation.messages.request_failed')),
                    notAvailable: @json(__('ai::content_generation.messages.not_available')),
                };
                var editor = null;
                var editorId = null;
                var selectedText = '';
                var hasSelection = false;
                var generatedHtml = '';
                var bookmark = null;
                var pendingMode = 'insert';
                var pendingInsert = false;
                var modal = null;

                function csrfToken() {
                    var meta = document.querySelector('meta[name="csrf-token"]');

                    return meta ? meta.getAttribute('content') : '';
                }

                function getModal() {
                    var el = document.getElementById('ai-content-modal');

                    if (!el) {
                        return null;
                    }

                    if (!modal) {
                        if (el.parentElement !== document.body) {
                            document.body.appendChild(el);
                        }

                        el.addEventListener('shown.bs.modal', function () {
                            document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                                backdrop.classList.add('ai-content-backdrop');
                            });
                        });

                        el.addEventListener('hidden.bs.modal', function () {
                            if (!generatedHtml || !pendingInsert) {
                                return;
                            }

                            pendingInsert = false;
                            window.setTimeout(applyGeneratedHtml, 50);
                        });

                        modal = bootstrap.Modal.getOrCreateInstance(el);
                    }

                    return modal;
                }

                function showError(message) {
                    var alertEl = document.getElementById('ai-content-alert');
                    if (!alertEl) {
                        return;
                    }

                    alertEl.textContent = message || i18n.genericError;
                    alertEl.classList.remove('d-none');
                }

                function hideError() {
                    var alertEl = document.getElementById('ai-content-alert');
                    if (!alertEl) {
                        return;
                    }

                    alertEl.textContent = '';
                    alertEl.classList.add('d-none');
                }

                function resetModal() {
                    generatedHtml = '';
                    hideError();
                    document.getElementById('ai-content-prompt').value = '';
                    document.getElementById('ai-content-mode').value = hasSelection ? 'replace' : 'insert';
                    document.getElementById('ai-content-loading').classList.add('d-none');
                    document.getElementById('ai-content-preview-wrap').classList.add('d-none');
                    document.getElementById('ai-content-preview').innerHTML = '';
                    document.getElementById('ai-content-insert-btn').classList.add('d-none');
                    document.getElementById('ai-content-generate-btn').disabled = false;
                }

                function resolveEditor() {
                    if (editor && !editor.removed && typeof editor.insertContent === 'function') {
                        return editor;
                    }

                    if (editorId && typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                        return tinymce.get(editorId);
                    }

                    if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                        return tinymce.activeEditor;
                    }

                    return null;
                }

                function applyGeneratedHtml() {
                    var current = resolveEditor();
                    var html = generatedHtml;

                    if (!current || !html) {
                        return false;
                    }

                    try {
                        current.focus();
                    } catch (e) {}

                    if (bookmark) {
                        try {
                            current.selection.moveToBookmark(bookmark);
                        } catch (e) {}
                    }

                    var before = current.getContent({format: 'html'});

                    try {
                        if (pendingMode === 'replace' && hasSelection) {
                            current.selection.setContent(html);
                        } else {
                            current.insertContent(html);
                        }
                    } catch (e) {}

                    var after = current.getContent({format: 'html'});

                    if (after === before) {
                        current.setContent(before + html);
                    }

                    try {
                        current.undoManager.add();
                    } catch (e) {}

                    current.fire('change');
                    current.nodeChanged();
                    current.save();

                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                    }

                    return current.getContent({format: 'html'}) !== before;
                }

                function open(activeEditor) {
                    editor = activeEditor;
                    editorId = activeEditor ? activeEditor.id : null;
                    hasSelection = !!(editor && !editor.selection.isCollapsed());
                    selectedText = hasSelection ? editor.selection.getContent({format: 'text'}) : '';
                    pendingInsert = false;
                    pendingMode = hasSelection ? 'replace' : 'insert';

                    try {
                        bookmark = editor.selection.getBookmark(2, true);
                    } catch (e) {
                        bookmark = null;
                    }

                    var instance = getModal();
                    if (!instance) {
                        return;
                    }

                    resetModal();

                    if (!routeUrl) {
                        showError(i18n.notAvailable);
                    }

                    instance.show();
                }

                document.addEventListener('click', function (event) {
                    if (event.target.closest('#ai-content-generate-btn')) {
                        var prompt = (document.getElementById('ai-content-prompt').value || '').trim();

                        if (!prompt) {
                            showError(i18n.emptyPrompt);
                            return;
                        }

                        if (!routeUrl) {
                            showError(i18n.notAvailable);
                            return;
                        }

                        hideError();
                        generatedHtml = '';
                        document.getElementById('ai-content-preview-wrap').classList.add('d-none');
                        document.getElementById('ai-content-insert-btn').classList.add('d-none');
                        document.getElementById('ai-content-loading').classList.remove('d-none');
                        document.getElementById('ai-content-generate-btn').disabled = true;

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
                                context: selectedText || null,
                            }),
                        })
                            .then(function (response) {
                                return response.json().catch(function () {
                                    return null;
                                }).then(function (json) {
                                    return {ok: response.ok, json: json};
                                });
                            })
                            .then(function (result) {
                                document.getElementById('ai-content-loading').classList.add('d-none');
                                document.getElementById('ai-content-generate-btn').disabled = false;

                                var json = result && result.json;
                                if (!result || !result.ok || !json || !json.success || !json.html) {
                                    var message = (json && (json.error || (json.errors && Object.values(json.errors)[0][0]))) || i18n.genericError;
                                    showError(message);
                                    return;
                                }

                                generatedHtml = json.html;
                                document.getElementById('ai-content-preview').innerHTML = generatedHtml;
                                document.getElementById('ai-content-preview-wrap').classList.remove('d-none');
                                document.getElementById('ai-content-insert-btn').classList.remove('d-none');
                            })
                            .catch(function () {
                                document.getElementById('ai-content-loading').classList.add('d-none');
                                document.getElementById('ai-content-generate-btn').disabled = false;
                                showError(i18n.genericError);
                            });
                    }

                    if (event.target.closest('#ai-content-insert-btn')) {
                        if (!generatedHtml) {
                            showError(i18n.genericError);
                            return;
                        }

                        pendingMode = document.getElementById('ai-content-mode').value;
                        pendingInsert = true;
                        getModal().hide();
                    }
                });

                return {open: open, buttonLabel: i18n.buttonLabel, buttonTooltip: i18n.buttonTooltip};
            })();
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        (function () {
            const selector = @json($editorSelector);
            const lfmUrl = @json($lfmUrl);
            const aiAssistantEnabled = @json($aiAssistantEnabled);

            function initEditor() {
                if (typeof tinymce === 'undefined' || typeof tinymce.init !== 'function') {
                    window.setTimeout(initEditor, 50);

                    return;
                }

                const target = document.querySelector(selector);

                if (!target || target.dataset.tinymceInit === 'true') {
                    return;
                }

                target.dataset.tinymceInit = 'true';

                tinymce.init({
                selector: selector,
                width: '100%',
                min_height: {{ (int) $editorHeight }},
                height: {{ (int) $editorHeight }},
                menubar: false,
                branding: false,
                promotion: false,
                relative_urls: false,
                remove_script_host: false,
                plugins: @json($editorPlugins),
                toolbar: @json($editorToolbar),
                file_picker_types: 'file image media',
                file_picker_callback: function (callback, value, meta) {
                    const width = window.innerWidth * 0.8;
                    const height = window.innerHeight * 0.8;
                    const type = meta.filetype === 'image' ? 'image' : 'file';
                    const cmsURL = lfmUrl + '?editor=tinymce5&type=' + type;

                    tinymce.activeEditor.windowManager.openUrl({
                        title: type === 'image' ? 'Image Manager' : 'File Manager (PDF & files)',
                        url: cmsURL,
                        width: width,
                        height: height,
                        onMessage: function (api, message) {
                            if (message.mceAction !== 'insert' || !message.content) {
                                return;
                            }

                            const url = message.content;
                            const filename = decodeURIComponent(url.split('/').pop().split('?')[0] || 'file');

                            if (meta.filetype === 'image') {
                                callback(url, { alt: filename });
                            } else {
                                callback(url, { text: filename, title: filename });
                            }

                            api.close();
                        }
                    });
                },
                @if(app()->getLocale() === 'ar')
                language: 'ar',
                directionality: 'rtl',
                @endif
                setup: function (editor) {
                    if (aiAssistantEnabled) {
                        editor.ui.registry.addButton('aiassistant', {
                            text: window.SymfonixAIAssistant ? window.SymfonixAIAssistant.buttonLabel : 'AI',
                            tooltip: window.SymfonixAIAssistant ? window.SymfonixAIAssistant.buttonTooltip : 'AI Assistant',
                            onAction: function () {
                                if (window.SymfonixAIAssistant) {
                                    window.SymfonixAIAssistant.open(editor);
                                }
                            }
                        });
                    }

                    editor.on('init', function () {
                        editor.targetElm.removeAttribute('required');
                        @if(app()->getLocale() === 'ar')
                        editor.getBody().setAttribute('dir', 'rtl');
                        @endif
                        editor.focus(false);
                    });
                    editor.on('SetContent', function () {
                        cleanFontStyles(editor);
                    });
                    editor.on('Change', function () {
                        cleanFontStyles(editor);
                    });
                }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initEditor);
            } else {
                initEditor();
            }

            document.querySelectorAll('form').forEach(function (form) {
                if (!form.querySelector(selector)) {
                    return;
                }

                var syncEditors = function () {
                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                    }
                };

                form.querySelectorAll('[type="submit"]').forEach(function (button) {
                    button.addEventListener('click', syncEditors);
                });

                form.addEventListener('submit', syncEditors);
            });

            function cleanFontStyles(editor) {
                var elements = editor.dom.select('span[style], p[style], div[style]');

                tinymce.each(elements, function (element) {
                    var style = editor.dom.getAttrib(element, 'style');
                    if (style && style.includes('font-family')) {
                        element.style.fontFamily = '';
                        if (!element.style.cssText) {
                            editor.dom.setAttrib(element, 'style', null);
                        }
                    }
                    editor.dom.setAttrib(element, 'face', null);
                });
            }
        })();
    </script>
@endpush
