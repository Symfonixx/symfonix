@php
    $editorSelector = $selector ?? '#tinymce';
    $editorHeight = $height ?? 500;
@endphp

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/{{ Config::get('core.tinymce_key') }}/tinymce/7/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = @json($editorSelector);

            if (typeof tinymce === 'undefined' || !document.querySelector(selector)) {
                return;
            }

            tinymce.init({
                selector: selector,
                height: {{ (int) $editorHeight }},
                menubar: false,
                branding: false,
                promotion: false,
                plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'code | undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                @if(app()->getLocale() === 'ar')
                language: 'ar',
                @endif
                setup: function (editor) {
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

                form.addEventListener('submit', function () {
                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                    }
                });
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
