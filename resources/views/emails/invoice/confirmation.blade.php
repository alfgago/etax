@component('mail::message', ['customImg' => @$customImg])
    <p>Estimado Cliente: <b>{{$data_invoice->client_first_name .' '.$data_invoice->client_last_name}}</b><br><br>

        A continuación, se le notifica que la Factura electrónica número <b>{{$data_invoice->document_number}}</b>
        la clave <b>{{$data_invoice->document_key}}</b> fue recibida por el Ministerio de Hacienda y se encuentra en
        condición de aceptado.
        <br><br>

        ¡Muchas Gracias!
    </p>
@endcomponent