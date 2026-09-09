@forelse(collect($tags ?? []) as $tag)
    <span class="badge badge-{{ !empty($solid) ? $tag->color : 'light-'.$tag->color }} {{ $class ?? 'me-1 mb-1' }}">
        <i class="bi bi-tag-fill me-1"></i>{{ $tag->display_name }}
    </span>
@empty
    @isset($empty)
        <span class="text-muted fs-7">{{ $empty }}</span>
    @endisset
@endforelse
