@component('mail::message')
# Your document has been published

Hi {{ $user->name }},

Your document **"{{ $document->title }}"** has been reviewed and published to the library. It is now available to all users.

@component('mail::button', ['url' => route('documents.show', $document)])
View Document
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
