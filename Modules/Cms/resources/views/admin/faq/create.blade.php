@section('title', __('Add New FAQ'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'FAQs', 'url' => route('admin.faqs.index')],
            ['label' => 'Add New FAQ'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle="Add New FAQ"
        :breadcrumbItems="$breadcrumbItems"
        pageDescription="Add a frequently asked question and control its display order."
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.faqs.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        title="Add New FAQ"
        :formUrl="route('admin.faqs.store')"
        description="Write the question and answer, then set rank and publish status."
        :cancelUrl="route('admin.faqs.index')"
        id="faq-form"
    >
        @include('cms::admin.faq._form')
    </x-admin.create-card>
</x-admin-layout>
