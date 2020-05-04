@component('mail::message', ['customImg' => @$customImg])
    <p>
        Estimado Cliente: <b>{{$data_invoice->client_first_name .' '.$data_invoice->client_last_name}}</b>
    </p>
    <p>
        Adjunto a este correo encontrará un Comprobante Electrónico en formato XML y su correspondiente representación en
        formato PDF, por concepto de facturación de <b>{{$company->business_name}}</b>. Lo anterior con base en las
        especificaciones del Ministerio de Hacienda. 
    </p>
    <p>
        Si no viene adjunto el XML de respuesta, recibirá una notificación cuando la factura sea procesada por hacienda.
    </p>
    <p>
        ¡Muchas Gracias!
    </p>
@endcomponent