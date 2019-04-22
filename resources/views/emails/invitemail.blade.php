@component('mail::message')
Hello,
<p>You have been invited to join company {{$content['team']->name}}</p>

@component('mail::button', ['url' => route($content['path'], $content['invite']->accept_token)])
Join Now
@endcomponent

Thanks,<br>
{{ config('app.name').' Team' }}
@endcomponent