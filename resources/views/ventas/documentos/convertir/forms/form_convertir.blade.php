<form action="" method="POST" id="enviar_documento">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-12">
            <h4 class=""><b>Documento de venta</b></h4>
            <div class="row">
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="sede" style="font-weight: bold;">SEDE DEL DOCUMENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-building"></i>
                    </span>
                </div>
                <input value="{{$sede->nombre}}" readonly name="sede" id="sede" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
       
        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="registrador" style="font-weight: bold;">REGISTRADOR</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-shield"></i>
                    </span>
                </div>
                <input value="{{$registrador->usuario}}" readonly name="registrador" id="registrador" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <!-- Fecha Registro -->
        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="fecha_registro" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input value="{{ date('Y-m-d') }}" readonly name="fecha_registro" id="fecha_registro" type="date" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="almacen" style="font-weight: bold;">ALMACÉN DEL DOCUMENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-warehouse"></i>
                    </span>
                </div>
                <input value="{{$almacen->descripcion}}" readonly name="almacen" id="almacen" type="text" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="cliente" style="font-weight: bold;">CLIENTE</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-tag"></i>
                    </span>
                </div>
                <input value="{{$cliente->tipo_documento.':'.$cliente->documento.'-'.$cliente->nombre}}" readonly name="cliente" id="cliente" type="text" class="form-control">
            </div>
        </div>

        
        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="documento" style="font-weight: bold;">DOCUMENTO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                </div>
                <input value="{{$documento->serie.'-'.$documento->correlativo}}" readonly name="documento" id="documento" type="text" class="form-control">
            </div>
        </div>

        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="tipo_comprobante" style="font-weight: bold;">COMPROBANTE:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-tag"></i>
                    </span>
                </div>
                <input value="{{$tipo_comprobante->descripcion}}" readonly name="tipo_comprobante" id="tipo_comprobante" type="text" class="form-control">
            </div>
        </div>



    </div>

</form>