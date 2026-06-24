@section('title', __('crm::lead.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::lead.menu.leads')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::lead.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.leads.create') }}">
            {{ __('crm::lead.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="__('crm::lead.search.placeholder')"
                   :formUrl="route('admin.leads.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('crm::lead.fields.details') }}</th>
            <th>{{ __('crm::lead.fields.company') }}</th>
            <th>{{ __('crm::lead.fields.source') }}</th>
            <th>{{ __('crm::lead.fields.service_interest') }}</th>
            <th>{{ __('crm::lead.fields.project_budget') }}</th>
            <th>{{ __('crm::lead.fields.status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $lead)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $lead->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 mb-1 fw-semibold">
                            {{ $lead->name ?? __('N/A') }}
                        </span>
                        @if($lead->email)
                            <a class="text-hover-primary text-gray-500 fs-7" target="_blank"
                               href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                        @endif
                    </div>
                </td>
                <td>
                    @if($lead->company)
                        <a href="{{ route('admin.companies.show', $lead->company) }}" class="text-hover-primary text-gray-800">
                            {{ $lead->company->name }}
                        </a>
                    @elseif($lead->company_name)
                        <span class="text-gray-600">{{ $lead->company_name }}</span>
                        <span class="badge badge-light-secondary fs-8 ms-1">{{ __('crm::lead.badges.unlinked') }}</span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>
                    @if($lead->source)
                        <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::sourceBadgeColor($lead->source) }}">
                            {{ __('crm::lead.sources.' . $lead->source) }}
                        </span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>
                    @if($lead->service)
                        {{ $lead->service->getTranslation('title', app()->getLocale()) }}
                    @else
                        {{ $lead->service_interest ?? __('N/A') }}
                    @endif
                </td>
                <td>{{ $lead->project_budget ?? __('N/A') }}</td>
                <td>
                    <span class="badge badge-light-{{ $lead->blocked ? 'danger' : 'success' }}">
                        {{ $lead->blocked ? __('crm::lead.status.blocked') : __('crm::lead.status.active') }}
                    </span>
                </td>
                <td>{{ $lead->created_at }}</td>
                <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                        <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-light-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-sm btn-light-info">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($lead->blocked)
                            <form method="POST" action="{{ route('admin.leads.unblock', $lead) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light-warning" title="{{ __('crm::lead.actions.unblock') }}">
                                    <i class="bi bi-unlock"></i>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.leads.block', $lead) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light-danger" title="{{ __('crm::lead.actions.block') }}">
                                    <i class="bi bi-lock"></i>
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-sm btn-light-danger" title="{{ __('Delete') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
