@props(['icon', 'title', 'description' => null])

<div class="settings-section mb-8">
    <div class="settings-section-header d-flex align-items-start gap-3 mb-6">
        <div class="settings-section-icon">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div>
            <h4 class="settings-section-title mb-1">{{ __($title) }}</h4>
            @if($description)
                <p class="settings-section-desc mb-0">{{ __($description) }}</p>
            @endif
        </div>
    </div>
    <div class="settings-section-body">
        {{ $slot }}
    </div>
</div>
