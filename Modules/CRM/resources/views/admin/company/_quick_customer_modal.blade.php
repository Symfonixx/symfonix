<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.customers.store') }}" id="quickCustomerForm">
                @csrf
                <input type="hidden" name="type" value="customer">
                <input type="hidden" name="return_url" value="{{ url()->current() }}">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('crm::company.actions.add_customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->hasAny(['name', 'email', 'mobile', 'password']) && old('type') === 'customer')
                        <div class="alert alert-danger mb-5">
                            <ul class="mb-0 ps-4">
                                @foreach (['name', 'email', 'mobile', 'password'] as $field)
                                    @error($field)
                                        <li>{{ $message }}</li>
                                    @enderror
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-5">
                        <label for="quick_customer_name" class="form-label required">{{ __('Name') }}</label>
                        <input type="text" id="quick_customer_name" name="name"
                               class="form-control form-control-solid @error('name') is-invalid @enderror"
                               value="{{ old('type') === 'customer' ? old('name') : '' }}" required>
                    </div>
                    <div class="mb-5">
                        <label for="quick_customer_email" class="form-label required">{{ __('Email') }}</label>
                        <input type="email" id="quick_customer_email" name="email"
                               class="form-control form-control-solid @error('email') is-invalid @enderror"
                               value="{{ old('type') === 'customer' ? old('email') : '' }}" required>
                    </div>
                    <div class="mb-5">
                        <label for="quick_customer_mobile" class="form-label required">{{ __('Mobile') }}</label>
                        <input type="tel" id="quick_customer_mobile" name="mobile"
                               inputmode="numeric" pattern="[0-9]{10,15}"
                               class="form-control form-control-solid @error('mobile') is-invalid @enderror"
                               value="{{ old('type') === 'customer' ? old('mobile') : '' }}" required>
                        <div class="form-text">{{ __('Enter 10–15 digits only, without spaces or symbols.') }}</div>
                    </div>
                    <div class="mb-0">
                        <label for="quick_customer_password" class="form-label required">{{ __('Password') }}</label>
                        <input type="password" id="quick_customer_password" name="password"
                               class="form-control form-control-solid @error('password') is-invalid @enderror"
                               required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerId = new URLSearchParams(window.location.search).get('customer_id');
            const userSelect = document.getElementById('user_id');

            if (customerId && userSelect && window.jQuery) {
                jQuery(userSelect).val(customerId).trigger('change');
            }

            @if ($errors->hasAny(['name', 'email', 'mobile', 'password']) && old('type') === 'customer')
            const modal = document.getElementById('quickCustomerModal');
            if (modal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
            @endif
        });
    </script>
@endpush
