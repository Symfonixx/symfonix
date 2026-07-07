@section('title', __('project::project.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::project.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::project.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.projects.show', $project) }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('project::project.actions.back_to_project') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('project::project.pages.edit_title')" :formUrl="route('admin.projects.update', $project->id)" :cancelUrl="route('admin.projects.show', $project)">
        @method('PUT')
        @include('project::admin.project._form', ['project' => $project])
    </x-admin.create-card>
</x-admin-layout>
