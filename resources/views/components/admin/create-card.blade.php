@props(['title', 'formUrl', 'description' => null, 'id' => null, 'cancelUrl' => null])

@php($discardUrl = $cancelUrl ?? route('admin.dashboard.index'))

<div class="card settings-form-card">
    <div class="card-header border-0 pt-6">
        <div class="card-title flex-column align-items-start">
            <h2 class="fw-bold mb-1">{{ __($title) }}</h2>
            @if($description)
                <span class="text-muted fs-7 fw-semibold">{{ __($description) }}</span>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ $formUrl }}" enctype="multipart/form-data" @if($id) id="{{ $id }}" @endif>
        @csrf
        <div class="card-body pt-2">
            {{ $slot }}
        </div>
        <div class="card-footer settings-form-footer d-flex justify-content-between align-items-center py-5 px-9">
            <span class="text-muted fs-7">
                <i class="bi bi-info-circle me-1"></i>{{ __('Changes are applied after saving.') }}
            </span>
            <div class="d-flex gap-2">
                <a href="{{ $discardUrl }}" class="btn btn-light btn-active-light-primary">{{ __('Discard') }}</a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Save Changes') }} <i class="bi bi-check2-circle ms-1"></i>
                </button>
            </div>
        </div>
    </form>
</div>
