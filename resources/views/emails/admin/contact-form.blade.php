@component('mail::message')
# {{ __('New contact form submission') }}

@component('mail::panel')
**{{ __('Name') }}:** {{ $contact->name }}

**{{ __('Email') }}:** {{ $contact->email }}

**{{ __('Mobile') }}:** {{ $contact->mobile }}

**{{ __('Subject') }}:** {{ $contact->subject }}

**{{ __('Message') }}:**

{{ $contact->message }}
@endcomponent

@component('mail::button', ['url' => $adminUrl])
{{ __('View in admin') }}
@endcomponent

@endcomponent
