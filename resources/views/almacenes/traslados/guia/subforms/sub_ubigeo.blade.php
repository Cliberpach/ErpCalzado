<div class="row mb-3">
    
    <!-- Punto de Partida -->
    <div class="col-12">
        <div class="p-2 bg-primary text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE PARTIDA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">SEDE PARTIDA</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-building"></i>
                </span>
            </div>
            <input value="{{$sede_origen->nombre}}" readonly name="sede_origen" id="sede_origen" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
    
        <span style="font-weight: bold;color:red;" class="sede_origen_error msgError"></span>
    </div>


   

    <!-- Punto de Llegada -->
    <div class="col-12 mt-3">
        <div class="p-2 bg-success text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE LLEGADA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3" id="divSedeDestino">
        <label class="required font-weight-bold">SEDE DESTINO</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-building"></i>
                </span>
            </div>
            <input value="{{$sede_destino->nombre}}" readonly name="sede_destino" id="sede_destino" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <span style="font-weight: bold;color:red;" class="sede_destino_error msgError"></span>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divCliente" style="display: none;">
        <label class="required" for="cliente" style="font-weight: bold;">CLIENTE</label>
        <select name="cliente" id="cliente" class="select2_form">
            @foreach ($clientes as $cliente)
                <option value="{{$cliente->id}}">{{$cliente->tipo_documento.':'.$cliente->documento.'-'.$cliente->nombre}}</option>
            @endforeach
        </select>
        <span style="font-weight: bold;color:red;" class="cliente_error msgError"></span>
    </div>

   
</div>
