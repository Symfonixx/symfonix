@section('title' , __('Firewall Logs'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Firewall Logs'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Firewall Logs')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('admin.firewall_logs.export') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
        </a>
    </div>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" search="Search in Firewall Logs" :formUrl="null">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{__('IP Address')}}</th>
            <th>{{__('Level')}}</th>
            <th>{{__('Middleware')}}</th>
            <th>{{__('URL')}}</th>
            <th>{{__('User')}}</th>
            <th>{{__('Created At')}}</th>
            <th>{{__('Actions')}}</th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $log)
            <tr>
                <td>
                    <a href="https://whatismyipaddress.com/ip/{{$log->ip}}" target="_blank">
                        {{$log->ip}}
                    </a>
                </td>
                <td>
                    @php
                        $levelColors = [
                            'low' => 'success',
                            'medium' => 'warning',
                            'high' => 'danger',
                        ];
                        $color = $levelColors[$log->level] ?? 'secondary';
                    @endphp
                    <span class="badge badge-light-{{$color}} fs-7 fw-bold">{{ ucfirst($log->level) }}</span>
                </td>
                <td>
                    <span class="badge badge-light-info fs-7 fw-bold">{{ $log->middleware }}</span>
                </td>
                <td>
                    @if($log->url)
                        <a href="{{ $log->url }}" target="_blank" class="text-primary text-hover-primary">
                            {{ Str::limit($log->url, 50) }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($log->user_id)
                        <span class="text-gray-800">{{ $log->user_id }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                        <i class="bi bi-eye"></i> {{ __('View Details') }}
                    </button>
                </td>
            </tr>

            <!-- Log Details Modal -->
            <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logModalLabel{{ $log->id }}">{{ __('Firewall Log Details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('IP Address') }}:</strong>
                                    <p><a href="https://whatismyipaddress.com/ip/{{$log->ip}}" target="_blank">{{$log->ip}}</a></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Level') }}:</strong>
                                    <p><span class="badge badge-light-{{$color}}">{{ ucfirst($log->level) }}</span></p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Middleware') }}:</strong>
                                    <p><span class="badge badge-light-info">{{ $log->middleware }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('User ID') }}:</strong>
                                    <p>{{ $log->user_id ?? '-' }}</p>
                                </div>
                            </div>
                            @if($log->url)
                            <div class="mb-3">
                                <strong>{{ __('URL') }}:</strong>
                                <p><a href="{{ $log->url }}" target="_blank" class="text-primary">{{ $log->url }}</a></p>
                            </div>
                            @endif
                            @if($log->referrer)
                            <div class="mb-3">
                                <strong>{{ __('Referrer') }}:</strong>
                                <p>{{ $log->referrer }}</p>
                            </div>
                            @endif
                            @if($log->request)
                            <div class="mb-3">
                                <strong>{{ __('Request Data') }}:</strong>
                                <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto; font-size: 12px;">{{ Str::limit($log->request, 1000) }}</pre>
                            </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Created At') }}:</strong>
                                    <p>{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Updated At') }}:</strong>
                                    <p>{{ $log->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="button" class="btn btn-danger" onclick="blockIp('{{ $log->ip }}')">
                                <i class="bi bi-lock"></i> {{ __('Block IP') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </tbody>
        <!--end::Table body-->
    </x-admin.table>
</x-admin-layout>

@section('js')
<script>
	function blockIp(ip) {
		if (!confirm('{{ __("Are you sure you want to block this IP address?") }}')) {
			return;
		}
		var form = document.createElement('form');
		form.method = 'POST';
		form.action = '{{ route('admin.firewall.block') }}';
		var token = document.createElement('input');
		token.type = 'hidden';
		token.name = '_token';
		token.value = '{{ csrf_token() }}';
		var ipInput = document.createElement('input');
		ipInput.type = 'hidden';
		ipInput.name = 'ip';
		ipInput.value = ip;
		form.appendChild(token);
		form.appendChild(ipInput);
		document.body.appendChild(form);
		form.submit();
	}
</script>
@endsection



