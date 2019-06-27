<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
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
            padding-bottom: 40px;
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
            height: 25px;
            margin-bottom: 15px;
        }

        .total {
            margin-top: 40px;
            height: 60px;
        }

        .title-total {
            background: #3333;
            width: 50%;
            text-align: center;
        }
        .box-total {
            background: black;
            color: white;
            font-size: 16px;
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
            font-size: 12px;
            line-height: 1;
            color: white;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }

        .footer {
            margin-top: 130px;
            height: 100px;
        }
        td {
            font-family: Pangram, sans-serif;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr class="top">
        <td colspan="2">
    <tr>
        <td class="title" style="width: 200px; height: 170px">
            @if( $company->logo_url )
                <img src="{{\Illuminate\Support\Facades\Storage::temporaryUrl($company->logo_url,  now()->addMinutes(1))}}" style="width:100%; max-width:150px; max-height: 150px">
            @endif
        </td>

        <td>
            <b>{{$company->business_name}}</b><br>
            <b>Cedula:</b> {{$company->id_number}}<br>
            <b>Tel:</b> {{$company->phone}}<br>
            <b>Fax:</b> <br>
            <b>Correo: {{$company->email}}</b> <br>
        </td>
        <td style="padding: 80px 0px 0px 30px; text-align: right;">
            <b>Direccion:</b>
            {{$company->address. " ".$company->zip}}<br>
        </td>
    </tr>
    </td>
    </tr>
</table>
    <table width="100%">
        <hr class="barra">
    </table>
    <table width="100%">
    <tr class="details">
        <td style="width: 42%">
            <b>Receptor: </b> {{$data_invoice->client_first_name.' '.$data_invoice->client_last_name}} <br>
            <b>Cedula: </b> {{$data_invoice->client_id_number}}<br>
            <b>Tel: </b> {{$data_invoice->client_phone}}<br>
            <b>Correo: </b> {{$data_invoice->client_email}}<br>
            <b>Codigo Interno :</b> {{$data_invoice->id}}<br>
            <b>Direccion: </b> {{$data_invoice->client_address}}<br>
        </td>

        <td>
            <b>Factura Electrónica N°: </b>{{$data_invoice->document_number}} <br>
            <b>Clave Numérica: </b> {{$data_invoice->document_key}}<br>
            <br><br>
            <b>Fecha de Emisión: </b> {{$data_invoice->generated_date}}<br>
            <b>Condición Venta: </b> Contado<br>
            <b>Medio de Pago: </b> Efectivo<br>
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
                {{$item->unit_price ? round($item->unit_price, 2) : ''}}
            </td>
            <td>
                {{$item->discount ? round($item->discount, 2) : ''}}
            </td>
            <td>
                {{$item->discount_reason ?? ''}}
            </td>
            <td>
                {{$item->subtotal ? round($item->subtotal, 2) : ''}}
            </td>
            <td>
                {{$item->iva_amount ? round($item->iva_amount, 2) : ''}}
            </td>
        </tr>
    @endforeach
</table>
    <hr class="barra" style="margin-top: 150px">
    <table width="100%" class="total">
        <tr class="item">
            <td class="title-total">

            </td>

            <td class="box-total">
                <b>Total {{ $data_invoice->currency }}: </b> {{round($data_invoice->total, 2)}}
            </td>
        </tr>
    </table>
    <table width="100%" class="footer">
        <tr class="item">
            <td class="title-total">
                Emitida conforme lo establecido en la resolución de Facturación Electrónica, N°DGT-R-48-2016 siete de
                octubre de dos mildieciséis de la Dirección General de Tributación v.4.2 <br>
                eTax - La herramienta para resolver el IVA. | Ofiplaza del Este. 200m al oeste de la Rotonda de la Bandera,
                San Pedro de Montes de Oca, san josé 6897-1000 Costa Rica +506 22802130
            </td>
        </tr>
    </table>

</body>
</html>
