@section('title', __('project::project.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => $project->title],
        ];

        $paymentColor = match($project->payment_status) {
            'fully_paid' => 'success',
            'partially_paid' => 'warning',
            default => 'danger',
        };

        $activeTab = 'overview';
        if ($errors->hasAny(['employee_id', 'started_at', 'ended_at']) || ($errors->has('role') && old('employee_id'))) {
            $activeTab = 'team';
        } elseif (
            $errors->hasAny(['amount', 'expense_category_id', 'transaction_date', 'issued_at', 'due_at', 'tax_amount', 'lines'])
            || ($errors->has('currency') && (old('amount') || old('issued_at')))
            || ($errors->has('description') && old('amount'))
        ) {
            $activeTab = 'finance';
        }
        $currency = $profitAndLoss['currency'] ?? ($collectionSummary['currency'] ?? '');
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
    {{-- Project header --}}
    <div class="card mb-5 mb-xl-8">
        <div class="card-body py-6">
            <div class="d-flex flex-wrap flex-sm-nowrap align-items-center gap-5">
                <div class="symbol symbol-60px symbol-circle bg-light-primary flex-shrink-0">
                    <span class="symbol-label fs-2 fw-bold text-primary">
                        {{ mb_strtoupper(mb_substr($project->title, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <h1 class="fs-2 fw-bold text-gray-900 mb-0">{{ $project->title }}</h1>
                        @include('project::admin.project._status_dropdown', ['project' => $project, 'statuses' => $statuses])
                        <span class="badge badge-light-{{ $paymentColor }}">
                            {{ __('project::project.payment_status.'.$project->payment_status) }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-4 text-muted fs-7">
                        @if($project->company)
                            <span class="d-flex align-items-center">
                                <i class="bi bi-building me-1"></i>
                                <a href="{{ route('admin.companies.show', $project->company_id) }}" class="text-muted text-hover-primary">
                                    {{ $project->company->name }}
                                </a>
                            </span>
                        @endif
                        @if($project->budget !== null)
                            <span class="d-flex align-items-center">
                                <i class="bi bi-wallet2 me-1"></i>
                                {{ number_format($project->budget, 2) }} {{ $currency }}
                            </span>
                        @endif
                        @if($project->start_date || $project->due_date)
                            <span class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $project->start_date?->format('Y-m-d') ?: '—' }}
                                →
                                {{ $project->due_date?->format('Y-m-d') ?: '—' }}
                            </span>
                        @endif
                        @if($project->deal)
                            <span class="d-flex align-items-center">
                                <i class="bi bi-briefcase me-1"></i>
                                <a href="{{ route('admin.deals.show', $project->deal_id) }}" class="text-muted text-hover-primary">
                                    {{ $project->deal->title }}
                                </a>
                            </span>
                        @endif
                        @if($project->services->isNotEmpty())
                            <span class="d-flex align-items-center flex-wrap gap-1">
                                <i class="bi bi-grid me-1"></i>
                                @foreach($project->services as $service)
                                    <span class="badge badge-light-primary">
                                        {{ $service->getTranslation('title', app()->getLocale()) }}
                                    </span>
                                @endforeach
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card card-flush">
                <div class="card-header pt-5 border-0">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 {{ $activeTab === 'overview' ? 'active' : '' }}"
                               data-bs-toggle="tab" href="#project_tab_overview">
                                {{ __('project::project.tabs.overview') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 {{ $activeTab === 'team' ? 'active' : '' }}"
                               data-bs-toggle="tab" href="#project_tab_team">
                                {{ __('project::project.tabs.team') }}
                                <span class="badge badge-circle badge-light-primary ms-2">{{ count($profitAndLoss['assignments']) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 {{ $activeTab === 'finance' ? 'active' : '' }}"
                               data-bs-toggle="tab" href="#project_tab_finance">
                                {{ __('project::project.tabs.finance') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 {{ $activeTab === 'review' ? 'active' : '' }}"
                               data-bs-toggle="tab" href="#project_tab_review">
                                {{ __('project::project.tabs.review') }}
                                @if($project->testimonial)
                                    <span class="badge badge-circle badge-light-{{ $project->testimonial->status === 'Published' ? 'success' : 'warning' }} ms-2">1</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }}" id="project_tab_overview" role="tabpanel">
                            @include('project::admin.project._overview')
                        </div>
                        <div class="tab-pane fade {{ $activeTab === 'team' ? 'show active' : '' }}" id="project_tab_team" role="tabpanel">
                            @include('project::admin.project._team')
                        </div>
                        <div class="tab-pane fade {{ $activeTab === 'finance' ? 'show active' : '' }}" id="project_tab_finance" role="tabpanel">
                            @include('project::admin.project._finance')
                        </div>
                        <div class="tab-pane fade {{ $activeTab === 'review' ? 'show active' : '' }}" id="project_tab_review" role="tabpanel">
                            @include('project::admin.project._review')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            @include('project::admin.project._sidebar')
        </div>
    </div>

    @include('project::admin.project._modals')
</x-admin-layout>
