@props([
    'target' => null,
    'id' => null,
    'field' => null,
    'imageUrl' => null,
    'label' => null,
])

<button type="button"
        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow ai-edit-trigger {{ $imageUrl ? '' : 'd-none' }} btn-info"
        style="position:absolute; top:-6px; left:-6px; z-index:2;"
        data-ai-target="{{ $target }}"
        data-ai-id="{{ $id }}"
        data-ai-field="{{ $field }}"
        data-ai-image="{{ $imageUrl }}"
        data-bs-toggle="tooltip"
        title="{{ $label ?? __('Edit this image with AI') }}" data-action="ai">
    <i class="bi bi-stars fs-7 text-primary"></i>
</button>

@once
    @push('scripts')
        <div class="modal fade" id="ai-edit-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-stars text-primary me-2"></i>{{ __('Edit Image with AI') }}
                        </h5>
                        <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                    </div>
                    <div class="modal-body">
                        <div id="ai-edit-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                        <div id="ai-edit-staged-hint" class="alert alert-info d-none mb-4" role="alert">
                            {{ __('This image has not been saved yet. Clicking "Use This Image" will stage the edited version in place of your selected file — save the form to keep it.') }}
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6 text-center">
                                <div class="text-muted fs-8 mb-2 text-uppercase">{{ __('Original') }}</div>
                                <div class="border rounded d-flex align-items-center justify-content-center" style="min-height:150px; max-height:260px; overflow:hidden;">
                                    <img id="ai-edit-original-img" src="" alt="" class="img-fluid" style="max-height:260px; object-fit:contain;"/>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="text-muted fs-8 mb-2 text-uppercase">{{ __('Preview') }}</div>
                                <div class="border rounded d-flex align-items-center justify-content-center position-relative" style="min-height:150px; max-height:260px; overflow:hidden;">
                                    <img id="ai-edit-result-img" src="" alt="" class="img-fluid d-none" style="max-height:260px; object-fit:contain;"/>
                                    <div id="ai-edit-loading" class="d-none text-muted fs-7">
                                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>{{ __('Generating...') }}
                                    </div>
                                    <span id="ai-edit-result-placeholder" class="text-muted fs-8">&mdash;</span>
                                </div>
                            </div>
                        </div>

                        @include('ai::components._brand-match', ['prefix' => 'ai-edit'])

                        <div class="mt-6">
                            <label class="form-label fw-semibold" for="ai-edit-prompt">{{ __('Describe the edit you want') }}</label>
                            <textarea id="ai-edit-prompt" class="form-control form-control-solid" rows="3" placeholder="{{ __('e.g. Change background to dusk, remove the person on the left...') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('Close') }}</button>
                        <div class="d-flex gap-2">
                            <button type="button" id="ai-edit-generate-btn" class="btn btn-info" data-action="ai">
                                <i class="bi bi-stars me-1"></i>{{ __('Generate') }}
                            </button>
                            <button type="button" id="ai-edit-use-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('Use This Image') }}
                            </button>
                            <button type="button" id="ai-edit-save-new-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('Save as New Version') }}
                            </button>
                            <button type="button" id="ai-edit-replace-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('Replace Original') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function ($) {
                var aiEditRoutes = {
                    generate: @json(route('admin.ai.image-edits.generate')),
                    apply: @json(route('admin.ai.image-edits.apply')),
                };
                var aiEditMessages = {
                    genericError: @json(__('An Error Occurred!')),
                    promptRequired: @json(__('This field is required.')),
                    applied: @json(__('Image updated successfully.')),
                    staged: @json(__('Image staged. Remember to save the form to keep it.')),
                };

                var $modal = null;
                var currentTrigger = null;
                var currentFileInput = null;
                var currentLocalFile = null;
                var currentObjectUrl = null;
                var currentToken = null;
                var currentResultDataUri = null;
                var currentMode = null;

                function getModal() {
                    if (!$modal) {
                        $modal = new bootstrap.Modal(document.getElementById('ai-edit-modal'));
                    }

                    return $modal;
                }

                function extensionForMime(mime) {
                    if (mime === 'image/jpeg' || mime === 'image/jpg') return 'jpg';
                    if (mime === 'image/webp') return 'webp';
                    return 'png';
                }

                // Decode data: URIs in-page. fetch(data:) is blocked by CSP
                // connect-src (no data: scheme), which made "Use This Image" fail.
                function dataUriToFile(dataUri, filenamePrefix) {
                    var comma = dataUri.indexOf(',');
                    if (comma === -1) {
                        throw new Error('Invalid data URI');
                    }

                    var header = dataUri.slice(0, comma);
                    var data = dataUri.slice(comma + 1);
                    var mimeMatch = header.match(/data:([^;]+)/);
                    var mime = (mimeMatch && mimeMatch[1]) || 'image/png';
                    var binary = atob(data);
                    var bytes = new Uint8Array(binary.length);

                    for (var i = 0; i < binary.length; i++) {
                        bytes[i] = binary.charCodeAt(i);
                    }

                    var filename = filenamePrefix + '-' + Date.now() + '.' + extensionForMime(mime);

                    return new File([bytes], filename, { type: mime });
                }

                function resetModal() {
                    $('#ai-edit-alert, #ai-edit-staged-hint').addClass('d-none').text('');
                    $('#ai-edit-prompt').val('');
                    $('#ai-edit-use-brand').prop('checked', true);
                    $('#ai-edit-result-img').addClass('d-none').attr('src', '');
                    $('#ai-edit-result-placeholder').removeClass('d-none');
                    $('#ai-edit-loading').addClass('d-none');
                    $('#ai-edit-save-new-btn, #ai-edit-replace-btn, #ai-edit-use-btn').addClass('d-none').prop('disabled', false);
                    $('#ai-edit-generate-btn').prop('disabled', false);
                    currentToken = null;
                    currentResultDataUri = null;
                    currentMode = null;

                    if (currentObjectUrl) {
                        URL.revokeObjectURL(currentObjectUrl);
                        currentObjectUrl = null;
                    }
                }

                function showError(message) {
                    $('#ai-edit-alert').removeClass('d-none').text(message || aiEditMessages.genericError);
                }

                // Reveal the "Edit with AI" trigger as soon as a file is locally
                // selected/previewed, even before the surrounding form is saved.
                $(document).on('change', '.image-input input[type="file"]', function () {
                    var file = this.files && this.files[0];
                    if (!file) {
                        return;
                    }

                    $(this).closest('.image-input').find('.ai-edit-trigger').removeClass('d-none');
                });

                $(document).on('click', '.ai-edit-trigger', function () {
                    currentTrigger = this;
                    resetModal();

                    var $imageInput = $(this).closest('.image-input');
                    currentFileInput = $imageInput.find('input[type="file"]').get(0) || null;
                    currentLocalFile = (currentFileInput && currentFileInput.files) ? currentFileInput.files[0] : null;

                    var originalSrc;
                    if (currentLocalFile) {
                        currentObjectUrl = URL.createObjectURL(currentLocalFile);
                        originalSrc = currentObjectUrl;
                    } else {
                        originalSrc = $(this).data('ai-image');
                    }

                    $('#ai-edit-original-img').attr('src', originalSrc);
                    getModal().show();
                });

                $(document).on('hidden.bs.modal', '#ai-edit-modal', function () {
                    if (currentObjectUrl) {
                        URL.revokeObjectURL(currentObjectUrl);
                        currentObjectUrl = null;
                    }
                });

                function runGenerate(payload) {
                    $('#ai-edit-alert').addClass('d-none');
                    $('#ai-edit-result-placeholder').addClass('d-none');
                    $('#ai-edit-result-img').addClass('d-none');
                    $('#ai-edit-save-new-btn, #ai-edit-replace-btn, #ai-edit-use-btn').addClass('d-none');
                    $('#ai-edit-loading').removeClass('d-none');
                    $('#ai-edit-generate-btn').prop('disabled', true);

                    $.ajax({
                        url: aiEditRoutes.generate,
                        method: 'POST',
                        dataType: 'json',
                        data: payload,
                        success: function (response) {
                            $('#ai-edit-loading').addClass('d-none');
                            $('#ai-edit-generate-btn').prop('disabled', false);

                            if (!response || !response.success) {
                                $('#ai-edit-result-placeholder').removeClass('d-none');
                                showError(response && response.error);
                                return;
                            }

                            currentToken = response.token || null;
                            currentMode = response.mode;
                            currentResultDataUri = response.previewUrl;

                            $('#ai-edit-result-img').attr('src', response.previewUrl).removeClass('d-none');

                            if (currentMode === 'persisted') {
                                $('#ai-edit-save-new-btn, #ai-edit-replace-btn').removeClass('d-none');
                            } else {
                                $('#ai-edit-use-btn').removeClass('d-none');
                                $('#ai-edit-staged-hint').removeClass('d-none');
                            }
                        },
                        error: function (xhr) {
                            $('#ai-edit-loading').addClass('d-none');
                            $('#ai-edit-result-placeholder').removeClass('d-none');
                            $('#ai-edit-generate-btn').prop('disabled', false);
                            var message = xhr.responseJSON && (xhr.responseJSON.error
                                || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors)[0][0]));
                            showError(message);
                        },
                    });
                }

                $(document).on('click', '#ai-edit-generate-btn', function () {
                    if (!currentTrigger) {
                        return;
                    }

                    var prompt = $('#ai-edit-prompt').val().trim();
                    if (!prompt) {
                        showError(aiEditMessages.promptRequired);
                        return;
                    }

                    if (currentLocalFile) {
                        // Ad-hoc mode: the admin picked a file that hasn't been
                        // saved yet, so send its bytes directly instead of
                        // reading anything from the server.
                        var reader = new FileReader();
                        reader.onload = function () {
                            runGenerate({
                                image: reader.result,
                                mime_type: currentLocalFile.type,
                                prompt: prompt,
                                use_brand_logo: $('#ai-edit-use-brand').length ? ($('#ai-edit-use-brand').is(':checked') ? 1 : 0) : 1,
                            });
                        };
                        reader.onerror = function () {
                            showError(aiEditMessages.genericError);
                        };
                        reader.readAsDataURL(currentLocalFile);
                        return;
                    }

                    var $trigger = $(currentTrigger);
                    runGenerate({
                        target: $trigger.data('ai-target'),
                        id: $trigger.data('ai-id'),
                        field: $trigger.data('ai-field'),
                        prompt: prompt,
                        use_brand_logo: $('#ai-edit-use-brand').length ? ($('#ai-edit-use-brand').is(':checked') ? 1 : 0) : 1,
                    });
                });

                function updateTriggerPreview(url) {
                    if (!currentTrigger) {
                        return;
                    }

                    var $trigger = $(currentTrigger);
                    $trigger.data('ai-image', url);
                    $trigger.attr('data-ai-image', url);

                    var $imageInput = $trigger.closest('.image-input');
                    $imageInput.css('background-image', 'url(' + url + ')');
                    $imageInput.find('.image-input-wrapper').css('background-image', 'url(' + url + ')');
                }

                function applyEdit(action) {
                    if (!currentToken) {
                        return;
                    }

                    $('#ai-edit-save-new-btn, #ai-edit-replace-btn').prop('disabled', true);

                    $.ajax({
                        url: aiEditRoutes.apply,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            token: currentToken,
                            action: action,
                        },
                        success: function (response) {
                            $('#ai-edit-save-new-btn, #ai-edit-replace-btn').prop('disabled', false);

                            if (!response || !response.success) {
                                showError(response && response.error);
                                return;
                            }

                            updateTriggerPreview(response.url);

                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || aiEditMessages.applied);
                            }

                            getModal().hide();
                        },
                        error: function (xhr) {
                            $('#ai-edit-save-new-btn, #ai-edit-replace-btn').prop('disabled', false);
                            var message = xhr.responseJSON && xhr.responseJSON.error;
                            showError(message);
                        },
                    });
                }

                $(document).on('click', '#ai-edit-replace-btn', function () {
                    applyEdit('replace');
                });

                $(document).on('click', '#ai-edit-save-new-btn', function () {
                    applyEdit('new_version');
                });

                // Ad-hoc mode: nothing is persisted server-side yet, so
                // "apply" just re-stages the edited image into the same
                // <input type="file"> the admin originally picked from, so
                // it uploads with the rest of the form on Save.
                $(document).on('click', '#ai-edit-use-btn', function () {
                    if (!currentResultDataUri || !currentFileInput) {
                        showError(aiEditMessages.genericError);
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true);

                    try {
                        var file = dataUriToFile(currentResultDataUri, 'ai-edit');
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        currentFileInput.files = dataTransfer.files;
                        currentFileInput.dispatchEvent(new Event('change', { bubbles: true }));

                        updateTriggerPreview(currentResultDataUri);

                        if (typeof toastr !== 'undefined') {
                            toastr.success(aiEditMessages.staged);
                        }

                        $btn.prop('disabled', false);
                        getModal().hide();
                    } catch (e) {
                        $btn.prop('disabled', false);
                        showError(aiEditMessages.genericError);
                    }
                });
            })(jQuery);
        </script>
    @endpush
@endonce
