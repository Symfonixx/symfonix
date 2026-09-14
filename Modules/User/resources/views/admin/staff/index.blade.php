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
        <a href="{{ route('admin.fingerprint.index') }}" class="btn btn-sm fw-bold btn-light-info">
            <i class="bi bi-fingerprint me-1"></i>{{ __('user::fingerprint.title') }}
        </a>
        <x-can perform="hr.employees.create">
            <a class="btn btn-sm fw-bold  btn-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
                {{__('Add New Employee')}} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
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

            $('.enroll-employee').on('click', function () {
                const employeeId = $(this).data('id');
                const $btn = $(this);
                const original = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                makeAjaxRequest('/admin/fingerprint/employees/' + employeeId + '/enroll', 'POST', null, 'json', function (res) {
                    $btn.prop('disabled', false).html(original);
                    if (res.success) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.reload(), 700);
                    } else {
                        toastr.error(res.message);
                    }
                }, function () {
                    $btn.prop('disabled', false).html(original);
                    toastr.error('{{ __('Connection request failed') }}');
                });
            });

            const convertModal = document.getElementById('convert_admin_modal');
            if (convertModal) {
                convertModal.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }
                    const form = convertModal.querySelector('.js-convert-admin-form');
                    const nameEl = convertModal.querySelector('.js-convert-employee-name');
                    const action = trigger.getAttribute('data-convert-action');
                    if (form && action) {
                        form.setAttribute('action', action);
                    }
                    if (nameEl) {
                        nameEl.textContent = trigger.getAttribute('data-name') || '';
                    }
                });

                const form = convertModal.querySelector('.js-convert-admin-form');
                if (form) {
                    const selectAll = form.querySelector('.js-permission-select-all');
                    const boxes = form.querySelectorAll('.js-permission-box');
                    const sections = form.querySelectorAll('.js-permission-section');
                    const syncSections = () => {
                        sections.forEach((section) => {
                            const key = section.getAttribute('data-section');
                            const related = form.querySelectorAll('.js-permission-box[data-section="' + key + '"]');
                            section.checked = related.length > 0 && Array.from(related).every((box) => box.checked);
                        });
                        if (selectAll) {
                            selectAll.checked = boxes.length > 0 && Array.from(boxes).every((box) => box.checked);
                        }
                    };
                    if (selectAll) {
                        selectAll.addEventListener('change', (event) => {
                            boxes.forEach((box) => {
                                box.checked = event.target.checked;
                            });
                            sections.forEach((section) => {
                                section.checked = event.target.checked;
                            });
                        });
                    }
                    sections.forEach((section) => {
                        section.addEventListener('change', (event) => {
                            const key = section.getAttribute('data-section');
                            form.querySelectorAll('.js-permission-box[data-section="' + key + '"]').forEach((box) => {
                                box.checked = event.target.checked;
                            });
                            syncSections();
                        });
                    });
                    boxes.forEach((box) => box.addEventListener('change', syncSections));
                    syncSections();
                }
            }

            @if($errors->any() && old('form_context') === 'convert_to_admin')
            const convertEl = document.getElementById('convert_admin_modal');
            if (convertEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(convertEl).show();
            }
            @endif
        })
    </script>
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Employees">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="min-w-125px">{{__('Name')}}</th>
            <th class="min-w-125px">{{__('Position')}}</th>
            <th class="min-w-125px">{{__('Mobile')}}</th>
            <th class="min-w-125px">{{__('Status')}}</th>
            <th class="min-w-125px">{{ __('user::fingerprint.title') }}</th>
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
                        <a href="{{ route('admin.employees.show', $employee->id) }}"
                           class="text-gray-800 mb-1">{{$employee->name}}
                        </a>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:{{$employee->email}}">{{$employee->email}}</a>
                    </div>
                </td>

                <td>{{ $employee->position ?: '—' }}</td>

                <td>
                    <a href="tel:{{$employee->mobile}}" target="_blank">
                        {{$employee->mobile}}
                    </a>
                </td>

                <td>
                    <span class="badge badge-light-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                    @if($employee->isAdminAccount())
                        <span class="badge badge-light-primary ms-1">{{ __('Admin') }}</span>
                    @endif
                    @if($employee->isOnWebsiteTeam())
                        <span class="badge badge-light-success ms-1">{{ __('Our Team') }}</span>
                    @endif
                </td>

                <td>
                    @if($employee->isFingerprintEnrolled())
                        <span class="badge badge-light-success">{{ __('user::fingerprint.status.enrolled_short') }}</span>
                    @else
                        <span class="badge badge-light-secondary">{{ __('user::fingerprint.status.pending_short') }}</span>
                    @endif
                    @if($employee->status === 'active')
                        <button type="button" class="btn btn-xs btn-light-info enroll-employee mt-1" data-id="{{ $employee->id }}">
                            <i class="bi bi-fingerprint"></i>
                        </button>
                    @endif
                </td>

                <td>{{$employee->created_at}}</td>

                <td>
                    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit_modal{{$employee->id}}">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <div class="modal fade" tabindex="-1" id="edit_modal{{$employee->id}}">
                        @include('user::admin.staff._edit_model' , ['employee' => $employee])
                    </div>
                    @if(! $employee->isAdminAccount())
                        <x-can perform="hr.admins.create">
                            <button type="button"
                                    class="btn btn-sm btn-light-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#convert_admin_modal"
                                    data-convert-action="{{ route('admin.employees.convert-to-admin', $employee) }}"
                                    data-name="{{ $employee->name }}"
                                    title="{{ __('Convert to Admin') }}">
                                <i class="bi bi-shield-lock"></i>
                            </button>
                        </x-can>
                    @endif
                    @if(! $employee->isOnWebsiteTeam())
                        <x-can perform="cms.team.create">
                            <form method="POST"
                                  action="{{ route('admin.employees.add-to-team', $employee) }}"
                                  class="d-inline"
                                  data-confirm="{{ __('user::emails.team.confirm', ['name' => $employee->name]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="{{ __('Add to Our Team') }}">
                                    <i class="bi bi-people"></i>
                                </button>
                            </form>
                        </x-can>
                    @endif

                    <a class="btn btn-sm btn-danger delete" data-id="{{ $employee->id }}">
                        <i class="bi bi-trash"></i>
                    </a>

                </td>

            </tr>
        @endforeach
        </tbody>
    </x-admin.table>
    <x-can perform="hr.employees.create">
        <div class="modal fade" tabindex="-1" id="create_modal">
            @include('user::admin.staff._create_model')
        </div>
    </x-can>
    <x-can perform="hr.admins.create">
        <div class="modal fade" tabindex="-1" id="convert_admin_modal">
            @include('user::admin.staff._convert_to_admin_modal', ['convertEmployee' => null, 'groups' => $groups])
        </div>
    </x-can>
</x-admin-layout>
