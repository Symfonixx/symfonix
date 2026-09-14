@section('title', __('crm::contact.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="crm.contacts.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.contacts.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('crm::contact.actions.add') }}
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" :search="__('crm::contact.search.placeholder')" :formUrl="route('admin.contacts.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th>{{ __('crm::contact.fields.name') }}</th>
            <th>{{ __('crm::contact.fields.email') }}</th>
            <th>{{ __('crm::contact.fields.phone') }}</th>
            <th>{{ __('crm::contact.fields.company') }}</th>
            <th>{{ __('crm::contact.fields.job_title') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $contact)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $contact->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="sx-table-avatar bg-light-primary text-primary me-3">
                            {{ strtoupper(substr($contact->name ?? 'C', 0, 1)) }}
                        </span>
                        <div>
                            <a href="{{ route('admin.contacts.show', $contact) }}" class="text-gray-800 fw-semibold text-hover-primary">
                                {{ $contact->name }}
                            </a>
                            @if($contact->is_primary)
                                <span class="badge badge-light-primary ms-1">{{ __('crm::contact.fields.is_primary') }}</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td>{{ $contact->email ?: __('N/A') }}</td>
                <td>{{ $contact->phone ?: __('N/A') }}</td>
                <td>
                    @if($contact->company)
                        <a href="{{ route('admin.companies.show', $contact->company) }}">{{ $contact->company->name }}</a>
                    @else
                        <span class="text-muted">{{ __('N/A') }}</span>
                    @endif
                </td>
                <td>{{ $contact->job_title ?: __('N/A') }}</td>
                <td>{{ $contact->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                        <i class="bi bi-eye fs-5"></i>
                    </a>
                    <a href="{{ route('admin.contacts.edit', $contact) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="bi bi-pencil fs-5"></i>
                    </a>
                    <form class="d-inline" method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" data-confirm-delete>
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
