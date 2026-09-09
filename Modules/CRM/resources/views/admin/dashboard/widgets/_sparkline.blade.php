@php
    $values = array_values($series ?? []);
    $count = count($values);
    $width = 88;
    $height = 30;
    $path = '';

    if ($count > 0) {
        $max = max($values);
        $min = min($values);
        $span = max($max - $min, 1);
        $points = [];

        foreach ($values as $index => $value) {
            $x = $count > 1 ? ($index / ($count - 1)) * $width : $width / 2;
            $y = $height - ((($value - $min) / $span) * ($height - 6)) - 3;
            $points[] = round($x, 1).' '.round($y, 1);
        }

        $path = 'M'.implode(' L', $points);
    }
@endphp

@if($path)
    <svg class="crm-sparkline" viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="none" aria-hidden="true">
        <path d="{{ $path }}" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endif
