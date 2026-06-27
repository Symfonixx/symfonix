@section('title', __('finance::salary.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::salary.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::salary.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::salary.actions.add') }}</h3>
        </div>
        <div class="card-body pt-0">
            <form method="POST" action="{{ route('admin.finance.salaries.store') }}" class="row g-4 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label for="employee_id" class="form-label">{{ __('finance::salary.fields.employee') }}</label>
                    <select id="employee_id" name="employee_id" class="form-select form-select-solid @error('employee_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select') }}...</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="base_salary" class="form-label">{{ __('finance::salary.fields.base_salary') }}</label>
                    <input type="number" step="0.01" min="0" id="base_salary" name="base_salary"
                           value="{{ old('base_salary') }}"
                           class="form-control form-control-solid @error('base_salary') is-invalid @enderror" required>
                    @error('base_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('finance::salary.actions.add') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('finance::salary.menu') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('finance::salary.fields.employee') }}</th>
                        <th>{{ __('finance::salary.fields.base_salary') }}</th>
                        <th>{{ __('finance::salary.fields.status') }}</th>
                        <th>{{ __('finance::salary.fields.paid_at') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($salaries as $salary)
                        <tr>
                            <td>{{ $salary->employee?->name ?? __('N/A') }}</td>
                            <td>{{ number_format($salary->base_salary, 2) }}</td>
                            <td>
                                <span class="badge badge-light-{{ $salary->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ __('finance::salary.status.'.$salary->status) }}
                                </span>
                            </td>
                            <td>{{ $salary->paid_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                    @if($salary->status === 'pending')
                                        <form method="POST" action="{{ route('admin.finance.salaries.payout', $salary) }}" class="d-inline-flex align-items-center gap-2">
                                            @csrf
                                            <input type="date" name="paid_at" value="{{ now()->toDateString() }}"
                                                   class="form-control form-control-solid form-control-sm w-auto"
                                                   title="{{ __('finance::salary.fields.paid_at') }}" required>
                                            <button type="submit" class="btn btn-sm btn-light-primary"
                                                    onclick="return confirm(@json(__('Are you sure?')))">
                                                <i class="bi bi-cash-coin me-1"></i>{{ __('finance::salary.actions.record_payout') }}
                                            </button>
                                        </form>
                                    @endif
                                    <form class="d-inline" method="POST" action="{{ route('admin.finance.salaries.destroy', $salary) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                                onclick="return confirm(@json(__('finance::salary.messages.confirm_delete')))">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-6">{{ __('finance::salary.messages.no_records') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $salaries->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
