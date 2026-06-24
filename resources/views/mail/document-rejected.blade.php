@component('mail::message')
# Document not approved

Hi {{ $user->name }},

Your document **"{{ $document->title }}"** was reviewed but could not be approved for publication at this time.

@if($reason)
**Reason:** {{ $reason }}
@endif

You can edit and resubmit the document from your account, or contact an administrator for more information.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
