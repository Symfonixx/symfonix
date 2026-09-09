@section('title', __('crm::subscription.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::subscription.menu.subscriptions'), 'url' => route('admin.subscriptions.index')],
            ['label' => __('crm::subscription.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::subscription.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.subscriptions.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::subscription.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::subscription.pages.edit_title')" :formUrl="route('admin.subscriptions.update', $subscription->id)" :cancelUrl="route('admin.subscriptions.index')" icon="arrow-repeat" color="success">
        @method('PUT')
        @include('crm::admin.subscription._form', ['subscription' => $subscription])
    </x-admin.create-card>
</x-admin-layout>
