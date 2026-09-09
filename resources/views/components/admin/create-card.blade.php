@props(['title', 'formUrl', 'description' => null, 'id' => null, 'cancelUrl' => null, 'icon' => 'pencil-square', 'color' => 'primary'])

@php($discardUrl = $cancelUrl ?? route('admin.dashboard.index'))

<div class="card settings-form-card">
    <div class="card-header border-0 pt-6">
        <div class="card-title d-flex align-items-center gap-3">
            <span class="sx-form-icon bg-light-{{ $color }} text-{{ $color }}">
                <i class="bi bi-{{ $icon }}"></i>
            </span>
            <div>
                <h2 class="fw-bold mb-1">{{ __($title) }}</h2>
                @if($description)
                    <span class="text-muted fs-7 fw-semibold">{{ __($description) }}</span>
                @endif
            </div>
        </div>
    </div>
    <form method="POST" action="{{ $formUrl }}" enctype="multipart/form-data" @if($id) id="{{ $id }}" @endif>
        @csrf
        <div class="card-body pt-2">
            {{ $slot }}
        </div>
        <div class="card-footer settings-form-footer d-flex justify-content-between align-items-center py-5 px-9 flex-wrap gap-3">
            <span class="text-muted fs-7">
                <i class="bi bi-info-circle me-1"></i>{{ __('Changes are applied after saving.') }}
            </span>
            <div class="d-flex gap-2">
                <a href="{{ $discardUrl }}" class="btn btn-light btn-active-light-primary">
                    <i class="bi bi-x-lg me-1"></i>{{ __('Discard') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i>{{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>
</div>
