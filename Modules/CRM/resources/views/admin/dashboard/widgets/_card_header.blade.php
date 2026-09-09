@php
    $headingIcon = $icon ?? ($widget['icon'] ?? 'grid');
    $headingColor = $color ?? ($widget['color'] ?? 'primary');
@endphp

<div class="card-header border-0 pt-6 pb-2">
    <div class="d-flex align-items-center gap-3 w-100">
        <span class="crm-drag-handle text-muted" title="{{ __('crm::dashboard.customize.drag_hint') }}">
            <i class="bi bi-grip-vertical fs-3"></i>
        </span>
        <span class="crm-widget-heading-icon bg-light-{{ $headingColor }} text-{{ $headingColor }}">
            <i class="bi bi-{{ $headingIcon }}"></i>
        </span>
        <div class="flex-grow-1 min-w-0">
            <h3 class="card-title fw-bold mb-0 text-gray-900">{{ $title }}</h3>
            @if(! empty($subtitle))
                <span class="text-muted fs-7">{{ $subtitle }}</span>
            @endif
        </div>
        @isset($actions)
            {{ $actions }}
        @endisset
    </div>
</div>
