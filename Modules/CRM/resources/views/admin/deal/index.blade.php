@section('title', __('crm::deal.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::deal.menu.deals')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::deal.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.deals.index', ['view' => 'kanban']) }}">
            <i class="bi bi-kanban me-1"></i>{{ __('crm::deal.actions.kanban_view') }}
        </a>
        <x-can perform="sales.deals.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.deals.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('crm::deal.actions.add') }}
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    @if(($tags ?? collect())->isNotEmpty())
        <div class="card sx-filter-bar mb-5">
            <div class="card-body py-4">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted fs-7 fw-semibold me-1">{{ __('crm::lead.fields.tags') }}:</span>
                    <a href="{{ route('admin.deals.index', collect($filters ?? [])->except('tag_id')->filter()->all()) }}"
                       class="badge {{ empty($filters['tag_id']) ? 'badge-primary' : 'badge-light' }} text-decoration-none px-3 py-2">
                        {{ __('crm::lead.filters.all') }}
                    </a>
                    @foreach($tags as $tag)
                        @php
                            $isActiveTag = (int) ($filters['tag_id'] ?? 0) === (int) $tag->id;
                            $chipFilters = collect($filters ?? [])->filter()->all();
                            $chipFilters['tag_id'] = $tag->id;
                        @endphp
                        <a href="{{ route('admin.deals.index', $chipFilters) }}"
                           class="badge {{ $isActiveTag ? 'badge-' . $tag->color : 'badge-light-' . $tag->color }} text-decoration-none px-3 py-2">
                            <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <x-admin.table :model="$model" :search="__('crm::deal.search.placeholder')" :formUrl="route('admin.deals.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('crm::deal.fields.title') }}</th>
            <th>{{ __('crm::deal.fields.company') }}</th>
            <th>{{ __('crm::lead.fields.tags') }}</th>
            <th>{{ __('crm::deal.fields.stage') }}</th>
            <th>{{ __('crm::deal.fields.assignee') }}</th>
            <th>{{ __('crm::deal.fields.value') }}</th>
            <th>{{ __('crm::deal.fields.probability') }}</th>
            <th>{{ __('crm::deal.fields.status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $deal)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $deal->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="sx-table-avatar bg-light-success text-success me-3">
                            <i class="bi bi-briefcase"></i>
                        </span>
                        <a href="{{ route('admin.deals.show', $deal->id) }}" class="text-gray-800 fw-semibold text-hover-primary">
                            {{ $deal->title }}
                        </a>
                    </div>
                </td>
                <td>{{ $deal->company?->name ?: __('N/A') }}</td>
                <td>
                    @include('crm::admin.partials.lead-tags', [
                        'tags' => $deal->lead?->tags ?? collect(),
                        'empty' => '—',
                    ])
                </td>
                <td>
                    <span class="badge badge-light-{{ $deal->pipelineStage?->color ?? 'primary' }}">
                        {{ $deal->pipelineStage?->display_name ?: __('N/A') }}
                    </span>
                </td>
                <td>{{ $deal->assignee?->name ?: __('N/A') }}</td>
                <td>
                    @if($deal->value)
                        {{ number_format($deal->value, 2) }} {{ $deal->currency }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>{{ $deal->probability !== null ? $deal->probability.'%' : __('N/A') }}</td>
                <td>
                    @php
                        $statusColor = match($deal->status) {
                            'won' => 'success',
                            'lost' => 'danger',
                            default => 'primary',
                        };
                    @endphp
                    <span class="badge badge-light-{{ $statusColor }}">
                        {{ __('crm::deal.status.'.$deal->status) }}
                    </span>
                </td>
                <td>{{ $deal->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.deals.show', $deal->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                        <i class="bi bi-eye fs-5"></i>
                    </a>
                    <a href="{{ route('admin.deals.edit', $deal->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="bi bi-pencil fs-5"></i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.deals.destroy', $deal->id) }}" data-confirm-delete>
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
