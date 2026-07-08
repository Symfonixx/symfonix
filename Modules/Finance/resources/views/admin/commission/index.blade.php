@section('title', __('finance::commission.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::commission.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::commission.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::commission.menu') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('finance::commission.fields.deal') }}</th>
                        <th>{{ __('finance::commission.fields.sales_rep') }}</th>
                        <th>{{ __('finance::commission.fields.percentage') }}</th>
                        <th>{{ __('finance::commission.fields.amount') }}</th>
                        <th>{{ __('finance::commission.fields.status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($commissions as $commission)
                        <tr>
                            <td>
                                @if($commission->deal)
                                    <a href="{{ route('admin.deals.show', $commission->deal_id) }}" class="text-hover-primary">
                                        {{ $commission->deal->title }}
                                    </a>
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </td>
                            <td>{{ $commission->employee?->name ?? __('N/A') }}</td>
                            <td>{{ number_format($commission->commission_percentage, 2) }}%</td>
                            <td>{{ number_format($commission->commission_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-light-{{ $commission->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ __('finance::commission.status.'.$commission->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($commission->status === 'pending')
                                    <form method="POST" action="{{ route('admin.finance.commissions.payout', $commission) }}"
                                          class="d-inline" data-confirm="{{ __('Are you sure?') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light-primary">
                                            <i class="bi bi-cash-coin me-1"></i>{{ __('finance::commission.actions.record_payout') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted fs-7">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-6">{{ __('finance::commission.messages.no_pending') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
