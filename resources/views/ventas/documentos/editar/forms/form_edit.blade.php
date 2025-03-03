<form action="" method="POST" id="enviar_documento">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-12">
            <h4 class=""><b>Documento de venta</b></h4>
            <div class="row">
                <div class="col-md-12">
                    <p>Edtar datos del documento de venta:</p>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="data_envio" id="data_envio">

    <div class="row">
        <div class="col-sm-6 b-r">
            <div class="row">
                <div class="col-12 col-md-6" id="fecha_documento">
                    <div class="form-group">
                        <label class="">Fecha de Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                class="form-control input-required{{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                value="{{ old('fecha_documento_campo', $documento->fecha_documento) }}" autocomplete="off"
                                required readonly>

                            @if ($errors->has('fecha_documento_campo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_documento_campo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6" id="fecha_entrega">
                    <div class="form-group">
                        <label class="">Fecha de Atención</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>

                            <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                    class="form-control input-required {{ $errors->has('fecha_atencion_campo') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_atencion_campo', $documento->fecha_atencion) }}" autocomplete="off" required
                                    readonly disabled>

                            @if ($errors->has('fecha_atencion_campo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_atencion_campo') }}</strong>
                                </span>
                                @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        <label class="required">Tipo de Comprobante: </label>
                        <select
                            class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('tipo_venta', $documento->tipo_venta) }}"
                            name="tipo_venta" id="tipo_venta" required onchange="consultarSeguntipo()" disabled required>
                            <option></option>

                            @foreach (tipos_venta() as $tipo)
                                @if ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')
                                    <option value="{{ $tipo->id }}" @if (old('tipo_venta') == $tipo->id) {{ 'selected' }} @endif  {{ $tipo->id == $documento->tipo_venta ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}</option>
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
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        <label>Moneda:</label>
                        <select id="moneda" name="moneda"
                            class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                            required disabled>
                            <option selected>SOLES</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="row">
                <div class="col-12">
                    <div class="form-group select-required d-none">
                        <label class="required">Empresa: </label>

                        <select class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('empresa_id') }}" name="empresa_id"
                            id="empresa_id" required onchange="obtenerTiposComprobantes(this)" disabled>
                            <option></option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @if (old('empresa_id') == $empresa->id) {{ 'selected' }} @endif {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 select-required">
                    <div class="form-group">
                        <label class="required">Condición</label>
                        <select id="condicion_id" name="condicion_id"
                            class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                            required disabled onchange="changeFormaPago()">
                            <option></option>
                            @foreach ($condiciones as $condicion)
                                <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                    {{ old('condicion_id') == $condicion->id || $documento->condicion_id == $condicion->id ? 'selected' : '' }} data-dias="{{$condicion->dias}}">
                                    {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6" id="fecha_vencimiento">
                    <div class="form-group">
                        <label class="required">Fecha de vencimiento</label>
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="date" id="fecha_vencimiento_campo" name="fecha_vencimiento_campo"
                                class="form-control input-required" autocomplete="off"
                                {{ $errors->has('fecha_vencimiento_campo') ? ' is-invalid' : '' }}
                                value="{{ old('fecha_vencimiento_campo', $documento->fecha_vencimiento) }}" required>
                            @if ($errors->has('fecha_vencimiento_campo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fecha_vencimiento_campo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group select-required">
                        <label class="required">Cliente:</label>
                        <input type="hidden" name="tipo_cliente_documento" id="tipo_cliente_documento">
                        <input type="hidden" name="tipo_cliente_2" id="tipo_cliente_2" value='1'>
                        <select class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('cliente_id', $documento->cliente_id) }}" name="cliente_id"
                            id="cliente_id" required onchange="obtenerTipocliente(this.value)">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group d-none">
                        <label>Observación:</label>

                        <textarea type="text" placeholder=""
                            class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                            name="observacion" id="observacion" onkeyup="return mayus(this)"
                            value="{{ old('observacion') }}">{{ old('observacion', $documento->observacion) }}</textarea>


                        @if ($errors->has('observacion'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                        @endif

                    </div>
                </div>
            </div>

            <input type="checkbox" id="igv_check" name="igv_check" class="d-none" checked>
            <!-- OBTENER TIPO DE CLIENTE -->
            <input type="hidden" class="form-control" name="" id="tipo_cliente">
            <!-- OBTENER DATOS DEL PRODUCTO -->
            <input type="hidden" class="form-control" name="" id="presentacion_producto">
            <input type="hidden" class="form-control" name="" id="codigo_nombre_producto">
            <!-- LLENAR DATOS EN UN ARRAY -->
            <input type="hidden" class="form-control" id="productos_tabla" name="productos_tabla">
            <input type="hidden" class="form-control" id="productos_detalle" name="productos_detalle" value="{{$detalles}}">
            <!-- TIPO PAGO -->
            <input type="hidden" class="form-control" name="tipo_pago_id" id="tipo_pago_id" value="{{ $documento->tipo_pago_id}}">
            <!-- EFECTIVO -->
            <input type="hidden" class="form-control" name="efectivo" id="efectivo_form">
            <!-- IMPORTE -->
            <input type="hidden" class="form-control" name="importe" id="importe_form">

        </div>

    </div>

    <input type="hidden" name="igv" id="igv" value="{{ $documento->igv ? $documento->igv : 18}}">

    {{-- <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
    <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
    <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}"> --}}

    <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
    <input type="hidden" name="monto_embalaje" id="monto_embalaje" value="{{ old('monto_embalaje') }}">
    <input type="hidden" name="monto_envio" id="monto_envio" value="{{ old('monto_envio') }}">
    <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
    <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}">
    <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{ old('monto_total_pagar') }}">
    <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ 'monto_descuento' }}">


</form>