@component('mail::message', ['customImg' => @$customImg])
    <p>
        Estimado Cliente: <b>{{$data_invoice->client_first_name .' '.$data_invoice->client_last_name}}</b>
    </p>
    <p>
        A continuación, se le notifica que la Nota de {{ $type }} <b>{{$data_invoice->document_key}}</b>,
        Correspondiente a la anulación de la factura  <b>{{$data_invoice->reference_document_key}}</b> fue recibida por el Ministerio de Hacienda y se encuentra en
        condición de aceptado.
    </p>
    <p>
        ¡Muchas Gracias!
    </p>
@endcomponent