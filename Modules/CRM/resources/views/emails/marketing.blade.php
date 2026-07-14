@component('mail::message')
{!! $subject !!}

{!! $body !!}

@slot('subcopy')
{{ __('You are receiving this message from :name.', ['name' => config('app.name')]) }}
@endslot

@endcomponent
