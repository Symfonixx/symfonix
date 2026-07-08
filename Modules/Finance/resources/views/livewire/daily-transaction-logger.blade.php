<div>
    <div class="card card-flush border-0 shadow-sm h-100">
        <div class="card-header border-0 pt-6">
            <div class="card-title d-flex align-items-center justify-content-between w-100">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>
                    {{ __('finance::finance.actions.daily_action_center') }}
                </h3>
                <a href="{{ route('admin.finance.expense-categories.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="bi bi-tags me-1"></i>{{ __('finance::finance.menu.expense_categories') }}
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            @if (session('finance_log_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('finance_log_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form wire:submit="logTransaction">
                <p class="text-muted fs-7 mb-4">{{ __('finance::finance.messages.double_entry_hint') }}</p>
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('finance::finance.fields.type') }}</label>
                    <div class="d-flex gap-4">
                        <label class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" wire:model.live="type" value="debit">
                            <span class="form-check-label">{{ __('finance::finance.fields.debit') }}</span>
                        </label>
                        <label class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" wire:model.live="type" value="credit">
                            <span class="form-check-label">{{ __('finance::finance.fields.credit') }}</span>
                        </label>
                    </div>
                    @error('type') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                    <div class="form-text">{{ __('finance::finance.messages.double_entry_hint') }}</div>
                </div>

                @if($type === 'debit')
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="expense_category_id">{{ __('finance::finance.fields.category') }}</label>
                        <select id="expense_category_id" wire:model="expense_category_id" class="form-select form-select-solid">
                            <option value="">{{ __('Select') }}...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="project_id">{{ __('finance::finance.fields.project') }}</label>
                    <select id="project_id" wire:model="project_id" class="form-select form-select-solid">
                        <option value="">{{ __('None') }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="amount">{{ __('finance::finance.fields.amount') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" id="amount" wire:model="amount"
                               class="form-control form-control-solid" placeholder="0.00">
                        <input type="text" id="currency" wire:model="currency" maxlength="3"
                               class="form-control form-control-solid w-75px text-uppercase"
                               placeholder="USD" style="max-width: 5rem;">
                    </div>
                    @error('amount') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                    @error('currency') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="description">{{ __('finance::finance.fields.description') }}</label>
                    <textarea id="description" wire:model="description" rows="2"
                              class="form-control form-control-solid" placeholder="{{ __('Optional') }}"></textarea>
                    @error('description') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold" for="transaction_date">{{ __('finance::finance.fields.date') }}</label>
                    <input type="date" id="transaction_date" wire:model="transaction_date" class="form-control form-control-solid">
                    @error('transaction_date') <div class="text-danger fs-7 mt-1">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="logTransaction">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('finance::finance.actions.log_transaction') }}
                    </span>
                    <span wire:loading wire:target="logTransaction">
                        <span class="spinner-border spinner-border-sm me-1"></span>{{ __('Processing') }}...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
