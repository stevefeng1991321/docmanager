@component('mail::message')
# Your password has been reset

Hi {{ $user->name }},

An administrator has reset your **{{ config('app.name') }}** password. Please sign in and change it to something only you know.

@component('mail::button', ['url' => route('login')])
Sign In
@endcomponent

If you did not expect this change, please contact your administrator immediately.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
