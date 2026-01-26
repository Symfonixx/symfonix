@section('title' , __('Edit Service Category'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Service Categories', 'url' => route('admin.service_categories.index')],
            ['label' => 'Edit Service Category'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Edit Service Category')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3"></div>
@endsection

<x-admin-layout>
    <x-admin.create-card title="Edit Service Category" :formUrl="route('admin.service_categories.update', $service_category->id)">
        @method('PUT')
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Image')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="image-input image-input-outline " data-kt-image-input="true"
                     style="background-image: url('{{$service_category->image_link}}')">
                    <div class="image-input-wrapper w-250px h-250px bgi-position-center"
                         style="background-size: 75%; background-image: url({{$service_category->image_link}})"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                           data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="image" accept=".png, .jpg, .jpeg, .webp"/>
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
                <input type="text" class="form-control form-control-solid" name="title" value="{{ old('title', $service_category->getTranslation('title', app()->getLocale(), false)) }}"
                       placeholder="{{__('Name')}}"/>
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Url')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" class="form-control form-control-solid" value="{{ $service_category->slug }}" readonly>
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Color Code')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <input type="text" class="form-control form-control-solid" name="color_code" value="{{ old('color_code', $service_category->color_code) }}" placeholder="#FFFFFF">
            </div>
        </div>

        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Description')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="{{__('Description')}}">{{ old('description', $service_category->getTranslation('description', app()->getLocale(), false)) }}</textarea>
            </div>
        </div>
    </x-admin.create-card>
</x-admin-layout>


