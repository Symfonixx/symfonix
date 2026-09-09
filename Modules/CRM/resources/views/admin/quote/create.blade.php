@section('title', __('crm::quote.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::quote.pages.index_title'), 'url' => route('admin.quotes.index')],
            ['label' => __('crm::quote.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::quote.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.quotes.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::quote.pages.index_title') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::quote.pages.create_title')"
        :formUrl="route('admin.quotes.store')"
        :cancelUrl="route('admin.quotes.index')"
        icon="file-earmark-text"
        color="info">
        @include('crm::admin.quote._form')
    </x-admin.create-card>
</x-admin-layout>
