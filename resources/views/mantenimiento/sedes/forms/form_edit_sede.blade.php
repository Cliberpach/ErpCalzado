<form action="" id="formActualizarSede" method="post">

    <h4 style="font-weight: bold;">DATOS SEDE</h4>
    <hr>

    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
            <div class="row">
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="nombre"  style="font-weight: bold;">NOMBRE</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-file-signature"></i>
                        </span>
                        <input value="{{$sede->nombre}}" required maxlength="160" value="" name="nombre" id="nombre" type="text" class="form-control" placeholder="NOMBRE" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="nombre_error msgError"  style="color:red;"></span>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="direccion"  style="font-weight: bold;">DIRECCIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <input value="{{$sede->direccion}}" required maxlength="150" value="" name="direccion" id="direccion" type="text" class="form-control" placeholder="DIRECCIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="direccion_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="telefono" style="font-weight: bold;">TELÉFONO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input value="{{$sede->telefono}}" maxlength="20" value="" name="telefono" id="telefono" type="text" class="form-control" placeholder="TELÉFONO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="telefono_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="correo" style="font-weight: bold;">CORREO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-envelope-open-text"></i>
                        </span>
                        <input value="{{$sede->correo}}" maxlength="100" value="" name="correo" id="correo" type="email" class="form-control" placeholder="CORREO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="correo_error msgError"  style="color:red;"></span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="departamento" style="font-weight: bold;">DEPARTAMENTO</label>
                    <select required name="departamento" required class="form-select select2_form" id="departamento" data-placeholder="Seleccionar" onchange="changeDepartment(this.value)">
                        <option></option>
                        @foreach ($departamentos as $departamento)
                            <option  value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="departamento_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="province" style="font-weight: bold;">PROVINCIA</label>
                    <select required name="provincia" required class="form-select select2_form" id="provincia" data-placeholder="Seleccionar" onchange="changeProvince(this.value)">
                        <option></option>
                    </select>
                    <span class="provincia_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="district" style="font-weight: bold;">DISTRITO</label>
                    <select required name="distrito" required class="form-select select2_form" id="distrito" data-placeholder="Seleccionar">
                        <option></option>
                    </select>
                    <span class="distrito_error msgError"  style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3 d-flex justify-content-center">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                    <div>
                        <label for="img_empresa" style="font-weight:bold;" class="form-label">IMAGEN</label><i class="fa-solid fa-trash-can btn btn-danger btnSetImageDefault" onclick="resetImage()"></i>
                        <input id="img_empresa" name="img_empresa" class="form-control"  type="file" accept="image/*" onchange="previewImage(event)">
                    </div> 
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <div id="img_preview_container" style="overflow-x:hidden;overflow-y:hidden;heigth:310px;width:100%;border: 2px dashed #ddd; border-radius: 10px; padding: 10px; text-align: center;display:flex;align-items:center;justify-content:center;">
                        <img class="imgShowLightBox" 
                        src="{{ $sede->logo_ruta ? asset('storage/' . ltrim($sede->logo_ruta, '/')) : asset('img/img_default.png') }}"
                        id="img_vista_previa" style="height: 260px;max-width:260px; object-fit: cover;cursor:pointer;">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="urbanizacion" style="font-weight: bold;">URBANIZACIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-road"></i>
                        </span>
                        <input value="{{$sede->urbanizacion}}" maxlength="100" value="" name="urbanizacion" id="urbanizacion" type="email" class="form-control" placeholder="URBANIZACIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="urbanizacion_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="required" for="codigo_local" style="font-weight: bold;">CÓDIGO LOCAL</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-city"></i>
                        </span>
                        <input value="{{$sede->codigo_local}}" required maxlength="100" value="" name="codigo_local" id="codigo_local" type="text" class="form-control" placeholder="CÓDIGO LOCAL" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="codigo_local_error msgError"  style="color:red;"></span>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary"> <i class="fas fa-save"></i> ACTUALIZAR </button>
        </div>
    </div>
</form>