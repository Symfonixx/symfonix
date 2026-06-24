@section('title', __('Add New Blog'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('Blogs'), 'url' => route('admin.blogs.index')],
            ['label' => __('Add New Blog')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('Add New Blog')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('Create a new blog post with SEO-optimized content.')"
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.blogs.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Add New Blog"
        :formUrl="route('admin.blogs.store')"
        :description="__('Fill in the content and use the sidebar to manage publishing and preview SEO.')"
        :cancelUrl="route('admin.blogs.index')"
        id="blog-form"
    >
        @include('cms::admin.blog._form')
    </x-admin.create-card>
</x-admin-layout>
