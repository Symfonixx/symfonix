{{-- @extends('admin-layout') --}}

@section('title' , __('Testimonials'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Testimonials'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Testimonials')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold  btn-primary" href="{{route('admin.testimonials.create')}}">
            {{__('Add New Testimonial')}} <i class="bi bi-plus-lg mx-1"></i>
        </a>
    </div>
@endsection
@section('js')
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Testimonials" :form-url="route('admin.testimonials.deleteMulti')">
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>
            <th class="min-w-100px">{{__('Avatar')}}</th>
            <th class="min-w-150px">{{__('Details')}}</th>
            <th class="min-w-100px">{{__('Published')}}</th>
            <th class="min-w-100px">{{__('Created At')}}</th>
            <th class="min-w-100px text-end rounded-end"></th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $testimonial)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$testimonial->id}}"/>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <a href="{{$testimonial->avatar_link}}" target="_blank" >
                            <img src="{{$testimonial->avatar_link}}" alt="{{$testimonial->name}}" class="img-fluid h-50px"/>
                        </a>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 mb-1">
                            {{$testimonial->name}}
                        </span>
                        @if($testimonial->position)
                            <span class="text-gray-500 mb-1">
                                {{$testimonial->position}}
                            </span>
                        @endif
                        @if($testimonial->url)
                            <a class="text-hover-primary text-gray-500" target="_blank"
                               href="{{$testimonial->url}}">{{$testimonial->url}}</a>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="badge badge-light-{{$testimonial->status == 'Published' ? 'success' : 'warning'}} fs-7 fw-bold">
                        {{__($testimonial->status)}}
                    </span>
                </td>
                <td>{{$testimonial->created_at->diffForHumans() }}</td>
                <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#testimonialModal{{ $testimonial->id }}">
                            <i class="bi bi-eye"></i> {{ __('View Details') }}
                        </button>
                        <a href="{{route('admin.testimonials.edit' , $testimonial->id)}}"
                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                            <i class="ki-duotone ki-message-edit fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    </div>
                </td>
            </tr>

            <!-- Testimonial Details Modal -->
            <div class="modal fade" id="testimonialModal{{ $testimonial->id }}" tabindex="-1" aria-labelledby="testimonialModalLabel{{ $testimonial->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testimonialModalLabel{{ $testimonial->id }}">{{ __('Testimonial Details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-3 text-center">
                                    <a href="{{$testimonial->avatar_link}}" target="_blank">
                                        <img src="{{$testimonial->avatar_link}}" alt="{{$testimonial->name}}" class="img-fluid mb-2">
                                    </a>
                                </div>
                                <div class="col-md-9">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <strong>{{ __('Name') }}:</strong>
                                            <p>{{ $testimonial->name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('Position') }}:</strong>
                                            <p>{{ $testimonial->position ?: '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <strong>{{ __('Url') }}:</strong>
                                            @if($testimonial->url)
                                                <p><a href="{{ $testimonial->url }}" target="_blank">{{ $testimonial->url }}</a></p>
                                            @else
                                                <p>-</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('Status') }}:</strong>
                                            <p>
                                                <span class="badge badge-light-{{$testimonial->status == 'Published' ? 'success' : 'warning'}} fs-7 fw-bold">
                                                    {{__($testimonial->status)}}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>{{ __('Quote') }}:</strong>
                                <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                    {{ $testimonial->quote }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Created At') }}:</strong>
                                    <p>{{ $testimonial->created_at ? $testimonial->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
