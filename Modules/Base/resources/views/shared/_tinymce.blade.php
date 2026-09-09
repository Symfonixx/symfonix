@php
    $editorSelector = $selector ?? '#tinymce';
    $editorHeight = $height ?? 500;
    $editorToolbar = $toolbar ?? 'code | undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat';
    $editorPlugins = $plugins ?? 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount';
    $lfmUrl = route('admin.unisharp.lfm.show');
@endphp

@once('tinymce-cdn')
    @push('scripts')
        <script src="https://cdn.tiny.cloud/1/{{ Config::get('core.tinymce_key') }}/tinymce/7/tinymce.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = @json($editorSelector);
            const lfmUrl = @json($lfmUrl);

            if (typeof tinymce === 'undefined' || typeof tinymce.init !== 'function' || !document.querySelector(selector)) {
                return;
            }

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
                    editor.on('init', function () {
                        editor.targetElm.removeAttribute('required');
                        @if(app()->getLocale() === 'ar')
                        editor.getBody().setAttribute('dir', 'rtl');
                        @endif
                    });
                    editor.on('SetContent', function () {
                        cleanFontStyles(editor);
                    });
                    editor.on('Change', function () {
                        cleanFontStyles(editor);
                    });
                }
            });

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
        });
    </script>
@endpush
