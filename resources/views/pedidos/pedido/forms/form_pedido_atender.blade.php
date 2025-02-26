<form method="POST" action="{{ route('pedidos.pedido.generarDocumentoVenta') }}" id="form-pedido-doc-venta">
    @csrf
    <div class="row">
        <div class="col-12">
            <h4><b>Datos Generales</b></h4>
        </div>

        <div class="col-12">
            <div class="form-group row">
                <div class="col-6">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required">Fecha de Atención</label>
                                <div class="input-group date">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="date" id="fecha_atencion" name="fecha_atencion_campo"
                                        class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                        value="{{ old('fecha_atencion', date('Y-m-d')) }}" autocomplete="off" required readonly>
                                    @if ($errors->has('fecha_atencion'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required">Tipo de Comprobante</label>
                                <select onchange="validarTipoComprobante(this.value)" name="tipo_venta" id="tipo_venta"
                                    class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}" required>
                                    @foreach ($tipoVentas as $tipo_venta)
                                        <option value="{{ $tipo_venta['id'] }}"
                                            {{ old('tipo_venta') == $tipo_venta['id'] ? 'selected' : '' }}
                                            {{ 129 == $tipo_venta['id'] ? 'selected' : '' }}>
                                            {{ $tipo_venta['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('tipo_venta'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('tipo_venta') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6" style="border-left: 2px solid #ccc;">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required">Condición</label>
                                <select id="condicion_id" name="condicion_id" onchange="cambiarCondicion(this.value)"
                                    class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}" required>
                                    <option></option>
                                    @foreach ($condiciones as $condicion)
                                        <option value="{{ $condicion->id }}"
                                            {{ old('condicion_id') == $condicion->id ? 'selected' : '' }}
                                            {{ $pedido->condicion_id == $condicion->id ? 'selected' : '' }}>
                                            {{ $condicion->descripcion }}
                                            {{ $condicion->dias > 0 ? $condicion->dias.' días' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('condicion_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('condicion_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required">Fecha de Vencimiento</label>
                                <div class="input-group date">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento_campo"
                                        class="form-control {{ $errors->has('fecha_vencimiento') ? ' is-invalid' : '' }}"
                                        value="{{ old('fecha_vencimiento', date('Y-m-d')) }}" autocomplete="off" required readonly>
                                    @if ($errors->has('fecha_vencimiento'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fecha_vencimiento') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required">Cliente</label>
                                <select id="cliente" name="cliente"
                                    class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                    required disabled>
                                    <option></option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                            {{ old('cliente') == $cliente->id ? 'selected' : '' }}
                                            {{ $pedido->cliente_id == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('cliente'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('cliente') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campos ocultos -->
        <input type="hidden" name="cliente_id" id="cliente_id">
        <input type="hidden" id="tipo_cliente">
        <input type="hidden" id="presentacion_producto">
        <input type="hidden" id="codigo_nombre_producto">
        <input type="hidden" id="productos_tabla" name="productos_tabla">
        <input type="hidden" name="igv" value="18">
        <input type="hidden" name="igv_check" value="on">
        <input type="hidden" name="efectivo" value="0">
        <input type="hidden" name="importe" value="0">
        <input type="hidden" name="empresa_id" value="{{ $pedido->empresa_id }}">
        <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ $pedido->sub_total }}">
        <input type="hidden" name="monto_embalaje" id="monto_embalaje" value="{{ $pedido->monto_embalaje }}">
        <input type="hidden" name="monto_envio" id="monto_envio" value="{{ $pedido->monto_envio }}">
        <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ $pedido->total_igv }}">
        <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ $pedido->monto_descuento }}">
        <input type="hidden" name="monto_total" id="monto_total" value="{{ $pedido->total }}">
        <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{ $pedido->total_pagar }}">
        <input type="hidden" name="data_envio" id="data_envio">
    </div>
</form>
