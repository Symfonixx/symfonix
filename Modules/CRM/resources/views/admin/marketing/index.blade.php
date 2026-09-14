@section('title', __('crm::marketing.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title')],
        ];
        $activeChannel = $channel ?? 'email';
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="marketing.email.send">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.crm.marketing.create') }}">
                {{ __('crm::marketing.actions.compose') }} <i class="bi bi-envelope-plus mx-1"></i>
            </a>
        </x-can>
        <x-can perform="marketing.whatsapp.send">
            <a class="btn btn-sm fw-bold btn-success" href="{{ route('admin.crm.marketing.whatsapp.create') }}">
                {{ __('crm::whatsapp.actions.compose') }} <i class="bi bi-whatsapp mx-1"></i>
            </a>
        </x-can>
        <x-can perform="marketing.whatsapp_templates.view">
            <a class="btn btn-sm fw-bold btn-light-success" href="{{ route('admin.crm.marketing.whatsapp-templates.index') }}">
                {{ __('crm::whatsapp.actions.manage_templates') }} <i class="bi bi-file-earmark-text mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold mb-0">{{ __('crm::marketing.sections.history') }}</h3>
            </div>
        </div>
        <div class="card-body pt-0">
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-8 fs-6 fw-semibold" role="tablist">
                @can('marketing.email.view')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeChannel === 'email' ? 'active' : '' }}"
                           href="{{ route('admin.crm.marketing.index', ['channel' => 'email']) }}">
                            <i class="bi bi-envelope me-2"></i>{{ __('crm::marketing.tabs.emails') }}
                        </a>
                    </li>
                @endcan
                @can('marketing.whatsapp.view')
                    <li class="nav-item">
                        <a class="nav-link {{ $activeChannel === 'whatsapp' ? 'active' : '' }}"
                           href="{{ route('admin.crm.marketing.index', ['channel' => 'whatsapp']) }}">
                            <i class="bi bi-whatsapp me-2"></i>{{ __('crm::marketing.tabs.whatsapp') }}
                        </a>
                    </li>
                @endcan
            </ul>

            @if($activeChannel === 'whatsapp')
                @include('crm::admin.marketing._whatsapp_campaigns_table')
            @else
                @include('crm::admin.marketing._email_campaigns_table')
            @endif
        </div>
    </div>
</x-admin-layout>
