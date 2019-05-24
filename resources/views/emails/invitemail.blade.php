@component('mail::message')
<strong>¡Hola!</strong>
<p>Usted ha sido invitado a unirse al equipo de empresa: {{ $content['name'] }}</p>

@component('mail::button', ['url' => route($content['path'], $content['invite']->accept_token)])
 Únase ya
@endcomponent

Si cree que esta invitación le ha llegado por error, ignore este correo.<br>

Saludos de parte de todo el equipo de eTax.
@endcomponent