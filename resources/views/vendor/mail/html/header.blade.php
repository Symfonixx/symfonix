@props(['url'])
@php
    $siteLogo = \Modules\Base\Models\Settings::get('site_logo');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if ($siteLogo)
<img src="{{ asset('storage/'.$siteLogo) }}" class="logo" alt="{{ config('app.name') }}" style="height: 42px; max-height: 42px; width: auto;">
@elseif (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<span style="color: #2189ca; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">{!! $slot !!}</span>
@endif
</a>
</td>
</tr>
