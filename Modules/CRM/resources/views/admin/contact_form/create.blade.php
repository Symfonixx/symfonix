@section('title', __('crm::contact_form.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact_form.pages.index_title'), 'url' => route('admin.contact_forms.index')],
            ['label' => __('crm::contact_form.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact_form.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.contact_forms.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::contact_form.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('crm::contact_form.pages.create_title')" :formUrl="route('admin.contact_forms.store')" :cancelUrl="route('admin.contact_forms.index')" icon="inbox" color="info">
        @include('crm::admin.contact_form._form')
    </x-admin.create-card>
</x-admin-layout>
