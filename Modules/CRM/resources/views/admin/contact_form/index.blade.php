@section('title', __('crm::contact_form.pages.index_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::contact_form.pages.index_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::contact_form.pages.index_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-can perform="crm.inquiries.create">
            <a href="{{ route('admin.contact_forms.create') }}" class="btn btn-sm fw-bold btn-primary">
                {{ __('crm::contact_form.actions.add') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
        <a href="{{ route('admin.contact_forms.export') }}" class="btn btn-sm btn-light-primary">
            <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
        </a>
    </div>
@endsection
<x-admin-layout>
    <x-admin.table :model="$model" :search="__('crm::contact_form.search.placeholder')"
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

            <th>{{ __('Details') }}</th>
            <th>{{ __('crm::contact_form.fields.company') }}</th>
            <th>{{ __('crm::contact_form.fields.ip_address') }}</th>
            <th>{{ __('crm::contact_form.fields.subject') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Created At') }}</th>
            <th class="text-end"></th>
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
                        <span class="text-gray-800 mb-1 fw-semibold">
                            {{$contact->name}}
                        </span>
                        @if($contact->mobile)
                            <a class="text-hover-primary text-gray-500 fs-7" target="_blank"
                               href="tel:{{$contact->mobile}}">{{$contact->mobile}}</a>
                        @endif
                        <a class="text-hover-primary text-gray-500 fs-7" target="_blank"
                           href="mailto:{{$contact->email}}">{{$contact->email}}</a>
                    </div>
                </td>

                <td>
                    @if($contact->company)
                        <a href="{{ route('admin.companies.show', $contact->company) }}" class="text-hover-primary text-gray-800">
                            {{ $contact->company->name }}
                        </a>
                    @else
                        <span class="text-muted">{{ __('N/A') }}</span>
                    @endif
                </td>

                <td>
                    @if($contact->ip_address)
                        <a href="https://whatismyipaddress.com/ip/{{$contact->ip_address}}" target="_blank">
                            {{$contact->ip_address}}
                        </a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>

                <td>
                    @if($contact->services->isNotEmpty())
                        @foreach($contact->services as $service)
                            <span class="badge badge-light-primary me-1 mb-1">
                                {{ $service->getTranslation('title', app()->getLocale()) }}
                            </span>
                        @endforeach
                    @elseif($contact->service)
                        {{ $contact->service->getTranslation('title', app()->getLocale()) }}
                    @else
                        {{ $contact->subject ?: __('N/A') }}
                    @endif
                </td>
                <td>
                    @if($contact->lead_id)
                        <span class="badge badge-light-success">{{ __('crm::contact_form.status.converted') }}</span>
                    @elseif($contact->contact_id)
                        <span class="badge badge-light-primary">{{ __('crm::contact.menu.contacts') }}</span>
                    @else
                        <span class="badge badge-light-secondary">{{ __('crm::contact_form.status.active') }}</span>
                    @endif
                </td>
                <td>
                    {{$contact->created_at}}
                </td>
                <td class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#contactModal{{ $contact->id }}">
                            <i class="bi bi-eye"></i>
                        </button>
                        @if(! $contact->lead_id)
                            <form method="POST" action="{{ route('admin.contact_forms.convertLead', $contact) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light-success" title="{{ __('crm::contact_form.actions.convert_to_lead') }}">
                                    <i class="bi bi-funnel"></i>
                                </button>
                            </form>
                        @endif
                        @if(! $contact->contact_id)
                            <form method="POST" action="{{ route('admin.contact_forms.convertContact', $contact) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light-warning" title="{{ __('crm::contact_form.actions.convert_to_contact') }}">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.contact_forms.edit', $contact) }}" class="btn btn-sm btn-light-info" title="{{ __('crm::contact_form.actions.edit') }}">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </td>
            </tr>

            <!-- Contact Details Modal -->
            <div class="modal fade" id="contactModal{{ $contact->id }}" tabindex="-1" aria-labelledby="contactModalLabel{{ $contact->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="contactModalLabel{{ $contact->id }}">{{ __('crm::contact_form.actions.view_details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.name') }}:</strong>
                                    <p>{{ $contact->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.email') }}:</strong>
                                    <p><a href="mailto:{{ $contact->email }}" target="_blank">{{ $contact->email }}</a></p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.mobile') }}:</strong>
                                    <p>
                                        @if($contact->mobile)
                                            <a href="tel:{{ $contact->mobile }}" target="_blank">{{ $contact->mobile }}</a>
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.company') }}:</strong>
                                    <p>
                                        @if($contact->company)
                                            <a href="{{ route('admin.companies.show', $contact->company) }}">{{ $contact->company->name }}</a>
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.ip_address') }}:</strong>
                                    <p>
                                        @if($contact->ip_address)
                                            <a href="https://whatismyipaddress.com/ip/{{ $contact->ip_address }}" target="_blank">{{ $contact->ip_address }}</a>
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('crm::contact_form.fields.blocked') }}:</strong>
                                    <p><span class="badge badge-light-{{ $contact->blocked ? 'danger' : 'success' }}">{{ $contact->blocked ? __('crm::contact_form.status.blocked') : __('crm::contact_form.status.active') }}</span></p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>{{ __('crm::contact_form.fields.services') }}:</strong>
                                <p>
                                    @if($contact->services->isNotEmpty())
                                        @foreach($contact->services as $service)
                                            <span class="badge badge-light-primary me-1 mb-1">
                                                {{ $service->getTranslation('title', app()->getLocale()) }}
                                            </span>
                                        @endforeach
                                    @elseif($contact->service)
                                        {{ $contact->service->getTranslation('title', app()->getLocale()) }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </p>
                            </div>
                            @if($contact->subject)
                            <div class="mb-3">
                                <strong>{{ __('crm::contact_form.fields.subject') }}:</strong>
                                <p>{{ $contact->subject }}</p>
                            </div>
                            @endif
                            <div class="mb-3">
                                <strong>{{ __('crm::contact_form.fields.message') }}:</strong>
                                <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                    {{ $contact->message }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>{{ __('Created At') }}:</strong>
                                <p>{{ $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : __('N/A') }}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                            @if(! $contact->lead_id)
                                <form method="POST" action="{{ route('admin.contact_forms.convertLead', $contact) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-funnel me-1"></i>{{ __('crm::contact_form.actions.convert_to_lead') }}
                                    </button>
                                </form>
                            @endif
                            @if(! $contact->contact_id)
                                <form method="POST" action="{{ route('admin.contact_forms.convertContact', $contact) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-person-plus me-1"></i>{{ __('crm::contact_form.actions.convert_to_contact') }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.contact_forms.edit', $contact) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i>{{ __('crm::contact_form.actions.edit') }}
                            </a>
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
