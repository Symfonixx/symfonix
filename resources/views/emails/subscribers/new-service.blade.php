@component('mail::message')
# New service: {{ $title }}

{{ \Illuminate\Support\Str::limit(strip_tags($service->description ?? ''), 180) }}

@component('mail::button', ['url' => $url])
View service
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
