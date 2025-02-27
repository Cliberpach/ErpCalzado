<div class="row mb-3" id="divTransportista">

    <div class="col-12">
        <div class="p-2 bg-success text-white rounded shadow-sm">
            <i class="fas fa-truck"></i> <label for="" style="font-weight: bold;">TRANSPORTISTA</label>
        </div>
        <hr class="mt-2 mb-3">
    </div>


    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
        <label class="required" for="conductor" style="font-weight: bold;">VEHÍCULO</label>
        <select required name="vehiculo" required class="form-select select2_form" id="vehiculo" data-placeholder="Seleccionar">
            <option></option>
            @foreach ($vehiculos as $vehiculo)
                
                <option value="{{$vehiculo->id}}">{{$vehiculo->placa.'-'.$vehiculo->modelo}}</option>
             
            @endforeach
        </select>
        <span class="vehiculo_error msgError"  style="color:red;"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
        <label class="required" for="conductor" style="font-weight: bold;">CONDUCTOR</label>
        <select required name="conductor" required class="form-select select2_form" id="conductor" data-placeholder="Seleccionar">
            <option></option>
            @foreach ($conductores as $conductor)
                
                <option value="{{$conductor->id}}">{{$conductor->tipo_documento_nombre.':'.$conductor->nro_documento.'-'.$conductor->nombre_completo}}</option>
             
            @endforeach
        </select>
        <span class="conductor_error msgError"  style="color:red;"></span>
    </div>

    

  
</div>