@section('title', __('project::use_case.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('project::project.menu.projects'), 'url' => route('admin.projects.index')],
            ['label' => __('project::use_case.menu.use_cases')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('project::use_case.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('use-cases.index') }}" target="_blank">
            <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('project::use_case.actions.view_on_site') }}
        </a>
        <x-can perform="project.use_cases.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.project-use-cases.create') }}">
                {{ __('project::use_case.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="__('Search')" :formUrl="route('admin.project-use-cases.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('project::use_case.fields.image') }}</th>
            <th>{{ __('project::use_case.fields.title') }}</th>
            <th>{{ __('project::use_case.fields.client_name') }}</th>
            <th>{{ __('project::use_case.fields.category_tag') }}</th>
            <th>{{ __('project::use_case.fields.status') }}</th>
            <th>{{ __('project::use_case.fields.visits') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $useCase)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $useCase->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="symbol symbol-50px">
                        <img src="{{ $useCase->image_link }}" alt="{{ $useCase->title }}" class="rounded"/>
                    </div>
                </td>
                <td>{{ $useCase->title }}</td>
                <td>{{ $useCase->client_name ?: __('N/A') }}</td>
                <td>{{ $useCase->category_tag ?: __('N/A') }}</td>
                <td>
                    <span class="badge badge-light-{{ $useCase->status === 'Published' ? 'success' : 'secondary' }}">
                        {{ __($useCase->status) }}
                    </span>
                    @if($useCase->featured)
                        <span class="badge badge-light-primary ms-1">{{ __('project::use_case.fields.featured') }}</span>
                    @endif
                </td>
                <td>{{ number_format($useCase->visits) }}</td>
                <td>{{ $useCase->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('use-cases.show', $useCase->slug) }}" target="_blank"
                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                        <i class="bi bi-box-arrow-up-right fs-5"></i>
                    </a>
                    <a href="{{ route('admin.project-use-cases.edit', $useCase->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.project-use-cases.destroy', $useCase->id) }}">
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
