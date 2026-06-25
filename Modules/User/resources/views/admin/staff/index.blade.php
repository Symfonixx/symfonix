@section('title' , __('Employees'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Employees'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Employees')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold  btn-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
            {{__('Add New Employee')}} <i class="bi bi-plus-lg mx-1"></i>
        </a>
        <div class="modal fade" tabindex="-1" id="create_modal">
            @include('user::admin.staff._create_model')
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
                    makeAjaxRequest('/admin/employees/' + id, 'DELETE', null, "json", function (res) {
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
    <x-admin.table :model="$model" search="Search In Employees">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="min-w-125px">{{__('Name')}}</th>
            <th class="min-w-125px">{{__('Mobile')}}</th>
            <th class="min-w-125px">{{__('Status')}}</th>
            <th class="min-w-125px">{{__('Created At')}}</th>
            <th class="text-end min-w-100px"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $employee)
            <tr id="tr{{$employee->id}}">

                <td class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <img src="{{$employee->avatar}}" alt="employee"/>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="#"
                           class="text-gray-800 mb-1">{{$employee->name}}
                        </a>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:{{$employee->email}}">{{$employee->email}}</a>
                    </div>
                </td>

                <td>
                    <a href="tel:{{$employee->mobile}}" target="_blank">
                        {{$employee->mobile}}
                    </a>
                </td>

                <td>
                    <span class="badge badge-light-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </td>

                <td>{{$employee->created_at}}</td>

                <td>
                    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{$employee->id}}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <div class="modal fade" tabindex="-1" id="edit_modal{{$employee->id}}">
                        @include('user::admin.staff._edit_model' , ['employee' => $employee])
                    </div>

                    <a class="btn btn-sm btn-danger delete" data-id="{{ $employee->id }}">
                        <i class="bi bi-trash"></i>
                    </a>

                </td>

            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
