@section('title', __('crm::lead_tag.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::settings.menu')],
            ['label' => __('crm::lead_tag.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::lead_tag.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="crm.lead_tags.create">
            <a href="{{ route('admin.crm.lead-tags.create') }}" class="btn btn-sm fw-bold btn-primary">
                {{ __('crm::lead_tag.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="false">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('crm::lead_tag.fields.name') }}</th>
            <th>{{ __('crm::lead_tag.fields.color') }}</th>
            <th>{{ __('crm::lead_tag.fields.sort_order') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $tag)
            <tr>
                <td>
                    <span class="badge badge-light-{{ $tag->color }}">{{ $tag->display_name }}</span>
                </td>
                <td>
                    <span class="badge badge-light-{{ $tag->color }}">{{ __('crm::lead_tag.colors.' . $tag->color) }}</span>
                </td>
                <td>{{ $tag->sort_order }}</td>
                <td>
                    <span class="badge badge-light-{{ $tag->is_active ? 'success' : 'danger' }}">
                        {{ $tag->is_active ? __('Active') : __('Inactive') }}
                    </span>
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.crm.lead-tags.edit', $tag) }}" class="btn btn-sm btn-light-primary">
                        {{ __('Edit') }}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
