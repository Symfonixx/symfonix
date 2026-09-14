@section('title', __('crm::marketing.pages.show_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => strip_tags($campaign->subject)],
        ];

        $sources = $campaign->recipient_sources ?? [];
        $sourceLabels = [];
        $customEmails = collect();

        if (! empty($sources['all_subscribers'])) {
            $sourceLabels[] = __('crm::marketing.sources.all_subscribers');
        } elseif (! empty($sources['subscriber_ids'])) {
            $sourceLabels[] = __('crm::marketing.sources.subscribers', ['count' => count($sources['subscriber_ids'])]);
        }

        if (! empty($sources['all_contacts'])) {
            $sourceLabels[] = __('crm::marketing.sources.all_contacts');
        } elseif (! empty($sources['contact_ids'])) {
            $sourceLabels[] = __('crm::marketing.sources.contacts', ['count' => count($sources['contact_ids'])]);
        }

        if (! empty($sources['all_contact_forms'])) {
            $sourceLabels[] = __('crm::marketing.sources.all_contact_forms');
        } elseif (! empty($sources['contact_form_ids'])) {
            $sourceLabels[] = __('crm::marketing.sources.contact_forms', ['count' => count($sources['contact_form_ids'])]);
        }

        if (! empty($sources['custom_emails'])) {
            if (is_array($sources['custom_emails'])) {
                $customEmails = collect($sources['custom_emails'])->filter()->values();
            } else {
                $customEmails = collect(preg_split('/[\s,;]+/', (string) $sources['custom_emails'], -1, PREG_SPLIT_NO_EMPTY) ?: []);
            }
        }
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::marketing.pages.show_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.crm.marketing.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column align-items-start">
                <h2 class="fw-bold mb-1">{!! $campaign->subject !!}</h2>
                @php
                    $status = $campaign->status ?? 'pending';
                    $statusBadge = $campaign::statusBadgeClass($status);
                @endphp
                <span class="text-muted fs-7">
                    <span class="badge {{ $statusBadge }} me-1">{{ __('crm::marketing.status.'.$status) }}</span>
                    {{ __('crm::marketing.fields.sent_by') }}: {{ $campaign->user?->name ?? '—' }}
                    · {{ $campaign->created_at?->format('Y-m-d H:i') }}
                    · {{ __('crm::marketing.fields.recipients_count') }}: {{ $campaign->recipients_count }}
                </span>
            </div>
        </div>
        <div class="card-body">
            @if(! empty($sourceLabels) || $customEmails->isNotEmpty())
                <div class="mb-8">
                    @if(! empty($sourceLabels))
                        <div class="mb-4">
                            <span class="text-muted fw-semibold me-2">{{ __('crm::marketing.fields.recipients') }}:</span>
                            @foreach($sourceLabels as $label)
                                <span class="badge badge-light-primary me-1">{{ $label }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($customEmails->isNotEmpty())
                        <div>
                            <span class="text-muted fw-semibold d-block mb-2">{{ __('crm::marketing.fields.custom_emails') }}:</span>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($customEmails as $email)
                                    <span class="badge badge-light-info">{{ $email }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-light rounded p-6">
                {!! $campaign->body !!}
            </div>
        </div>
    </div>
</x-admin-layout>
