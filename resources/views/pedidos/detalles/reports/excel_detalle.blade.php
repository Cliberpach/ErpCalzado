<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 11px;">
    <thead>
        <tr>
            <th colspan="19" style="background-color: #1a5276; color: #ffffff; font-size: 13px; font-weight: bold; text-align: center; padding: 8px; border: 1px solid #1a5276;">
                REPORTE DE DETALLES DE PEDIDOS
            </th>
        </tr>
        <tr>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">PEDIDO</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">FECHA REGISTRO</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">FECHA PROPUESTA</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">CLIENTE</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">DOC CLIENTE</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">TELEFONO</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">VENDEDOR</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">MODELO</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">PRODUCTO</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">COLOR</th>
            <th style="background-color: #2e86c1; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">TALLA</th>
            <th style="background-color: #1a5276; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">CANTIDAD</th>
            <th style="background-color: #1a5276; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">PRECIO UNIT.</th>
            <th style="background-color: #1a5276; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #1a5276; white-space: nowrap;">IMPORTE</th>
            <th style="background-color: #1e8449; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #145a32; white-space: nowrap;">ATENDIDA</th>
            <th style="background-color: #e74c3c; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #922b21; white-space: nowrap;">PENDIENTE</th>
            <th style="background-color: #d4ac0d; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #9a7d0a; white-space: nowrap;">ENVIADA</th>
            <th style="background-color: #8e44ad; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #6c3483; white-space: nowrap;">FABRICACION</th>
            <th style="background-color: #e67e22; color: #ffffff; font-weight: bold; text-align: center; padding: 6px 8px; border: 1px solid #a04000; white-space: nowrap;">DEVUELTA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detalles as $i => $detalle)
        @php $bg = $i % 2 === 0 ? '#f2f3f4' : '#ffffff'; @endphp
        <tr>
            <td style="background-color: {{ $bg }}; font-weight: bold; color: #1a5276; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->pedido_codigo }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc; white-space: nowrap;">{{ \Carbon\Carbon::parse($detalle->pedido_fecha)->format('d/m/Y') }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc; white-space: nowrap;">{{ $detalle->fecha_propuesta ? \Carbon\Carbon::parse($detalle->fecha_propuesta)->format('d/m/Y') : '-' }}</td>
            <td style="background-color: {{ $bg }}; text-align: left; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->cliente_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->cliente_doc }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->cliente_telefono }}</td>
            <td style="background-color: {{ $bg }}; text-align: left; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->vendedor_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: left; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->modelo_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: left; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->producto_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->color_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ $detalle->talla_nombre }}</td>
            <td style="background-color: {{ $bg }}; text-align: center; padding: 5px 7px; border: 1px solid #d5d8dc; font-weight: bold;">{{ $detalle->cantidad }}</td>
            <td style="background-color: {{ $bg }}; text-align: right; padding: 5px 7px; border: 1px solid #d5d8dc;">{{ number_format($detalle->precio_unitario_nuevo, 2) }}</td>
            <td style="background-color: {{ $bg }}; text-align: right; padding: 5px 7px; border: 1px solid #d5d8dc; font-weight: bold;">{{ number_format($detalle->importe_nuevo, 2) }}</td>
            <td style="background-color: #d5f5e3; text-align: center; padding: 5px 7px; border: 1px solid #a9dfbf; font-weight: bold; color: #1e8449;">{{ $detalle->cantidad_atendida }}</td>
            <td style="background-color: #fadbd8; text-align: center; padding: 5px 7px; border: 1px solid #f1948a; font-weight: bold; color: #922b21;">{{ $detalle->cantidad_pendiente }}</td>
            <td style="background-color: #fef9e7; text-align: center; padding: 5px 7px; border: 1px solid #f9e79f; color: #9a7d0a;">{{ $detalle->cantidad_enviada }}</td>
            <td style="background-color: #f5eef8; text-align: center; padding: 5px 7px; border: 1px solid #d7bde2; color: #6c3483;">{{ $detalle->cantidad_fabricacion }}</td>
            <td style="background-color: #fdebd0; text-align: center; padding: 5px 7px; border: 1px solid #f0b27a; color: #a04000;">{{ $detalle->cantidad_devuelta }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
