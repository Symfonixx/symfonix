@include('base::shared._tinymce', ['selector' => '#tinymce', 'height' => 560])

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tagifyInput = document.querySelector('#kt_tagify_1');
        if (tagifyInput && typeof Tagify !== 'undefined') {
            new Tagify(tagifyInput);
        }
    });
</script>
@endpush
