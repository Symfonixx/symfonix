@section('title' , __('Search In Admins'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Admins'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Admins')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="hr.admins.create">
            <a class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
                {{__('Add New Admin')}} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
        <div class="modal fade" tabindex="-1" id="create_modal">
            @include('user::admin.admin._create_model')
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('.delete').on('click', function () {
                let id = $(this).data('id');
                Swal.fire({
                    text: "{{__('Are you sure you want to delete it')}}",
                    icon: "warning",
                    showCancelButton: !0,
                    buttonsStyling: !1,
                    confirmButtonText: "{{__('Yes, Delete!')}}",
                    cancelButtonText: "{{__('No, Cancel')}}",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then((function (e) {
                    makeAjaxRequest('/admin/admins/' + id, 'DELETE', null, "json", function (res) {
                        if (res.success) {
                            toastr.success('{{ __('The Operation Done Successfully') }}');
                            $('#tr' + id).remove()
                        }

                    })
                }))
            })
        })
    </script>
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Admins">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="min-w-125px">{{__('Name')}}</th>
            <th class="min-w-125px">{{__('Mobile')}}</th>
            <th class="min-w-125px">{{__('Last Login')}}</th>
            <th class="min-w-125px">{{__('Created At')}}</th>
            <th class="text-end min-w-100px"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $admin)
            <tr id="tr{{$admin->id}}">
                <td class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <img src="{{$admin->avatar}}" alt="admin"/>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.admins.show', $admin) }}" class="text-gray-800 mb-1 text-hover-primary">{{$admin->name}}</a>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:{{$admin->email}}">{{$admin->email}}</a>
                    </div>
                </td>
                <td>
                    <a href="tel:{{$admin->mobile}}" target="_blank">{{$admin->mobile}}</a>
                </td>
                <td>
                    <div class="badge badge-light fw-bolder">{{$admin->last_login_human}}</div>
                </td>
                <td>{{$admin->created_at}}</td>
                <td>
                    <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-sm btn-light-info me-1">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{$admin->id}}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <div class="modal fade" tabindex="-1" id="edit_modal{{$admin->id}}">
                        @include('user::admin.admin._edit_model' , ['user' => $admin])
                    </div>
                    <a class="btn btn-sm btn-danger delete" data-id="{{ $admin->id }}">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
