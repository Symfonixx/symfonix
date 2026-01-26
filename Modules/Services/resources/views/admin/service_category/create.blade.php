@if(isset($modal) && $modal)

    <!-- Create Service Category Modal -->
    <div class="modal fade" id="createServiceCategoryModal" tabindex="-1" aria-labelledby="createServiceCategoryModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.service_categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createServiceCategoryModalLabel">{{ __('Add New Service Category') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">{{ __('Url') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="gslug" name="gslug" required>
                            <input type="hidden" name="slug" value="{{old('slug')}}" id="slug">
                            <div class="my-3" id="link">{{old('slug')}}</div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">{{ __('Image') }} <span class="text-danger">*</span></label>
                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                 style="background-image: url('{{asset('images/default.jpg')}}')">
                                <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                                     style="background-size: 75%; background-image: url({{asset('images/default.jpg')}})"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                       data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="image" accept=".png, .jpg, .jpeg, .webp" required/>
                                    <input type="hidden" name="avatar_remove"/>
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                      data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                      data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                            <div class="form-text">500px * 250px</div>
                        </div>

                        <div class="mb-3">
                            <label for="color_code" class="form-label">{{ __('Color Code') }}</label>
                            <input type="text" class="form-control" name="color_code" placeholder="#FFFFFF">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    @section('title' , __('Add New Service Category'))

    @section('toolbar')
        @php
            $breadcrumbItems = [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
                ['label' => 'Service Categories', 'url' => route('admin.service_categories.index')],
                ['label' => 'Add New Service Category'],
            ];
        @endphp
        <x-admin.breadcrumb :pageTitle="__('Add New Service Category')" :breadcrumbItems="$breadcrumbItems"/>
        <div class="d-flex align-items-center gap-2 gap-lg-3"></div>
    @endsection

    <x-admin-layout>
        <x-admin.create-card title="Add New Service Category" :formUrl="route('admin.service_categories.store')">
            <div class="row mb-8">
                <div class="col-xl-3">
                    <div class="fs-6 fw-bold mt-2 mb-3">{{__('Image')}} <span class="text-danger">*</span></div>
                </div>
                <div class="col-xl-9 fv-row">
                    <div class="image-input image-input-outline " data-kt-image-input="true"
                         style="background-image: url('{{asset('images/default.jpg')}}')">
                        <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                             style="background-size: 75%; background-image: url({{asset('images/default.jpg')}})"></div>
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image" accept=".png, .jpg, .jpeg, .webp" required/>
                            <input type="hidden" name="avatar_remove"/>
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                              data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                              data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                    <div class="form-text">500px * 250px</div>
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="bi bi-translate text-primary mx-1 "></i>{{__('Name')}} <span class="text-danger">*</span></div>
                </div>
                <div class="col-xl-9 fv-row">
                    <input type="text" class="form-control form-control-solid" name="title" value="{{old('title')}}"
                           placeholder="{{__('Name')}}"/>
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <div class="fs-6 fw-bold mt-2 mb-3">{{__('Url')}} <span class="text-danger">*</span></div>
                </div>
                <div class="col-xl-9 fv-row">
                    <input type="text" id="gslug" name="gslug" class="form-control form-control-solid mb-3 mb-lg-0"
                           placeholder="" value="{{old('slug')}}"/>
                    <input type="hidden" name="slug" value="{{old('slug')}}" id="slug">
                    <div class="my-3" id="link">{{old('slug')}}</div>
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <div class="fs-6 fw-bold mt-2 mb-3">{{__('Color Code')}}</div>
                </div>
                <div class="col-xl-9 fv-row">
                    <input type="text" class="form-control form-control-solid" name="color_code" value="{{old('color_code')}}" placeholder="#FFFFFF"/>
                </div>
            </div>

            <div class="row mb-8">
                <div class="col-xl-3">
                    <div class="fs-6 fw-bold mt-2 mb-3">{{__('Description')}}</div>
                </div>
                <div class="col-xl-9 fv-row">
                    <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="{{__('Description')}}">{{old('description')}}</textarea>
                </div>
            </div>
        </x-admin.create-card>
    </x-admin-layout>
@endif

