@component('mail::message')
# {{ __('New service available') }}

**{{ $title }}**

{{ \Illuminate\Support\Str::limit(strip_tags($service->description ?? ''), 180) }}

@component('mail::button', ['url' => $url, 'color' => 'success'])
{{ __('View service') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
