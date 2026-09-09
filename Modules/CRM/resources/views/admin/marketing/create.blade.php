@section('title', __('crm::marketing.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => __('crm::marketing.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.crm.marketing.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
    </div>
@endsection

@section('js')
    @include('base::shared._tinymce', [
        'selector' => '#subject',
        'height' => 120,
        'toolbar' => 'bold italic underline strikethrough | removeformat',
        'plugins' => 'autolink',
    ])

    @include('base::shared._tinymce', [
        'selector' => '#body',
        'height' => 450,
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.querySelector('#custom_emails');
            if (!input || typeof Tagify === 'undefined') {
                return;
            }

            new Tagify(input, {
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                delimiters: ',| ',
                dropdown: {enabled: 0},
                editTags: {clicks: 1, keepInvalid: false},
            });
        });
    </script>
@endsection

<x-admin-layout>
    <div class="card settings-form-card">
        <div class="card-header border-0 pt-6">
        <div class="card-title d-flex align-items-center gap-3">
            <span class="sx-form-icon bg-light-info text-info">
                <i class="bi bi-megaphone"></i>
            </span>
            <div>
                <h2 class="fw-bold mb-1">{{ __('crm::marketing.pages.create_title') }}</h2>
                <span class="text-muted fs-7 fw-semibold">{{ __('crm::marketing.sections.compose_hint') }}</span>
            </div>
        </div>
        </div>
        <form method="POST" action="{{ route('admin.crm.marketing.store') }}">
            @csrf
            <div class="card-body pt-2">
                @include('crm::admin.marketing._form')
            </div>
            <div class="card-footer settings-form-footer d-flex justify-content-between align-items-center py-5 px-9">
                <span class="text-muted fs-7">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ __('crm::marketing.hints.send_warning') }}
                </span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.crm.marketing.index') }}" class="btn btn-light btn-active-light-primary">
                        <i class="bi bi-x-lg me-1"></i>{{ __('Discard') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>{{ __('crm::marketing.actions.send') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
