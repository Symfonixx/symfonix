@section('title' , __('Blocked IPs'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Blocked IPs'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Blocked IPs')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3"></div>
@endsection
@section('js')
<script>
	function unblockIp(ip) {
		var form = document.createElement('form');
		form.method = 'POST';
		form.action = '{{ route('admin.firewall.unblock') }}';
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
<x-admin-layout>
    <x-admin.table :model="$model" search="Search in Blocked IPs" :formUrl="null">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th>{{__('Ip Address')}}</th>
            <th>{{__('Created At')}}</th>
            <th></th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $ip)
            <tr>
                <td>
                    <a href="https://whatismyipaddress.com/ip/{{$ip->ip}}" target="_blank">
                        {{$ip->ip}}
                    </a>
                </td>
                <td>
                    {{$ip->created_at}}
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="unblockIp('{{ $ip->ip }}')">
                      <i class="bi bi-unlock"></i>  {{ __('Unblock') }}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
        <!--end::Table body-->
    </x-admin.table>
</x-admin-layout>





