@component('mail::layout')
<p>Estimado Cliente: <b>{{$data_invoice->client_first_name .' '.$data_invoice->client_last_name}}</b><br>
    Adjunto a este correo encontrará un Comprobante Electrónico en formato XML y su correspondiente representación en
    formato PDF, por concepto de facturación de <b>{{$company->business_name}}</b>. Lo anterior con base en las
    especificaciones del Ministerio de Hacienda. <br>
    ¡Muchas Gracias!
</p>
@endcomponent