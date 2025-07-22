<form method="POST" action="" id="form-pedido-facturar">
    @csrf
    <div class="row">

        <div class="col-12">
            <h4><b>Datos Generales</b></h4>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label for="registrador" style="font-weight: bold;">REGISTRADOR</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-shield"></i>
                    </span>
                </div>
                <input value="{{ $registrador_nombre }}" readonly name="registrador" id="registrador" type="text"
                    class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label for="fecha_registro" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input value="{{ date('Y-m-d') }}" readonly name="fecha_registro" id="fecha_registro" type="date"
                    class="form-control">
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="almacen">
                    ALMACÉN
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                    </div>
                    <input value="{{ $almacen->descripcion }}" readonly type="text" id="almacen" name="almacen"
                        class="form-control" required placeholder="Escriba el almacén">
                </div>
            </div>
            <span style="font-weight: bold;color:red;" class="almacen_error msgError"></span>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="condicion_id">CONDICIÓN</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                    </div>
                    <input readonly value="{{ $condicion->descripcion }}" type="text" id="condicion_id"
                        name="condicion_id" class="form-control" required placeholder="Condición de venta">
                </div>
            </div>
            <span style="font-weight: bold;color:red;" class="condicion_id_error msgError"></span>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label class="required" style="font-weight: bold;">FECHA PROPUESTA</label>
            <div class="d-flex align-items-end">
                <input value="{{ $pedido->fecha_propuesta }}" readonly required type="date" class="form-control"
                    id="fecha_propuesta" name="fecha_propuesta">
            </div>
            <span style="font-weight: bold;color:red;" class="fecha_propuesta_error msgError"></span>
        </div>

        <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required">CLIENTE:</label>
                <input readonly value="{{ $pedido->cliente_nombre }}" type="text" id="cliente" name="cliente"
                    class="form-control" required placeholder="Escriba nombre o documento del cliente">
                <span style="font-weight: bold;color:red;" class="fecha_propuesta_error msgError"></span>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="comprobante">COMPROBANTE</label>
                <select data-placeholder="Seleccionar" id="comprobante" name="comprobante" class="select2_form" required>
                    <option></option>
                    @foreach ($tipos_ventas as $tipo_venta)
                        <option value="{{ $tipo_venta->id }}">
                            {{ $tipo_venta->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span style="font-weight: bold;color:red;" class="condicion_id_error msgError"></span>
        </div>

    </div>
</form>
