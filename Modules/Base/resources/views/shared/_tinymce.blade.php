@push('scripts')
    <script src="https://cdn.tiny.cloud/1/{{ Config::get('core.tinymce_key') }}/tinymce/7/tinymce.min.js"></script>
@endpush

<script>
    $(document).ready(function () {
        tinymce.init({
            selector: 'textarea',
            height: 750,
            plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'code| undo redo | blocks fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            @if(app()->getLocale() == 'ar') language: 'ar', @endif
            setup: function (editor) {
                editor.on('SetContent', function (e) {
                    // Cleanup on load
                    cleanFontStyles(editor);
                });
                editor.on('Change', function (e) {
                    // Cleanup on change (typing/pasting)
                    cleanFontStyles(editor);
                });
            }
        });

        function cleanFontStyles(editor) {
            // Select all elements (like <span> or <p>) that might contain font styles
            var elements = editor.dom.select('span[style], p[style], div[style]');

            tinymce.each(elements, function (element) {
                // Remove the style attribute if it contains 'font-family'
                var style = editor.dom.getAttrib(element, 'style');
                if (style && style.includes('font-family')) {
                    // This line removes font-family from the style attribute
                    element.style.fontFamily = '';

                    // If the style attribute is now empty after removal, remove the style attribute entirely
                    if (!element.style.cssText) {
                        editor.dom.setAttrib(element, 'style', null);
                    }
                }

                // Also remove the deprecated <font> tag attribute
                editor.dom.setAttrib(element, 'face', null);
            });
        }
    });
</script>
