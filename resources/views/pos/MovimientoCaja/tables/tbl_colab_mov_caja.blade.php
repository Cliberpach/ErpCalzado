<table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase"
    id="usuarios_venta">
    <thead>
        <tr>

            <th class="text-center"></th>

            <th class="text-center">COLABORADOR</th>

            <th class="text-center">NOMBRES</th>


        </tr>
    </thead>
    <tbody>
        {{-- @if (count($usuariosDesocupados) == 0)
            <tr>
                <th colspan="3" class="text-center">Usuarios ventas no disponibles</th>
            </tr>
        @else
            @foreach ($usuariosDesocupados as $u)
                <tr>
                    <th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                id="checkBox{{ $u->id }}"
                                onclick="verificarSeleccion({{ $u->id }})">
                        </div>
                    </th>
                    <th>
                        <input type="hidden" id='idUsuario{{ $u->id }}'
                            value="{{ $u->id }}"> {{ $u->usuario }}
                        @if ($errors->has('usuarioVentas'))
                            <span class="invalid-feedback" role="alert">
                                <strong
                                    id="error-usuarioVentas">{{ $errors->first('usuarioVentas') }}</strong>
                            </span>
                        @endif
                    </th>
                    <th>
                        {{ $u->usuario }}
                    </th>
                </tr>
            @endforeach

        @endif --}}
    </tbody>
    <tbody>

    </tbody>
</table>