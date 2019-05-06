@component('mail::message')
<strong>Hello!</strong>
<p>As per your request,Please click below to cancel your subscription plan {{$content['plan_name']}}.</p>

@component('mail::button', ['url' => route('Plan.confirm-cancel-plan', $content['token'])])
Cancel Now
@endcomponent

<p>This cancel subscription plan link will expire in 24 Hours.</p>
<p>If you did not request a cancellation, no further action is required.</p>

Regards,<br>
{{ config('app.name').' Team' }}
@endcomponent