@section('title', __('project::status.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::status.pages.index_title'), 'url' => route('admin.project-statuses.index')],
            ['label' => __('project::status.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::status.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.project-statuses.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('project::status.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('project::status.pages.create_title')" :formUrl="route('admin.project-statuses.store')" :cancelUrl="route('admin.project-statuses.index')">
        @include('project::admin.project_status._form')
    </x-admin.create-card>
</x-admin-layout>
