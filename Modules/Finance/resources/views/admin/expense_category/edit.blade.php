@section('title', __('finance::expense_category.pages.edit_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::expense_category.pages.index_title'), 'url' => route('admin.finance.expense-categories.index')],
            ['label' => __('finance::expense_category.pages.edit_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::expense_category.pages.edit_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.finance.expense-categories.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('finance::expense_category.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.create-card
        :title="__('finance::expense_category.pages.edit_title')"
        :formUrl="route('admin.finance.expense-categories.update', $category->id)"
        :cancelUrl="route('admin.finance.expense-categories.index')">
        @method('PUT')
        @include('finance::admin.expense_category._form', ['category' => $category])
    </x-admin.create-card>
</x-admin-layout>
