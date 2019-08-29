<!doctype html>
<html>
<head>
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
                $documentType = 'Factura de exportación';
            break;
            case '09':
                $documentType = 'Factura de compra';
            break;
        }
        $totalServiciosGravados = 0;
        $totalServiciosExentos = 0;
        $totalMercaderiasGravadas = 0;
        $totalMercaderiasExentas = 0;
        $totalDescuentos = 0;
        $totalImpuestos = 0;
        
        foreach ($data_invoice->items as $item){
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
            
            $totalDescuentos += $discount;
            $totalImpuestos += $item->iva_amount;
        }
        $totalGravado = $totalServiciosGravados + $totalMercaderiasGravadas;
        $totalExento = $totalServiciosExentos + $totalMercaderiasExentas;
        $totalVenta = $totalGravado + $totalExento;
        $totalNeta = $totalVenta - $totalDescuentos;
        $totalComprobante = $totalNeta + $totalImpuestos;
    ?>
    <meta charset="utf-8">
    <style>
        body {
            color: #000;
            font-family: Pangram, sans-serif;
            font-size: 16px;
            position: relative;
        }
        .content {
            position: relative;
            padding-bottom: 4rem;
        }
        .trifami-blue {
            color: #39669C;
        }
        .trifami-gray {
            color: #8C8784;
        }
        h1 {
            font-weight: 300;
        }
        b, th {
            font-weight: 900;
        }
        .info-table th {
            padding-right: 15px;
            vertical-align: top;
            font-size: 13px;
        }
        .info-table td {
            text-transform: uppercase;
            font-size: 13px;
        }
        .items-table {
            margin: 50px 0;
            border-collapse: collapse;
            border: 1px solid black;
        }
        .items-table th {
            font-size: 17px;
        }
        .items-table th, .items-table td {
            padding: 5px;
            border: 1px solid black;
        }
        .items-table .heading th, .items-table .heading th {
            border: 0;
        }
        .resumen-totales {
            float: right;
            width: 100%;
            text-align: right;
        }
        .resumen-totales th.trifami-blue{
            padding-right: 15px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="content">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td class="logo" style="width: 200px; height: 1px; vertical-align: top;">
                @if( $company->logo_url )
                    <img src="{{\Illuminate\Support\Facades\Storage::temporaryUrl($company->logo_url,  now()->addMinutes(1))}}" style="width:100%; max-width:200px; max-height: 200px">
                @endif
            </td>
            <td style="padding-left: 80px;">
                <h2 class="trifami-blue" style="font-weight: 300;">
                    {{ $documentType }}
                </h2>
                <table class="info-table">
                    <tr>
                        <th class="trifami-gray">No. Consecutivo</td>
                        <td>{{$data_invoice->document_number}}</td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Cliente</td>
                        <td>{{ $data_invoice->client_first_name.' '.$data_invoice->client_last_name }}</td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">No. Contrato</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Proceso Contratación</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Orden Pedido</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Cuenta Cliente</td>
                        <td>1510001010334652</td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Fecha</td>
                        <td>{{ $data_invoice->generatedDate()->format('dd/mm/YY H:i:s O') }}</td>
                    </tr>
                    <tr>
                        <th class="trifami-gray">Dirección</td>
                        <td>{{$company->address}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h1 class="trifami-blue">TRIFAMI S.A</h1>
                <div class="trifami-gray">
                    Cédula Jurídica: {{$company->id_number}} <br>
                    Tel: {{$company->phone}}
                </div>
            </td>
        </tr>
    </table>
    <table width="100%" class="items-table" border="1">
        <thead>
            <tr class="heading trifami-blue">
                <th class="tb-header">
                    Cantidad
                </th>
                <th class="tb-header">
                    Descripción
                </th>
                <th class="tb-header" style="text-align: right;">
                    Subtotal
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($data_invoice->items as $item)
            <tr class="item">
                <td>
                    {{  number_format($item->item_count, 2) ?? '0.00' }}
                </td>
                <td>
                    {{$item->name ?? ''}}
                </td>
                <td style="text-align: right;">
                    {{$item->subtotal ? number_format($item->subtotal, 2) : '0'}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table width="100%" class="total" style="width: 100%;">
        <td style="width: 50%;">
            <b>Observaciones</b>
            {{ $data_invoice->description }}
        </td>
        <td style="width: 50%;" style="text-align: right;">
            <div style="float: right;">
                <div>
                    <div style="display: inline-block; width: 90px; font-weight: bold;" class="trifami-blue">Subtotal</div>
                    <div style="display: inline-block; font-weight: bold;">{{ number_format( $data_invoice->subtotal, 2 ) }}</div>
                </div>
                <div>
                    <div style="display: inline-block; width: 90px; font-weight: bold;" class="trifami-blue">Total</div>
                    <div style="display: inline-block; font-weight: bold;">{{ number_format( $totalComprobante , 2 ) }}</div>
                </div>
            </div>
        </td>
    </table>
    <div class="footer">
        Autorizada mediante resolución N° DGT-R-48-2016 del 7 de octubre de 2016. <br>
        <div style="font-size: 11px;">
        Clave: {{ $data_invoice->document_key }} <br>
        Versión del Documento Electrónico: 4.3
        </div>
    </div>
    </div>
</body>
</html>
