@section('title' , __('Dashboard'))
@section('toolbar')
    <x-admin.breadcrumb :pageTitle="__('Dashboard')" :breadcrumbItems="[]"/>
@endsection
<x-admin-layout>
    <div class="row gy-5 g-xl-8">
        <div class="col-xxl-4">
            <div class="card card-xl-stretch mb-xl-8">
                <!--begin::Beader-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">{{__('Visitors Overview')}}</span>
                    </h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0 d-flex flex-column">
                    <!--begin::Stats-->
                    <div class="card-p pt-5 bg-body flex-grow-1">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col mr-8">
                                <!--begin::Label-->
                                <div class="fs-7 text-muted fw-bold">{{__('Total Visitors')}}</div>
                                <!--end::Label-->
                                <!--begin::Stat-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder">{{$visitorsStats['totalVisitorsCount']}}</div>
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->

                                    <!--end::Svg Icon-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col">
                                <!--begin::Label-->
                                <div class="fs-7 text-muted fw-bold">{{__('Today Visitors')}}</div>
                                <!--end::Label-->
                                <!--begin::Stat-->
                                <div class="fs-4 fw-bolder">{{$visitorsStats['todayVisitorsCount']}}</div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Col-->

                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->

                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                    <!--begin::Chart-->
                    <div class="card">

                        <div class="card-body p-0 d-flex flex-column">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-bordered">
                                    <!--begin::Table head-->
                                    <thead>
                                    <tr>
                                        <th class="w-50px">{{__('Page')}}</th>
                                        <th class="w-50px">{{__('Visiting Count')}}</th>
                                    </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                    @foreach($topVisitedPages as $topVisitedPage)
                                        <tr>
                                            <td>
{{--                                                <b>{{$topVisitedPage->page_title}} </b>--}}
{{--                                                <br/>--}}
                                                <a href="{{$topVisitedPage->url}}" target="_blank">
                                                    {{$topVisitedPage->url}}
                                                </a>
                                            </td>
                                            <td>
                                                {{$topVisitedPage->total}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!--end::Chart-->
                </div>
                <!--end::Body-->
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card card-xxl-stretch">
                <!--begin::Header-->
                <div class="card-header border-0 bg-primary py-5">
                    <h3 class="card-title fw-bolder text-white">{{__('Overall CMS Statistics')}}</h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <!--begin::Chart-->
                    <div class="mixed-widget-2-chart card-rounded-bottom bg-primary" data-kt-color="danger"
                         style="height: 100px"></div>
                    <!--end::Chart-->
                    <!--begin::Stats-->
                    <div class="card-p mt-n20 position-relative">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-warning px-6 py-8 rounded-2 me-7 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->

                                <div class="fs-2hx fw-bolder">{{$stats['pages'] ?? 0}}</div>

                                <!--end::Svg Icon-->
                                <a href="{{route('admin.pages.index')}}" target="_blank" class="text-warning fw-bold fs-6"> {{__('Pages')}} </a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-primary px-6 py-8 rounded-2 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                <div class="fs-2hx fw-bolder">  {{$stats['blogs'] ?? 0}} </div>
                                <!--end::Svg Icon-->
                                <a href="{{route('admin.blogs.index')}}" target="_blank" class="text-primary fw-bold fs-6">{{__('Blogs')}}</a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-danger px-6 py-8 rounded-2 me-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <div class="fs-2hx fw-bolder">  0 </div>
                                <!--end::Svg Icon-->
                                <a href="" target="_blank" class="text-danger fw-bold fs-6 mt-2">{{__('Products')}}</a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-success px-6 py-8 rounded-2">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
                                <div class="fs-2hx fw-bolder">  {{$stats['services'] ?? 0}} </div>
                                <!--end::Svg Icon-->
                                <a href="{{route('admin.services.index')}}" target="_blank" class="text-success fw-bold fs-6 mt-2">{{__('Services')}}</a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Body-->
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card card-xxl-stretch">
                <!--begin::Header-->
                <div class="card-header border-0 bg-warning py-5">
                    <h3 class="card-title fw-bolder text-white">{{__('Overall Users Statistics')}}</h3>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body p-0">
                    <!--begin::Chart-->
                    <div class="mixed-widget-2-chart card-rounded-bottom bg-warning" data-kt-color="warning"
                         style="height: 100px"></div>
                    <!--end::Chart-->
                    <!--begin::Stats-->
                    <div class="card-p mt-n20 position-relative">
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-warning px-6 py-8 rounded-2 me-7 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->

                                <div class="fs-2hx fw-bolder">{{$stats['admins'] ?? 0}}</div>

                                <!--end::Svg Icon-->
                                <a href="{{route('admin.staffs.index')}}" target="_blank" class="text-warning fw-bold fs-6"> {{__('Staffs')}} </a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-primary px-6 py-8 rounded-2 mb-7">
                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                <div class="fs-2hx fw-bolder">  {{$stats['leads'] ?? 0}} </div>
                                <!--end::Svg Icon-->
                                <a href="{{route('admin.users.index')}}" target="_blank" class="text-primary fw-bold fs-6">{{__('Leads')}}</a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <div class="row g-0">
                            <!--begin::Col-->
                            <div class="col bg-light-danger px-6 py-8 rounded-2 me-7">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                <div class="fs-2hx fw-bolder">  {{$stats['subscribers'] ?? 0}} </div>
                                <!--end::Svg Icon-->
                                <a href="{{route('admin.subscribers.index')}}" target="_blank"  class="text-danger fw-bold fs-6 mt-2">{{__('Newsletter Subscribers')}}</a>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col bg-light-success px-6 py-8 rounded-2">
                                <!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
                                <div class="fs-2hx fw-bolder">  {{$stats['contacts'] ?? 0}} </div>
                                <!--end::Svg Icon-->
                                <a href="{{route('admin.contact_forms.index')}}" target="_blank" class="text-success fw-bold fs-6 mt-2">{{__('Contacts')}}</a>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>

    @can('App Monitoring')
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-5 g-xl-9 mb-10">
            <div class="col">
                <!--begin::Card-->
                <div class="card h-md-100">
                    <!--begin::Card body-->
                    <div class="card-body d-flex flex-center">
                        <!--begin::Button-->
                        <a target="_blank" href="/{{config('telescope.path')}}">
                            <!--begin::Label-->
                            <span class="fw-bolder fs-3 text-gray-600 text-hover-primary">Telescope</span>
                            <!--end::Label-->
                        </a>
                        <!--begin::Button-->
                    </div>
                    <!--begin::Card body-->
                </div>
                <!--begin::Card-->
            </div>
            <div class="col">
                <!--begin::Card-->
                <div class="card h-md-100">
                    <!--begin::Card body-->
                    <div class="card-body d-flex flex-center">
                        <!--begin::Button-->
                        <a target="_blank" href="/{{config('pulse.path')}}">

                            <!--begin::Label-->
                            <span class="fw-bolder fs-3 text-gray-600 text-hover-primary">Pulse</span>
                            <!--end::Label-->
                        </a>
                        <!--begin::Button-->
                    </div>
                    <!--begin::Card body-->
                </div>
                <!--begin::Card-->
            </div>
        </div>
    @endcan
</x-admin-layout>
