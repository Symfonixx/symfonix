@section('title' , __('Branches'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Branches'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Branches')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.branches.create') }}">
            {{__('Add New Branch')}} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection
@section('js')
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Branches"
                   :form-url="route('admin.branches.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-150px">{{__('Name')}}</th>
            <th class="min-w-150px">{{__('City')}}</th>
            <th class="min-w-150px">{{__('Phone')}}</th>
            <th class="min-w-150px">{{__('Created At')}}</th>
            <th class="min-w-100px text-end rounded-end"></th>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $branch)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$branch->id}}"/>
                    </div>
                </td>
                <td>{{$branch->name}}</td>
                <td>{{$branch->city}}</td>
                <td>{{$branch->phone}}</td>
                <td>{{$branch->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.branches.edit', $branch->id) }}"
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



