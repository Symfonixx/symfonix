@section('title', __('finance::finance.pages.dashboard_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::finance.pages.dashboard_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::finance.pages.dashboard_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
  <livewire:finance.financial-dashboard />
</x-admin-layout>

@section('js')
<script>
    (function () {
        const initFinanceMonthSelect = () => {
            const select = document.getElementById('finance-month-filter');
            if (!select || typeof $ === 'undefined' || !$.fn.select2) {
                return;
            }

            const $select = $(select);
            const root = select.closest('[wire\\:id]');
            const component = root ? Livewire.find(root.getAttribute('wire:id')) : null;

            if (!component) {
                return;
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.off('change.financeMonths');
                $select.select2('destroy');
            }

            $select.select2({
                placeholder: select.getAttribute('data-placeholder') || '',
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
            });

            $select.val(component.get('selectedMonths') || []).trigger('change.select2');

            $select.on('change.financeMonths', function () {
                component.set('selectedMonths', $(this).val() || []);
            });
        };

        const clearFinanceMonthSelect = () => {
            const select = document.getElementById('finance-month-filter');
            if (!select || typeof $ === 'undefined') {
                return;
            }

            $(select).val(null).trigger('change.select2');
        };

        const setupFinanceMonthFilter = () => {
            initFinanceMonthSelect();
            Livewire.on('finance-month-filter-cleared', clearFinanceMonthSelect);
        };

        if (window.Livewire) {
            setupFinanceMonthFilter();
        }

        document.addEventListener('livewire:initialized', setupFinanceMonthFilter);
    })();
</script>
@endsection
