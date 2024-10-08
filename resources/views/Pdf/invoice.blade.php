<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: Pangram, sans-serif;
        }
    
        .invoice-box {
            /*max-width: 700px;*/
            /*margin: auto;*/
            /*padding: 30px;*/
            width: 700px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: Pangram, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.heading td {
            background: #444;
            border-bottom: 1px solid #ddd;
            /* font-weight: bold; */
            height: auto;
            font-size: 12px;
            line-height: 1;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        .invoice-box table tr.heading {
            height: 25px;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .heading {
            height: 25px;
        }

        .resumen-totales td {
            border-bottom: 1px solid #ccc;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .barra {
            background: black;
            color: black;
            height: 5px;
            margin-bottom: 15px;
        }

        .total {
            margin-top: 30px;
            height: 40px;
        }

        .currency .total {
            background: #3333;
            width: 50%;
            text-align: center;
        }
        .box-total {
            background: black;
            color: white;
            font-size: 14px;
            text-align: center;
        }
        .tb-header {
            background: #444;
            color: white;
        }

        .heading td {
            background: #444;
            border-bottom: 1px solid #ddd;
            /* font-weight: bold; */
            height: auto;
            font-size: 10px;
            line-height: 1;
            color: white;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }

        .footer {
            margin-top: 100px;
            height: 100px;
        }
        td {
            font-family: Pangram, sans-serif;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <?php
        switch ($data_invoice->document_type){
            case '01':
                $documentType = 'Factura electrónica';
            break;
            case '02':
                $documentType = 'Nota de débito electrónica';
            break;
            case '03':
                $documentType = 'Nota de crédito electrónica';
            break;
            case '04':
                $documentType = 'Tiquete electrónico';
                break;
            case '08':
                $documentType = 'Factura de compra';
            break;
            case '09':
                $documentType = 'Factura de exportación';
            break;
        }
        $numExoneracion = null;
        foreach($data_invoice->items as $item){
            if( isset($item->exoneration_document_number) ){ 
                $numExoneracion = $item->exoneration_document_number;
            }
        }
    ?>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
        <tr>
            <td class="title" style="width: 180px; height: 100px">
                @if( $company->logo_url )
                    <img src="{{\Illuminate\Support\Facades\Storage::temporaryUrl($company->logo_url,  now()->addMinutes(1))}}" style="width:100%; max-width:150px; max-height: 150px">
                @endif
            </td>
            @if($data_invoice->document_type == '08' && $provider !== null)
            <td>
                <b>{{$provider->first_name }} {{ $provider->last_name }}}</b><br>
                <b>Cedula:</b> {{$provider->id_number}}<br>
                <b>Tel:</b> {{$provider->phone}}<br>
                <b>Correo: {{$provider->email}}</b> <br>
            </td>
            <td style="padding: 40px 0px 0px 30px; text-align: right;">
                <b>Direccion:</b>
                {{$provider->address. " ".$provider->zip}}<br>
            </td>
            @else
            <td>
                <b>{{$company->business_name}}</b><br>
                <b>Cedula:</b> {{$company->id_number}}<br>
                <b>Tel:</b> {{$company->phone}}<br>
                <b>Correo: {{$company->email}}</b> <br>
            </td>
            <td style="padding: 40px 0px 0px 30px; text-align: right;">
                <b>Direccion:</b>
                {{$company->address. " ".$company->zip}}<br>
            </td>
            @endif
        </tr>
        </td>
        </tr>
    </table>
    <table>
        <h1 style="text-align: center; font-size: 23px; margin: 0 !important; line-height: 1;">{{ $documentType }}</h1>
    </table>
    <table width="100%">
        <hr class="barra">
    </table>
    <table width="100%">
        <tr class="details">
            <td style="width: 40%;margin-top: 0.75% !important;">
                <b>Receptor: </b> {{$data_invoice->client_first_name.' '.$data_invoice->client_last_name}} <br>
                <b>Cédula: </b> {{$data_invoice->client_id_number}}<br>
                <b>Tel: </b> {{$data_invoice->client_phone}}<br>
                <b>Correo: </b> {{$data_invoice->client_email}}<br>
                <b>Código Interno :</b> {{$data_invoice->id}}<br>
                <b>Dirección: </b> {{$data_invoice->client_address}}<br>
            </td>

            <td style="width: 42%;margin-top: 0.75% !important;">
                <b>Documento N°: </b>{{$data_invoice->document_number}} <br>
                <b>Clave Numérica: </b> {{$data_invoice->document_key}}<br>
                <b>Tipo de Documento: </b> {{ $documentType }}<br>
                <b>Fecha de Emisión: </b> {{ $data_invoice->generatedDate()->format('d/m/Y') }}<br>
                <b>Condición Venta: </b> {{ $data_invoice->getCondicionVenta() }}<br>
                <b>Medio de Pago: </b> {{ $data_invoice->getMetodoPago() }}<br>
                <b>Fecha de Vencimiento: </b> {{ $data_invoice->dueDate()->format('d/m/Y') }}<br>
                @if( isset($data_invoice->buy_order) )
                    <b>Orden de compra: </b> {{ $data_invoice->buy_order }}<br>
                @endif
                @if( !empty($data_invoice->other_reference) )
                    <b>Consecutivo referencia: </b> {{ $data_invoice->other_reference }}<br>
                @endif
                @if( isset($numExoneracion) )
                    <b>Exoneración: </b> {{ $numExoneracion }}<br>
                @endif
                <b>Tipo de cambio: </b> <span>{{ $data_invoice->currency_rate }}</span>
            </td>
        </tr>
    </table>
    <table width="100%" style="margin-top: 10px">
        <tr class="heading">
            <td class="tb-header">
                Código
            </td>
    
            <td class="tb-header">
                Cantidad
            </td>
            <td class="tb-header">
                Unidad medida
            </td>
            <td class="tb-header">
                Descripción producto/servicio
            </td>
            <td class="tb-header">
                Precio unitario
            </td>
            <td class="tb-header">
                Descuento
            </td>
            <td class="tb-header">
                Subtotal
            </td>
            <td class="tb-header">
                % IVA
            </td>
            <td class="tb-header">
                Monto de impuesto
            </td>
    
        </tr>
        <?php
            $invoiceUtils = new \App\Utils\InvoiceUtils();
            $requestDetails = $invoiceUtils->setDetails43($data_invoice->items);
            $requestData = $invoiceUtils->setInvoiceData43($data_invoice, $requestDetails, false, false);
     
            $totalServiciosGravados = $requestData['servgravados'] ?? 0;
            $totalServiciosExentos = $requestData['servexentos'] ?? 0;
            $totalServiciosExonerados = $requestData['servexonerados'] ?? 0;
            $totalMercaderiasGravadas = $requestData['mercgravados'] ?? 0;
            $totalMercaderiasExentas = $requestData['mercexentos'] ?? 0;
            $totalMercaderiasExonerados = $requestData['mercexonerados'] ?? 0;
            $totalDescuentos = $requestData['totdescuentos'] ?? 0;
            $totalImpuestos = $requestData['totimpuestos'] ?? 0;
            $totalIvaDevuelto = $requestData['totalivadevuelto'] ?? 0;
            $totalOtrosCargos = $requestData['totalotroscargos'] ?? 0;
            $totalGravado = $requestData['totgravado'] ?? 0;
            $totalExento = $requestData['totexento'] ?? 0;
            $totalExonerados = $requestData['totexonerados'] ?? 0;
            $totalVenta = $requestData['totventa'] ?? 0;
            $totalNeta = $requestData['totventaneta'] ?? 0;
            $totalComprobante = $requestData['totcomprobante'] ?? 0;
        ?>
        @foreach($data_invoice->items as $item)
            <tr class="item">
                <td>
                    {{$item->code ?? ''}}
                </td>
    
                <td>
                    {{$item->item_count ?? ''}}
                </td>
                <td>
                    {{$item->measure_unit ?? ''}}
                </td>
                <td>
                    {{$item->name ?? ''}}
                </td>
                <td>
                    {{$item->unit_price ? number_format($item->unit_price, 2) : '0'}}
                </td>
                <td>
                    {{$item->discount ? number_format($item->discount, 0) : '0'}}{{isset($item->discount_type) ? $item->discount_type == '01' ? '%' : 'monto': ''}}
                </td>
                <td>
                    {{$item->subtotal ? number_format($item->subtotal, 2) : ''}}
                </td>
                <td>
                    {{$item->iva_percentage}}%
                </td>
                <td>
                    {{$item->iva_amount ? number_format($item->iva_amount, 2) : '0'}}
                </td>
            </tr>
        @endforeach
    </table>
    
    @if($totalOtrosCargos > 0)
        <div style="font-size: 12px; margin-bottom: 0; margin-top: 11px;">Otros Cargos:</div>
        <table width="100%" style="margin-top: 10px">
            <tr class="heading">
                <td class="tb-header">
                    #
                </td>
        
                <td class="tb-header">
                    Tipo
                </td>
                <td class="tb-header">
                    Receptor
                </td>
                <td class="tb-header">
                    Detalle
                </td>
                <td class="tb-header">
                    Monto del cargo
                </td>
        
            </tr>
            @foreach ( $data_invoice->otherCharges as $item )
               <tr class="item item-ot">
                  <td><span class="numero-fila">{{ $loop->index+1 }}</span></td>
                  <td>{{ $item->getTypeString() }}</td>
                  <td>{{ $item->provider_id_number }} {{ $item->provider_name }}</td>
                  <td>{{ $item->description }}</td>
                  <td>{{ number_format($item->amount,2) }} </td>
              </tr>
            @endforeach
        </table>
    @endif
    
    <hr class="barra" style="margin-top: 60px">
    <table width="100%" class="total">
        <thead>
            <tr class="item">
                <th class="obs"  style="text-align: left;">
                    Observaciones
                </th>
                <th class="box-total">
                    Totales ({{ $data_invoice->currency }})
                </th>
            </tr>
        </thead>
        <tbody>
            <td class="espacio-observaciones" style="vertical-align: top; padding-right: 15px; color: #444; width: 50%;">
                @if( isset($data_invoice->description) )
					<p>{!! nl2br(e($data_invoice->description)) !!}</p>
                @endif
                @if( isset($company->payment_notes) )
                    <b style="margin: 1rem 0;color:#000;font-size: 15px;">Información de pago</b>
					<p>{!! nl2br(e($company->payment_notes)) !!}</p>
                @endif
            </td>
            <td style="width: 50%;">
                
                <table style="width: 100%;" class="resumen-totales">
                    <tr>
                        <td style="padding-right: 30px;"><b>Total servicios gravados</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalServiciosGravados, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total servicios exentos</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalServiciosExentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total servicios exonerados</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalServiciosExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total mercancías gravadas</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalMercaderiasGravadas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total mercancías exentas</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalMercaderiasExentas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total mercancías exoneradas</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalMercaderiasExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total gravado</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalGravado, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total exento</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalExento, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total exonerado</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total venta</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalVenta, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total descuento</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalDescuentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total venta neta</b></td>
                        <td style="text-align: right;"><span>{{ number_format( ($totalNeta), 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total IVA</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalImpuestos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total IVA Devuelto</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalIvaDevuelto, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total Otros Cargos</b></td>
                        <td style="text-align: right;"><span>{{ number_format($totalOtrosCargos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total comprobante</b></td>
                        <td style="text-align: right;"><span>{{ number_format( ($totalComprobante) , 2)}}</span></td>
                    </tr>
                </table>
                
            </thead>
        </tbody>
    </table>
    <table width="100%" class="footer">
        <tr class="item">
            <td class="currency total">
                Emitida conforme lo establecido en la resolución de Facturación Electrónica, N° DGT-R033-2019 del 20 de junio del 2019 de la Dirección General de Tributación v.4.3 <br>
                eTax - La herramienta para resolver el IVA. | Ofiplaza del Este. 200m al oeste de la Rotonda de la Bandera,
                San Pedro de Montes de Oca, San José 6897-1000 Costa Rica +506 22802130
            </td>
        </tr>
    </table>

</body>
</html>
