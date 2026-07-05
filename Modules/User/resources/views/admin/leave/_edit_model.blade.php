<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{{__('Edit Leave Request')}}</h3>

            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                 aria-label="Close">
                <i class="ki-duotone ki-cross fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>

        <form method="POST" action="{{route('admin.leaves.update', $leaveRequest->id)}}">
            @csrf
            @method('PUT')

            <div class="modal-body">
                <div class="mb-5">
                    <div class="row">
                        <div class="col-md-12 mb-7">
                            <label for="employee_id_{{ $leaveRequest->id }}" class="required form-label">{{__('Employee')}}</label>
                            <select id="employee_id_{{ $leaveRequest->id }}"
                                    class="form-control form-control-solid @error('employee_id') is-invalid @enderror"
                                    name="employee_id" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id', $leaveRequest->employee_id) == $employee->id)>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-7">
                            <label for="type_{{ $leaveRequest->id }}" class="required form-label">{{__('Leave Type')}}</label>
                            <select id="type_{{ $leaveRequest->id }}"
                                    class="form-control form-control-solid @error('type') is-invalid @enderror"
                                    name="type" required>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', $leaveRequest->type) === $value)>
                                        {{ __($label) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-7">
                            <label for="status_{{ $leaveRequest->id }}" class="required form-label">{{__('Status')}}</label>
                            <select id="status_{{ $leaveRequest->id }}"
                                    class="form-control form-control-solid @error('status') is-invalid @enderror"
                                    name="status" required>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $leaveRequest->status) === $value)>
                                        {{ __($label) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-7">
                            <label for="start_date_{{ $leaveRequest->id }}" class="required form-label">{{__('Start Date')}}</label>
                            <input type="date" id="start_date_{{ $leaveRequest->id }}"
                                   class="form-control form-control-solid @error('start_date') is-invalid @enderror"
                                   name="start_date"
                                   value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-7">
                            <label for="end_date_{{ $leaveRequest->id }}" class="required form-label">{{__('End Date')}}</label>
                            <input type="date" id="end_date_{{ $leaveRequest->id }}"
                                   class="form-control form-control-solid @error('end_date') is-invalid @enderror"
                                   name="end_date"
                                   value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-7">
                            <label for="reason_{{ $leaveRequest->id }}" class="form-label">{{__('Reason')}}</label>
                            <textarea id="reason_{{ $leaveRequest->id }}"
                                      class="form-control form-control-solid @error('reason') is-invalid @enderror"
                                      name="reason" rows="3">{{ old('reason', $leaveRequest->reason) }}</textarea>
                            @error('reason')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-7">
                            <label for="manager_note_{{ $leaveRequest->id }}" class="form-label">{{__('Manager Note')}}</label>
                            <textarea id="manager_note_{{ $leaveRequest->id }}"
                                      class="form-control form-control-solid @error('manager_note') is-invalid @enderror"
                                      name="manager_note" rows="3">{{ old('manager_note', $leaveRequest->manager_note) }}</textarea>
                            @error('manager_note')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">{{__('Discard')}}</button>
                <button type="submit" class="btn btn-primary">{{__('Save Changes')}} <i class="bi bi-check2-circle"></i></button>
            </div>
        </form>
    </div>
</div>
