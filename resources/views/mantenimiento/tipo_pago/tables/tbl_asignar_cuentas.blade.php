<table class="table table-hover table-striped" id="tbl_asignar_cuentas">
    <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">#</th>
            <th scope="col">BANCO</th>
            <th scope="col">N° CUENTA</th>
            <th scope="col">CCI</th>
            <th scope="col">CELULAR</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cuentas as $cuenta)
            @php
                $tipo_pago_cuenta = $cuentas_asignadas->first(function ($cuenta_asignada) use ($cuenta) {
                    return (int) $cuenta_asignada->cuenta_id === (int) $cuenta->id;
                });
            @endphp
            <tr>
                <td>
                    <input @if ($tipo_pago_cuenta) checked @endif type="checkbox"
                        aria-label="Checkbox for following text input" class="chk-cuenta" data-id="{{ $cuenta->id }}">
                </td>
                <td>{{ $cuenta->id }}</td>
                <td>{{ $cuenta->banco_nombre }}</td>
                <td>{{ $cuenta->nro_cuenta }}</td>
                <td>{{ $cuenta->cci }}</td>
                <td>{{ $cuenta->celular }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
