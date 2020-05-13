<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: Pangram, sans-serif;
        }
        
        table {
            width: 100%;
            text-align: left;
            font-size: 9px;
            line-height: 1.2;
        }

        table td,
        table tr {
            width: 100%;
            padding: 1px;
            vertical-align: top;
        }
        
        .details-table td{
            min-width: 50%;
        }
        
        .details-table td b{
            width: 170px;
            display: inline-block;
            vertical-align: top;
            padding-top: 1px;
        }
        
        .details-table td span{
            display: inline-block;
            width: 175px;
            vertical-align: top;
            padding-top: 1px;
        }
        
        .details3 td b{
            display: inline-block;
            width: 200px;
        }
        
        .table-lines-cont {
            min-height: 300px;
        }
        
        .table-lines .heading td {
            border-bottom: 2px solid black;
            padding: 3px;
            font-size: 8.5px;
        }
        
        .totalizado td {
            border-top: 2px solid black;
            padding: 3px;
        }
        
        .table-lines .item td {
            padding: 3px;
            width: 60px;
            max-width: none;
            font-size: 8.5px;
        }
        
        .resumen-totales tr td,
        .resumen-totales span {
            text-align: right;
            font-size: 8.5px;
        }
        
        .resumen-totales tr td.texto-resumen {
            text-align: left;
            white-space: nowrap;
            font-size: 8.5px;
        }
        
        .table-lines .item td.maslarga {
            width: 225px;
            text-align: left !important;
        }
        
        .tb-header {
            text-align: center;
        }
        
    </style>
</head>
<?php

    switch ($data_invoice->document_type){
        case '01':
            $documentType = 'Factura electrónica';
        break;
        case '02':
            $documentType = 'Nota de débito';
        break;
        case '03':
            $documentType = 'Nota de crédito';
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
    $totalGravado = $requestData['totgravado'] ?? 0;
    $totalExento = $requestData['totexento'] ?? 0;
    $totalExonerados = $requestData['totexonerados'] ?? 0;
    $totalVenta = $requestData['totventa'] ?? 0;
    $totalNeta = $requestData['totventaneta'] ?? 0;
    $totalComprobante = $requestData['totcomprobante'] ?? 0;
    
    $otherData = \App\OtherInvoiceData::where("invoice_id", $data_invoice->id)->get();
    
    $currencySymbol = $data_invoice->currency == 'USD' ? '$' : '<span style="font-family: arial;">&cent;</span>';

    $fechaExoneracion = null;
    $porcExoneracion = 100;
    $numExoneracion = null;
    foreach($data_invoice->items as $item){
        if( isset($item->exoneration_document_number) ){ 
            $numExoneracion = $item->exoneration_document_number;
            $fechaExoneracion = \Carbon\Carbon::parse($item->exoneration_date)->format('d-m-Y');
            $porcExoneracion = $item->exoneration_porcent;
        }
    }
    $tipoPersonaInterno = "Nacional";
    $tipoPersonaStr = 'Física';
    if( $data_invoice->client_id_type == 1 || $data_invoice->client_id_type == 'F' ) {
      $tipoPersonaStr = 'Física';
    }else if( $data_invoice->client_id_type == 2 || $data_invoice->client_id_type == 'J' ) {
      $tipoPersonaStr = 'Jurídica';
    }else if( $data_invoice->client_id_type == 3 || $data_invoice->client_id_type == 'D' ) {
      $tipoPersonaStr = 'DIMEX';
    }else if( $data_invoice->client_id_type == 4 || $data_invoice->client_id_type == 'E' ) {
      $tipoPersonaStr = 'Extranjero';
    }else if( $data_invoice->client_id_type == 5 || $data_invoice->client_id_type == 'N' ) {
      $tipoPersonaStr = 'NITE';
    }else if( $data_invoice->client_id_type == 6 || $data_invoice->client_id_type == 'O') {
      $tipoPersonaStr = 'Otro';
    }
    
    $procedencia = \App\OtherInvoiceData::findData($otherData, 'PROCEDENCIA');
    if($procedencia == "E"){
        $tipoPersonaInterno = "Extranjero";
    }
    
    if( $data_invoice->document_type == '09' ){
        $codigoExportador = \App\OtherInvoiceData::findData($otherData, 'COD_EXP');
        $fechaEmbarque = \App\OtherInvoiceData::findData($otherData, 'FECHA_EMB');
        $numeroEmbarque = \App\OtherInvoiceData::findData($otherData, 'COD_EMB');
        $nombreEmbarque = \App\OtherInvoiceData::findData($otherData, 'NOM_VAP');
        $codVap = \App\OtherInvoiceData::findData($otherData, 'COD_VAP');
        $procedenciaEmbarque = \App\OtherInvoiceData::findData($otherData, 'PRO_FRU');
        $puertoSalida = \App\OtherInvoiceData::findData($otherData, 'PUE_SAL');
        $puertoDestino = \App\OtherInvoiceData::findData($otherData, 'PUE_DES');
        $paisDestino = \App\OtherInvoiceData::findData($otherData, 'PAIS_DES');
        $declaracionAduanera = \App\OtherInvoiceData::findData($otherData, 'NO_DUA');
    }
    
?>
<body>
    <table class="main-table" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="2" >
                <?php
                    $logoSrc = "https://staging.etaxcr.com/assets/images/corbana-logo-factura.png";
                    if( '3101011989' == $company->id_number){
                        $logoSrc = "https://staging.etaxcr.com/assets/images/fincasanpablo.jpg";
                    }
                ?>
                <img style="width: auto; height: 80px margin-bottom: 3px;" src="{{ $logoSrc }}" >
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 3px;" colspan="2">
                <table class="details-table details1">
                    <tr>
                        <td style="min-width: 100%; " colspan="2" >
                            @if($documentType == '08' && $provider !== null)
                                <b style="width: 100%; font-size: 14px;">{{$provider->first_name }} {{ $provider->last_name }}}</b>
                            @else
                                <b style="width: 100%; font-size: 14px;">{{$company->business_name}}</b> 
                            @endif
                        </td>
                    </tr>
                    <tr>
                        @if($documentType == '08' && $provider !== null)
                        <td>
                            <b>Cédula:</b> {{$provider->id_number}}<br>
                            <b>Tel:</b> {{$provider->phone}}<br>
                            <b>Correo:</b> {{$provider->email}} <br>
                        </td>
                        <td style="padding: 40px 0px 0px 30px; text-align: right;">
                            <b>Dirección:</b>
                            {{$provider->address. " ".$provider->zip}}<br>
                        </td>
                        @else
                        <td>
                            <b>Cédula:</b> <span>{{$company->id_number}}</span><br>
                            <b>Correo electrónico:</b> <span>{{$company->email}}</span><br>
                            <b>Tel:</b> <span>{{$company->phone}}</span><br>
                            <b>Apdo:</b> <span>6504-1000 San José, Costa Rica</span><br>
                            <b>Código postal:</b> <span>{{ $company->zip }}</span>
                        </td>
                        <td>
                            <b>Provincia(Province):</b> <span>{{ \App\Variables::getProvinciaFromID( $company->state ) }}</span><br>
                            <b>Cantón(City):</b> <span>{{ \App\Variables::getCantonFromID( $company->city ) }}</span><br>
                            <b>Distrito(District):</b> <span>{{ \App\Variables::getDistritoFromID( $company->district ) }}</span><br>
                            <b>Dirección:</b> <span>{{ $company->address }}</span>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 3px;" colspan="2">
                <table class="details-table details2">
                    <tr>
                        <td>
                            @if($data_invoice->buy_order)
                            <b>ORDEN DE COMPRA / BUY ORDER:</b> <span style='text-wrap: wrap;'>{{ $data_invoice->buy_order }}</span><br>
                            @endif
                            <b>VENDIDO A / (SOLD TO):</b> <span style='text-wrap: wrap;'>{{$data_invoice->client_first_name.' '.$data_invoice->client_last_name}}</span><br>
                            <b>TIPO ID. / (ID. TYPE):</b> <span>{{ $tipoPersonaStr }}</span><br>
                            <b>IDENTIFICACIÓN / (ID.):</b> <span>{{$data_invoice->client_id_number}}</span><br>
                            <b>CORREO ELECTRÓNICO / (EMAIL):</b> <span>{{$data_invoice->client_email}}</span><br>
                            <b>TELÉFONO / (PHONE NUMBER):</b> <span>{{$data_invoice->client_phone}}</span><br>
                            <?php $referenciaFactura = \App\OtherInvoiceData::findData($otherData, 'REFERENCIA');
                                if( !empty($referenciaFactura) ){  ?>
                                    <b>REFERENCIA / (REFERENCE):</b> <span>{{ $referenciaFactura }}</span><br>
                            <?php } ?>
                            <?php $consignatario = \App\OtherInvoiceData::findData($otherData, 'CONSIG');
                                if( !empty($consignatario) ){  ?>
                                <b>CONSIGNATARIO/(CONSIGNES):</b> <span>{{ $consignatario }}</span><br>
                            <?php } ?>
                            <?php $dirConsignatario = \App\OtherInvoiceData::findData($otherData, 'DIR_CONSIG');
                                if( !empty($dirConsignatario) ){  ?>
                                <b>DIR. CONSIG:</b> <span>{{ $dirConsignatario }}</span><br>
                            <?php } ?>
                        </td>
                        <td>
                            <b>TIPO DOC. / (DOC. TYPE): </b> <span>{{ $documentType }}</span><br>
                            <b>NÚMERO DOC. / (DOC. NUMBER):</b> <span>{{$data_invoice->document_number}}</span><br>
                            <b style="width:100%;">CLAVE NUMÉRICA / (DOCUMENT KEY):</b> <br><span style="width:100%; padding-top:0;">{{$data_invoice->document_key}}</span><br>
                            <b>FECHA / (DATE)</b> <span>{{ $data_invoice->generatedDate()->format('d-m-Y') }}</span><br>
                            @if( !empty($data_invoice->other_reference) )
                                <b>DOC. REFERENCIA / REFERENCED DOC.: </b> {{ $data_invoice->other_reference }}<br>
                            @endif
                            <b>TIPO CLIENTE / (CLIENT TYPE): </b> <span>{{ $tipoPersonaInterno }}</span><br>
                        </td>
                    </tr>
                    <tr class="details3">
                        <td>
                            <b>TÉRMINOS DE PAGO/(TERMS PAYMENT):</b> <span>{{ $data_invoice->getCondicionVenta() }}</span><br>
                            @if( $data_invoice->sale_condition == '02')
                                <b>PLAZO CRÉDITO / (CREDIT TERM):</b> <span>{{ $data_invoice->credit_time }} días</span><br>
                            @endif
                            <b>MEDIO DE PAGO / (MEANS OF PAYMENT):</b> <span>{{ $data_invoice->getMetodoPago() }}</span><br>
                        </td>
                        <td>
                            <b>MONEDA / (CURRENCY)</b> <span>{{ $data_invoice->currency }}</span><br>
                            <b>TIPO DE CAMBIO / (EXCHANGE RATE)</b> <span>{{ $data_invoice->currency_rate }}</span>
                        </td>
                    </tr>
                    @if($data_invoice->document_type == '09' )
                    <tr>
                        <td><b>Datos de exportación</b></td>
                    </tr>
                    <tr class="details4">
                        <td>
                            <?php if( !empty($fechaEmbarque) ){  ?>
                                <b>FECHA EMBARQUE / LOAD DATE:</b> <span>{{ \Carbon\Carbon::parse($fechaEmbarque)->format('d-m-Y') }}</span><br>
                            <?php } ?>
                            <?php if( !empty($numeroEmbarque) ){  ?>
                                <b>NUM EMBARQUE / SHIPPING NUM:</b> <span>{{ $numeroEmbarque }}</span><br>
                            <?php } ?>
                            <?php if( !empty($nombreEmbarque) ){  ?>
                                <b>VAPOR / VESSEL:</b> <span>{{ $codVap . " - " . $nombreEmbarque }}</span><br>
                            <?php } ?>
                            <?php if( !empty($procedenciaEmbarque) ){  ?>
                                <b>PROCEDENCIA DE FRUTA:</b> <span>{{ $procedenciaEmbarque }}</span><br>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if( !empty($codigoExportador) ){  ?>
                                <b>CÓDIGO EXPORTADOR:</b> <span>{{ $codigoExportador }}</span><br>
                            <?php } ?>
                            <?php if( !empty($puertoSalida) ){  ?>
                                <b>PUERTO SALIDA / DELIVERY PORT:</b> <span>{{ $puertoSalida }}</span><br>
                            <?php } ?>
                            <?php if( !empty($puertoDestino) ){  ?>
                                <b>PUERTO DESTINO / DESTINATION PORT:</b> <span>{{ $puertoDestino }}</span><br>
                            <?php } ?>
                            <?php if( !empty($paisDestino) ){  ?>
                                <b>PAIS DESTINO / DESTINATION COUNTRY:</b> <span>{{ $paisDestino }}</span><br>
                            <?php } ?>
                            <?php if( !empty($declaracionAduanera) ){  ?>
                                <b>DUA:</b> <span>{{ $declaracionAduanera }}</span><br>
                            <?php } ?>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
        <tr style=" min-height: 300px;">
            <td style="border: 2px solid black;" colspan="2">
                <table style="width: 100%;" class="table-lines" cellpadding="0" cellspacing="0" >
                    <tr style="width: 100%;" class="heading">
                        <td class="tb-header">
                            CANTIDAD (QUANTITY)
                        </td>
                        <td class="tb-header">
                            UNIDAD MEDIDA (MEASURE UNIT)
                        </td>
                        <td  style="text-align: left;"  class="tb-header maslarga">
                            DETALLE (DESCRIPTION)
                        </td>
                        <td class="tb-header">
                            IMPUESTOS (TAX)
                        </td>
                        <td class="tb-header">
                            PESO NETO / (NET WEIGHT)
                        </td>
                        <td class="tb-header">
                            PESO BRUTO (GROSS WEIGHT)
                        </td>
                        <td class="tb-header">
                            PRECIO UNIT / (UNIT PRICE)
                        </td>
                        <td class="tb-header">
                            TOTAL
                        </td>
                    </tr>
                    <?php
                        $cantidadCount = 0;
                        $pesoNetoTotal = 0;
                        $pesoBrutoTotal = 0;
                    ?>
                    @foreach($data_invoice->items as $item)
                        <?php 
                        $totalLinea = $item->total;
                        $ivaAmountLinea = $item->iva_amount;
                        if( isset($numExoneracion) ){
                            $totalLinea = $item->total - $item->iva_amount;
                            $ivaAmountLinea = 0;
                        }
                        ?>
                        <tr style="width: 100%;" class="item">
                            <td style="text-align: right;">
                                {{$item->item_count ?? ''}}
                                <?php 
                                    $cantidadCount = $cantidadCount + $item->item_count ?? 0;
                                ?>
                            </td>
                            <td style="text-align: center;">
                                {{$item->measure_unit ? ($item->measure_unit == '0' ? 'Unid' : $item->measure_unit) : 'Unid'}}
                            </td>
                            <td  style="text-align: left;" class="maslarga" >
                                {{$item->name ?? ''}}
                            </td>
                            <td style="text-align: right;">
                                {!! $currencySymbol !!}{{$ivaAmountLinea ? number_format($ivaAmountLinea, 2) : '0'}} ({{$item->iva_percentage}}%)
                            </td>
                            <td style="text-align: right;">
                                <?php $pneto = \App\OtherInvoiceData::findData($otherData, 'PESO_NETO', $item->id);
                                if( isset($pneto) ){  
                                    $pesoNetoTotal = $pesoNetoTotal + $pneto;
                                ?>
                                    {{ $pneto }}
                                <?php } ?>
                            </td>
                            <td style="text-align: right;">
                                <?php $pbruto = \App\OtherInvoiceData::findData($otherData, 'PESO_BRUTO', $item->id);
                                if( isset($pbruto) ){  
                                    $pesoBrutoTotal = $pesoBrutoTotal + $pbruto;
                                ?>
                                    {{ $pbruto }}
                                <?php } ?>
                            </td>
                            <td style="text-align: right;">
                                {!! $currencySymbol !!}{{$item->unit_price ? number_format($item->unit_price, 2) : '0'}}
                            </td>
                            <td style="text-align: right;">
                                {!! $currencySymbol !!}{{$totalLinea ? number_format($totalLinea, 2) : '0'}}
                            </td>
                        </tr>
                    @endforeach
                        <tr class="filler">
                            <td colspan="8">&nbsp;</td>
                        </tr>
                        <tr class="filler">
                            <td colspan="8">&nbsp;</td>
                        </tr>
                        <tr class="filler">
                            <td colspan="8">&nbsp;</td>
                        </tr>
                        <tr class="filler">
                            <td colspan="8">&nbsp;</td>
                        </tr>
                        <tr class="totalizado">
                            <td style="text-align: right;">
                                {{ $cantidadCount > 1 ? $cantidadCount : '' }}
                            </td>
                            <td style="text-align: center; font-size: 0.9em" colspan="3">
                                TOTAL (TOTAL)
                            </td>
                            <td style="text-align: right;">
                                {{ $pesoNetoTotal > 0 ? $pesoNetoTotal : '' }}
                            </td>
                            <td style="text-align: right;">
                                {{ $pesoBrutoTotal > 0 ? $pesoBrutoTotal : '' }}
                            </td>
                            <td>
                                TOTAL
                            </td>
                            <td style="text-align: right;">
                                {!! $currencySymbol !!}{{$data_invoice->total ? number_format($data_invoice->total, 2) : '0'}}
                            </td>
                        </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 5px; border-right:0; width: 65%;" colspan="1">
                @if( isset($data_invoice->description) )
                    <b style="margin: 1rem 0;">NOTAS/(NOTES):</b>
					<p>{!! nl2br(e($data_invoice->description)) !!}</p>
                @endif
                @if( isset($company->payment_notes) )
                    <b style="margin: 1rem 0;">OBSERVACIONES/(OBSERVATIONS):</b>
					<p>{!! nl2br(e($company->payment_notes)) !!}</p>
                @endif
                @if( isset($numExoneracion) )
                    </br>
                    Se aplica un porcentaje de exoneración del {{ $porcExoneracion }}%, 
                    según lo autorizado por la Dirección General de Hacienda, mediante el documento {{ $numExoneracion }} del {{ $fechaExoneracion }}.
                @endif
            </td>
            <td style="border: 2px solid black; padding: 5px; width: 35%;" colspan="1">
                <table class="resumen-totales">
                    <tr>
                        <td class="texto-resumen">Total servicios gravados/(Total taxed services)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalServiciosGravados, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total servicios exentos/(Total exempt services)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalServiciosExentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total servicios exonerados/(Total exempt)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalServiciosExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias gravadas/(Total taxed goods)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalMercaderiasGravadas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias exentas/(Total exempt goods)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalMercaderiasExentas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias exoneradas/(Total exempt)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalMercaderiasExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total gravado/(total taxed)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalGravado, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total exento/(Total tax-exempt)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalExento, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total exonerado/(Total exempt)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total Venta/(Sale total)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalVenta, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Descuento/(Discount)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalDescuentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total venta neta/(total net sale)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format( ($totalNeta), 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total impuestos/(total taxes)</td>
                        <td><span>{!! $currencySymbol !!}{{ number_format($totalImpuestos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen" style="font-size: 12px; font-weight: bold;"><b>Total</b></td>
                        <td style="font-size: 12px; font-weight: bold;"><span>{!! $currencySymbol !!}{{ number_format( ($totalComprobante) , 2)}}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 5px;" colspan="2">
                <table class="table-usuario">
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <?php 
                                if($data_invoice->currency == 'USD'){
                                    $monedaLetras = 'DOLARES';
                                }else{
                                    $monedaLetras = 'COLONES';
                                }
                                $letras = \App\Utils\NumeroALetras::convertir($totalComprobante, '', 'CENTAVOS') . " $monedaLetras";
                                if( !empty($letras) ){  ?>
                                <span>{{ $letras }}</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>HECHO POR/(DONE BY):</b> <span>{{ \App\OtherInvoiceData::findData($otherData, 'HECHO_POR') }}</span></td>
                        <td><b>APROBADO POR/(APROVED BY): </b> <span>{{ \App\OtherInvoiceData::findData($otherData, 'REVISADO_POR') }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <table width="100%" class="footer">
        <tr class="item">
            <td class="currency total">
                Documento emitido conforme lo establecido en la resolución de Facturación Electrónica, N°DGT-R-48-2016 del siete de
                octubre de dos mil dieciséis de la Dirección General de Tributación v.4.3 <br><br>
                eTax - La herramienta para resolver el IVA. | Ofiplaza del Este, 200m al oeste de la Rotonda de la Bandera,
                San Pedro de Montes de Oca, San José 6897-1000 Costa Rica +506 22802130
            </td>
        </tr>
    </table>

</body>
</html>
