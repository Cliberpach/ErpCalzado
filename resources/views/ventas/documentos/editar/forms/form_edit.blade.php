<form action="" method="POST" id="formActualizarVenta">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-12">
            <h4 class=""><b>Documento de venta</b></h4>
            <div class="row">
                <div class="col-md-12">
                    <p>Editar datos del documento de venta:</p>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="data_envio" id="data_envio">

    <div class="row mb-3">

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="registrador" style="font-weight: bold;">REGISTRADOR</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-shield"></i>
                    </span>
                </div>
                <input value="{{ $registrador->usuario }}" readonly name="registrador" id="registrador" type="text"
                    class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="fecha_registro" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input value="{{ $documento->created_at }}" readonly name="fecha_registro" id="fecha_registro"
                    type="text" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="fecha_vencimiento" style="font-weight: bold;">FECHA VENCIMIENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input value="{{ $documento->fecha_vencimiento }}" readonly name="fecha_vencimiento"
                    id="fecha_vencimiento" type="date" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="documento" style="font-weight: bold;">DOCUMENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-file-signature"></i>
                    </span>
                </div>
                <input value="{{ $documento->serie . '-' . $documento->correlativo }}" readonly name="documento"
                    id="documento" type="text" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="sede" style="font-weight: bold;">SEDE DEL DOCUMENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-building"></i>
                    </span>
                </div>
                <input value="{{ $sede->nombre }}" readonly name="sede" id="sede" type="text"
                    class="form-control">
            </div>
        </div>


        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="almacen">ALMACÉN</label>
                <select onchange="cambiarAlmacen(this.value)" id="almacen" name="almacen"
                    class="select2_form form-control" required>
                    <option></option>
                    @foreach ($almacenes as $almacen)
                        <option @if ($almacen->id == $documento->almacen_id) selected @endif value="{{ $almacen->id }}">
                            {{ $almacen->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span style="font-weight: bold;color:red;" class="almacen_error msgError"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label style="font-weight: bold;" class="required" for="condicion_id">CONDICIÓN</label>
            <select onchange="cambiarCondicion(this.value);" id="condicion_id" name="condicion_id"
                class="select2_form form-control" required>
                <option></option>
                @foreach ($condiciones as $condicion)
                    <option value="{{ $condicion->id }}" @if ($condicion->id == $documento->condicion_id) selected @endif>
                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias . ' días' : '' }}
                    </option>
                @endforeach
            </select>
            <span style="font-weight: bold;color:red;" class="condicion_id_error msgError"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label for="cliente" class="required" style="font-weight: bold;">Cliente</label>
            <button type="button" class="btn btn-outline btn-primary"
                onclick="openModalCliente()">Registrar</button>
            <select id="cliente" name="cliente" class="" required>
                <option value="{{ $cliente->id }}" data-telefono="{{ $cliente->telefono_movil }}"
                    data-departamento-id="{{ $cliente->departamento_id }}"
                    data-provincia-id="{{ $cliente->provincia_id }}" data-distrito-id="{{ $cliente->distrito_id }}">
                    {{ $cliente->tipo_documento . ':' . $cliente->documento . '-' . $cliente->nombre }}
                </option>
            </select>
            <span style="font-weight: bold;color:red;" class="cliente_error msgError"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label style="font-weight: bold;" class="required" for="tipo_venta">COMPROBANTE</label>
            <select disabled id="tipo_venta" name="tipo_venta" class="select2_form form-control" required>
                <option></option>
                @foreach ($tipos_venta as $tipo_venta)
                    <option value="{{ $tipo_venta->id }}" @if ($tipo_venta->id == $documento->tipo_venta_id) selected @endif>
                        {{ $tipo_venta->descripcion }}
                    </option>
                @endforeach
            </select>
            <span style="font-weight: bold;color:red;" class="tipo_venta_error msgError"></span>
        </div>

        <div class="col-12 col-lg-3 col-md-3">
            <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
            <textarea maxlength="200" class="form-control" name="observacion" id="observacion" cols="30" rows="3"></textarea>
            <span style="font-weight: bold;color:red;" class="observacion_error msgError"></span>
        </div>

           <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="telefono" style="font-weight: bold;">TELÉFONO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-phone"></i>
                    </span>
                </div>
                <input value="{{ $documento->telefono }}" name="telefono" id="telefono" type="text"
                    class="form-control" placeholder="Número de teléfono" aria-label="Telefono"
                    aria-describedby="basic-addon1">
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary" id="panel_detalle">
                <div class="panel-heading">
                    <h4 class=""><b>Seleccione productos</b></h4>
                </div>
                <div class="panel-body ibox-content">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <label class="required" style="font-weight: bold;">CATEGORÍA - MARCA - MODELO -
                            PRODUCTO</label>
                        <select id="producto" class="" onchange="getColoresTallas()">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
                        <label class="required" style="font-weight: bold;">PRECIO VENTA</label>
                        <select id="precio_venta" class="select2_form form-control">
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            @include('ventas.documentos.editar.tables.tbl_stock')
                        </div>
                    </div>


                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label" for="amount">&nbsp;</label>
                            <button type=button class="btn btn-block btn-warning" style='color:white;'
                                id="btn_agregar_detalle"> <i class="fa fa-plus"></i>
                                AGREGAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary" id="panel_detalle">
                <div class="panel-heading">
                    <h4 class=""><b>Detalle del Documento de Venta</b></h4>
                </div>
                <div class="panel-body ibox-content">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('ventas.documentos.editar.tables.tbl_detalle')
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('ventas.documentos.editar.tables.tbl_montos')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4><b>Datos de Pago</b></h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="metodo_pago_1" style="font-weight: bold;">MÉTODO PAGO</label>
                            <select name="metodo_pago_1" id="metodo_pago_1" class="select2_form"
                                onchange="cambiarMetodoPago1(this.value)">
                                <option value=""></option>
                                @foreach ($metodos_pago as $metodo_pago)
                                    <option value="{{ $metodo_pago->id }}">
                                        {{ $metodo_pago->descripcion }}</option>
                                @endforeach
                            </select>
                            <p class="metodo_pago_1_error msgError"></p>
                        </div>
                        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12">
                            <label for="cuenta_1" style="font-weight: bold;">CUENTAS</label>
                            <select name="cuenta_1" id="cuenta_1" class="select2_form">
                                <option value=""></option>
                            </select>
                            <p class="cuenta_1_error msgError"></p>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                            <label for="monto_1" style="font-weight: bold;">MONTO</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control monto_1" id="monto_1"
                                    placeholder="Monto" name="monto_1">
                            </div>
                            <p class="monto_1_error msgError"></p>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
                            <label for="nro_operacion_1" style="font-weight: bold;">N°.
                                OPERACIÓN</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-receipt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control nro_operacion_1" id="nro_operacion_1"
                                    placeholder="N°. operación" name="nro_operacion_1">
                            </div>
                            <p class="nro_operacion_1_error msgError"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-12">

                            <label for="img_pago_1" style="font-weight: bold;"> IMAGEN</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupFileAddon01">
                                        <i class="fas fa-image"></i>
                                    </span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="img_pago_1"
                                        aria-describedby="inputGroupFileAddon01" name="img_pago_1"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                    <label class="custom-file-label" for="img_pago_1">Imagen</label>
                                </div>
                            </div>
                            <small class="text-muted">Formatos permitidos: JPG, JPEG, PNG. Máx:
                                4MB</small>
                            <p class="img_pago_1_error msgError"></p>
                        </div>
                        <div class="col-12"></div>
                        <div class="col-4 border d-flex align-items-center justify-content-center"
                            style="min-height: 100px;">
                            <img id="previewImage1" class="imgShowLightBox" src="{{ asset('img/default.png') }}"
                                alt="Vista previa" style="max-width: 100%; max-height: 90px; object-fit: cover;">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
                            <label for="fecha_operacion_1" style="font-weight: bold;">FECHA
                                OPERACIÓN</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-receipt"></i>
                                    </span>
                                </div>
                                <input value="{{ date('Y-m-d') }}" type="date" name="fecha_operacion_1"
                                    class="form-control fecha_operacion_1" id="fecha_operacion_1"
                                    placeholder="Fecha operación">
                            </div>
                            <p class="fecha_operacion_1_error msgError"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
