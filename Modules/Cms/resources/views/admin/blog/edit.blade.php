@section('title', __('Edit Blog'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Blogs', 'url' => route('admin.blogs.index')],
            ['label' => 'Edit Blog'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle='Edit Blog'
        :breadcrumbItems="$breadcrumbItems"
        pageDescription='Update blog content, publishing options, and SEO preview.'
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.blogs.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Edit Blog"
        :formUrl="route('admin.blogs.update', $blog->id)"
        description='Update content and review the live SEO preview in the sidebar.'
        :cancelUrl="route('admin.blogs.index')"
        id="blog-form"
    >
        @method('PUT')
        @include('cms::admin.blog._form', ['blog' => $blog])
    </x-admin.create-card>
</x-admin-layout>
