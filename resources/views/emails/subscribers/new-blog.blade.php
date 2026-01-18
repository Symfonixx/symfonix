@component('mail::message')
# New blog: {{ $title }}

{{ \Illuminate\Support\Str::limit(strip_tags($blog->description ?? ''), 180) }}

@component('mail::button', ['url' => $url])
Read blog
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
