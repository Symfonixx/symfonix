@props(['perform'])

@can($perform)
    {{ $slot }}
@endcan
