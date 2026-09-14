@section('title', __('project::status.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::status.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::status.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.projects.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('project::status.actions.back_to_projects') }}
        </a>
        <x-can perform="project.statuses.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.project-statuses.create') }}">
                {{ __('project::status.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="dataTable">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 gs-0">
                        <th>{{ __('project::status.fields.sort_order') }}</th>
                        <th>{{ __('project::status.fields.name') }}</th>
                        <th>{{ __('project::status.fields.color_code') }}</th>
                        <th>{{ __('project::status.fields.projects_count') }}</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($statuses as $status)
                        <tr>
                            <td>{{ $status->sort_order }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $status->color_code }}; color: #fff;">
                                    {{ $status->name }}
                                </span>
                            </td>
                            <td><code>{{ $status->color_code }}</code></td>
                            <td>{{ $status->projects_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.project-statuses.edit', $status->id) }}"
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="ki-duotone ki-message-edit fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.project-statuses.destroy', $status->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-10">
                                {{ __('No records found') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
