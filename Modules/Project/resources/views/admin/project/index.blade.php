@section('title', __('project::project.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::project.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.project-statuses.index') }}">
            <i class="bi bi-palette me-1"></i>{{ __('project::project.actions.manage_statuses') }}
        </a>
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.projects.create') }}">
            {{ __('project::project.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('project::project.filters.title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-4 align-items-end">
                <div class="col-md-4">
                    <label for="company_id" class="form-label">{{ __('project::project.fields.company') }}</label>
                    <select id="company_id" name="company_id" class="form-select form-select-solid"
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
                    <label for="project_status_id" class="form-label">{{ __('project::project.fields.status') }}</label>
                    <select id="project_status_id" name="project_status_id" class="form-select form-select-solid"
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
                    <button type="submit" class="btn btn-primary">
                        {{ __('project::project.actions.apply_filters') }}
                    </button>
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-light">
                        {{ __('project::project.actions.clear_filters') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="__('project::project.search.placeholder')" :formUrl="route('admin.projects.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('project::project.fields.title') }}</th>
            <th>{{ __('project::project.fields.company') }}</th>
            <th>{{ __('project::project.fields.status') }}</th>
            <th>{{ __('project::project.fields.deal') }}</th>
            <th>{{ __('project::project.fields.budget') }}</th>
            <th>{{ __('project::project.fields.payment_status') }}</th>
            <th>{{ __('project::project.fields.start_date') }}</th>
            <th>{{ __('project::project.fields.due_date') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $project)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $project->id }}"/>
                    </div>
                </td>
                <td>{{ $project->title }}</td>
                <td>
                    @if($project->company)
                        <a href="{{ route('admin.companies.show', $project->company_id) }}" class="text-hover-primary">
                            {{ $project->company->name }}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>
                    @if($project->status)
                        <span class="badge" style="background-color: {{ $project->status->color_code }}; color: #fff;">
                            {{ $project->status->name }}
                        </span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>
                    @if($project->deal)
                        <a href="{{ route('admin.deals.show', $project->deal_id) }}" class="text-hover-primary">
                            {{ $project->deal->title }}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>{{ $project->budget !== null ? number_format($project->budget, 2) : __('N/A') }}</td>
                <td>
                    @php
                        $paymentColor = match($project->payment_status) {
                            'fully_paid' => 'success',
                            'partially_paid' => 'warning',
                            default => 'danger',
                        };
                    @endphp
                    <span class="badge badge-light-{{ $paymentColor }}">
                        {{ __('project::project.payment_status.'.$project->payment_status) }}
                    </span>
                </td>
                <td>{{ $project->start_date?->format('Y-m-d') ?: __('N/A') }}</td>
                <td>{{ $project->due_date?->format('Y-m-d') ?: __('N/A') }}</td>
                <td>{{ $project->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.projects.edit', $project->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.projects.destroy', $project->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
