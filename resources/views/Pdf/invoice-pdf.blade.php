<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
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
        }

        .total {
            margin-top: 40px;
            height: 60px;
        }

        .title-total {
            background: #3333;
            width: 50%;
        }
        .box-total {
            background: black;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title" style="width: 200px; height: 170px">
                            <img src="https://www.sparksuite.com/images/logo.png" style="width:100%; max-width:150px; max-height: 150px">
                        </td>

                        <td>
                            <b>Nombre de la empresa</b><br>
                            <b>Cedula:</b> 123<br>
                            <b>Tel:</b> January 1, 2015<br>
                            <b>Cel:</b> 5555<br>
                            <b>Fax:</b> <br>
                            <b>Correo:</b> <br>
                        </td>
                        <td style="padding: 80px 0px 0px 30px; text-align: right;">
                            <b>Direccion:</b>
                            Villas Don Alfonso casa 50 <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td style="background: black">

            </td>

            <td style="background: black">

            </td>
        </tr>

        <tr class="details">
            <td style="width: 400px">
                <b>Receptor:</b> Xavier <br>
                <b>Cedula:</b> <br>
                <b>Tel:</b> January 1, 2015<br>
                <b>Cel:</b> 5555<br>
                <b>Fax:</b> <br>
                <b>Correo:</b> <br>
                <b>Codigo Interno:</b> <br>
                <b>Direccion:</b> <br>
            </td>

            <td>
                <b>Factura Electrónica N°: </b> <br>
                <b>Clave Numérica: </b> <br>
                <br><br>
                <b>Fecha de Emisión:</b> 11/05/2019   2:00 a.m <br>
                <b>Condición Venta:</b> Contado<br>
                <b>Medio de Pago:</b> Efectivo<br>
            </td>
        </tr>
        <table>
            <tr class="heading">
                <td>
                    Código
                </td>

                <td>
                    Cantidad
                </td>
                <td>
                    Unidad medida
                </td>
                <td>
                    Descripción producto/servicio
                </td>
                <td>
                    Precio unitario
                </td>
                <td>
                    Descuento
                </td>
                <td>
                    Tipo de descuento
                </td>
                <td>
                    Subtotal
                </td>
                <td>
                    Monto de impuesto
                </td>

            </tr>

            <tr class="item">
                <td>
                    001
                </td>

                <td>
                    1
                </td>
                <td>
                    M2
                </td>
                <td>
                    Hosting
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
            </tr>
            <tr class="item">
                <td>
                    001
                </td>

                <td>
                    1
                </td>
                <td>
                    M2
                </td>
                <td>
                    Hosting
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
                <td>
                    200,00
                </td>
            </tr>


        </table>
        <hr class="barra">
        <table class="total">
            <tr class="item">
                <td class="title-total">
                    001
                </td>

                <td class="box-total">
                    1
                </td>
            </tr>
        </table>
    </table>
</div>
</body>
</html>
