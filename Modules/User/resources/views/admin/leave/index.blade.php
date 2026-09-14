@section('title' , __('Leave Management'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('Leave Management')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Leave Management')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="hr.leaves.create">
            <a class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
                {{__('Add Leave Request')}} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
        <div class="modal fade" tabindex="-1" id="create_modal">
            @include('user::admin.leave._create_model')
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
                }).then((function () {
                    makeAjaxRequest('/admin/leaves/' + id, 'DELETE', null, "json", function (res) {
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
    @php
        $statusBadges = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
    @endphp

    <x-admin.table :model="$model" search="Search In Leave Requests">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="min-w-175px">{{__('Employee')}}</th>
            <th class="min-w-125px">{{__('Leave Type')}}</th>
            <th class="min-w-125px">{{__('Dates')}}</th>
            <th class="min-w-100px">{{__('Days')}}</th>
            <th class="min-w-100px">{{__('Status')}}</th>
            <th class="min-w-175px">{{__('Reason')}}</th>
            <th class="min-w-125px">{{__('Created At')}}</th>
            <th class="text-end min-w-100px"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $leaveRequest)
            <tr id="tr{{$leaveRequest->id}}">
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 mb-1">{{ $leaveRequest->employee?->name }}</span>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:{{ $leaveRequest->employee?->email }}">{{ $leaveRequest->employee?->email }}</a>
                    </div>
                </td>

                <td>{{ __($types[$leaveRequest->type] ?? ucfirst($leaveRequest->type)) }}</td>

                <td>
                    {{ $leaveRequest->start_date->format('Y-m-d') }}
                    <span class="text-muted">-</span>
                    {{ $leaveRequest->end_date->format('Y-m-d') }}
                </td>

                <td>{{ $leaveRequest->days_count }}</td>

                <td>
                    <span class="badge badge-light-{{ $statusBadges[$leaveRequest->status] ?? 'secondary' }}">
                        {{ __($statuses[$leaveRequest->status] ?? ucfirst($leaveRequest->status)) }}
                    </span>
                </td>

                <td>{{ \Illuminate\Support\Str::limit($leaveRequest->reason ?? '-', 60) }}</td>

                <td>{{ $leaveRequest->created_at }}</td>

                <td>
                    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{$leaveRequest->id}}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <div class="modal fade" tabindex="-1" id="edit_modal{{$leaveRequest->id}}">
                        @include('user::admin.leave._edit_model', ['leaveRequest' => $leaveRequest])
                    </div>

                    <a class="btn btn-sm btn-danger delete" data-id="{{ $leaveRequest->id }}">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
