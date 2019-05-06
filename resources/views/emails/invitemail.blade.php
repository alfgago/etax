@component('mail::message')
<strong>Hello!</strong>
<p>You have been invited to join company {{$content['team']->name}}</p>

@component('mail::button', ['url' => route($content['path'], $content['invite']->accept_token)])
Join Now
@endcomponent

Regards,<br>
{{ config('app.name').' Team' }}
@endcomponent