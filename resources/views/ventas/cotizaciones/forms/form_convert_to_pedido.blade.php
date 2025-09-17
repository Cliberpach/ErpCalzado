<form action="" method="POST" id="form-convert-to-pedido">
    @csrf
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Datos Generales</h5>
        </div>
        <div class="card-body">
            <div class="row">

                <!-- Registrador -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-shield fa-lg text-primary mr-2"></i>
                        <div>
                            <small class="text-muted">REGISTRADOR</small><br>
                            <span class="font-weight-bold">{{ $cotizacion->registrador_nombre }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fecha Registro -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-lg text-success mr-2"></i>
                        <div>
                            <small class="text-muted">FECHA REGISTRO</small><br>
                            <span class="font-weight-bold">{{ date('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Almacén -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-warehouse fa-lg text-warning mr-2"></i>
                        <div>
                            <small class="text-muted">ALMACÉN</small><br>
                            <span class="font-weight-bold">{{ $cotizacion->almacen_nombre }}</span>
                        </div>
                    </div>
                </div>

                <!-- Condición -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-contract fa-lg text-info mr-2"></i>
                        <div>
                            <small class="text-muted">CONDICIÓN</small><br>
                            <span class="font-weight-bold">{{ $condicion->descripcion }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cliente -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-tie fa-lg text-danger mr-2"></i>
                        <div>
                            <small class="text-muted">CLIENTE</small><br>
                            <span class="font-weight-bold">{{ $cotizacion->cliente_nombre }}</span>
                        </div>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone fa-lg text-success mr-2"></i>
                        <div>
                            <small class="text-muted">TELÉFONO</small><br>
                            <span class="font-weight-bold">{{ $cotizacion->telefono }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fecha Propuesta de Entrega (opcional) -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <label for="fecha_propuesta" style="font-weight: bold;">FECHA PROPUESTA DE ENTREGA</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-primary">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                        </div>
                        <input type="date" class="form-control" id="fecha_propuesta" name="fecha_propuesta"
                            value="">
                    </div>
                </div>

                <!-- Observación -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                    <textarea id="observacion" name="observacion" class="form-control" rows="2" maxlength="200"
                        placeholder="Ingrese una observación (máximo 200 caracteres)"></textarea>
                    <small class="form-text text-muted">Máx. 200 caracteres</small>
                </div>

            </div>
        </div>
    </div>

    <div class="hr-line-dashed"></div>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><b>Detalle de la Cotización</b></h4>
                </div>
                <div class="panel-body">

                    @include('ventas.cotizaciones.tables.tbl_convert_detalle')

                    <div class="col-12 d-flex justify-content-end">
                        <div class="table-responsive">
                            @include('ventas.cotizaciones.tables.tbl_convert_montos')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
