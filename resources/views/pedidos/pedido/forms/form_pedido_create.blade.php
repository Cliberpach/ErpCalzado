<form  method="POST" action=""
id="form-pedido">
@csrf
<div class="row">

    <div class="col-12">
        <h4><b>Datos Generales</b></h4>
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

    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
        <div class="form-group">
            <label style="font-weight: bold;" class="required" for="condicion_id">ALMACÉN</label>
            <select onchange="cambiarAlmacen(this.value)"  id="almacen" name="almacen" class="select2_form form-control" required>
                <option></option>
                @foreach ($almacenes as $almacen)
                    <option
                    @if ($almacen->sede_id == $sede_id)
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
        <div class="form-group">
            <label style="font-weight: bold;" class="required" for="condicion_id">CONDICIÓN</label>
            <select id="condicion_id" name="condicion_id" class="select2_form form-control" required>
                <option></option>
                @foreach ($condiciones as $condicion)
                    <option value="{{ $condicion->id }}">
                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' días' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <span style="font-weight: bold;color:red;" class="condicion_id_error msgError"></span>
    </div>

    <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12" style="display: flex;flex-direction:column;justify-content:center;">
        <label class="required" style="font-weight: bold;">FECHA PROPUESTA</label>
        <div class="d-flex align-items-end">
            <input required type="date" class="form-control" id="fecha_propuesta" name="fecha_propuesta">
        </div>
        <span style="font-weight: bold;color:red;" class="fecha_propuesta_error msgError"></span>
    </div>

    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 select-required">
        <div class="form-group">
            <label class="required">Cliente:
                <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                    Registrar
                </button>
            </label>
            <select id="cliente" name="cliente"
                class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                 required>
                <option></option>
                @foreach ($clientes as $cliente)
                    <option @if ($cliente->id == 1) selected @endif 
                    value="{{ $cliente->id }}">
                    {{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
            <span style="font-weight: bold;color:red;" class="fecha_propuesta_error msgError"></span>
        </div>
    </div>
</div>
</form>