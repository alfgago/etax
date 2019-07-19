@component('mail::message')
<strong>¡Hola {{@$data["first_name"] }} {{@$data["last_name"] }} {{@$data["last_name2"] }}!</strong>
<p>Hemos recibido una solicitud de retiro de sus fondos de su billetera de eTax.<br>
Esta solicitud se le envió a uno de nuestros colaboradores que estará realizando el transferencia lo más pronto posible.<br>
Saludos de parte de todo el equipo de eTax.
@endcomponent