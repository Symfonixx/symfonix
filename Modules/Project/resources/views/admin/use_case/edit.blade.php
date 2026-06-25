@section('title', __('project::use_case.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::use_case.menu.use_cases'), 'url' => route('admin.project-use-cases.index')],
            ['label' => __('project::use_case.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::use_case.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

@section('js')
    @include('base::shared._tinymce')
    <script>
        $(document).ready(function () {
            new Tagify(document.querySelector('#kt_tagify_tech'));
        });
    </script>
@endsection

<x-admin-layout>
    <x-admin.create-card :title="__('project::use_case.pages.edit_title')" :formUrl="route('admin.project-use-cases.update', $useCase->id)" :cancelUrl="route('admin.project-use-cases.index')">
        @method('PUT')
        @include('project::admin.use_case._form', ['useCase' => $useCase])
    </x-admin.create-card>
</x-admin-layout>
