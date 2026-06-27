@component('mail::message')
# {{ __('New blog published') }}

**{{ $title }}**

{{ \Illuminate\Support\Str::limit(strip_tags($blog->description ?? ''), 180) }}

@component('mail::button', ['url' => $url, 'color' => 'success'])
{{ __('Read blog') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
