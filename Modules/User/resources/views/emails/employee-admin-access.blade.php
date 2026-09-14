@component('mail::message')
# {{ __('user::emails.admin.heading') }}

{{ __('user::emails.admin.greeting', ['name' => $employee->name]) }}

{{ __('user::emails.admin.intro', ['company' => $company]) }}

**{{ __('user::emails.admin.email_label') }}:** {{ $email }}  
**{{ __('user::emails.admin.password_label') }}:** {{ $password }}

@component('mail::button', ['url' => $loginUrl])
{{ __('user::emails.admin.login') }}
@endcomponent

{{ __('user::emails.admin.security') }}

{{ __('user::emails.admin.thanks') }},<br>
{{ $company }}
@endcomponent
