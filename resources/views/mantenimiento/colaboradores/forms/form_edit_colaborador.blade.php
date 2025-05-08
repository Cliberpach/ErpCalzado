<form action="" id="formActualizarColaborador" method="post">    
    <div class="row">
            @csrf   
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="tipo_documento" style="font-weight: bold;">TIPO DOCUMENTO</label>
                <select required name="tipo_documento" required class="form-select select2_form" id="tipo_documento" data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                    <option></option>
                    @foreach (tipos_documento() as $tipo_documento)

                        @if ($tipo_documento->simbolo != 'RUC')
                            <option value="{{$tipo_documento->id}}" 
                                @if ($colaborador->tipo_documento_id == $tipo_documento->id)
                                    selected
                                @endif
                            >{{$tipo_documento->descripcion}}</option>
                        @endif
                    @endforeach
                </select>
                <span class="tipo_documento_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="nro_documento" style="font-weight: bold;" class="required">Nro Doc</label>
                <div class="input-group mb-3">
                    <button
                        @if ($colaborador->tipo_documento_id == 2)
                            disabled
                        @endif
                     id="btn_consultar_documento" class="btn btn-primary" type="button" id="button-addon1">
                        <i class="fas fa-search"></i>
                    </button>
                    <input
                        @if (!$colaborador->tipo_documento_id)
                            readonly
                        @endif
                        value="{{$colaborador->nro_documento}}" 
                        @if ($colaborador->tipo_documento_id == 6)
                            maxlength='8'
                        @endif
                        @if ($colaborador->tipo_documento_id != 6)
                            maxlength='20'
                        @endif
                     required id="nro_documento" name="nro_documento" type="text" class="form-control" placeholder="Nro de Documento" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>                 
                <span class="nro_documento_error msgError"  style="color:red;"></span>
            </div>  
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="sede" style="font-weight: bold;">SEDE</label>
                <select required name="sede" required class="form-select select2_form" id="sede" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach ($sedes as $sede)
                        <option
                        @if ($sede->id == $colaborador->sede_id)
                            selected
                        @endif
                         value="{{$sede->id}}">{{$sede->nombre}}</option>
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
                    <input value="{{$colaborador->nombre}}" required id="nombre" maxlength="260"  name="nombre" type="text" class="form-control" placeholder="Nombre" aria-label="Username" aria-describedby="basic-addon1">
                </div>                  
                <span class="nombre_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="cargo" style="font-weight: bold;">CARGO</label>
                <select required name="cargo" required class="form-select select2_form select2_form" id="cargo" data-placeholder="Seleccionar">
                    <option></option>
                    @foreach (cargos() as $cargo)
                        <option value="{{$cargo->id}}" 
                            @if ($cargo->id == $colaborador->cargo_id)
                                selected
                            @endif
                        >{{$cargo->descripcion}}</option>
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
                    <input value="{{$colaborador->direccion}}" maxlength="200"  id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección" aria-label="Username" aria-describedby="basic-addon1">
                </div>                   
                <span class="direccion_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label for="telefono" style="font-weight: bold;">Teléfono</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input value="{{$colaborador->telefono}}" maxlength="20"  id="telefono" name="telefono" type="text" class="form-control" placeholder="Teléfono" aria-label="Username" aria-describedby="basic-addon1">
                </div>                 
                <span class="telefono_error msgError"  style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="dias_trabajo" style="font-weight: bold;">Días Trabajo</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    <input value="{{$colaborador->dias_trabajo}}" required maxlength="20" id="dias_trabajo" name="dias_trabajo" type="text" class="form-control" placeholder="Días de trabajo" aria-label="Username" aria-describedby="basic-addon1">
                </div>                
                <span class="dias_trabajo_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label class="required" for="dias_descanso" style="font-weight: bold;">Días Descanso</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="far fa-clock"></i>
                    </span>
                    <input value="{{$colaborador->dias_descanso}}" required maxlength="20" id="dias_descanso" name="dias_descanso" type="text" class="form-control" placeholder="Días de descanso" aria-label="Username" aria-describedby="basic-addon1">
                </div>                
                <span class="dias_descanso_error msgError" style="color:red;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
                <label  for="pago_mensual" style="font-weight: bold;">Pago Mensual</label>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
                    <input value="{{$colaborador->pago_mensual}}" maxlength="10" name="pago_mensual" id="pago_mensual" type="text" class="form-control" placeholder="Pago mensual" aria-label="Username" aria-describedby="basic-addon1">
                </div>       
                <span class="pago_mensual_error msgError" style="color:red;"></span>
            </div>
    </div>
</form> 