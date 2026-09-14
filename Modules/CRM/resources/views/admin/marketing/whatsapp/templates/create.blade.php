@section('title', __('crm::whatsapp.pages.create_template_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::whatsapp.pages.templates_title'), 'url' => route('admin.crm.marketing.whatsapp-templates.index')],
            ['label' => __('crm::whatsapp.pages.create_template_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::whatsapp.pages.create_template_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card settings-form-card">
        <div class="card-header border-0 pt-6">
            <div class="card-title d-flex align-items-center gap-3">
                <span class="sx-form-icon bg-light-success text-success"><i class="bi bi-file-earmark-text"></i></span>
                <div>
                    <h2 class="fw-bold mb-1">{{ __('crm::whatsapp.pages.create_template_title') }}</h2>
                    <span class="text-muted fs-7">{{ __('crm::whatsapp.sections.template_form_hint') }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.crm.marketing.whatsapp-templates.store') }}">
            @csrf
            <div class="card-body pt-2">
                @include('crm::admin.marketing.whatsapp.templates._form')
            </div>
            <div class="card-footer settings-form-footer d-flex justify-content-end gap-2 py-5 px-9">
                <a href="{{ route('admin.crm.marketing.whatsapp-templates.index') }}" class="btn btn-light btn-active-light-primary">
                    <i class="bi bi-x-lg me-1"></i>{{ __('Discard') }}
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>{{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
