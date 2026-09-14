@props(['kpis' => [], 'currency' => null, 'columns' => 3])

<div class="row g-5 mb-5">
    @foreach($kpis as $key => $kpi)
        <div class="col-md-{{ 12 / min($columns, count($kpis)) }} col-lg-{{ 12 / min($columns, count($kpis)) }}">
            <div class="card card-flush h-100">
                <div class="card-body">
                    <span class="text-muted fs-7">{{ __('reporting::report.kpis.'.$key) }}</span>
                    <div class="d-flex align-items-end gap-2 mt-1">
                        <span class="fs-2 fw-bold">
                            @if($currency && ! str_contains($key, 'rate') && ! str_contains($key, 'count') && ! str_contains($key, 'campaigns') && ! str_contains($key, 'tickets') && ! str_contains($key, 'projects') && ! str_contains($key, 'leads') && ! str_contains($key, 'recipients') && ! str_contains($key, 'hours'))
                                {{ number_format($kpi['value'], 2) }} {{ $currency }}
                            @elseif(str_contains($key, 'rate'))
                                {{ number_format($kpi['value'], 1) }}%
                            @else
                                {{ number_format($kpi['value']) }}
                            @endif
                        </span>
                        @if(isset($kpi['change']) && $kpi['change'] !== null)
                            <span class="badge badge-light-{{ $kpi['trend'] === 'up' ? 'success' : ($kpi['trend'] === 'down' ? 'danger' : 'secondary') }} fs-8">
                                @if($kpi['trend'] === 'up')<i class="bi bi-arrow-up"></i>@elseif($kpi['trend'] === 'down')<i class="bi bi-arrow-down"></i>@endif
                                {{ abs($kpi['change']) }}%
                            </span>
                        @endif
                    </div>
                    @if(isset($kpi['change']) && $kpi['change'] !== null)
                        <span class="text-muted fs-8">{{ __('reporting::report.vs_previous') }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
