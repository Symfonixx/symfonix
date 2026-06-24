@section('title', __('Edit FAQ'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('FAQs'), 'url' => route('admin.faqs.index')],
            ['label' => __('Edit FAQ')],
        ];
    @endphp
    <x-admin.breadcrumb
        :pageTitle="__('Edit FAQ')"
        :breadcrumbItems="$breadcrumbItems"
        :pageDescription="__('Update the question, answer, display order, and publish status.')"
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.faqs.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Edit FAQ"
        :formUrl="route('admin.faqs.update', $faq->id)"
        :description="__('Update content and publishing options in the sidebar.')"
        :cancelUrl="route('admin.faqs.index')"
        id="faq-form"
    >
        @method('PUT')
        @include('cms::admin.faq._form', ['faq' => $faq])
    </x-admin.create-card>
</x-admin-layout>
