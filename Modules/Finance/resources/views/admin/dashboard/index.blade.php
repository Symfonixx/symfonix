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

@section('js')
<script>
    (function () {
        const getComponent = () => {
            const select = document.getElementById('finance-month-filter');
            if (!select) {
                return null;
            }

            const root = select.closest('[wire\\:id]');

            return root ? Livewire.find(root.getAttribute('wire:id')) : null;
        };

        const initFinanceMonthSelect = () => {
            const jQuery = window.jQuery;
            const select = document.getElementById('finance-month-filter');
            const component = getComponent();

            if (!select || !jQuery?.fn?.select2 || !component) {
                return false;
            }

            const $select = jQuery(select);

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
                component.set('selectedMonths', jQuery(this).val() || []);
            });

            return true;
        };

        const clearFinanceMonthSelect = () => {
            const jQuery = window.jQuery;
            const select = document.getElementById('finance-month-filter');

            if (!select || !jQuery) {
                return;
            }

            jQuery(select).val(null).trigger('change.select2');
        };

        const bootFinanceMonthSelect = (attempt = 0) => {
            if (initFinanceMonthSelect() || attempt >= 40) {
                return;
            }

            setTimeout(() => bootFinanceMonthSelect(attempt + 1), 50);
        };

        const setupFinanceMonthSelect = () => {
            bootFinanceMonthSelect();
            Livewire.on('finance-month-filter-cleared', clearFinanceMonthSelect);
        };

        if (window.Livewire) {
            setupFinanceMonthSelect();
        } else {
            document.addEventListener('livewire:initialized', setupFinanceMonthSelect);
        }
    })();
</script>
@endsection

<x-admin-layout>
  <livewire:finance.financial-dashboard />
</x-admin-layout>
