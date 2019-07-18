@component('mail::message')
<strong>Hola,</strong>
<p>Usted ha cambiado de plan <b>{{ucfirst($content['old_plan_details']->plan_type)}} - {{$content['old_plan_details']->plan_name}} ( {{$content['old_plan_details']->unique_no}} )</b> has been upgraded to <b>{{ucfirst($content['new_plan_details']->plan_type)}} - {{$content['new_plan_details']->plan_name}} ( {{$content['old_plan_details']->unique_no}} ) </b></p>

Saludos,<br>
{{ config('app.name').' Team' }}
@endcomponent