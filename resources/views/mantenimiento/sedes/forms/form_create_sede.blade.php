<form action="" id="formStoreSede" method="post">

    <h4 style="font-weight: bold;">DATOS EMPRESA</h4>
    <hr>

    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="ruc" class="required" style="font-weight: bold;">RUC</label>
                    <div class="input-group">
                        <input required value="" id="ruc" name="ruc" maxlength="11" type="text" class="form-control" placeholder="RUC" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="button" id="btn_consultar_documento">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <span class="ruc_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="razon_social" class="required" style="font-weight: bold;">RAZÓN SOCIAL</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-building"></i>
                        </span>
                        <input required maxlength="150" value="" name="razon_social" id="razon_social" type="text" class="form-control" placeholder="RAZÓN SOCIAL" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="razon_social_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="direccion"  style="font-weight: bold;">DIRECCIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <input required maxlength="150" value="" name="direccion" id="direccion" type="text" class="form-control" placeholder="DIRECCIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="direccion_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="telefono" style="font-weight: bold;">TELÉFONO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input maxlength="20" value="" name="telefono" id="telefono" type="text" class="form-control" placeholder="TELÉFONO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="telefono_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label for="correo" style="font-weight: bold;">CORREO</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-envelope-open-text"></i>
                        </span>
                        <input maxlength="100" value="" name="correo" id="correo" type="email" class="form-control" placeholder="CORREO" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="correo_error msgError"  style="color:red;"></span>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="department" style="font-weight: bold;">DEPARTAMENTO</label>
                    <select required name="departamento" required class="form-select select2_form" id="department" data-placeholder="Seleccionar" onchange="changeDepartment(this.value)">
                        <option></option>
                        @foreach ($departamentos as $departamento)
                            <option  value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="departamento_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="province" style="font-weight: bold;">PROVINCIA</label>
                    <select required name="provincia" required class="form-select select2_form" id="province" data-placeholder="Seleccionar" onchange="changeProvince(this.value)">
                        <option></option>
                    </select>
                    <span class="provincia_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                    <label class="required" for="district" style="font-weight: bold;">DISTRITO</label>
                    <select required name="distrito" required class="form-select select2_form" id="district" data-placeholder="Seleccionar">
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
                        <label for="img_empresa" style="font-weight:bold;" class="form-label">IMAGEN</label> <i class="fa-solid fa-trash-can btn btn-danger btnSetImageDefault"></i>
                        <input id="img_empresa" name="img_empresa" class="form-control form-control-sm"  type="file" accept="image/*">
                    </div> 
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                    <div id="img_preview_container" style="overflow-x:hidden;overflow-y:hidden;heigth:310px;width:100%;border: 2px dashed #ddd; border-radius: 10px; padding: 10px; text-align: center;display:flex;align-items:center;justify-content:center;">
                        <img class="imgShowLightBox"
                        {{-- @if ($empresa->img_ruta)
                            src="{{asset($empresa->img_ruta)}}"
                        @else 
                            src="{{asset('img/img_default.png')}}"
                        @endif  --}}
                        id="img_vista_previa" style="height: 260px;max-width:260px; object-fit: cover;cursor:pointer;">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                    <label for="urbanizacion" style="font-weight: bold;">URBANIZACIÓN</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-road"></i>
                        </span>
                        <input maxlength="100" value="" name="urbanizacion" id="urbanizacion" type="email" class="form-control" placeholder="URBANIZACIÓN" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="urbanizacion_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="required" for="codigo_local" style="font-weight: bold;">CÓDIGO LOCAL</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-city"></i>
                        </span>
                        <input required maxlength="100" value="" name="codigo_local" id="codigo_local" type="text" class="form-control" placeholder="CÓDIGO LOCAL" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <span class="codigo_local_error msgError"  style="color:red;"></span>
                </div>
            </div>
        </div>

        

        <div class="col-12 mb-3">
            <h4 style="font-weight: bold;">FACTURACIÓN</h4>
            <hr>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="usuario_sol" class="" style="font-weight: bold;">USUARIO SOL</label>
                    <div class="input-group">
                        <input  value="" id="usuario_sol" name="usuario_sol"  type="text" class="form-control" placeholder="Usuario Sol" aria-label="Recipient's username" aria-describedby="button-addon2">
                    </div>
                    <span class="ruc_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="clave_sol" class="" style="font-weight: bold;">CLAVE SOL</label>
                    <div class="input-group">
                        <input  value="" id="clave_sol" name="clave_sol" type="text" class="form-control" placeholder="Contraseña Sol" aria-label="Recipient's username" aria-describedby="button-addon2">
                    </div>
                    <span class="ruc_error msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="id_api_guia" class="" style="font-weight: bold;">ID API GUÍA REMISIÓN</label>
                    <div class="input-group">
                        <input  value="" id="id_api_guia" name="id_api_guia"  type="text" class="form-control" placeholder="ID API GUÍA" aria-label="Recipient's username" aria-describedby="button-addon2">
                    </div>
                    <span class="id_api_guia msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="clave_api_guia" class="" style="font-weight: bold;">CLAVE API GUÍA REMISIÓN</label>
                    <div class="input-group">
                        <input  value="" id="clave_api_guia" name="clave_api_guia"  type="text" class="form-control" placeholder="CLAVE API GUÍA" aria-label="Recipient's username" aria-describedby="button-addon2">
                    </div>
                    <span class="clave_api_guia msgError"  style="color:red;"></span>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="" style="font-weight: bold;">CERTIFICADO(PEM)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="inputGroupFileAddon01">
                            <i class="fas fa-file-archive"></i>
                          </span>
                        </div>
                        <div class="custom-file">
                          <input accept=".pem" name="certificado"  type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                          <label class="custom-file-label" for="inputGroupFile01">Seleccionar certificado</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary"> <i class="fas fa-save"></i> GUARDAR </button>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12 mt-3 mb-3">
            <div class="card">
                <div class="card-header" style="background-color: rgb(0, 102, 255);font-weight:bold;color:white;">
                DATOS DE GUÍA DE REMISIÓN
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="usuario_sol" class="" style="font-weight: bold;">USUARIO SOL</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/user/user1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->usuario_sol}}" maxlength="100" type="text" name="usuario_sol" id="usuario_sol" class="form-control">
                            </div>
                            <span class="usuario_sol_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="clave_sol" class="" style="font-weight: bold;">CLAVE SOL</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/password/password1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->clave_sol}}" value="" maxlength="100" type="text" name="clave_sol" id="clave_sol" class="form-control">
                            </div>
                            <span class="clave_sol_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="usuario_api_guias" class="" style="font-weight: bold;">USUARIO API</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/api/api1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->usuario_api_guias}}" value="" maxlength="100" type="text" name="usuario_api_guias" id="usuario_api_guias" class="form-control">
                            </div>
                            <span class="usuario_api_guias_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="clave_api_guias" class="" style="font-weight: bold;">CLAVE API</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/password/password1.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->clave_api_guias}}" maxlength="100" type="text" name="clave_api_guias" id="clave_api_guias" class="form-control">
                            </div>
                            <span class="clave_api_guias_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <div class="">
                                <label for="formFileSm" class="form-label" style="font-weight: bold;">CERTIFICADO PEM</label> <i class="fa-solid fa-trash-can btn btn-danger btnDeleteCertificado"></i>
                                <input accept=".pem" class="form-control form-control-sm" id="certificado" name="certificado" type="file">

                                @if (!$empresa->certificado_ruta)
                                    <p style="margin:0; color:blue; font-style:italic;" class="certificado_previo">SIN CERTIFICADO</p>
                                @else 
                                    <p style="margin:0; color:blue; font-style:italic;" class="certificado_previo">
                                        {{$empresa->certificado_nombre}}
                                    </p>
                                @endif
                            </div>
                            <span class="certificado_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="nro_inicio" class="required_field" style="font-weight: bold;">N° INICIO</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/hash/hash2.png')}}" alt="coins"/>
                                </span>
                                <input value="{{$empresa->nro_inicio}}" value="" maxlength="100" type="text" name="nro_inicio" id="nro_inicio" class="form-control inputEnteroPositivo">
                            </div>
                            <span class="nro_inicio_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="serie" style="font-weight: bold;">SERIE</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/hash/hash2.png')}}" alt="coins"/>
                                </span>
                                <input disabled value="{{$empresa->serie}}" value="" maxlength="100" type="text" name="serie" id="serie" class="form-control">
                            </div>
                            <span class="serie_error msgError"  style="color:red;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="estado" style="font-weight: bold;">ESTADO</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <img width="30" height="30" src="{{asset('img/icons/estado/estado1.png')}}" alt="coins"/>
                                </span>
                                <input disabled value="{{ $empresa->iniciado == 1 ? 'INICIADO' : 'SIN INICIAR' }}" maxlength="100" type="text" name="estado" id="estado" class="form-control">
                            </div>
                            <span class="estado_error msgError"  style="color:red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

</form>