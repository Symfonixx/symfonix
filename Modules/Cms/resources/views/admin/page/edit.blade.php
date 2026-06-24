@section('title', __('Edit Page'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('Pages'), 'url' => route('admin.pages.index')],
            ['label' => __('Edit Page')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('Edit Page')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('Update page content, navigation placement, and SEO preview.')"
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.pages.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Edit Page"
        :formUrl="route('admin.pages.update', $page->id)"
        :description="__('Update content and review the live SEO preview in the sidebar.')"
        :cancelUrl="route('admin.pages.index')"
        id="page-form"
    >
        @method('PUT')
        @include('cms::admin.page._form', ['page' => $page])
    </x-admin.create-card>
</x-admin-layout>
