@section('title', __('tax::tax_rate.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('tax::tax.menu.tax')],
            ['label' => __('tax::tax_rate.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('tax::tax_rate.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="tax.rates.create">
            <a class="btn btn-sm fw-bold btn-primary" href="{{ route('admin.tax.rates.create') }}">
                {{ __('tax::tax_rate.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('tax::tax_rate.pages.index_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('tax::tax_rate.fields.name') }}</th>
                        <th>{{ __('tax::tax_rate.fields.percentage') }}</th>
                        <th>{{ __('tax::tax_rate.fields.type') }}</th>
                        <th>{{ __('tax::tax_rate.fields.region_code') }}</th>
                        <th>{{ __('tax::tax_rate.fields.status') }}</th>
                        <th>{{ __('tax::tax_rate.fields.is_default') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($taxRates as $taxRate)
                        <tr>
                            <td>{{ $taxRate->name }}</td>
                            <td>{{ number_format((float) $taxRate->percentage, 2) }}%</td>
                            <td>{{ __('tax::tax_rate.types.'.$taxRate->type) }}</td>
                            <td>{{ $taxRate->region_code ?: '—' }}</td>
                            <td>
                                <span class="badge badge-light-{{ $taxRate->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ __('tax::tax_rate.statuses.'.$taxRate->status) }}
                                </span>
                            </td>
                            <td>
                                @if($taxRate->is_default)
                                    <span class="badge badge-light-primary">{{ __('Yes') }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.tax.rates.edit', $taxRate->id) }}"
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="ki-duotone ki-message-edit fs-1"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.tax.rates.destroy', $taxRate->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-10">{{ __('No records found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
