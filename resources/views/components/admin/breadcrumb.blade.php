@props(['pageTitle', 'breadcrumbItems' => [], 'pageDescription' => null])

<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
        {{ __($pageTitle) }}
    </h1>

    @if($pageDescription)
        <span class="text-muted fs-7 fw-semibold mt-1">{{ __($pageDescription) }}</span>
    @endif

    @if(count($breadcrumbItems) > 0)
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
            @foreach($breadcrumbItems as $item)
                <li class="breadcrumb-item {{ $loop->last ? 'text-dark' : 'text-muted' }}">
                    @if(!$loop->last && isset($item['url']))
                        <a href="{{ $item['url'] }}"
                           class="text-muted text-hover-primary">
                            @if($loop->first)
                                <i class="bi bi-house-door mx-1"></i>
                            @endif
                            {{ __($item['label']) }}
                        </a>
                    @else
                        <span class="{{ $loop->last ? 'text-dark fw-bold' : 'text-muted' }}">
                            @if($loop->first && !isset($item['url']))
                                <i class="bi bi-house-door mx-1"></i>
                            @endif
                            {{ __($item['label']) }}
                        </span>
                    @endif
                </li>
                @if(!$loop->last)
                    <li class="breadcrumb-item">
                        <span class="bullet bg-primary opacity-25 w-5px h-2px"></span>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
