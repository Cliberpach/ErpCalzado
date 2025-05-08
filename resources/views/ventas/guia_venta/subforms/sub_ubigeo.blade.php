<div class="row mb-3">
    
    <!-- Punto de Partida -->
    <div class="col-12">
        <div class="p-2 bg-primary text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE PARTIDA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">SEDE DEL ALMACÉN DE LA VENTA</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-building"></i>
                </span>
            </div>
            <input value="{{$sede_origen->nombre}}" readonly name="sede_origen" id="sede_origen" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        
        {{-- <select required class="form-control select2_form" name="sede_origen" id="sede_origen">
            @foreach ($sedes as $sede)
                <option
                @if ($sede_id == $sede->id)
                    selected
                @endif
                value="{{$sede->id}}">{{$sede->nombre}}</option>
            @endforeach
        </select> --}}
        <span style="font-weight: bold;color:red;" class="sede_origen_error msgError"></span>
    </div>


   

    <!-- Punto de Llegada -->
    <div class="col-12 mt-3">
        <div class="p-2 bg-success text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE LLEGADA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    {{-- <div class="col-lg-4 col-md-4 col-sm-6 mb-3" id="divSedeDestino">
        <label class="required font-weight-bold">SEDE DESTINO</label>
        <select required class="form-control select2_form" name="sede_destino" id="sede_destino">
            <option value=""></option>
            @foreach ($sedes as $sede)
                <option value="{{$sede->id}}">{{$sede->nombre}}</option>
            @endforeach
        </select>
        <span style="font-weight: bold;color:red;" class="sede_destino_error msgError"></span>
    </div> --}}

    <div class="col-12 col-lg-6 col-md-6 mb-3">
        <label for="cliente" style="font-weight: bold;">CLIENTE</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-user-shield"></i>
                </span>
            </div>
            <input value="{{$cliente->tipo_documento.':'.$cliente->documento.'-'.$cliente->nombre}}" readonly name="cliente" id="cliente" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <span style="font-weight: bold;color:red;" class="cliente_error msgError"></span>
    </div>

    <div class="col-12 col-lg-6 col-md-6 mb-3">
        <label for="cliente_ubigeo" style="font-weight: bold;">UBIGEO</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-user-shield"></i>
                </span>
            </div>
            <input value="{{$cliente->departamento_nombre.'-'.$cliente->provincia_nombre.'-'.$cliente->distrito_nombre}}" readonly name="cliente_ubigeo" id="cliente_ubigeo" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <span style="font-weight: bold;color:red;" class="cliente_ubigeo_error msgError"></span>
    </div>
    <div class="col-12 col-lg-3 col-md-3 mb-3">
        <label for="cliente_codigo_ubigeo" style="font-weight: bold;">CÓDIGO UBIGEO</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-user-shield"></i>
                </span>
            </div>
            <input value="{{$cliente->distrito_id}}" readonly name="cliente_codigo_ubigeo" id="cliente_codigo_ubigeo" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <span style="font-weight: bold;color:red;" class="cliente_codigo_ubigeo_error msgError"></span>
    </div>
    <div class="col-12 col-lg-9 col-md-9 mb-3">
        <label for="cliente_direccion" style="font-weight: bold;">DIRECCIÓN</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-user-shield"></i>
                </span>
            </div>
            <input value="{{$cliente->direccion}}" readonly name="cliente_direccion" id="cliente_direccion" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <span style="font-weight: bold;color:red;" class="cliente_direccion_error msgError"></span>
    </div>

   
</div>
