<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>EXAMPLE</title>
        <link href="https://allfont.es/allfont.css?fonts=dot-matrix" rel="stylesheet" type="text/css" />
        <style>
            body {
                font-family: 'Dot Matrix', arial;
                color: #2980B9;
                padding: 10px;
            }

            .tbl-cabecera {
                width: 100%;
                word-spacing: 0.90em;
            }

            .tbl-datos {
                width: 100%;
                word-spacing: 0.90em;
            }

            /* .tbl-datos td{
               border: 1px solid #3333
            } */

            .tbl-detalles {
                width: 100%;
            }

            .tbl-detalles thead{
                border-top: 1px dashed #2980B9;
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            .tbl-detalles tbody{
                border-top: 1px dashed #2980B9;
                border-bottom: 1px dashed #2980B9;
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            .tbl-firmas {
                width: 100%;
            }

            .tbl-footer {
                width: 100%;
            }

            .m-0 {
                margin: 0px;
            }

            .p-0 {
                padding: 0px;
            }
        </style>
    </head>

    <body>
        <table class="tbl-cabecera" cellpadding="2" cellspacing="0">
            <tr>
                <td>
                    <p class="p-0 m-0">AGROTEC E.I.R.L. - TEF.: 920042328</p>
                    <p class="p-0 m-0">CARR. PANAMERICANA NORTE 510</p>
                </td>
            </tr>
            <tr>
                <td style="text-align: center">
                    NOTA DE PEDIDO N°: B.0005-0336686
                </td>
            </tr>
        </table>
        <table class="tbl-datos"  cellpadding="2" cellspacing="0">
            <tr>
                <td style="width: 20%">
                    CLIENTE
                </td>
                <td style="width: 2%">
                    :
                </td>
                <td style="text-align: left;" colspan="3">
                    HUGO DE LA CRUZ CUBAS
                </td>
            </tr>
            <tr>
                <td>..</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>..</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    FECHA
                </td>
                <td>
                    :
                </td>
                <td style="width: 20%">
                    07/12/2020
                </td>
                <td style="text-align: right">
                    CONDICóN :
                </td>
                <td style="padding-left: 10px; width: 20%">
                    CONTADO
                </td>
            </tr>
            <tr>
                <td>
                    VENDEDOR
                </td>
                <td>
                    :
                </td>
                <td>
                    JMORI
                </td>
                <td style="text-align: right">
                    LLEVA ARTICULO :
                </td>
                <td style="padding-left: 10px">
                    ENTREGADO
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    ESTE DOCUMENTO SIRVASE CANJEAR POR BOLETA O FACTURA
                </td>
            </tr>
        </table>
        <br>
        <table class="tbl-detalles" cellpadding="2" cellspacing="0">
            <thead>
                <tr>
                    <td style="width: 12.5%; padding-top: 8px; padding-bottom: 8px;">
                        CANTID.
                    </td>
                    <td style="width: 12,5%; text-align:center; padding-top: 8px; padding-bottom: 8px;">
                        U. MED
                    </td>
                    <td style="width: 45%; padding-top: 8px; padding-bottom: 8px;">
                        DESCRIPCION
                    </td>
                    <td style="width: 15%; padding-top: 8px; padding-bottom: 8px;">
                        PRECIO
                    </td>
                    <td style="width: 15%; text-align: right; padding-top: 8px; padding-bottom: 8px;">
                        IMPORT
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: right; padding-top: 8px;">2</td>
                    <td style="padding-top: 8px; text-align:center">1 KG</td>
                    <td style="padding-top: 8px;">SULFA PLUS</td>
                    <td style="padding-top: 8px;">18.00</td>
                    <td style="text-align: right; padding-top: 8px;">36.00</td>
                </tr>
                <tr>
                    <td style="text-align: right;">2</td>
                    <td style="text-align:center">100 G</td>
                    <td>AKRON</td>
                    <td>40.00</td>
                    <td style="text-align: right;">80.00</td>
                </tr>
                <tr>
                    <td style="text-align: right; padding-bottom: 8px;">1</td>
                    <td style="padding-bottom: 8px;text-align:center">LT</td>
                    <td style="padding-bottom: 8px;">TRONKAL</td>
                    <td style="padding-bottom: 8px;">180.00</td>
                    <td style="text-align: right; padding-bottom: 8px;">180.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="padding-top: 8px;">
                        Son: DOSCIENTOS NOVENTA Y SEIS Y 00/100 SOLES
                    </td>
                    <td style="text-align: right; padding-top: 8px;">
                        296.00
                    </td>
                </tr>
            </tfoot>
        </table>
        <br><br>
        <table class="tbl-firmas" cellpadding="10" cellspacing="0">
            <tr>
                <td></td>
                <td style="text-align: center">
                    <div style="width: 150px; border-top: 1px dashed #2980B9; margin-bottom: 5px;" class="p-0 m-0"></div>
                    Firma
                </td>
                <td style="text-align: center">
                    <div style="width: 120px; border-top: 1px dashed #2980B9; margin-bottom: 5px;" class="p-0 m-0"></div>
                    DNI
                </td>
                <td style="text-align: center">
                    <div style="width: 250px; border-top: 1px dashed #2980B9; margin-bottom: 5px;" class="p-0 m-0"></div>
                    Nombre
                </td>
                <td></td>
            </tr>
        </table>
        <br>
        <table class="tbl-footer" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th></th>
                    <th>Impreso: 07/12/2020</th>
                    <th>Hora: 19:47:13</th>
                    <th>Usuario: JMORI</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </body>
</html>
