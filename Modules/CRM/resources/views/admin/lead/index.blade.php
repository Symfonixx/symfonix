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
        <x-can perform="crm.leads.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.leads.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('crm::lead.actions.add') }}
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card sx-filter-bar mb-5">
        <div class="card-body py-5">
            <form method="GET" action="{{ route('admin.leads.index') }}" class="row g-4 align-items-end">
                <div class="col-md-4 col-xl-2">
                    <label class="form-label">{{ __('crm::lead.filters.status') }}</label>
                    <select name="status" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::lead.filters.all') }}</option>
                        @foreach(\Modules\CRM\Models\Lead::STATUSES as $leadStatus)
                            <option value="{{ $leadStatus }}" @selected(($filters['status'] ?? '') === $leadStatus)>
                                {{ __('crm::lead.status.' . $leadStatus) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label">{{ __('crm::lead.filters.source') }}</label>
                    <select name="source" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::lead.filters.all') }}</option>
                        @foreach(\Modules\CRM\Models\Lead::SOURCES as $source)
                            <option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>
                                {{ __('crm::lead.sources.' . $source) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label">{{ __('crm::lead.filters.company') }}</label>
                    <select name="company_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::lead.filters.all') }}</option>
                        @foreach(($companies ?? collect()) as $company)
                            <option value="{{ $company->id }}" @selected((int) ($filters['company_id'] ?? 0) === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label">{{ __('crm::lead.filters.assignee') }}</label>
                    <select name="assigned_to" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::lead.filters.all') }}</option>
                        @foreach(($assignees ?? collect()) as $assignee)
                            <option value="{{ $assignee->id }}" @selected((int) ($filters['assigned_to'] ?? 0) === $assignee->id)>
                                {{ $assignee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label">{{ __('crm::lead.filters.tags') }}</label>
                    <select name="tag_id" class="form-select form-select-solid" data-control="select2">
                        <option value="">{{ __('crm::lead.filters.all') }}</option>
                        @foreach(($tags ?? collect()) as $tag)
                            <option value="{{ $tag->id }}" @selected((int) ($filters['tag_id'] ?? 0) === $tag->id)>
                                {{ $tag->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Reset') }}
                    </a>
                </div>
            </form>

            @if(($tags ?? collect())->isNotEmpty())
                <div class="separator my-5"></div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted fs-7 fw-semibold me-1">{{ __('crm::lead.fields.tags') }}:</span>
                    <a href="{{ route('admin.leads.index', collect($filters)->except('tag_id')->filter()->all()) }}"
                       class="badge {{ empty($filters['tag_id']) ? 'badge-primary' : 'badge-light' }} cursor-pointer text-decoration-none px-3 py-2">
                        {{ __('crm::lead.filters.all') }}
                    </a>
                    @foreach($tags as $tag)
                        @php
                            $isActiveTag = (int) ($filters['tag_id'] ?? 0) === (int) $tag->id;
                            $chipFilters = collect($filters)->filter()->all();
                            $chipFilters['tag_id'] = $tag->id;
                        @endphp
                        <a href="{{ route('admin.leads.index', $chipFilters) }}"
                           class="badge {{ $isActiveTag ? 'badge-' . $tag->color : 'badge-light-' . $tag->color }} cursor-pointer text-decoration-none px-3 py-2">
                            <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

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
            <th>{{ __('crm::lead.fields.name') }}</th>
            <th>{{ __('crm::lead.fields.status') }}</th>
            <th>{{ __('crm::lead.fields.tags') }}</th>
            <th>{{ __('crm::lead.fields.phone') }}</th>
            <th>{{ __('crm::lead.fields.company') }}</th>
            <th>{{ __('crm::lead.fields.assignee') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end" data-orderable="false">{{ __('crm::lead.actions.actions') }}</th>
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
                    <div class="d-flex align-items-center">
                        <span class="sx-table-avatar bg-light-info text-info me-3">
                            {{ strtoupper(substr($lead->name ?? 'L', 0, 1)) }}
                        </span>
                        <div class="d-flex flex-column">
                            <a href="{{ route('admin.leads.show', $lead) }}" class="text-gray-800 mb-1 fw-semibold text-hover-primary">
                                {{ $lead->name ?? __('N/A') }}
                            </a>
                            @if($lead->email)
                                <a class="text-hover-primary text-gray-500 fs-7" href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-light-{{ \Modules\CRM\Models\Lead::statusBadgeColor($lead->status) }}">
                        {{ __('crm::lead.status.' . ($lead->status ?? 'new')) }}
                    </span>
                    @if($lead->blocked)
                        <span class="badge badge-light-danger ms-1">{{ __('crm::lead.status.blocked') }}</span>
                    @endif
                </td>
                <td>
                    @include('crm::admin.partials.lead-tags', [
                        'tags' => $lead->tags,
                        'empty' => '—',
                    ])
                </td>
                <td>{{ $lead->phone ?: __('N/A') }}</td>
                <td>
                    @if($lead->company)
                        <a href="{{ route('admin.companies.show', $lead->company) }}" class="text-hover-primary text-gray-800">
                            {{ $lead->company->name }}
                        </a>
                    @elseif($lead->company_name)
                        <span class="text-gray-600">{{ $lead->company_name }}</span>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>{{ $lead->assignee?->name ?: __('N/A') }}</td>
                <td>{{ $lead->created_at?->diffForHumans() }}</td>
                <td class="text-end">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light btn-active-light-primary" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('crm::lead.actions.actions') }} <i class="bi bi-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.leads.show', $lead) }}">
                                    <i class="bi bi-eye me-2"></i>{{ __('crm::lead.actions.view_details') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.leads.edit', $lead) }}">
                                    <i class="bi bi-pencil me-2"></i>{{ __('crm::lead.actions.edit') }}
                                </a>
                            </li>
                            @if(! $lead->deal_id && ($lead->company_name || $lead->company_id))
                                <li>
                                    <form method="POST" action="{{ route('admin.leads.convertCustomer', $lead) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-person-check me-2"></i>{{ __('crm::lead.conversion.convert_customer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route($lead->blocked ? 'admin.leads.unblock' : 'admin.leads.block', $lead) }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-{{ $lead->blocked ? 'unlock' : 'lock' }} me-2"></i>
                                        {{ $lead->blocked ? __('crm::lead.actions.unblock') : __('crm::lead.actions.block') }}
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" data-confirm-delete>
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i>{{ __('Delete') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
