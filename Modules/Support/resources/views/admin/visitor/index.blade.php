@section('title' , __('Visitors'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Visitors'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Visitors')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Visitors" :formUrl="null">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{__('IP Address')}}</th>
            <th>{{__('URL')}}</th>
            <th>{{__('Referrer')}}</th>
            <th>{{__('User Agent')}}</th>
            <th>{{__('Created At')}}</th>
            <th>{{__('Actions')}}</th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $visitor)
            <tr>
                <td>
                    <a href="https://whatismyipaddress.com/ip/{{$visitor->ip}}" target="_blank">
                        {{$visitor->ip}}
                    </a>
                </td>
                <td>
                    @if($visitor->url)
                        <a href="{{ $visitor->url }}" target="_blank" class="text-primary text-hover-primary">
                            {{ Str::limit($visitor->url, 50) }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($visitor->referrer)
                        <a href="{{ $visitor->referrer }}" target="_blank" class="text-primary text-hover-primary">
                            {{ Str::limit($visitor->referrer, 40) }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($visitor->user_agent)
                        <span class="text-gray-800" title="{{ $visitor->user_agent }}">
                            {{ Str::limit($visitor->user_agent, 50) }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    {{ $visitor->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#visitorModal{{ $visitor->id }}">
                            <i class="bi bi-eye"></i> {{ __('View Details') }}
                        </button>
                        @if(!empty($visitor->ip))
                            <button type="button" class="btn btn-sm btn-danger" onclick="blockIp('{{ $visitor->ip }}')">
                                <i class="bi bi-lock"></i> {{ __('Block IP') }}
                            </button>
                        @endif
                    </div>
                </td>
            </tr>

            <!-- Visitor Details Modal -->
            <div class="modal fade" id="visitorModal{{ $visitor->id }}" tabindex="-1" aria-labelledby="visitorModalLabel{{ $visitor->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="visitorModalLabel{{ $visitor->id }}">{{ __('Visitor Details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('IP Address') }}:</strong>
                                    <p><a href="https://whatismyipaddress.com/ip/{{$visitor->ip}}" target="_blank">{{$visitor->ip}}</a></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Created At') }}:</strong>
                                    <p>{{ $visitor->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            @if($visitor->url)
                            <div class="mb-3">
                                <strong>{{ __('URL') }}:</strong>
                                <p><a href="{{ $visitor->url }}" target="_blank" class="text-primary">{{ $visitor->url }}</a></p>
                            </div>
                            @endif
                            @if($visitor->referrer)
                            <div class="mb-3">
                                <strong>{{ __('Referrer') }}:</strong>
                                <p><a href="{{ $visitor->referrer }}" target="_blank" class="text-primary">{{ $visitor->referrer }}</a></p>
                            </div>
                            @endif
                            @if($visitor->user_agent)
                            <div class="mb-3">
                                <strong>{{ __('User Agent') }}:</strong>
                                <p class="bg-light p-3 rounded" style="word-break: break-all;">{{ $visitor->user_agent }}</p>
                            </div>
                            @endif
                            @if(isset($visitor->device))
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Device') }}:</strong>
                                    <p>{{ $visitor->device ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Browser') }}:</strong>
                                    <p>{{ $visitor->browser ?? '-' }}</p>
                                </div>
                            </div>
                            @endif
                            @if(isset($visitor->platform))
                            <div class="mb-3">
                                <strong>{{ __('Platform') }}:</strong>
                                <p>{{ $visitor->platform ?? '-' }}</p>
                            </div>
                            @endif
                            @if(isset($visitor->updated_at))
                            <div class="mb-3">
                                <strong>{{ __('Updated At') }}:</strong>
                                <p>{{ $visitor->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            @if(!empty($visitor->ip))
                                <button type="button" class="btn btn-danger" onclick="blockIp('{{ $visitor->ip }}')">
                                    <i class="bi bi-lock"></i> {{ __('Block IP') }}
                                </button>
                            @endif
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
