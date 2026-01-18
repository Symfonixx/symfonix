@component('mail::message')
# New contact form

**Name:** {{ $contact->name }}  
**Email:** {{ $contact->email }}  
**Mobile:** {{ $contact->mobile }}  
**Subject:** {{ $contact->subject }}

**Message:**  
{{ $contact->message }}

@component('mail::button', ['url' => $adminUrl])
View in admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
