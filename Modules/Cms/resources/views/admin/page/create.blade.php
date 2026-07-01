@section('title', __('Add New Page'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Pages', 'url' => route('admin.pages.index')],
            ['label' => 'Add New Page'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle='Add New Page'
        :breadcrumbItems="$breadcrumbItems"
        pageDescription='Create a new static page with navigation and SEO options.'
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.pages.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Add New Page"
        :formUrl="route('admin.pages.store')"
        description='Configure page content, placement in menus, and SEO preview.'
        :cancelUrl="route('admin.pages.index')"
        id="page-form"
    >
        @include('cms::admin.page._form')
    </x-admin.create-card>
</x-admin-layout>
