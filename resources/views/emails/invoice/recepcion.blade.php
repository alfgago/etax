@component('mail::message')
    <p>Estimado Cliente: <b>{{$data_invoice->provider_first_name .' '.$data_invoice->last_last_name}}</b><br><br>

        Se ha recibido el documento eletrónico emitido por  <b>{{$data_invoice->provider_first_name}}</b>,
        y se presentó ante la Dirección General de Tributación (D.G.T.) por <b>{{$company->business_name}}</b>
        <br>
        Se aceptó esta solicitud, de acuerdo a la respuesta de la D.G.T.

        Documento (Clave): {{$data_invoice->document_key}}
        <br><br>
        <b>Respuesta de Hacienda: </b> {{$response}}
        <br><br>
        ¡Muchas Gracias!
    </p>
@endcomponent