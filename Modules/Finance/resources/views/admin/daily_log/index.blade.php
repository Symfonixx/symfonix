@section('title', __('finance::finance.menu.daily_log'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::finance.menu.daily_log')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::finance.menu.daily_log')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.finance.dashboard') }}">
            <i class="bi bi-graph-up me-1"></i>{{ __('finance::finance.menu.dashboard') }}
        </a>
        <a class="btn btn-sm fw-bold btn-light" href="{{ route('admin.finance.expense-categories.index') }}">
            <i class="bi bi-tags me-1"></i>{{ __('finance::finance.menu.expense_categories') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="row g-5 g-xl-8">
        <div class="col-xl-5">
            <livewire:finance.daily-transaction-logger />
        </div>
        <div class="col-xl-7">
            <livewire:finance.today-transactions />
        </div>
    </div>
</x-admin-layout>
