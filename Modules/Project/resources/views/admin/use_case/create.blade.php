@section('title', __('project::use_case.pages.create_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::use_case.menu.use_cases'), 'url' => route('admin.project-use-cases.index')],
            ['label' => __('project::use_case.pages.create_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::use_case.pages.create_title')" :breadcrumbItems="$breadcrumbItems"/>
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
    <x-admin.create-card :title="__('project::use_case.pages.create_title')" :formUrl="route('admin.project-use-cases.store')" :cancelUrl="route('admin.project-use-cases.index')">
        @include('project::admin.use_case._form')
    </x-admin.create-card>
</x-admin-layout>
