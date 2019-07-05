@component('mail::message')
<strong>¡Hola {{@$content->first_name}} {{@$content->last_name}} {{@$content->last_name2}}!</strong>
<p>Se ha cancelado el plan de su cuenta en eTax.</p>


Si desea reactivar su cuenta por favor comuniquese con nosotros dentro de los próximos 15 días.<br>

Saludos de parte de todo el equipo de eTax.
@endcomponent