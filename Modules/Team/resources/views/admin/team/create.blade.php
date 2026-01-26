@section('title' , __('Add New Team Member'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Team', 'url' => route('admin.teams.index')],
            ['label' => 'Add New Team Member']
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Add New Team Member')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
    </div>
@endsection
@section('js')
@endsection
{{-- @extends('admin-layout') --}}
<x-admin-layout>
    <x-admin.create-card title="Add New Team Member" :formUrl="route('admin.teams.store')">
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Avatar')}} <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="image-input image-input-outline " data-kt-image-input="true"
                     style="background-image: url('{{asset('images/default.jpg')}}')">
                    <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                         style="background-size: 75%; background-image: url({{asset('images/default.jpg')}})"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                           data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg, .gif" required/>
                        <input type="hidden" name="avatar_remove"/>
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                          data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                          data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                </div>
                <div class="form-text"> 500px * 500px</div>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Name')}} <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" class="form-control form-control-solid" name="name" value="{{old('name')}}" placeholder="" required/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Position')}} <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" class="form-control form-control-solid" name="position" value="{{old('position')}}" placeholder="" required/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('LinkedIn')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="url" class="form-control form-control-solid" name="linked_in" value="{{old('linked_in')}}" placeholder=""/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Facebook')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="url" class="form-control form-control-solid" name="facebook" value="{{old('facebook')}}" placeholder=""/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('GitHub')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="url" class="form-control form-control-solid" name="github" value="{{old('github')}}" placeholder=""/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Behance')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="url" class="form-control form-control-solid" name="behance" value="{{old('behance')}}" placeholder=""/>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Resume')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="file" class="form-control form-control-solid" name="resume" accept=".pdf,.doc,.docx"/>
                <div class="form-text">{{__('Accepted formats: PDF, DOC, DOCX. Max size: 5MB')}}</div>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Key Skills')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea class="form-control form-control-solid" name="key_skills" rows="4" placeholder="{{__('Enter key skills separated by commas or new lines')}}">{{old('key_skills')}}</textarea>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Published')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="form-check form-switch form-check-custom form-check-solid me-10">
                    <input class="form-check-input h-30px w-50px" @checked(old('publish', true)) type="checkbox" name="publish" id="flexSwitch30x50"/>
                </div>
            </div>
        </div>
    </x-admin.create-card>
</x-admin-layout>
