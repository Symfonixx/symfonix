<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold">{{ __('project::project.sections.profit_and_loss') }}</h3>
    </div>
    <div class="card-body pt-0">
        <p class="text-muted fs-7 mb-5">{{ __('project::project.hints.profit_and_loss') }}</p>
        <div class="row g-5">
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.invoiced') }}</div>
                <div class="fw-bold fs-4">
                    {{ number_format($profitAndLoss['invoiced'], 2) }} {{ $profitAndLoss['currency'] }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.labor_cost') }}</div>
                <div class="fw-bold fs-4">
                    {{ number_format($profitAndLoss['labor_cost'], 2) }} {{ $profitAndLoss['currency'] }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.project_expenses') }}</div>
                <div class="fw-bold fs-4">
                    {{ number_format($profitAndLoss['expenses'], 2) }} {{ $profitAndLoss['currency'] }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.total_cost') }}</div>
                <div class="fw-bold fs-4">
                    {{ number_format($profitAndLoss['total_cost'], 2) }} {{ $profitAndLoss['currency'] }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.profit_or_loss') }}</div>
                <div class="fw-bold fs-4 {{ $profitAndLoss['is_profit'] ? 'text-success' : ($profitAndLoss['is_loss'] ? 'text-danger' : '') }}">
                    {{ number_format($profitAndLoss['profit_or_loss'], 2) }} {{ $profitAndLoss['currency'] }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted fs-7">{{ __('project::project.fields.margin_rate') }}</div>
                <div class="fw-bold fs-4 {{ $profitAndLoss['is_profit'] ? 'text-success' : ($profitAndLoss['is_loss'] ? 'text-danger' : '') }}">
                    {{ $profitAndLoss['margin_rate'] }}%
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold">{{ __('project::project.sections.team') }}</h3>
    </div>
    <div class="card-body pt-0">
        @can('update', $project)
            <form method="POST" action="{{ route('admin.projects.employees.store', $project) }}" class="row g-4 align-items-end mb-8">
                @csrf
                <div class="col-md-3">
                    <label class="form-label required" for="employee_id">{{ __('project::project.fields.employee') }}</label>
                    <select id="employee_id" name="employee_id" class="form-select form-select-solid @error('employee_id') is-invalid @enderror" required>
                        <option value="">{{ __('project::project.fields.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((int) old('employee_id') === $employee->id)>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="role">{{ __('project::project.fields.role') }}</label>
                    <input type="text" id="role" name="role" value="{{ old('role') }}"
                           class="form-control form-control-solid" placeholder="{{ __('project::project.placeholders.role') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label required" for="started_at">{{ __('project::project.fields.started_at') }}</label>
                    <input type="date" id="started_at" name="started_at"
                           class="form-control form-control-solid @error('started_at') is-invalid @enderror"
                           value="{{ old('started_at', now()->toDateString()) }}" required>
                    @error('started_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="notes">{{ __('project::project.fields.notes') }}</label>
                    <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                           class="form-control form-control-solid">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus me-1"></i>{{ __('project::project.actions.assign_employee') }}
                    </button>
                </div>
            </form>
            <p class="text-muted fs-7 mb-5">
                {{ __('project::project.hints.labor_cost', ['days' => $profitAndLoss['working_days_per_month']]) }}
            </p>
        @endcan

        <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-0 gy-4">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th>{{ __('project::project.fields.employee') }}</th>
                    <th>{{ __('project::project.fields.role') }}</th>
                    <th>{{ __('project::project.fields.started_at') }}</th>
                    <th>{{ __('project::project.fields.ended_at') }}</th>
                    <th>{{ __('project::project.fields.days_worked') }}</th>
                    <th>{{ __('project::project.fields.daily_rate') }}</th>
                    <th>{{ __('project::project.fields.labor_cost') }}</th>
                    <th>{{ __('project::project.fields.assignment_status') }}</th>
                    <th class="text-end"></th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                @forelse($profitAndLoss['assignments'] as $row)
                    @php $assignment = $row['assignment']; @endphp
                    <tr>
                        <td class="fw-bold">{{ $assignment->employee?->name ?? __('N/A') }}</td>
                        <td>{{ $assignment->role ?: '—' }}</td>
                        <td>{{ $assignment->started_at?->format('Y-m-d') }}</td>
                        <td>{{ $assignment->ended_at?->format('Y-m-d') ?: '—' }}</td>
                        <td>{{ $row['days_worked'] }}</td>
                        <td>{{ number_format($row['daily_rate'], 2) }} {{ $profitAndLoss['currency'] }}</td>
                        <td>{{ number_format($row['labor_cost'], 2) }} {{ $profitAndLoss['currency'] }}</td>
                        <td>
                            @if($row['is_active'])
                                <span class="badge badge-light-success">{{ __('project::project.assignment_status.active') }}</span>
                            @else
                                <span class="badge badge-light-secondary">{{ __('project::project.assignment_status.finished') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @can('update', $project)
                                <div class="d-inline-flex align-items-center gap-2">
                                    @if($row['is_active'])
                                        <form method="POST" action="{{ route('admin.projects.employees.finish', [$project, $assignment]) }}" class="d-inline-flex align-items-center gap-2">
                                            @csrf
                                            <input type="date" name="ended_at" class="form-control form-control-sm form-control-solid"
                                                   value="{{ now()->toDateString() }}"
                                                   min="{{ $assignment->started_at?->format('Y-m-d') }}">
                                            <button type="submit" class="btn btn-sm btn-light-warning">
                                                {{ __('project::project.actions.finish_work') }}
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.projects.employees.destroy', [$project, $assignment]) }}"
                                          onsubmit="return confirm('{{ __('project::project.messages.confirm_remove_employee') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted text-center py-10">
                            {{ __('project::project.messages.no_employees') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold">{{ __('project::project.sections.expenses') }}</h3>
    </div>
    <div class="card-body pt-0">
        @can('Finance Management')
            <form method="POST" action="{{ route('admin.projects.expenses.store', $project) }}" class="row g-4 align-items-end mb-8">
                @csrf
                <div class="col-md-2">
                    <label class="form-label required" for="expense_amount">{{ __('finance::finance.fields.amount') }}</label>
                    <input type="number" step="0.01" min="0.01" id="expense_amount" name="amount"
                           class="form-control form-control-solid @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label required" for="expense_currency">{{ __('finance::finance.fields.currency') }}</label>
                    <input type="text" id="expense_currency" name="currency" maxlength="3"
                           class="form-control form-control-solid text-uppercase @error('currency') is-invalid @enderror"
                           value="{{ old('currency', $profitAndLoss['currency']) }}" required>
                    @error('currency') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label required" for="expense_category_id">{{ __('finance::finance.fields.category') }}</label>
                    <select id="expense_category_id" name="expense_category_id"
                            class="form-select form-select-solid @error('expense_category_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select') }}...</option>
                        @foreach($expenseCategories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('expense_category_id') === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label required" for="expense_date">{{ __('finance::finance.fields.date') }}</label>
                    <input type="date" id="expense_date" name="transaction_date"
                           class="form-control form-control-solid @error('transaction_date') is-invalid @enderror"
                           value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    @error('transaction_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="expense_description">{{ __('finance::finance.fields.description') }}</label>
                    <input type="text" id="expense_description" name="description" value="{{ old('description') }}"
                           class="form-control form-control-solid">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-light-danger">
                        <i class="bi bi-cash-stack me-1"></i>{{ __('project::project.actions.log_expense') }}
                    </button>
                </div>
            </form>
        @endcan

        <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-0 gy-4">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th>{{ __('finance::finance.fields.date') }}</th>
                    <th>{{ __('finance::finance.fields.category') }}</th>
                    <th>{{ __('finance::finance.fields.description') }}</th>
                    <th>{{ __('finance::finance.fields.amount') }}</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                @forelse($profitAndLoss['expense_entries'] as $entry)
                    @php
                        $category = $entry->lines
                            ->firstWhere('account', \Modules\Finance\Models\JournalLine::ACCOUNT_EXPENSE)
                            ?->expenseCategory;
                    @endphp
                    <tr>
                        <td>{{ $entry->transaction_date?->format('Y-m-d') }}</td>
                        <td>{{ $category?->name ?: '—' }}</td>
                        <td>{{ $entry->description ?: '—' }}</td>
                        <td>{{ number_format($entry->amount, 2) }} {{ $entry->currency }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center py-10">
                            {{ __('project::project.messages.no_expenses') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
