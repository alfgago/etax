@component('mail::message')
    <strong>Notificación</strong>
    <p>Estimad@ {{ $content['name'] }}</p>
    <br>
    @if($content['card'])
        Le informamos que el pago para su subscripción a eTax en su plan {{ $content['product'] }}, asociado a su tarjeta {{ $content['card'] }}, <br>
        no pudo ser procesado.<br><br>
    @else
        Le informamos que el pago para su subscripción a eTax en su plan {{ $content['product'] }}, <br>
        no pudo ser procesado.<br><br>
    @endif

    Si ya ha actualizado la información de su método de pago y ha recibido su factura, ignore este correo.<br><br>

    Saludos de parte de todo el equipo de eTax.
@endcomponent
