@section('title', __('support::ticket.category.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('support::ticket.category.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('support::ticket.category.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="support.ticket_categories.create">
            <a href="{{ route('admin.ticket_categories.create') }}" class="btn btn-sm fw-bold btn-primary">
                {{ __('support::ticket.category.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="false">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('support::ticket.category.fields.name') }}</th>
            <th>{{ __('support::ticket.category.fields.sort_order') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $category)
            <tr>
                <td>{{ $category->getTranslation('name', app()->getLocale()) }}</td>
                <td>{{ $category->sort_order }}</td>
                <td>
                    <span class="badge badge-light-{{ $category->is_active ? 'success' : 'danger' }}">
                        {{ $category->is_active ? __('Active') : __('Inactive') }}
                    </span>
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.ticket_categories.edit', $category) }}" class="btn btn-sm btn-light-primary">
                        {{ __('Edit') }}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
