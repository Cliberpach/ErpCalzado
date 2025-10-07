<div class="row">
    <div class="col-md-6">
        <div class="row align-items-end">

            <div class="col-md-12 mb-3">
                <label for="cliente_text" class="required font-weight-bold">CLIENTE</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="fa fa-user text-primary"></i>
                        </span>
                    </div>
                    <input type="text" name="cliente_text" id="cliente_text" class="form-control" disabled>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="numero" class="required font-weight-bold">DOCUMENTO</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">
                                <i class="fa fa-file-alt text-success"></i>
                            </span>
                        </div>
                        <input type="text" name="numero" id="numero" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="pedido_nro" class="required font-weight-bold">PEDIDO</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-shopping-cart text-primary"></i>
                            </span>
                        </div>
                        <input type="text" name="pedido_nro" id="pedido_nro" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="monto" class="required font-weight-bold">MONTO</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-money-bill text-success"></i>
                            </span>
                        </div>
                        <input type="text" name="monto" id="monto" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="saldo" class="required font-weight-bold">SALDO</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-balance-scale text-warning"></i></span>
                        </div>
                        <input type="text" name="saldo" id="saldo" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="estado" class="required font-weight-bold">ESTADO</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-info-circle text-primary"></i></span>
                        </div>
                        <input type="text" name="estado" id="estado" class="form-control" disabled>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <a class="btn btn-danger" id="btn-detalle" target="_blank">
                        <i class="far fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <label class="font-weight-bold d-block mb-2" style="font-size: 0.95rem;">DETALLE PAGO</label>
                <div class="table-responsive">
                    @include('ventas.cuentaCliente.tables.tbl_detalle_pagos')
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <form id="frmDetalle" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label style="font-weight: bold;" for="pago" class="required_field">PAGO</label>
                        <select name="pago" id="pago" class="pago" required onchange="tipoPago(this)">
                            <option value="A CUENTA">A CUENTA</option>
                            <option value="TODO">TODO</option>
                        </select>
                        <span class="pago_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="fecha" class="required_field">FECHA</label>
                        <input type="date" name="fecha" id="fecha" class="form-control"
                            value="{{ $fecha_hoy }}" required>
                        <span class="fecha_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="cantidad" class="required_field">MONTO</label>
                        <input type="number" min="1" name="cantidad" id="cantidad" value="0.00"
                            class="form-control" onkeypress="return filterFloat(event, this);" readonly required>
                        <span class="cantidad_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                        <textarea name="observacion" id="observacion" cols="30" rows="3" class="form-control"></textarea>
                        <span class="observacion_error msgError"></span>
                    </div>
                    {{-- <div class="form-group">
                        <label style="font-weight: bold;" for="modo_despacho" class="col-form-label required">
                            MODO DESPACHO</label>
                        <select name="modo_despacho" id="modo_despacho" class="modo_despacho"
                            data-placeholder="SELECCIONAR">
                            <option value="ATENCION">ATENCION</option>
                            <option value="RESERVA">RESERVA</option>
                        </select>
                        <span class="modo_despacho_error msgError"></span>
                    </div> --}}
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group d-none">
                        <label style="font-weight: bold;" class="col-form-label required">EFECTIVO</label>
                        <input type="text" value="0" class="form-control" id="efectivo_venta"
                            onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo()"
                            name="efectivo_venta">
                        <span class="efectivo_venta_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="modo_pago" class="col-form-label required_field">MODO
                            DE PAGO</label>
                        <select required name="modo_pago" id="modo_pago" class="modo_pago"
                            data-placeholder="SELECCIONAR" onchange="changeModoPago(this)" required>
                            <option value=""></option>
                            @foreach (modos_pago() as $modo)
                                <option @if ($modo->id == 1) selected @endif value="{{ $modo->id }}">
                                    {{ $modo->descripcion }}</option>
                            @endforeach
                        </select>
                        <span class="modo_pago_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="cuenta"
                            class="col-form-label required">CUENTA</label>
                        <select name="cuenta" id="cuenta" class="cuenta" data-placeholder="SELECCIONAR">
                            <option value="">SELECCIONAR</option>
                        </select>
                        <span class="cuenta_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" class="col-form-label required">N° OPERACIÓN</label>
                        <input maxlength="20" type="text" class="form-control" id="nro_operacion"
                            name="nro_operacion">
                        <span class="nro_operacion_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" class="col-form-label required">IMPORTE</label>
                        <input readonly type="text" class="form-control" id="importe_venta" value="0.00"
                            onkeypress="return filterFloat(event, this);" onkeyup="changeImporte()"
                            name="importe_venta" required>
                        <span class="importe_venta_error msgError"></span>
                    </div>

                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label id="imagen_label" style="font-weight: bold;">IMAGEN:</label>

                        <div class="custom-file">
                            <input id="imagen" type="file" name="imagen" class="custom-file-input"
                                accept="image/*">

                            <label for="imagen" id="imagen_txt"
                                class="custom-file-label selected">Seleccionar</label>

                            <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>

                        </div>

                        <span class="imagen_error msgError"></span>
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="col-6 align-content-center">
                            <div class="row justify-content-end">
                                <a href="javascript:void(0);" id="limpiar_imagen">
                                    <span class="badge badge-danger">x</span>
                                </a>
                            </div>
                            <div class="row justify-content-center">
                                <p>
                                    <img class="imagen" src="{{ asset('img/default.png') }}" alt="">
                                    <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
