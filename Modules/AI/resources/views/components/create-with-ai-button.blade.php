@props([
    'target' => null,
    'id' => null,
    'field' => null,
    'label' => null,
])

<button type="button"
        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow ai-create-trigger btn-info"
        style="position:absolute; bottom:-6px; left:-6px; z-index:2;"
        data-ai-target="{{ $target }}"
        data-ai-id="{{ $id }}"
        data-ai-field="{{ $field }}"
        data-bs-toggle="tooltip"
        title="{{ $label ?? __('ai::image_edit.button.create_tooltip') }}" data-action="ai">
    <i class="bi bi-magic fs-7 text-primary"></i>
</button>

@once
    @push('scripts')
        <div class="modal fade" id="ai-create-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-magic text-primary me-2"></i>{{ __('ai::image_edit.modal.create_title') }}
                        </h5>
                        <button type="button" class="btn-close btn-light" data-bs-dismiss="modal" aria-label="Close" data-action="back"></button>
                    </div>
                    <div class="modal-body">
                        <div id="ai-create-alert" class="alert alert-danger d-none mb-4" role="alert"></div>
                        <div id="ai-create-staged-hint" class="alert alert-info d-none mb-4" role="alert">
                            {{ __('ai::image_edit.messages.staged_hint') }}
                        </div>

                        @include('ai::components._brand-match', ['prefix' => 'ai-create'])

                        <div class="mb-6">
                            <label class="form-label fw-semibold" for="ai-create-prompt">{{ __('ai::image_edit.modal.create_prompt_label') }}</label>
                            <textarea id="ai-create-prompt" class="form-control form-control-solid" rows="3" placeholder="{{ __('ai::image_edit.modal.create_prompt_placeholder') }}"></textarea>
                        </div>

                        <div class="text-center">
                            <div class="text-muted fs-8 mb-2 text-uppercase">{{ __('ai::image_edit.modal.result_title') }}</div>
                            <div class="border rounded d-flex align-items-center justify-content-center position-relative mx-auto" style="min-height:200px; max-height:320px; overflow:hidden;">
                                <img id="ai-create-result-img" src="" alt="" class="img-fluid d-none" style="max-height:320px; object-fit:contain;"/>
                                <div id="ai-create-loading" class="d-none text-muted fs-7">
                                    <span class="spinner-border spinner-border-sm text-primary me-2"></span>{{ __('ai::image_edit.modal.generating') }}
                                </div>
                                <span id="ai-create-result-placeholder" class="text-muted fs-8">&mdash;</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-action="back">{{ __('ai::image_edit.modal.close') }}</button>
                        <div class="d-flex gap-2">
                            <button type="button" id="ai-create-generate-btn" class="btn btn-info" data-action="ai">
                                <i class="bi bi-magic me-1"></i>{{ __('ai::image_edit.modal.generate') }}
                            </button>
                            <button type="button" id="ai-create-use-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('ai::image_edit.modal.use_this_image') }}
                            </button>
                            <button type="button" id="ai-create-save-new-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('ai::image_edit.modal.save_as_new') }}
                            </button>
                            <button type="button" id="ai-create-replace-btn" class="btn btn-info d-none" data-action="ai">
                                {{ __('ai::image_edit.modal.replace') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function ($) {
                var aiCreateRoutes = {
                    generate: @json(route('admin.ai.image-edits.generate')),
                    apply: @json(route('admin.ai.image-edits.apply')),
                };
                var aiCreateMessages = {
                    genericError: @json(__('An Error Occurred!')),
                    promptRequired: @json(__('This field is required.')),
                    applied: @json(__('ai::image_edit.messages.applied')),
                    staged: @json(__('ai::image_edit.messages.staged')),
                };

                var $modal = null;
                var currentTrigger = null;
                var currentFileInput = null;
                var currentToken = null;
                var currentResultDataUri = null;
                var currentMode = null;

                function getModal() {
                    if (!$modal) {
                        $modal = new bootstrap.Modal(document.getElementById('ai-create-modal'));
                    }

                    return $modal;
                }

                function extensionForMime(mime) {
                    if (mime === 'image/jpeg' || mime === 'image/jpg') return 'jpg';
                    if (mime === 'image/webp') return 'webp';
                    return 'png';
                }

                function resetModal() {
                    $('#ai-create-alert, #ai-create-staged-hint').addClass('d-none').text('');
                    $('#ai-create-prompt').val('');
                    $('#ai-create-use-brand').prop('checked', true);
                    $('#ai-create-result-img').addClass('d-none').attr('src', '');
                    $('#ai-create-result-placeholder').removeClass('d-none');
                    $('#ai-create-loading').addClass('d-none');
                    $('#ai-create-save-new-btn, #ai-create-replace-btn, #ai-create-use-btn').addClass('d-none').prop('disabled', false);
                    $('#ai-create-generate-btn').prop('disabled', false);
                    currentToken = null;
                    currentResultDataUri = null;
                    currentMode = null;
                }

                function showError(message) {
                    $('#ai-create-alert').removeClass('d-none').text(message || aiCreateMessages.genericError);
                }

                // Reused across pages so the sibling "Edit with AI" button also
                // reveals itself once a file is staged, in case only the
                // "Create with AI" component was rendered on a given page.
                $(document).on('change', '.image-input input[type="file"]', function () {
                    if (!this.files || !this.files[0]) {
                        return;
                    }

                    $(this).closest('.image-input').find('.ai-edit-trigger').removeClass('d-none');
                });

                $(document).on('click', '.ai-create-trigger', function () {
                    currentTrigger = this;
                    resetModal();

                    var $imageInput = $(this).closest('.image-input');
                    currentFileInput = $imageInput.find('input[type="file"]').get(0) || null;

                    getModal().show();
                });

                $(document).on('click', '#ai-create-generate-btn', function () {
                    if (!currentTrigger) {
                        return;
                    }

                    var prompt = $('#ai-create-prompt').val().trim();
                    if (!prompt) {
                        showError(aiCreateMessages.promptRequired);
                        return;
                    }

                    var $trigger = $(currentTrigger);

                    $('#ai-create-alert').addClass('d-none');
                    $('#ai-create-result-placeholder').addClass('d-none');
                    $('#ai-create-result-img').addClass('d-none');
                    $('#ai-create-save-new-btn, #ai-create-replace-btn, #ai-create-use-btn').addClass('d-none');
                    $('#ai-create-loading').removeClass('d-none');
                    $(this).prop('disabled', true);

                    $.ajax({
                        url: aiCreateRoutes.generate,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            mode: 'create',
                            target: $trigger.data('ai-target'),
                            id: $trigger.data('ai-id'),
                            field: $trigger.data('ai-field'),
                            prompt: prompt,
                            use_brand_logo: $('#ai-create-use-brand').length ? ($('#ai-create-use-brand').is(':checked') ? 1 : 0) : 1,
                        },
                        success: function (response) {
                            $('#ai-create-loading').addClass('d-none');
                            $('#ai-create-generate-btn').prop('disabled', false);

                            if (!response || !response.success) {
                                $('#ai-create-result-placeholder').removeClass('d-none');
                                showError(response && response.error);
                                return;
                            }

                            currentToken = response.token || null;
                            currentMode = response.mode;
                            currentResultDataUri = response.previewUrl;

                            $('#ai-create-result-img').attr('src', response.previewUrl).removeClass('d-none');

                            if (currentMode === 'persisted') {
                                $('#ai-create-save-new-btn, #ai-create-replace-btn').removeClass('d-none');
                            } else {
                                $('#ai-create-use-btn').removeClass('d-none');
                                $('#ai-create-staged-hint').removeClass('d-none');
                            }
                        },
                        error: function (xhr) {
                            $('#ai-create-loading').addClass('d-none');
                            $('#ai-create-result-placeholder').removeClass('d-none');
                            $('#ai-create-generate-btn').prop('disabled', false);
                            var message = xhr.responseJSON && (xhr.responseJSON.error
                                || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors)[0][0]));
                            showError(message);
                        },
                    });
                });

                function updateTriggerPreview(url) {
                    if (!currentTrigger) {
                        return;
                    }

                    var $imageInput = $(currentTrigger).closest('.image-input');
                    $imageInput.css('background-image', 'url(' + url + ')');
                    $imageInput.find('.image-input-wrapper').css('background-image', 'url(' + url + ')');
                    $imageInput.find('.ai-edit-trigger')
                        .data('ai-image', url)
                        .attr('data-ai-image', url)
                        .removeClass('d-none');
                }

                function applyEdit(action) {
                    if (!currentToken) {
                        return;
                    }

                    $('#ai-create-save-new-btn, #ai-create-replace-btn').prop('disabled', true);

                    $.ajax({
                        url: aiCreateRoutes.apply,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            token: currentToken,
                            action: action,
                        },
                        success: function (response) {
                            $('#ai-create-save-new-btn, #ai-create-replace-btn').prop('disabled', false);

                            if (!response || !response.success) {
                                showError(response && response.error);
                                return;
                            }

                            updateTriggerPreview(response.url);

                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || aiCreateMessages.applied);
                            }

                            getModal().hide();
                        },
                        error: function (xhr) {
                            $('#ai-create-save-new-btn, #ai-create-replace-btn').prop('disabled', false);
                            var message = xhr.responseJSON && xhr.responseJSON.error;
                            showError(message);
                        },
                    });
                }

                $(document).on('click', '#ai-create-replace-btn', function () {
                    applyEdit('replace');
                });

                $(document).on('click', '#ai-create-save-new-btn', function () {
                    applyEdit('new_version');
                });

                // Ad-hoc mode: stage the generated image straight into the
                // same <input type="file"> so it uploads with the form.
                $(document).on('click', '#ai-create-use-btn', function () {
                    if (!currentResultDataUri || !currentFileInput) {
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true);

                    fetch(currentResultDataUri)
                        .then(function (res) { return res.blob(); })
                        .then(function (blob) {
                            var filename = 'ai-create-' + Date.now() + '.' + extensionForMime(blob.type);
                            var file = new File([blob], filename, { type: blob.type });

                            var dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            currentFileInput.files = dataTransfer.files;
                            currentFileInput.dispatchEvent(new Event('change', { bubbles: true }));

                            updateTriggerPreview(currentResultDataUri);

                            if (typeof toastr !== 'undefined') {
                                toastr.success(aiCreateMessages.staged);
                            }

                            $btn.prop('disabled', false);
                            getModal().hide();
                        })
                        .catch(function () {
                            $btn.prop('disabled', false);
                            showError(aiCreateMessages.genericError);
                        });
                });
            })(jQuery);
        </script>
    @endpush
@endonce
