@section('title' , __('Search Keywords'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Search Keywords'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Search Keywords')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('admin.search_keywords.export') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Keywords"
                   :formUrl="route('admin.search_keywords.deleteMulti')">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>

            <th>{{ __('Keyword') }}</th>
            <th>{{ __('Count') }}</th>
            <th>{{ __('Created At') }}</th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $item)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $item->id }}"/>
                    </div>
                </td>

                <td>
                    {{ $item->keyword }}
                </td>

                <td>
                    {{ $item->count }}
                </td>

                <td>
                    {{ $item->created_at }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <!--end::Table body-->
    </x-admin.table>
</x-admin-layout>




