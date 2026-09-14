<div class="crm-quick-actions">
    <x-can perform="crm.activities.create">
        <a href="{{ route('admin.crm.calendar') }}" class="crm-qa-btn crm-qa-btn--success">
            <span class="crm-qa-icon"><i class="bi bi-telephone-outbound"></i></span>
            <span>{{ __('crm::dashboard.actions.log_call') }}</span>
        </a>
    </x-can>
    <x-can perform="sales.deals.create">
        <a href="{{ route('admin.deals.create') }}" class="crm-qa-btn crm-qa-btn--primary">
            <span class="crm-qa-icon"><i class="bi bi-briefcase"></i></span>
            <span>{{ __('crm::dashboard.actions.new_deal') }}</span>
        </a>
    </x-can>
    <x-can perform="crm.contacts.create">
        <a href="{{ route('admin.contacts.create') }}" class="crm-qa-btn crm-qa-btn--info">
            <span class="crm-qa-icon"><i class="bi bi-person-plus"></i></span>
            <span>{{ __('crm::dashboard.actions.add_contact') }}</span>
        </a>
    </x-can>
    <x-can perform="crm.leads.create">
        <a href="{{ route('admin.leads.create') }}" class="crm-qa-btn crm-qa-btn--warning">
            <span class="crm-qa-icon"><i class="bi bi-person-lines-fill"></i></span>
            <span>{{ __('crm::dashboard.actions.new_lead') }}</span>
        </a>
    </x-can>
    <x-can perform="sales.quotes.create">
        <a href="{{ route('admin.quotes.create') }}" class="crm-qa-btn crm-qa-btn--danger">
            <span class="crm-qa-icon"><i class="bi bi-file-earmark-text"></i></span>
            <span>{{ __('crm::dashboard.actions.new_quote') }}</span>
        </a>
    </x-can>
</div>
