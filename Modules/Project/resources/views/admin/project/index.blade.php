@section('title', __('project::project.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects')],
        ];
        $hasFilters = filled($filters['company_id'] ?? null) || filled($filters['project_status_id'] ?? null);
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::project.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.project-statuses.index') }}">
            <i class="bi bi-palette me-1"></i>{{ __('project::project.actions.manage_statuses') }}
        </a>
        <x-can perform="project.projects.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.projects.create') }}">
                {{ __('project::project.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-5">
        <div class="card-body py-5">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-4 align-items-end">
                <div class="col-md-4">
                    <label for="company_id" class="form-label fs-7 text-muted mb-1">{{ __('project::project.fields.company') }}</label>
                    <select id="company_id" name="company_id" class="form-select form-select-solid form-select-sm"
                            data-control="select2" data-placeholder="{{ __('project::project.filters.all_companies') }}">
                        <option value="">{{ __('project::project.filters.all_companies') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((int) ($filters['company_id'] ?? 0) === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="project_status_id" class="form-label fs-7 text-muted mb-1">{{ __('project::project.fields.status') }}</label>
                    <select id="project_status_id" name="project_status_id" class="form-select form-select-solid form-select-sm"
                            data-control="select2" data-placeholder="{{ __('project::project.filters.all_statuses') }}">
                        <option value="">{{ __('project::project.filters.all_statuses') }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected((int) ($filters['project_status_id'] ?? 0) === $status->id)>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel me-1"></i>{{ __('project::project.actions.apply_filters') }}
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light">
                            {{ __('project::project.actions.clear_filters') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="__('project::project.search.placeholder')" :formUrl="route('admin.projects.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0 text-uppercase">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-200px">{{ __('project::project.fields.title') }}</th>
            <th class="min-w-100px">{{ __('project::project.fields.status') }}</th>
            <th class="min-w-120px">{{ __('project::project.fields.budget') }}</th>
            <th class="min-w-120px">{{ __('project::project.fields.payment_status') }}</th>
            <th class="min-w-125px">{{ __('project::project.fields.due_date') }}</th>
            <th class="min-w-100px">{{ __('Created At') }}</th>
            <th class="text-end min-w-100px"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $project)
            @php
                $paymentColor = match($project->payment_status) {
                    'fully_paid' => 'success',
                    'partially_paid' => 'warning',
                    default => 'danger',
                };
            @endphp
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $project->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.projects.show', $project) }}" class="text-gray-800 text-hover-primary fw-bold mb-1">
                            {{ $project->title }}
                        </a>
                        <span class="text-muted fs-7">
                            @if($project->company)
                                <a href="{{ route('admin.companies.show', $project->company_id) }}" class="text-muted text-hover-primary">
                                    {{ $project->company->name }}
                                </a>
                            @else
                                {{ __('N/A') }}
                            @endif
                            @if($project->deal)
                                <span class="mx-1">·</span>
                                <a href="{{ route('admin.deals.show', $project->deal_id) }}" class="text-muted text-hover-primary">
                                    {{ $project->deal->title }}
                                </a>
                            @endif
                        </span>
                    </div>
                </td>
                <td>
                    @include('project::admin.project._status_dropdown', ['project' => $project, 'statuses' => $statuses])
                </td>
                <td>
                    @if($project->budget !== null)
                        <span class="fw-bold text-gray-800">
                            <x-finance-money :amount="$project->budget" :currency="$project->currency ?? 'USD'" />
                        </span>
                    @else
                        <span class="text-muted">{{ __('N/A') }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-light-{{ $paymentColor }}">
                        {{ __('project::project.payment_status.'.$project->payment_status) }}
                    </span>
                </td>
                <td>
                    @if($project->due_date)
                        <span class="{{ $project->due_date->isPast() && ! $project->isCompleted() ? 'text-danger' : '' }}">
                            {{ $project->due_date->format('Y-m-d') }}
                        </span>
                    @else
                        <span class="text-muted">{{ __('N/A') }}</span>
                    @endif
                </td>
                <td>
                    <span class="text-muted fs-7" title="{{ $project->created_at }}">
                        {{ $project->created_at->diffForHumans() }}
                    </span>
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.projects.show', $project->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                       title="{{ __('View') }}">
                        <i class="bi bi-eye fs-5"></i>
                    </a>
                    <a href="{{ route('admin.projects.edit', $project->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                       title="{{ __('Edit') }}">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.projects.destroy', $project->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                title="{{ __('Delete') }}">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
