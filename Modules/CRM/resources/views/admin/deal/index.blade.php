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
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.deals.create') }}">
            {{ __('crm::deal.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection

<x-admin-layout>
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
                <td>{{ $deal->title }}</td>
                <td>{{ $deal->company?->name ?: __('N/A') }}</td>
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
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
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
