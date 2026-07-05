@section('title', __('crm::company.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::company.menu.companies')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::company.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.companies.create') }}">
            {{ __('crm::company.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card mb-5">
        <div class="card-body py-5">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="row g-4 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('crm::company.filters.activity_type') }}</label>
                    <select name="activity_type" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::company.filters.all') }}</option>
                        @foreach(\Modules\CRM\Models\Company::ACTIVITY_TYPES as $type)
                            <option value="{{ $type }}" @selected(($filters['activity_type'] ?? '') === $type)>
                                {{ __('crm::company.activity_types.' . $type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('crm::company.fields.status') }}</label>
                    <select name="status" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::company.filters.all') }}</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('crm::company.status.active') }}</option>
                        <option value="disabled" @selected(($filters['status'] ?? '') === 'disabled')>{{ __('crm::company.status.disabled') }}</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Filter') }}</button>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-light btn-sm">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :model="$model" :search="__('crm::company.search.placeholder')" :formUrl="route('admin.companies.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('crm::company.fields.name') }}</th>
            <th>{{ __('crm::company.fields.activity_type') }}</th>
            <th>{{ __('crm::company.fields.customer') }}</th>
            <th>{{ __('crm::company.fields.email') }}</th>
            <th>{{ __('crm::company.fields.phone') }}</th>
            <th>{{ __('crm::company.fields.country') }}</th>
            <th>{{ __('crm::company.fields.city') }}</th>
            <th>{{ __('crm::company.fields.status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $company)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $company->id }}"/>
                    </div>
                </td>
                <td>{{ $company->name }}</td>
                <td>
                    @if($company->activity_type)
                        {{ __('crm::company.activity_types.' . $company->activity_type) }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>{{ $company->user?->name ?: __('N/A') }}</td>
                <td>{{ $company->email ?: __('N/A') }}</td>
                <td>{{ $company->phone ?: __('N/A') }}</td>
                <td>{{ $company->country ?: __('N/A') }}</td>
                <td>{{ $company->city ?: __('N/A') }}</td>
                <td>
                    <span class="badge badge-light-{{ $company->status === 'active' ? 'success' : 'danger' }}">
                        {{ __('crm::company.status.'.$company->status) }}
                    </span>
                </td>
                <td>{{ $company->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.companies.show', $company->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                        <i class="bi bi-eye fs-5"></i>
                    </a>
                    <a href="{{ route('admin.companies.edit', $company->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.companies.destroy', $company->id) }}" data-confirm-delete>
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
