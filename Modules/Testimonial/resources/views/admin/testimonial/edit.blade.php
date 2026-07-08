@section('title' , __('Edit Testimonial'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Testimonials', 'url' => route('admin.testimonials.index')],
            ['label' => 'Edit Testimonial']
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Edit Testimonial')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
    </div>
@endsection
@section('js')
@endsection
<x-admin-layout>
    <x-admin.create-card title="Edit Testimonial" :formUrl="route('admin.testimonials.update', $testimonial->id)">
        @method('PUT')
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Customer')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $testimonial->avatar_link }}" alt="{{ $testimonial->name }}" class="rounded-circle h-50px">
                    <div>
                        <div class="fw-bold text-gray-800">{{ $testimonial->name }}</div>
                        <div class="text-muted">{{ $testimonial->customer?->email }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Project')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="form-control form-control-solid bg-light">
                    {{ $testimonial->project?->title ?? '—' }}
                    @if($testimonial->project?->company?->name)
                        <span class="text-muted"> — {{ $testimonial->project->company->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Quote')}} <span class="text-danger">*</span></div>
            </div>
            <div class="col-xl-9 fv-row">
                <textarea class="form-control form-control-solid" name="quote" rows="4" required>{{old('quote', $testimonial->quote)}}</textarea>
            </div>
        </div>
        <div class="row mb-8">
            <div class="col-xl-3">
                <div class="fs-6 fw-bold mt-2 mb-3">{{__('Approve for Website')}}</div>
            </div>
            <div class="col-xl-9 fv-row">
                <div class="form-check form-switch form-check-custom form-check-solid me-10">
                    <input class="form-check-input h-30px w-50px"
                           @checked(old('publish', $testimonial->status == 'Published'))
                           type="checkbox"
                           name="publish"
                           id="flexSwitch30x50"/>
                </div>
                <div class="form-text">{{ __('Only approved reviews appear as testimonials on the public website.') }}</div>
            </div>
        </div>

        <x-admin.auto-translate-checkbox :default="false" class="mb-0"/>
    </x-admin.create-card>
</x-admin-layout>
