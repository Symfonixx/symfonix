@props(['count' => 0, 'color' => 'primary'])

@php
    $count = (int) $count;
@endphp

@if($count > 0)
    <span {{ $attributes->class(['badge', 'badge-circle', 'badge-'.$color, 'nav-count-badge', 'ms-auto']) }}>
        {{ $count > 99 ? '99+' : $count }}
    </span>
@endif
