@props(['active' => 'finance'])

<ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-5">
    @can('reporting.finance.view')
        <li class="nav-item">
            <a class="nav-link text-active-primary py-3 me-4 {{ $active === 'finance' ? 'active' : '' }}"
               href="{{ route('admin.reporting.finance') }}">
                <i class="bi bi-currency-dollar me-2"></i>{{ __('reporting::report.menu.finance') }}
            </a>
        </li>
    @endcan
    @can('reporting.sales.view')
        <li class="nav-item">
            <a class="nav-link text-active-primary py-3 me-4 {{ $active === 'sales' ? 'active' : '' }}"
               href="{{ route('admin.reporting.sales') }}">
                <i class="bi bi-graph-up-arrow me-2"></i>{{ __('reporting::report.menu.sales') }}
            </a>
        </li>
    @endcan
    @can('reporting.marketing.view')
        <li class="nav-item">
            <a class="nav-link text-active-primary py-3 me-4 {{ $active === 'marketing' ? 'active' : '' }}"
               href="{{ route('admin.reporting.marketing') }}">
                <i class="bi bi-megaphone me-2"></i>{{ __('reporting::report.menu.marketing') }}
            </a>
        </li>
    @endcan
    @can('reporting.operations.view')
        <li class="nav-item">
            <a class="nav-link text-active-primary py-3 me-4 {{ $active === 'operations' ? 'active' : '' }}"
               href="{{ route('admin.reporting.operations') }}">
                <i class="bi bi-gear-wide-connected me-2"></i>{{ __('reporting::report.menu.operations') }}
            </a>
        </li>
    @endcan
    @can('reporting.employee.view')
        <li class="nav-item">
            <a class="nav-link text-active-primary py-3 me-4 {{ $active === 'employee' ? 'active' : '' }}"
               href="{{ route('admin.reporting.employee') }}">
                <i class="bi bi-person-badge me-2"></i>{{ __('reporting::report.menu.employee') }}
            </a>
        </li>
    @endcan
</ul>
