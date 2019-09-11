<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: Pangram, sans-serif;
        }
    
        .bill-box {
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

        .bill-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .bill-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .bill-box table tr td:nth-child(2) {
            text-align: left;
        }

        .bill-box table tr.top table td {
            padding-bottom: 20px;
        }

        .bill-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .bill-box table tr.information table td {
            padding-bottom: 20px;
        }

        .bill-box table tr.heading td {
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

        .bill-box table tr.heading {
            height: 25px;
        }

        .bill-box table tr.details td {
            padding-bottom: 20px;
        }

        .bill-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .bill-box table tr.item.last td {
            border-bottom: none;
        }

        .bill-box table tr.total td:nth-child(2) {
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
            .bill-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .bill-box table tr.information table td {
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

        .currency }}otal {
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
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
        <tr>
            <td class="title" style="width: 180px; height: 100px">
                <div style="font-size: 23x; line-heiht: 1; max-width: 100%;">{{$data_bill->provider_first_name }} {{ $data_bill->provider_last_name }}</div>
            </td>
            <td>
                <b>{{$data_bill->provider_first_name }} {{ $data_bill->provider_last_name }}</b><br>
                <b>Cedula:</b> {{$data_bill->provider_id_number}}<br>
                <b>Tel:</b> {{$data_bill->provider_phone}}<br>
                <b>Correo: {{$data_bill->provider_email}}</b> <br>
            </td>
            <td style="padding: 40px 0px 0px 30px; text-align: right;">
                <b>Direccion:</b>
                {{ $data_bill->provider_address }}<br>
            </td>
        </tr>
        </td>
        </tr>
    </table>
    <?php
        switch ($data_bill->document_type){
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
    ?>
    <table width="100%">
        <hr class="barra">
    </table>
    <table width="100%">
        <tr class="details">
            <td style="width: 40%;margin-top: 0.75% !important;">
                <b>Receptor: <b>{{$company->business_name}}</b><br>
                <b>Cédula: </b> {{$company->_id_number}}<br>
                <b>Tel: </b> {{$company->phone}}<br>
                <b>Correo: </b> {{$company->email}}<br>
                <b>Dirección: </b> {{$company->address}}<br>
            </td>

            <td style="width: 42%;margin-top: 0.75% !important;">
                <b>Documento N°: </b>{{$data_bill->document_number}} <br>
                <b>Clave Numérica: </b> {{$data_bill->document_key}}<br>
                <b>Tipo de Documento: </b> {{ $documentType }}<br>
                <b>Fecha de Emisión: </b> {{ $data_bill->generated_date }}<br>
                <b>Condición Venta: </b> {{ $data_bill->getCondicionVenta() }}<br>
                <b>Medio de Pago: </b> {{ $data_bill->getMetodoPago() }}<br>
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
            Tipo de descuento
        </td>
        <td class="tb-header">
            Subtotal
        </td>
        <td class="tb-header">
            Monto de impuesto
        </td>

    </tr>
    <?php
        $totalServiciosGravados = 0;
        $totalServiciosExentos = 0;
        $totalMercaderiasGravadas = 0;
        $totalMercaderiasExentas = 0;
        $totalDescuentos = 0;
        $totalImpuestos = 0;
        $totalIvaDevuelto = 0;
        
        foreach ($data_bill->items as $item){
            $productType = $item->ivaType;
            if( !isset($productType) ){
                $item->fixIvaType();
                $productType = $item->ivaType;
            }
            $isGravado = isset($productType) ? $productType->is_gravado : true;
            $netaLinea = $item->item_count * $item->unit_price;
            
            if($item->measure_unit == 'Sp' || $item->measure_unit == 'Spe' || $item->measure_unit == 'St'
                || $item->measure_unit == 'Al' || $item->measure_unit == 'Alc' || $item->measure_unit == 'Cm'
                || $item->measure_unit == 'I' || $item->measure_unit == 'Os'){
                if($item->iva_amount == 0 && !$isGravado ){
                    $totalServiciosExentos += $netaLinea;
                }else{
                    $totalServiciosGravados += $netaLinea;
                }

            } else {
                if($item->iva_amount == 0 && !$isGravado ){
                    $totalMercaderiasExentas += $netaLinea;
                }else{
                    $totalMercaderiasGravadas += $netaLinea;
                }
            }
            $discount = 0;
            if( $item['discount'] ) {
                if($item['discount_type'] == "01" && $item['discount'] > 0 ) {
                     $discount = $netaLinea * ($item['discount'] / 100);
                } else {
                    $discount= $item['discount'];
                }
            }

            if ($data_bill->payment_type == '02' && $item->product_type == 12) {
                $totalIvaDevuelto += $item->iva_amount;
            }
            
            $totalDescuentos += $discount;
            $totalImpuestos += $item->iva_amount;
        }
        $totalGravado = $totalServiciosGravados + $totalMercaderiasGravadas;
        $totalExento = $totalServiciosExentos + $totalMercaderiasExentas;
        $totalVenta = $totalGravado + $totalExento;
        $totalNeta = $totalVenta - $totalDescuentos;
        $totalComprobante = $totalNeta + $totalImpuestos;
    ?>
    @foreach($data_bill->items as $item)
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
                {{$item->discount ? number_format($item->discount, 0) : '0'}}
            </td>
            <td>
                {{isset($item->discount_type) ? $item->discount_type == '01' ? '%' : 'monto': ''}}
            </td>
            <td>
                {{$item->subtotal ? number_format($item->subtotal, 2) : ''}}
            </td>
            <td>
                {{$item->iva_amount ? number_format($item->iva_amount, 2) : '0'}}
            </td>
        </tr>
    @endforeach
</table>
    <hr class="barra" style="margin-top: 60px">
    <table width="100%" class="total">
        <thead>
            <tr class="item">
                <th class="obs">
                    Observaciones
                </th>
                <th class="box-total">
                    Totales ({{ $data_bill->currency }})
                </th>
            </tr>
        </thead>
        <tbody>
            <td class="espacio-observaciones" style="vertical-align: top; padding-right: 15px; color: #444; width: 50%;">
                {{ $data_bill->description }}
            </td>
            <td style="width: 50%;">
                
                <table class="resumen-totales">
                    <tr>
                        <td style="padding-right: 30px;"><b>Total servicios gravados</b></td>
                        <td><span>{{ number_format($totalServiciosGravados, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total servicios exentos</b></td>
                        <td><span>{{ number_format($totalServiciosExentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total mercancías gravadas</b></td>
                        <td><span>{{ number_format($totalMercaderiasGravadas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total mercancías exentas</b></td>
                        <td><span>{{ number_format($totalMercaderiasExentas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total gravado</b></td>
                        <td><span>{{ number_format($totalGravado, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total exento</b></td>
                        <td><span>{{ number_format($totalExento, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total venta</b></td>
                        <td><span>{{ number_format($totalVenta, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total descuento</b></td>
                        <td><span>{{ number_format($totalDescuentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total venta neta</b></td>
                        <td><span>{{ number_format( ($totalNeta), 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total IVA</b></td>
                        <td><span>{{ number_format($totalImpuestos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total IVA Devuelto</b></td>
                        <td><span>{{ number_format($totalIvaDevuelto, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td><b>Total comprobante</b></td>
                        <td><span>{{ number_format( ($totalComprobante - $totalIvaDevuelto) , 2)}}</span></td>
                    </tr>
                </table>
                
            </thead>
        </tbody>
    </table>
    <table width="100%" class="footer">
        <tr class="item">
            <td class="currency total">
                Emitida conforme lo establecido en la resolución de Facturación Electrónica, N°DGT-R-48-2016 siete de
                octubre de dos mildieciséis de la Dirección General de Tributación v.4.3 <br>
                eTax - La herramienta para resolver el IVA. | Ofiplaza del Este. 200m al oeste de la Rotonda de la Bandera,
                San Pedro de Montes de Oca, San José 6897-1000 Costa Rica +506 22802130
            </td>
        </tr>
    </table>

</body>
</html>
