<form action="" method="POST" id="enviar_documento">
    {{ csrf_field() }}

    @if (!empty($cotizacion))
        <input type="hidden" name="cotizacion_id" value="{{ $cotizacion->id }}">
        <input type="hidden" name="data_envio" id="data_envio">
    @endif
    <div class="row">
        <div class="col-12 col-md-6 b-r">
            <div class="row">
                <div class="col-12 col-md-6" id="fecha_documento">
                    <div class="form-group">
                        <label class="">Fecha de Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            @if (!empty($cotizacion))
                                <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                    class="form-control {{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_documento_campo', $fecha_hoy) }}"
                                    autocomplete="off" required readonly>
                            @else
                                <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                    class="form-control input-required{{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_documento_campo', $fecha_hoy) }}" autocomplete="off"
                                    required>
                            @endif

                            @if ($errors->has('fecha_documento_campo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_documento_campo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        <label class="required">Tipo de Comprobante: </label>
                        <select
                            class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('tipo_venta') }}"
                            name="tipo_venta" id="tipo_venta" required @if (!empty($cotizacion)) '' @else onchange="consultarSeguntipo()" @endif>
                            <option></option>

                            @foreach (tipos_venta() as $tipo)
                                @if (ifComprobanteSeleccionado($tipo->id) && ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS'))
                                    <option value="{{ $tipo->id }}" @if (old('tipo_venta') == $tipo->id || $tipo->id == 129) {{ 'selected' }} @endif>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endif
                            @endforeach

                            @if ($errors->has('tipo_venta'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tipo_venta') }}</strong>
                                </span>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-6" id="fecha_entrega">
                    <div class="form-group d-none">
                        <label class="">Fecha de Atención</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>

                            @if (!empty($cotizacion))
                                <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                    class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_atencion', $cotizacion->fecha_atencion) }}"
                                    autocomplete="off" readonly disabled>
                            @else

                                <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                    class="form-control input-required {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_atencion', $fecha_hoy) }}" autocomplete="off" required
                                    readonly disabled>

                            @endif

                            @if ($errors->has('fecha_atencion'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group d-none">
                        <label>Placa</label>
                        <input type="text" type="text" placeholder=""
                        class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                        name="observacion" id="observacion" onkeyup="return mayus(this)"
                        value="{{ old('observacion') }}" maxlength="7">
                        @if ($errors->has('observacion'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
               
                <div class="col-12 col-md-6 select-required d-none">
                    <div class="form-group">
                        <label>Moneda:</label>
                        <select id="moneda" name="moneda"
                            class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                            disabled>
                            <option selected>SOLES</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">

            <div class="row  d-none">
                <div class="col-12">
                    <div class="form-group select-required">
                        <label class="required">Empresa: </label>

                        @if (!empty($cotizacion))
                            <select
                                class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                style="text-transform: uppercase; width:100%"
                                value="{{ old('empresa_id', $cotizacion->empresa_id) }}" name="empresa_id" id="empresa_id"
                                disabled>
                                <option></option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" @if (old('empresa_id', $cotizacion->empresa_id) == $empresa->id){{ 'selected' }}@endif {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                style="text-transform: uppercase; width:100%" value="{{ old('empresa_id') }}" name="empresa_id"
                                id="empresa_id" required onchange="obtenerTiposComprobantes(this)" disabled>
                                <option></option>
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" @if (old('empresa_id') == $empresa->id)
                                        {{ 'selected' }}
                                @endif
                                {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        @if (!empty($cotizacion))
                        <label class="required">Condición</label>
                        <select id="condicion_id" name="condicion_id"
                            class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                            required onchange="changeFormaPago()" disabled>
                            <option></option>
                            @foreach ($condiciones as $condicion)
                                <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                    {{ old('condicion_id') == $condicion->id.'-'.$condicion->descripcion || $condicion->id == $cotizacion->condicion_id ? 'selected' : '' }}
                                    data-dias="{{$condicion->dias}}">
                                    {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @else
                        <label class="required">Condición</label>
                        <select id="condicion_id" name="condicion_id"
                            class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                            required onchange="changeFormaPago()">
                            <option></option>
                            @foreach ($condiciones as $condicion)
                                <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                    {{ old('condicion_id') == $condicion->id.'-'.$condicion->descripcion || $condicion->descripcion == 'CONTADO' ? 'selected' : '' }}
                                    data-dias="{{$condicion->dias}}">
                                    {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-6" id="fecha_vencimiento">
                    <div class="form-group">
                        <label class="required">Fecha de Vencimiento</label>
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="date" id="fecha_vencimiento_campo" name="fecha_vencimiento_campo"
                                class="form-control input-required" autocomplete="off"
                                {{ $errors->has('fecha_vencimiento_campo') ? ' is-invalid' : '' }}
                                value="{{ old('fecha_vencimiento_campo', $fecha_hoy) }}" required>
                            @if ($errors->has('fecha_vencimiento_campo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_vencimiento_campo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-end">
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        <label class="required">Cliente: @if (empty($cotizacion))<button type="button" class="btn btn-outline btn-primary" onclick="modalCliente()">Registrar</button>@endif</label>
                        <input type="hidden" name="tipo_cliente_documento" id="tipo_cliente_documento">
                        <input type="hidden" name="tipo_cliente_2" id="tipo_cliente_2" value='1'>
                        @if (!empty($cotizacion))
                            <select
                                class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                style="text-transform: uppercase; width:100%"
                                value="{{ old('cliente_id', $cotizacion->cliente_id) }}" name="cliente_id" id="cliente_id"
                                disabled>
                                <option></option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @if (old('cliente_id', $cotizacion->cliente_id) == $cliente->id){{ 'selected' }}@endif tabladetalle="{{$cliente->tabladetalles_id}}">{{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <select class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                style="text-transform: uppercase; width:100%" value="{{ old('cliente_id') }}" name="cliente_id"
                                id="cliente_id" required onchange="obtenerTipocliente(this.value)"> <!-- disabled -->
                                <option></option>
                            </select>
                        @endif
                    </div>
                </div>
               
            </div>

        
        </div>
    </div>

   
    <input type="hidden" name="cot_doc" id="cot_doc" value="SI">

</form>