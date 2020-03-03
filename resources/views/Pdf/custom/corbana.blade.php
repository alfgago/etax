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
            font-size: 8px;
            line-height: 1.1;
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
        }
        
        .details3 td b{
            display: inline-block;
            width: 200px;
        }
        
        .details-table td span{
            display: inline-block;
            vertical-align: top;
        }
        
        .table-lines-cont {
            min-height: 300px;
        }
        
        .table-lines .heading td {
            border-bottom: 2px solid black;
            padding: 5px;
        }
        
        .totalizado td {
            border-top: 2px solid black;
            padding: 5px;
        }
        
        .table-lines .item td {
            padding: 5px;
        }
        
        .resumen-totales tr td,
        .resumen-totales span {
            text-align: right;
            font-size: 10px;
        }
        
        .resumen-totales tr td.texto-resumen {
            text-align: left;
            white-space: nowrap;
            font-size: 8px;
        }
        
    </style>
</head>
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
    
?>
<body>
    <table class="main-table" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="2" >
                <img style="width: 400px; height: 80px margin-bottom: 3px;" src="https://staging.etaxcr.com/assets/images/corbana-logo-factura.png" >
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 3px;" colspan="2">
                <table class="details-table details1">
                    <tr>
                        <td style="min-width: 100%;" colspan="2" >
                            @if($documentType == '08' && $provider !== null)
                                <b style="width: 100%; font-size: 12px;">{{$provider->first_name }} {{ $provider->last_name }}}</b>
                            @else
                                <b style="width: 100%; font-size: 12px;">{{$company->business_name}}</b> 
                            @endif
                        </td>
                    </tr>
                    <tr>
                        @if($documentType == '08' && $provider !== null)
                        <td>
                            <b>Cedula:</b> {{$provider->id_number}}<br>
                            <b>Tel:</b> {{$provider->phone}}<br>
                            <b>Correo:</b> {{$provider->email}} <br>
                        </td>
                        <td style="padding: 40px 0px 0px 30px; text-align: right;">
                            <b>Direccion:</b>
                            {{$provider->address. " ".$provider->zip}}<br>
                        </td>
                        @else
                        <td>
                            <b>Cedula:</b> <span>{{$company->id_number}}</span><br>
                            <b>Correo electrónico:</b> <span>{{$company->email}}</span><br>
                            <b>Tel:</b> <span>{{$company->phone}}</span><br>
                            <b>APDO:</b> <span>6504-1000 San José, Costa Rica</span><br>
                        </td>
                        <td>
                            <b>Direccion:</b><br>
                            Provincia(Province): <span>San José</span><br>
                            Cantón(City): <span>San José</span><br>
                            Distrito(District): <span>ZAPOTE</span><br>
                            Otras señas: <span>{{$company->address. " ".$company->zip}}</span>
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
                            <b>VENDIDO A / (SOLD TO):</b> <span>{{$data_invoice->client_first_name.' '.$data_invoice->client_last_name}}</span><br>
                            <b>IDENTIFICACION / (ID):</b> <span>{{$data_invoice->client_id_number}}</span><br>
                            <b>CORREO ELECTRONICO / (EMAIL):</b> <span>{{$data_invoice->client_email}}</span><br>
                            <b>TELEFONO / (PHONE NUMBER):</b> <span>{{$data_invoice->client_phone}}</span><br>
                            <b>REFERENCIA / (REFERENCE):</b> <span>{{ \App\OtherInvoiceData::findData($otherData, 'REFERENCIA') }}</span><br>
                            <?php $consignatario = \App\OtherInvoiceData::findData($otherData, 'CONSIG');
                                if( isset($consignatario) ){  ?>
                            <b>CONSIGNATARIO/(CONSIGNES):</b> <span>{{ $consignatario }}</span><br>
                            <?php } ?>
                        </td>
                        <td>
                            <b>FACTURA ELECTRÓNICA / (ELECTRONIC INVOICE):</b> <span>{{$data_invoice->document_number}}</span><br>
                            <b style="width:100%;">CLAVE NUMERICA / (DOCUMENT KEY):</b> <br><b style="width:100%;">{{$data_invoice->document_key}}</b><br>
                            <b>TIPO / (TYPE): </b> <span>{{ $documentType }}</span><br>
                            <b>FECHA / (DATE)</b> <span>{{ $data_invoice->generated_date }}</span><br>
                            <b>MONEDA / (CURRENCY)</b> <span>{{ $data_invoice->currency }}</span>
                        </td>
                    </tr>
                    <tr class="details3">
                        <td>
                            <b>TERMINOS DE PAGO/(TERMS PAYMENT):</b> <span>{{ $data_invoice->getCondicionVenta() }}</span><br>
                            <b>PLAZO CREDITO / (CREDIT TERM):</b> <span>{{ $data_invoice->credit_time }}</span><br>
                            <b>MEDIO DE PAGO / (MEANS OF PAYMENT):</b> <span>{{ $data_invoice->getMetodoPago() }}</span><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style=" min-height: 300px;">
            <td style="border: 2px solid black;" colspan="2">
                <table class="table-lines" cellpadding="0" cellspacing="0" >
                    <tr class="heading">
                        <td class="tb-header">
                            CANTIDAD (QUANTITY)
                        </td>
                        <td class="tb-header">
                            UNIDAD MEDIDA (MEASURE UNIT)
                        </td>
                        <td class="tb-header">
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
                    ?>
                    @foreach($data_invoice->items as $item)
                        <tr class="item">
                            <td>
                                {{$item->item_count ?? ''}}
                                <?php 
                                    $cantidadCount = $cantidadCount + $item->item_count ?? 0;
                                ?>
                            </td>
                            <td>
                                {{$item->measure_unit ?? ''}}
                            </td>
                            <td>
                                {{$item->name ?? ''}}
                            </td>
                            <td>
                                {{$item->iva_amount ? number_format($item->iva_amount, 2) : '0'}}
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                {{$item->unit_price ? number_format($item->unit_price, 2) : '0'}}
                            </td>
                            <td>
                                {{$item->total ? number_format($item->total, 2) : '0'}}
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
                           <td colspan="3">
                                {{ $cantidadCount }}
                            </td>
                            </td>
                            <td>
                                TOTAL
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                {{$data_invoice->total ? number_format($data_invoice->total, 2) : '0'}}
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
            </td>
            <td style="border: 2px solid black; padding: 5px; width: 35%;" colspan="1">
                <table class="resumen-totales">
                    <tr>
                        <td class="texto-resumen">Total servicios gravados/(Total taxed services)</td>
                        <td><span>{{ number_format($totalServiciosGravados, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total servicios exentos/(Total exempt services)</td>
                        <td><span>{{ number_format($totalServiciosExentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total servicios exonerados/(Total exempt)</td>
                        <td><span>{{ number_format($totalServiciosExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias gravadas/(Total taxed goods)</td>
                        <td><span>{{ number_format($totalMercaderiasGravadas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias exentas/(Total exempt goods)</td>
                        <td><span>{{ number_format($totalMercaderiasExentas, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total mercancias exoneradas/(Total exempt)</td>
                        <td><span>{{ number_format($totalMercaderiasExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total gravado/(total taxed)</td>
                        <td><span>{{ number_format($totalGravado, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total exento/(Total tax-exempt)</td>
                        <td><span>{{ number_format($totalExento, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total exonerado/(Total exempt)</td>
                        <td><span>{{ number_format($totalExonerados, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total Venta/(Sale total)</td>
                        <td><span>{{ number_format($totalVenta, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Descuento/(Discount)</td>
                        <td><span>{{ number_format($totalDescuentos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total venta neta/(total net sale)</td>
                        <td><span>{{ number_format( ($totalNeta), 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen">Total impuestos/(total taxes)</td>
                        <td><span>{{ number_format($totalImpuestos, 2)}}</span></td>
                    </tr>
                    <tr>
                        <td class="texto-resumen" style="font-size: 12px; font-weight: bold;"><b>Total</b></td>
                        <td style="font-size: 12px; font-weight: bold;"><span>{{ number_format( ($totalComprobante) , 2)}}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black; padding: 5px; border-right:0;" colspan="2">
                <table class="table-usuario">
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
                Documento emitido conforme lo establecido en la resolución de Facturación Electrónica, N°DGT-R-48-2016 siete de
                octubre de dos mildieciséis de la Dirección General de Tributación v.4.3 <br><br>
                eTax - La herramienta para resolver el IVA. | Ofiplaza del Este. 200m al oeste de la Rotonda de la Bandera,
                San Pedro de Montes de Oca, San José 6897-1000 Costa Rica +506 22802130
            </td>
        </tr>
    </table>

</body>
</html>
