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
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.projects.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('project::project.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    @if(isset($collectionSummary))
        <div class="card mb-6">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title fw-bold">{{ __('project::project.sections.collection') }}</h3>
            </div>
            <div class="card-body pt-0">
                <div class="row g-5">
                    <div class="col-md-3">
                        <div class="text-muted fs-7">{{ __('project::project.fields.payment_status') }}</div>
                        @php
                            $paymentColor = match($collectionSummary['payment_status']) {
                                'fully_paid' => 'success',
                                'partially_paid' => 'warning',
                                default => 'danger',
                            };
                        @endphp
                        <span class="badge badge-light-{{ $paymentColor }} mt-1">
                            {{ __('project::project.payment_status.'.$collectionSummary['payment_status']) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted fs-7">{{ __('project::project.fields.collected') }}</div>
                        <div class="fw-bold fs-4">
                            {{ number_format($collectionSummary['collected'], 2) }} {{ $collectionSummary['currency'] }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted fs-7">{{ __('project::project.fields.budget') }}</div>
                        <div class="fw-bold fs-4">
                            {{ number_format($collectionSummary['budget'], 2) }} {{ $collectionSummary['currency'] }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted fs-7">{{ __('project::project.fields.collection_rate') }}</div>
                        <div class="fw-bold fs-4">{{ $collectionSummary['collection_rate'] }}%</div>
                        <div class="progress h-6px mt-2">
                            <div class="progress-bar bg-primary" style="width: {{ $collectionSummary['collection_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-admin.create-card :title="__('project::project.pages.edit_title')" :formUrl="route('admin.projects.update', $project->id)" :cancelUrl="route('admin.projects.index')">
        @method('PUT')
        @include('project::admin.project._form', ['project' => $project])
    </x-admin.create-card>
</x-admin-layout>
