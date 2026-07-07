@section('title', __('project::project.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => $project->title],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="$project->title" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.projects.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('project::project.actions.back_to_list') }}
        </a>
        @can('update', $project)
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.projects.edit', $project) }}">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
            </a>
        @endcan
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.sections.basic_information') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.title') }}</div>
                <div class="col-md-9">{{ $project->title }}</div>
            </div>
            @if($project->description)
                <div class="row mb-6">
                    <div class="col-md-3 fw-bold">{{ __('project::project.fields.description') }}</div>
                    <div class="col-md-9">{{ $project->description }}</div>
                </div>
            @endif
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.company') }}</div>
                <div class="col-md-9">
                    @if($project->company)
                        <a href="{{ route('admin.companies.show', $project->company_id) }}" class="text-hover-primary">
                            {{ $project->company->name }}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.status') }}</div>
                <div class="col-md-9">
                    @if($project->status)
                        <span class="badge" style="background-color: {{ $project->status->color_code }}; color: #fff;">
                            {{ $project->status->name }}
                        </span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.deal') }}</div>
                <div class="col-md-9">
                    @if($project->deal)
                        <a href="{{ route('admin.deals.show', $project->deal_id) }}" class="text-hover-primary">
                            {{ $project->deal->title }}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.budget') }}</div>
                <div class="col-md-9">
                    {{ $project->budget !== null ? number_format($project->budget, 2) : __('N/A') }}
                </div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.start_date') }}</div>
                <div class="col-md-9">{{ $project->start_date?->format('Y-m-d') ?: __('N/A') }}</div>
            </div>
            <div class="row mb-6">
                <div class="col-md-3 fw-bold">{{ __('project::project.fields.due_date') }}</div>
                <div class="col-md-9">{{ $project->due_date?->format('Y-m-d') ?: __('N/A') }}</div>
            </div>
            @if(!empty($project->attachments))
                <div class="row mb-6">
                    <div class="col-md-3 fw-bold">{{ __('project::project.fields.attachments') }}</div>
                    <div class="col-md-9">
                        @foreach($project->attachments as $attachment)
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-paperclip"></i>
                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="text-hover-primary">
                                    {{ $attachment['name'] ?? basename($attachment['path']) }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @include('project::admin.project._invoices')
</x-admin-layout>
