@component('mail::message')
<strong>¡Hola!</strong>
<p>Hemos recibido una solicitud de retiro de fondos de la billetera en eTax.<br>
Datos de la solicitud.<br>
Nombre: {{@$data["first_name"] }} {{@$data["last_name"] }} {{@$data["last_name2"] }}<br>
Correo: {{@$data["email"] }}<br>
Monto: {{@$data["amount"] }}<br>
Cuenta: {{@$data["account"] }}<br>
Cedula: {{@$data["dni_number"] }}<br>
Saludos de parte de todo el equipo de eTax.
@endcomponent