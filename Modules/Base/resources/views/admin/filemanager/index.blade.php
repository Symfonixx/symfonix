@section('title', __('File Manager'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'File Manager'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('File Manager')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card card-flush">
        <div class="card-header border-0 pt-6">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary active" data-bs-toggle="tab" href="#fm-images" role="tab">
                        <i class="bi bi-image me-2"></i>{{ __('Images') }}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary" data-bs-toggle="tab" href="#fm-files" role="tab">
                        <i class="bi bi-file-earmark-pdf me-2"></i>{{ __('Files & PDFs') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body pt-0">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="fm-images" role="tabpanel">
                    <iframe
                        src="{{ route('admin.unisharp.lfm.show') }}?type=image"
                        title="{{ __('Image Manager') }}"
                        style="width: 100%; height: 70vh; min-height: 500px; border: none; border-radius: 0.475rem;"
                    ></iframe>
                </div>
                <div class="tab-pane fade" id="fm-files" role="tabpanel">
                    <iframe
                        src="{{ route('admin.unisharp.lfm.show') }}?type=file"
                        title="{{ __('File Manager') }}"
                        style="width: 100%; height: 70vh; min-height: 500px; border: none; border-radius: 0.475rem;"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
