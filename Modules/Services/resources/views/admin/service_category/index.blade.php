@section('title' , __('Service Categories'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Service Categories'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Service Categories')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.service_categories.create') }}">
            {{__('Add New Service Category')}} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection
@section('js')
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Service Categories"
                   :form-url="route('admin.service_categories.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-80px">{{__('Image')}}</th>
            <th class="min-w-100px">{{__('Name')}}</th>
            <th class="min-w-100px">{{__('Url')}}</th>
            <th class="min-w-100px">{{__('Services')}}</th>
            <th class="min-w-100px">{{__('Created At')}}</th>
            <th class="min-w-100px text-end rounded-end"></th>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $category)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$category->id}}"/>
                    </div>
                </td>
                <td>
                    <img src="{{$category->image_link}}" alt="{{$category->title}}" class="img-fluid h-75px rounded"/>
                </td>
                <td>{{$category->title}}</td>
                <td>{{$category->slug}}</td>
                <td>{{$category->services_count}}</td>
                <td>{{$category->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <a href="{{ route('admin.service_categories.edit', $category->id) }}"
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


