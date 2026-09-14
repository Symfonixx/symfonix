@section('title', __('Our Clients'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Our Clients'],
        ];
    @endphp
    <x-admin.breadcrumb
        pageTitle='Our Clients'
        :breadcrumbItems="$breadcrumbItems"
        pageDescription='Manage client logos shown on the homepage and About Us page.'
    />
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="cms.clients.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.clients.create') }}">
                {{ __('Add New Client') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Clients" :form-url="route('admin.clients.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-80px">{{ __('Logo') }}</th>
            <th class="min-w-150px">{{ __('Company Name') }}</th>
            <th class="min-w-120px">{{ __('URL') }}</th>
            <th class="min-w-60px">{{ __('Rank') }}</th>
            <th class="min-w-60px">{{ __('Status') }}</th>
            <th class="min-w-60px">{{ __('Created At') }}</th>
            <th class="min-w-60px text-end rounded-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $client)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $client->id }}"/>
                    </div>
                </td>
                <td>
                    <div class="symbol symbol-50px">
                        <img src="{{ $client->logo_link }}" alt="{{ $client->name }}" class="object-fit-contain"/>
                    </div>
                </td>
                <td>
                    <h5 class="fw-bolder text-hover-primary mb-1 fs-6">{{ $client->name }}</h5>
                </td>
                <td>
                    @if($client->url)
                        <a href="{{ $client->url }}" target="_blank" rel="noopener noreferrer" class="text-primary">
                            {{ Str::limit($client->url, 40) }}
                        </a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $client->rank }}</td>
                <td>
                    <span class="badge badge-light-{{ $client->status == 'Published' ? 'success' : 'warning' }} fs-7 fw-bold">
                        {{ __($client->status) }}
                    </span>
                </td>
                <td>{{ $client->created_at->diffForHumans() }}</td>
                <td>
                    <a href="{{ route('admin.clients.edit', $client->id) }}"
                       class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                        <i class="ki-duotone ki-message-edit fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
