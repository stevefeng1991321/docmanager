@component('mail::message')
# Registration not approved

Hi {{ $user->name }},

Thank you for your interest in **{{ config('app.name') }}**. Unfortunately, your registration request was not approved.

@if($reason)
**Reason:** {{ $reason }}
@endif

If you believe this is a mistake, please contact your administrator.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
