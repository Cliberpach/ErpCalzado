<div class="row mb-3">

    <div class="col-12">
        <i class="fas fa-truck"></i> <label for="" style="font-weight: bold;">TRANSPORTISTA</label>
        <hr style="margin:0px 0 10px 0;">
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
        <label class="required" for="conductor" style="font-weight: bold;">CONDUCTOR</label>
        <select required name="conductor" required class="form-select select2_form" id="conductor" data-placeholder="Seleccionar">
            <option></option>
            @foreach ($conductores as $conductor)
                
                <option value="{{$conductor->id}}">{{$conductor->nombre}}</option>
             
            @endforeach
        </select>
        <span class="tipo_documento_error msgError"  style="color:red;"></span>
    </div>

    

  
</div>