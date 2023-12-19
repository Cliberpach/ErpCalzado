<table>
    <tbody>
        <tr>
            <td colspan="7" style="text-align: center;background: #CFD8DC;color:#000000">
                <strong>DATOS DE EMPRESA</strong>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><strong>Empresa</strong></td>
            <td style="text-align: left">{{ $empresa->razon_social }}</td>
        </tr>
        <tr>
            <td style="text-align: right"><strong>RUC</strong></td>
            <td style="text-align: left">{{ $empresa->ruc }}</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;background: #B2DFDB"><strong>FILTROS</strong></td>
        </tr>
        <tr>
            <td style="text-align: right"><strong>Fecha Inicial</strong></td>
            <td style="text-align: left">{{ $parametros["d_start"] }}</td>
            <td style="text-align: right"><strong>Fecha Final</strong></td>
            <td style="text-align: left">{{ $parametros["d_end"] }}</td>
        </tr>
        <tr>
            <td style="text-align: right"><strong>Producto</strong></td>
            <td colspan="3" style="text-align: left">{{ $parametros["input"] }}</td>
            <td style="text-align: right"><strong>Stock</strong></td>
            <td style="text-align: left">{{ (int)$parametros["stock"] > 0 ? 'Stock mayor a 0': 'Stock en 0' }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">CODIGO</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="45">PRODUCTO</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="20">MARCA</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">STOCK INI.</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">COMPRAS</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">INGRESOS</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="10">VENTAS</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">DEVOLUCIONES</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">SALIDAS</th>
            <th style="background: #E1F5FE;font-weight: bold;text-align: center" width="15">STOCK</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kardex as $item)
            <tr>
                <td style="text-align: center">{{ $item->codigo }}</td>
                <td style="text-align: left">{{ $item->nombre }}</td>
                <td style="text-align: center">{{ $item->marca }}</td>
                <td style="text-align: center">{{ $item->STOCKINI }}</td>
                <td style="text-align: center">{{ $item->COMPRAS }}</td>
                <td style="text-align: center">{{ $item->INGRESOS }}</td>
                <td style="text-align: center">{{ $item->VENTAS }}</td>
                <td style="text-align: center">{{ $item->DEVOLUCIONES }}</td>
                <td style="text-align: center">{{ $item->SALIDAS }}</td>
                <td style="text-align: center">{{ $item->STOCK }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
