@component('mail::message')
<strong>Hello!</strong>
<p>As per your request,Please click below to deactivate your company {{$content['team']}}.</p>

@component('mail::button', ['url' => route('company-deactivate', $content['token'])])
Deactivate Now
@endcomponent

<p>This company deactivation link will expire in 24 Hours.</p>
<p>If you did not request a deactivation, no further action is required.</p>

Regards,<br>
{{ config('app.name').' Team' }}
@endcomponent