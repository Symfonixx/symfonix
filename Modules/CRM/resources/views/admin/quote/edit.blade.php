@section('title', __('crm::quote.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::quote.pages.index_title'), 'url' => route('admin.quotes.index')],
            ['label' => $quote->quote_number, 'url' => route('admin.quotes.show', $quote)],
            ['label' => __('crm::quote.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::quote.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.quotes.show', $quote) }}">
            <i class="bi bi-eye me-1"></i>{{ __('crm::quote.pages.show_title') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('crm::quote.pages.edit_title')"
        :formUrl="route('admin.quotes.update', $quote)"
        :cancelUrl="route('admin.quotes.show', $quote)"
        icon="file-earmark-text"
        color="info">
        @method('PUT')
        @include('crm::admin.quote._form')
    </x-admin.create-card>
</x-admin-layout>
