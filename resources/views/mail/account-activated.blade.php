@component('mail::message')
# Your account is active

Hi {{ $user->name }},

Your **{{ config('app.name') }}** account has been reviewed and activated. You can now sign in and access the document library.

@component('mail::button', ['url' => route('login')])
Sign In
@endcomponent

**Username:** {{ $user->username }}

If you didn't request this account, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
