@section('title', __('crm::custom_field.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::settings.menu')],
            ['label' => __('crm::custom_field.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::custom_field.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="crm.custom_fields.create">
            <a href="{{ route('admin.crm.custom-fields.create') }}" class="btn btn-sm fw-bold btn-primary">
                {{ __('crm::custom_field.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="false">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{ __('crm::custom_field.fields.label') }}</th>
            <th>{{ __('crm::custom_field.fields.key') }}</th>
            <th>{{ __('crm::custom_field.fields.type') }}</th>
            <th>{{ __('crm::custom_field.fields.is_required') }}</th>
            <th>{{ __('crm::custom_field.fields.sort_order') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $field)
            <tr>
                <td>{{ $field->display_label }}</td>
                <td><code>{{ $field->key }}</code></td>
                <td>
                    <span class="badge badge-light-primary">
                        {{ __('crm::custom_field.types.' . $field->type) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-light-{{ $field->is_required ? 'warning' : 'secondary' }}">
                        {{ $field->is_required ? __('Yes') : __('No') }}
                    </span>
                </td>
                <td>{{ $field->sort_order }}</td>
                <td>
                    <span class="badge badge-light-{{ $field->is_active ? 'success' : 'danger' }}">
                        {{ $field->is_active ? __('Active') : __('Inactive') }}
                    </span>
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.crm.custom-fields.edit', $field) }}" class="btn btn-sm btn-light-primary">
                        {{ __('Edit') }}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
