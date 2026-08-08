@section('title', __('Create Job Position'))

@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Create Job Position')" :breadcrumbItems="[
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
        ['label' => __('Job Positions'), 'url' => route('admin.job-positions.index')],
        ['label' => __('Create')],
    ]"/>
@endsection

@section('js')
    @include('base::shared._tinymce', ['selector' => '#job-description-editor', 'height' => 450])
    @include('base::shared._tinymce', ['selector' => '#job-requirements-editor', 'height' => 350])
@endsection

<x-admin-layout>
    <form method="POST" action="{{ route('admin.job-positions.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                @include('user::admin.recruitment.positions._form')
            </div>
            <div class="card-footer d-flex justify-content-end gap-3">
                <a href="{{ route('admin.job-positions.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('Create Position') }}</button>
            </div>
        </div>
    </form>
</x-admin-layout>
