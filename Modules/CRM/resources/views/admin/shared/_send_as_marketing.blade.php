@can('marketing.email.send')
    <div class="separator my-10"></div>

    <div class="card card-bordered mb-0">
        <div class="card-header min-h-50px">
            <h5 class="card-title mb-0">{{ __('crm::marketing.sections.send_as_marketing') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-check form-check-custom form-check-solid mb-5">
                <input class="form-check-input" type="checkbox" name="send_as_marketing" value="1"
                       id="send_as_marketing" @checked(old('send_as_marketing'))/>
                <label class="form-check-label" for="send_as_marketing">
                    <span class="fw-semibold">{{ __('crm::marketing.fields.send_as_marketing') }}</span>
                    <span class="d-block text-muted fs-7">{{ __('crm::marketing.hints.send_as_marketing') }}</span>
                </label>
            </div>

            <div id="marketing-audience-options" class="@unless(old('send_as_marketing')) d-none @endunless">
                <label class="form-label fw-semibold required mb-4">
                    {{ __('crm::marketing.fields.marketing_audience') }}
                </label>

                @error('marketing_audience')
                <div class="alert alert-danger mb-4">{{ $message }}</div>
                @enderror

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="d-flex align-items-start border border-dashed rounded p-4 cursor-pointer h-100 @error('marketing_audience') border-danger @enderror">
                            <input class="form-check-input mt-1 me-3" type="radio" name="marketing_audience"
                                   value="subscribers" id="marketing_audience_subscribers"
                                   @checked(old('marketing_audience', 'subscribers') === 'subscribers')/>
                            <span>
                                <span class="fw-semibold d-block">{{ __('crm::marketing.fields.subscribers') }}</span>
                                <span class="text-muted fs-7">{{ __('crm::marketing.hints.all_subscribers') }}</span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="d-flex align-items-start border border-dashed rounded p-4 cursor-pointer h-100">
                            <input class="form-check-input mt-1 me-3" type="radio" name="marketing_audience"
                                   value="contacts" id="marketing_audience_contacts"
                                   @checked(old('marketing_audience') === 'contacts')/>
                            <span>
                                <span class="fw-semibold d-block">{{ __('crm::marketing.fields.contacts') }}</span>
                                <span class="text-muted fs-7">{{ __('crm::marketing.hints.all_contacts') }}</span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="d-flex align-items-start border border-dashed rounded p-4 cursor-pointer h-100">
                            <input class="form-check-input mt-1 me-3" type="radio" name="marketing_audience"
                                   value="inquiries" id="marketing_audience_inquiries"
                                   @checked(old('marketing_audience') === 'inquiries')/>
                            <span>
                                <span class="fw-semibold d-block">{{ __('crm::marketing.fields.contact_forms') }}</span>
                                <span class="text-muted fs-7">{{ __('crm::marketing.hints.all_contact_forms') }}</span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="d-flex align-items-start border border-dashed rounded p-4 cursor-pointer h-100">
                            <input class="form-check-input mt-1 me-3" type="radio" name="marketing_audience"
                                   value="all" id="marketing_audience_all"
                                   @checked(old('marketing_audience') === 'all')/>
                            <span>
                                <span class="fw-semibold d-block">{{ __('crm::marketing.fields.all_audiences') }}</span>
                                <span class="text-muted fs-7">{{ __('crm::marketing.hints.all_audiences') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-text mt-4">{{ __('crm::marketing.hints.send_warning') }}</div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const toggle = document.getElementById('send_as_marketing');
                const options = document.getElementById('marketing-audience-options');

                if (!toggle || !options) {
                    return;
                }

                function syncVisibility() {
                    options.classList.toggle('d-none', !toggle.checked);
                }

                toggle.addEventListener('change', syncVisibility);
                syncVisibility();
            })();
        </script>
    @endpush
@endcan
