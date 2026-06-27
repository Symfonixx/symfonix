@component('mail::message')
# {{ $subject }}

{!! nl2br(e($body)) !!}

@slot('subcopy')
{{ __('You are receiving this message from :name.', ['name' => config('app.name')]) }}
@endslot

@endcomponent
