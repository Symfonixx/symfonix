@section('title' , __('Contacts'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard.index')],
            ['label' => 'Contacts'],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('Contacts')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('admin.contact_forms.export') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
        </a>
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
                   :formUrl="route('admin.contact_forms.deleteMulti')">
        <!--begin::Table head-->
        <thead>
        <tr class="text-start text-muted fw-bold fs-7 gs-0">
            <th class="w-10px pe-2" data-orderable="false">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                           data-kt-check-target="#dataTable .form-check-input" value="1"/>
                </div>
            </th>

            <th>{{__('Details')}}</th>
            <th>{{__('Ip Address')}}</th>
            <th>{{__('Subject')}}</th>
            <th>{{__('Created At')}}</th>
            <th></th>
        </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="text-gray-600 fw-semibold">
        @foreach($model as $contact)
            <tr>
                <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="{{$contact->id}}"/>
                    </div>
                </td>

                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 mb-1">
                            {{$contact->name}}
                        </span>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="tel:{{$contact->mobile}}">{{$contact->mobile}}</a>
                        <a class="text-hover-primary text-gray-500" target="_blank"
                           href="mailto:{{$contact->email}}">{{$contact->email}}</a>
                    </div>
                </td>

                <td>
                    <a href="https://whatismyipaddress.com/ip/{{$contact->ip_address}}" target="_blank">
                        {{$contact->ip_address}}
                    </a>
                </td>

                <td>
                    {{$contact->subject}}
                </td>
                <td>
                    {{$contact->created_at}}
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#contactModal{{ $contact->id }}">
                            <i class="bi bi-eye"></i> {{ __('View Details') }}
                        </button>
                        @if(!empty($contact->ip_address))
                            @if(!empty($contact->blocked))
                                <span class="badge badge-light-danger fs-7 fw-bold">{{ __('Blocked') }}</span>
                            @else
                                <button type="button" class="btn btn-sm btn-danger" onclick="blockIp('{{ $contact->ip_address }}')">
                                   <i class="bi bi-lock"></i> {{ __('Block IP') }}
                                </button>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>

            <!-- Contact Details Modal -->
            <div class="modal fade" id="contactModal{{ $contact->id }}" tabindex="-1" aria-labelledby="contactModalLabel{{ $contact->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="contactModalLabel{{ $contact->id }}">{{ __('Contact Details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Name') }}:</strong>
                                    <p>{{ $contact->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Email') }}:</strong>
                                    <p><a href="mailto:{{ $contact->email }}" target="_blank">{{ $contact->email }}</a></p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Mobile') }}:</strong>
                                    <p><a href="tel:{{ $contact->mobile }}" target="_blank">{{ $contact->mobile }}</a></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('IP Address') }}:</strong>
                                    <p><a href="https://whatismyipaddress.com/ip/{{ $contact->ip_address }}" target="_blank">{{ $contact->ip_address }}</a></p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>{{ __('Subject') }}:</strong>
                                <p>{{ $contact->subject }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>{{ __('Message') }}:</strong>
                                <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                    {{ $contact->message }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Blocked') }}:</strong>
                                    <p><span class="badge badge-light-{{ $contact->blocked ? 'danger' : 'success' }}">{{ $contact->blocked ? __('Yes') : __('No') }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Created At') }}:</strong>
                                    <p>{{ $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            @if(!empty($contact->ip_address) && !$contact->blocked)
                                <button type="button" class="btn btn-danger" onclick="blockIp('{{ $contact->ip_address }}')">
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
    <!--end::Card-->
</x-admin-layout>


