@section('title', __('crm::sales_target.title'))

@section('toolbar')
    @php
        $breadcrumbItems = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard.index')],
            ['label' => __('CRM'), 'url' => route('admin.crm.dashboard')],
            ['label' => __('crm::settings.menu')],
            ['label' => __('crm::sales_target.menu')],
        ];
    @endphp
    <x-admin.breadcrumb :pageTitle="__('crm::sales_target.title')" :breadcrumbItems="$breadcrumbItems"/>
@endsection

<x-admin-layout>
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-8">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card mb-8">
        <div class="card-body p-6 p-lg-8">
            <h3 class="fw-bold mb-2">{{ __('crm::sales_target.title') }}</h3>
            <p class="text-muted mb-0">{{ __('crm::sales_target.subtitle') }}</p>
            <p class="text-muted fs-7 mt-3 mb-0">
                {{ __('crm::sales_target.default_hint', ['count' => config('crm.sales_target_per_period', 10)]) }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.crm.sales-targets.update') }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gy-5 mb-0">
                        <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase">
                            <th>{{ __('crm::sales_target.rep') }}</th>
                            <th class="w-175px">{{ __('crm::sales_target.deals_target') }}</th>
                            <th class="w-225px">{{ __('crm::sales_target.value_target') }} ({{ $currency }})</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-700 fw-semibold">
                        @foreach($reps as $index => $rep)
                            <tr>
                                <td>
                                    <input type="hidden" name="targets[{{ $index }}][employee_id]" value="{{ $rep['employee_id'] }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="symbol symbol-35px symbol-circle bg-light-primary text-primary fw-bold">
                                            {{ strtoupper(substr($rep['name'], 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="fw-bold text-gray-800">{{ $rep['name'] }}</div>
                                            <div class="text-muted fs-8">{{ $rep['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number"
                                           name="targets[{{ $index }}][deals_target]"
                                           value="{{ old('targets.'.$index.'.deals_target', $rep['deals_target']) }}"
                                           min="1"
                                           class="form-control form-control-solid @error('targets.'.$index.'.deals_target') is-invalid @enderror"
                                           required>
                                    @error('targets.'.$index.'.deals_target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number"
                                           name="targets[{{ $index }}][value_target]"
                                           value="{{ old('targets.'.$index.'.value_target', $rep['value_target']) }}"
                                           min="0"
                                           step="0.01"
                                           placeholder="{{ __('crm::sales_target.value_target_hint') }}"
                                           class="form-control form-control-solid @error('targets.'.$index.'.value_target') is-invalid @enderror">
                                    @error('targets.'.$index.'.value_target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-6">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-2"></i>{{ __('crm::sales_target.save') }}
                </button>
            </div>
        </div>
    </form>
</x-admin-layout>
