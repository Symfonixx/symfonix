<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{{__('Add New Employee')}}</h3>

            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                 aria-label="Close">
                <i class="ki-duotone ki-cross fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>

        <form method="POST" action="{{route('admin.employees.store')}}" enctype="multipart/form-data">
            @csrf

            <div class="modal-body">
                <div class="mb-5">
                    <div class="row">
                        <div class="col-md-12 mb-7">
                            <label for="name" class=" required form-label">{{__('Name')}}</label>
                            <input type="text" id="name"
                                   class="form-control form-control-solid @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-7">
                            <label for="email" class="required form-label">{{__('Email')}}</label>
                            <input type="email" id="email"
                                   class="form-control form-control-solid @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-7">
                            <label for="mobile" class="required form-label">{{__('Mobile')}}</label>
                            <input type="number" id="mobile"
                                   class="form-control form-control-solid @error('mobile') is-invalid @enderror"
                                   name="mobile" value="{{ old('mobile') }}" required>
                            @error('mobile')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-7">
                            <label for="position" class="form-label">{{ __('Position') }}</label>
                            <input type="text" id="position"
                                   class="form-control form-control-solid @error('position') is-invalid @enderror"
                                   name="position" value="{{ old('position') }}" maxlength="120">
                            @error('position')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="resume" class="form-label">{{ __('Resume') }}</label>
                            <input type="file" id="resume"
                                   class="form-control form-control-solid @error('resume') is-invalid @enderror"
                                   name="resume" accept=".pdf,.doc,.docx">
                            <div class="form-text">{{ __('Accepted formats: PDF, DOC, DOCX. Max size: 5MB') }}</div>
                            @error('resume')
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
