@section('title', __('crm::deal.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::deal.menu.deals'), 'url' => route('admin.deals.index')],
            ['label' => __('crm::deal.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::deal.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.deals.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::deal.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::deal.pages.edit_title')" :formUrl="route('admin.deals.update', $deal->id)" :cancelUrl="route('admin.deals.index')" icon="briefcase" color="success">
        @method('PUT')
        @include('crm::admin.deal._form', ['deal' => $deal])
    </x-admin.create-card>
</x-admin-layout>
