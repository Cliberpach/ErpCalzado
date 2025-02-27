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
        <select class="form-control select2_form" name="sede_origen" id="sede_origen">
            @foreach ($sedes as $sede)
                <option value="{{$sede->id}}">{{$sede->nombre}}</option>
            @endforeach
        </select>
    </div>


   

    <!-- Punto de Llegada -->
    <div class="col-12 mt-3">
        <div class="p-2 bg-success text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE LLEGADA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">SEDE DESTINO</label>
        <select class="form-control select2_form" name="sede_destino" id="sede_destino">
            <option value=""></option>
            @foreach ($sedes as $sede)
                <option value="{{$sede->id}}">{{$sede->nombre}}</option>
            @endforeach
        </select>
    </div>

   
</div>
