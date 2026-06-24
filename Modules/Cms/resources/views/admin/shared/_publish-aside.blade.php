@props([
    'item' => null,
    'showFeatured' => true,
    'showNavigation' => false,
])

@php
    $isPublished = old('_token')
        ? old('publish') !== null
        : ($item?->status === 'Published');
    $isFeatured = old('_token')
        ? old('featured') !== null
        : (bool) ($item?->featured ?? false);
    $inNav = old('_token')
        ? old('add_to_nav') !== null
        : (bool) ($item?->add_to_nav ?? false);
    $inFooter = old('_token')
        ? old('add_to_footer') !== null
        : (bool) ($item?->add_to_footer ?? false);
    $inTopBar = old('_token')
        ? old('add_to_top_bar') !== null
        : (bool) ($item?->add_to_top_bar ?? false);
@endphp

<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title fw-bold fs-5">
            <i class="bi bi-sliders text-success me-2"></i>{{ __('Publishing') }}
        </h3>
    </div>
    <div class="card-body pt-0">
        <div class="cms-aside-option">
            <div>
                <div class="cms-aside-option-label">{{ __('Publish Status') }}</div>
                <div class="cms-aside-option-hint">{{ __('Make this content visible on the website.') }}</div>
            </div>
            <div class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input h-30px w-50px" type="checkbox" name="publish"
                       @checked($isPublished) id="cms-publish-switch"/>
            </div>
        </div>

        @if($showFeatured)
            <div class="cms-aside-option">
                <div>
                    <div class="cms-aside-option-label">{{ __('Featured') }}</div>
                    <div class="cms-aside-option-hint">{{ __('Highlight on homepage and listings.') }}</div>
                </div>
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input h-30px w-50px" type="checkbox" name="featured"
                           @checked($isFeatured) id="cms-featured-switch"/>
                </div>
            </div>
        @endif

        @if($showNavigation)
            <div class="cms-aside-option">
                <div>
                    <div class="cms-aside-option-label">{{ __('Add To Navigation') }}</div>
                    <div class="cms-aside-option-hint">{{ __('Show in the main menu.') }}</div>
                </div>
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input h-30px w-50px" type="checkbox" name="add_to_nav"
                           @checked($inNav)/>
                </div>
            </div>
            <div class="cms-aside-option">
                <div>
                    <div class="cms-aside-option-label">{{ __('Add To Footer') }}</div>
                    <div class="cms-aside-option-hint">{{ __('Show in the site footer.') }}</div>
                </div>
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input h-30px w-50px" type="checkbox" name="add_to_footer"
                           @checked($inFooter)/>
                </div>
            </div>
            <div class="cms-aside-option">
                <div>
                    <div class="cms-aside-option-label">{{ __('Add To Top Bar') }}</div>
                    <div class="cms-aside-option-hint">{{ __('Show in the top announcement bar.') }}</div>
                </div>
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input h-30px w-50px" type="checkbox" name="add_to_top_bar"
                           @checked($inTopBar)/>
                </div>
            </div>
        @endif
    </div>
</div>
