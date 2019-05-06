@component('mail::message')
<strong>Hello!</strong>
<p>Your subscription plan <b>{{ucfirst($content['old_plan_details']->plan_type)}} - {{$content['old_plan_details']->plan_name}} ( {{$content['old_plan_details']->unique_no}} )</b> has been upgraded to <b>{{ucfirst($content['new_plan_details']->plan_type)}} - {{$content['new_plan_details']->plan_name}} ( {{$content['old_plan_details']->unique_no}} ) </b></p>

Regards,<br>
{{ config('app.name').' Team' }}
@endcomponent