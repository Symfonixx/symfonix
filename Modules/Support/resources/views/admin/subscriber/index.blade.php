@section('title' , __('Newsletter Subscribers'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Newsletter Subscribers'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Newsletter Subscribers')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('admin.subscribers.export') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
        </a>
        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload"></i> {{ __('Import from Excel') }}
        </button>
    </div>
@endsection
@section('js')
<script>
	function blockIp(ip) {
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
<x-admin-layout>
    <x-admin.table :model="$model" search="Search In Contacts"
                   :formUrl="route('admin.subscribers.deleteMulti')">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>

            <th>{{__('Email')}}</th>
            <th>{{__('Ip Address')}}</th>
            <th>{{__('Language')}}</th>
            <th>{{__('Subscription Date')}}</th>
            <th></th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $subscriber)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$subscriber->id}}"/>
                    </div>
                </td>

                <td>
                    <a href="mailto:{{$subscriber->email}}">
                        {{$subscriber->email}}
                    </a>
                </td>

                <td>
                    <a href="https://whatismyipaddress.com/ip/{{$subscriber->ip_address}}" target="_blank">
                        {{$subscriber->ip_address}}
                    </a>
                </td>

                <td>
                    {{$subscriber->lang}}
                </td>

                <td>
                    {{$subscriber->created_at}}
                </td>
                <td>
                    @if(!empty($subscriber->ip_address))
                        @if(!empty($subscriber->blocked))
                            <span class="badge badge-light-danger fs-7 fw-bold">{{ __('Blocked') }}</span>
                        @else
                            <button type="button" class="btn btn-sm btn-danger" onclick="blockIp('{{ $subscriber->ip_address }}')">
                            <i class="bi bi-lock"></i>    {{ __('Block IP') }}
                            </button>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        <!--end::Table body-->
    </x-admin.table>
    <!--end::Card-->
    
    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">{{ __('Import Subscribers from Excel') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.subscribers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('Select Excel File') }}</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">{{ __('Accepted formats: .xlsx, .xls, .csv (Max: 10MB)') }}</div>
                        </div>
                        <div class="alert alert-info">
                            <strong>{{ __('Excel Format (matches export format):') }}</strong><br>
                            <strong>{{ __('Required:') }}</strong> Email<br>
                            <strong>{{ __('Optional:') }}</strong> IP Address, Language, Blocked<br>
                            <strong>{{ __('Ignored:') }}</strong> ID, Created At (will be auto-generated)<br>
                            <small>{{ __('Note: If email already exists, the subscriber will be updated instead of creating a duplicate.') }}</small>
                            <br><br>
                            <strong>{{ __('Example format:') }}</strong><br>
                            <code>Email | IP Address | Language | Blocked</code><br>
                            <code>user@example.com | 192.168.1.1 | en | No</code>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>


