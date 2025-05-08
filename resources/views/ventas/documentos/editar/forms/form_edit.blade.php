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

    <div class="row">

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
    
        <div class="col-12 col-lg-3 col-md-3 mb-3">
            <label for="fecha_registro" style="font-weight: bold;">FECHA REGISTRO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
                <input value="{{ $documento->created_at}}" readonly name="fecha_registro" id="fecha_registro" type="text" class="form-control">
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
                <input value="{{ $documento->fecha_vencimiento}}" readonly name="fecha_vencimiento" id="fecha_vencimiento" type="date" class="form-control">
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
                <input value="{{ $documento->serie.'-'.$documento->correlativo}}" readonly name="documento" id="documento" type="text" class="form-control">
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
                <input value="{{ $sede->nombre}}" readonly name="sede" id="sede" type="text" class="form-control">
            </div>
        </div>


        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="almacen">ALMACÉN</label>
                <select onchange="cambiarAlmacen(this.value)" id="almacen" name="almacen" class="select2_form form-control" required>
                    <option></option>
                    @foreach ($almacenes as $almacen)
                        <option
                        @if ($almacen->id == $documento->almacen_id)
                            selected
                        @endif
                        value="{{ $almacen->id }}">
                            {{ $almacen->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span style="font-weight: bold;color:red;" class="almacen_error msgError"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label style="font-weight: bold;" class="required" for="condicion_id">CONDICIÓN</label>
            <select onchange="cambiarCondicion(this.value);" id="condicion_id" name="condicion_id" class="select2_form form-control" required>
                <option></option>
                @foreach ($condiciones as $condicion)
                    <option value="{{ $condicion->id }}"
                    @if ($condicion->id == $documento->condicion_id)
                        selected
                    @endif    
                    >
                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' días' : '' }}
                    </option>
                @endforeach
            </select>
            <span style="font-weight: bold;color:red;" class="condicion_id_error msgError"></span>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label for="cliente" class="required" style="font-weight: bold;">Cliente</label>
            <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">Registrar</button>
            <select id="cliente" name="cliente" class="" required>
                <option></option>
                <option value="{{ $cliente->id }}" selected>{{ $cliente->descripcion }}</option>
            </select>   
            <span style="font-weight: bold;color:red;" class="cliente_error msgError"></span>                       
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <label style="font-weight: bold;" class="required" for="tipo_venta">COMPROBANTE</label>
            <select disabled id="tipo_venta" name="tipo_venta" class="select2_form form-control" required>
                <option></option>
                @foreach ($tipos_venta as $tipo_venta)
                    <option value="{{ $tipo_venta->id }}"
                    @if ($tipo_venta->id == $documento->tipo_venta_id)
                        selected
                    @endif    
                    >
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

    </div>

</form>