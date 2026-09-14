@section('title', __('crm::whatsapp.pages.templates_title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('crm::marketing.pages.index_title'), 'url' => route('admin.crm.marketing.index')],
            ['label' => __('crm::whatsapp.pages.templates_title')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::whatsapp.pages.templates_title')" :breadcrumbItems="$breadcrumbItems"/>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a class="btn btn-sm fw-bold btn-light-primary" href="{{ route('admin.crm.marketing.index') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('crm::marketing.actions.back_to_list') }}
        </a>
        <x-can perform="marketing.whatsapp_templates.create">
            <a class="btn btn-sm fw-bold btn-success" href="{{ route('admin.crm.marketing.whatsapp-templates.create') }}">
                {{ __('crm::whatsapp.actions.add_template') }} <i class="bi bi-plus-lg mx-1"></i>
            </a>
        </x-can>
    </div>
@endsection

<x-admin-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title fw-bold">{{ __('crm::whatsapp.pages.templates_title') }}</h3>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                    <tr class="text-muted fw-bold fs-7">
                        <th>{{ __('crm::whatsapp.fields.name') }}</th>
                        <th>{{ __('crm::whatsapp.fields.language') }}</th>
                        <th>{{ __('crm::whatsapp.fields.category') }}</th>
                        <th>{{ __('crm::marketing.fields.status') }}</th>
                        <th>{{ __('crm::whatsapp.fields.is_active') }}</th>
                        <th>{{ __('crm::whatsapp.fields.variables') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    @forelse($templates as $template)
                        @php
                            $varCount = $template->bodyVariableCount();
                            $statusBadge = match ($template->status) {
                                'approved' => 'badge-light-success',
                                'rejected' => 'badge-light-danger',
                                'pending' => 'badge-light-warning',
                                default => 'badge-light-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $template->name }}</span>
                                @if($template->footer)
                                    <span class="d-block text-muted fs-7">{{ Str::limit($template->footer, 40) }}</span>
                                @endif
                            </td>
                            <td>{{ $template->language }}</td>
                            <td>{{ __('crm::whatsapp.categories.'.$template->category) }}</td>
                            <td><span class="badge {{ $statusBadge }}">{{ __('crm::whatsapp.template_status.'.$template->status) }}</span></td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge badge-light-success">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge badge-light-secondary">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td><span class="badge badge-light-info">{{ $varCount }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.crm.marketing.whatsapp-templates.edit', $template) }}"
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                    <i class="ki-duotone ki-message-edit fs-1"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.crm.marketing.whatsapp-templates.destroy', $template) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                            onclick="return confirm(@json(__('crm::whatsapp.messages.confirm_delete')))">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-10">
                                <i class="bi bi-file-earmark-text fs-2x d-block mb-3"></i>
                                {{ __('crm::whatsapp.messages.no_templates') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $templates->links() }}</div>
        </div>
    </div>
</x-admin-layout>
