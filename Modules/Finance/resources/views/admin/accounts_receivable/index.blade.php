@section('title', __('finance::accounts_receivable.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('finance::finance.menu.finance')],
            ['label' => __('finance::accounts_receivable.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('finance::accounts_receivable.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <div class="row g-5 mb-8">
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('finance::accounts_receivable.metrics.total_outstanding') }}</div>
                    <div class="fs-2hx fw-bold text-danger">
                        {{ number_format($aging['total_outstanding'], 2) }} {{ $aging['currency'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('finance::accounts_receivable.metrics.open_invoices') }}</div>
                    <div class="fs-2hx fw-bold">{{ $aging['invoices']->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <div class="text-muted fs-7">{{ __('finance::accounts_receivable.metrics.open_projects') }}</div>
                    <div class="fs-2hx fw-bold">{{ $aging['projects']->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-8">
        @foreach($aging['buckets'] as $bucket => $amount)
            <div class="col">
                <div class="card card-flush h-100">
                    <div class="card-body text-center">
                        <div class="text-muted fs-8 mb-1">{{ __('finance::accounts_receivable.buckets.'.$bucket) }}</div>
                        <div class="fw-bold">{{ number_format($amount, 2) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-8">
        <div class="card-header"><h3 class="card-title">{{ __('finance::accounts_receivable.metrics.open_invoices') }}</h3></div>
        <div class="card-body pt-0">
            @if($aging['invoices']->isEmpty())
                <p class="text-muted py-10 mb-0 text-center">{{ __('finance::accounts_receivable.empty_invoices') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gy-4">
                        <thead>
                        <tr class="text-muted fw-bold fs-7">
                            <th>{{ __('finance::accounts_receivable.fields.invoice_number') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.company') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.due_at') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.days_overdue') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.amount') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.status') }}</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($aging['invoices'] as $invoice)
                            @php
                                $daysOverdue = $invoice->due_at->isPast() ? $invoice->due_at->diffInDays(now()) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-hover-primary">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $invoice->company?->name }}</td>
                                <td>{{ $invoice->due_at->format('Y-m-d') }}</td>
                                <td>{{ $daysOverdue > 0 ? $daysOverdue : '—' }}</td>
                                <td class="fw-bold">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $invoice->status === 'overdue' ? 'danger' : 'primary' }}">
                                        {{ __('finance::invoice.status.'.$invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('finance::accounts_receivable.metrics.open_projects') }}</h3></div>
        <div class="card-body pt-0">
            @if($aging['projects']->isEmpty())
                <p class="text-muted py-10 mb-0 text-center">{{ __('finance::accounts_receivable.empty_projects') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gy-4">
                        <thead>
                        <tr class="text-muted fw-bold fs-7">
                            <th>{{ __('finance::accounts_receivable.fields.project') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.company') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.due_at') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.days_overdue') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.remaining') }}</th>
                            <th>{{ __('finance::accounts_receivable.fields.status') }}</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                        @foreach($aging['projects'] as $row)
                            @php
                                /** @var \Modules\Project\Models\Project $project */
                                $project = $row['project'];
                                $daysOverdue = $project->due_date?->isPast()
                                    ? $project->due_date->diffInDays(now())
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.projects.show', $project) }}" class="text-hover-primary">
                                        {{ $project->title }}
                                    </a>
                                </td>
                                <td>{{ $project->company?->name }}</td>
                                <td>{{ $project->due_date?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $daysOverdue > 0 ? $daysOverdue : '—' }}</td>
                                <td class="fw-bold">{{ number_format($row['remaining'], 2) }} {{ $row['currency'] }}</td>
                                <td>
                                    <span class="badge badge-light-warning">
                                        {{ __('project::project.payment_status.'.$row['payment_status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
