@component('mail::message')
# {{ __('user::emails.hired.heading') }}

{{ __('user::emails.hired.greeting', ['name' => $employee->name]) }}

{{ __('user::emails.hired.intro', ['company' => $company]) }}

@if(filled($position))
**{{ __('user::emails.hired.position', ['position' => $position]) }}**
@endif

{{ __('user::emails.hired.closing') }}

{{ __('user::emails.hired.thanks') }},<br>
{{ $company }}
@endcomponent
