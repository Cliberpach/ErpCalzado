<form action="" id="formRegistrarColaborador" method="post">    
    <div class="row">
            @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
                <select required name="tipo_documento" required class="form-select select2_form" id="tipo_documento" data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                    <option></option>
                    @foreach (tipos_documento() as $tipo_documento)
                        @if ($tipo_documento->simbolo != 'RUC')
                            <option value="{{$tipo_documento->id}}">{{$tipo_documento->descripcion}}</option>
                        @endif
                    @endforeach
                </select>
                <span class="tipo_documento_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nro_documento" style="font-weight: bold;" class="required">Nro Doc</label>
                <div class="input-group mb-3">
                    <button id="btn_consultar_documento" disabled class="btn btn-primary" type="button" id="button-addon1">
                        <i class="fas fa-search"></i>
                    </button>
                    <input required readonly id="nro_documento" name="nro_documento" type="text" class="form-control" placeholder="Nro de Documento" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>                 
                <span class="nro_documento_error msgError"  style="color:red;"></span>
            </div> 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="sede" style="font-weight: bold;">SEDE</label>
                <select required name="sede" required class="form-select select2_form" id="sede" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($sedes as $sede)
                        <option value="{{$sede->id}}">{{$sede->nombre}}</option>
                    @endforeach
                </select>
                <span class="cargo_error msgError"  style="color:red;"></span>
            </div>   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nombre" style="font-weight: bold;" class="required">Nombre</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-file-signature"></i>                   
                    </span>
                    <input required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="cargo" style="font-weight: bold;">CARGO</label>
                <select required name="cargo" required class="form-select select2_form" id="cargo" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach (cargos() as $cargo)
                        <option value="{{$cargo->id}}">{{$cargo->descripcion}}</option>
                    @endforeach
                </select>
                <span class="cargo_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label  for="direccion" style="font-weight: bold;">Dirección</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-map-marked-alt"></i>                    
                    </span>
                    <input maxlength="200"  id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
                </div>                   
                <span class="direccion_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="telefono" style="font-weight: bold;">Teléfono</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input maxlength="20"  id="telefono" name="telefono" type="text" class="form-control" placeholder="Teléfono" aria-label="Username" aria-describedby="basic-addon1">
                </div>                 
                <span class="telefono_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="dias_trabajo" style="font-weight: bold;">Días Trabajo</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    <input required maxlength="20" id="dias_trabajo" name="dias_trabajo" type="text" class="form-control" placeholder="Días de trabajo" aria-label="Username" aria-describedby="basic-addon1">
                </div>                
                <span class="dias_trabajo_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="dias_descanso" style="font-weight: bold;">Días Descanso</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="far fa-clock"></i>
                    </span>
                    <input required maxlength="20" id="dias_descanso" name="dias_descanso" type="text" class="form-control" placeholder="Días de descanso" aria-label="Username" aria-describedby="basic-addon1">
                </div>                
                <span class="dias_descanso_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="pago_mensual" style="font-weight: bold;">Pago Mensual</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
                    <input maxlength="10" name="pago_mensual" id="pago_mensual" type="text" class="form-control" placeholder="Pago mensual" aria-label="Username" aria-describedby="basic-addon1">
                </div>       
                <span class="pago_mensual_error msgError" style="color:red;"></span>
            </div>
    </div>
</form> 