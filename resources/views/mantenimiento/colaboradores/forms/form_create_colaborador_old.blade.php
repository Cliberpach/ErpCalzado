<form class="wizard-big" action="{{ route('mantenimiento.colaborador.create') }}" method="POST" enctype="multipart/form-data" id="form_registrar_empleado">
    @csrf
    <h1>Datos Personales</h1>
    <fieldset>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required" style="font-weight: bold;" >Tipo de documento</label>
                <select id="tipo_documento" name="tipo_documento" class="select2_form form-control {{ $errors->has('tipo_documento') ? ' is-invalid' : '' }}">
                    <option></option>
                    @foreach(tipos_documento() as $tipo_documento)
                        @if ($tipo_documento->simbolo != 'RUC')
                            <option value="{{ $tipo_documento->simbolo }}" {{ (old('tipo_documento') == $tipo_documento->simbolo ? "selected" : "") }} >{{ $tipo_documento->descripcion }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12" id="documento_requerido">
                <label class="required">Nro. Documento : </label>
                <div class="input-group">
                    <input type="text" class="form-control {{ $errors->has('documento') ? ' is-invalid' : '' }}" maxlength="8" name="documento" id="documento" value="{{old('documento')}}">
                    <span class="input-group-append"><a style="color:white" onclick="consultarDocumento()" class="btn btn-primary"><i class=    "fa fa-search"></i> Reniec</a></span>
                    @if ($errors->has('documento'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('documento') }}</strong>
                    </span>
                    @endif
                    <div class="invalid-feedback"><b><span id="error-documento"></span></b></div>
                </div>
            </div>
            <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">

            <div class="form-group col-lg-4 col-xs-12">
                <label class="">Estado: </label>
                <input type="text" id="estado_documento"
                    class="form-control text-center {{ $errors->has('estado_documento') ? ' is-invalid' : '' }}"
                    name="estado_documento" value="{{old('estado_documento',"SIN VERIFICAR")}}"
                    onkeyup="return mayus(this)" disabled>
                @if ($errors->has('estado_documento'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('estado_documento') }}</strong>
                </span>
                @endif
            </div>


        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Nombre(s)</label>
                <input type="text" id="nombres" name="nombres" class="form-control {{ $errors->has('nombres') ? ' is-invalid' : '' }}" value="{{old('nombres')}}" maxlength="100" onkeyup="return mayus(this)" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Apellido paterno</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control {{ $errors->has('apellido_paterno') ? ' is-invalid' : '' }}" value="{{old('apellido_paterno')}}" onkeyup="return mayus(this)" maxlength="100" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Apellido materno</label>
                <input type="text" id="apellido_materno" name="apellido_materno" class="form-control {{ $errors->has('apellido_materno') ? ' is-invalid' : '' }}" value="{{old('apellido_materno')}}" onkeyup="return mayus(this)" maxlength="100" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12" id="fecha_nacimiento">
                <label class="required">Fecha de Nacimiento</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control {{ $errors->has('fecha_nacimiento') ? ' is-invalid' : '' }}" value="{{old('fecha_nacimiento')}}" autocomplete="off" required >
                </div>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Sexo</label>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="radio">
                            <input type="radio" name="sexo" id="sexo_hombre" value="H" checked="">
                            <label for="sexo_hombre">
                                Hombre
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="radio">
                            <input type="radio" name="sexo" id="sexo_mujer" value="M">
                            <label for="sexo_mujer">
                                Mujer
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label>Estado Civil</label>
                <select id="estado_civil" name="estado_civil" class="select2_form form-control {{ $errors->has('estado_civil') ? ' is-invalid' : '' }}">
                    <option></option>
                    @foreach(estados_civiles() as $estado_civil)
                        <option value="{{ $estado_civil->simbolo }}" {{ (old('estado_civil') == $estado_civil->simbolo ? "selected" : "") }}>{{ $estado_civil->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="m-t-md col-lg-8">
                <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (*) son obligatorios.</small>
            </div>
        </div>
    </fieldset>
    <h1>Datos de Contacto</h1>
    <fieldset>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Departamento</label>
                <select id="departamento" name="departamento" class="select2_form form-control {{ $errors->has('departamento') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(departamentos() as $departamento)
                        <option value="{{ $departamento->id }}" {{ (old('departamento') == $departamento->id ? "selected" : "") }} >{{ $departamento->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Provincia</label>
                <select id="provincia" name="provincia" class="select2_form form-control {{ $errors->has('provincia') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Distrito</label>
                <select id="distrito" name="distrito" class="select2_form form-control {{ $errors->has('distrito') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-12 col-xs-12">
                <label class="required">Dirección completa</label>
                <input type="text" id="direccion" name="direccion" class="form-control {{ $errors->has('direccion') ? ' is-invalid' : '' }}" value="{{old('direccion')}}" maxlength="191" onkeyup="return mayus(this)" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Correo electrónico</label>
                <input type="email" id="correo_electronico" name="correo_electronico" class="form-control {{ $errors->has('correo_electronico') ? ' is-invalid' : '' }}" value="{{old('correo_electronico')}}" maxlength="100" onkeyup="return mayus(this)" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Teléfono móvil</label>
                <input type="text" id="telefono_movil" name="telefono_movil" class="form-control {{ $errors->has('telefono_movil') ? ' is-invalid' : '' }}" value="{{old('telefono_movil')}}" onkeypress="return isNumber(event)" maxlength="9" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label>Teléfono fijo</label>
                <input type="text" id="telefono_fijo" name="telefono_fijo" class="form-control {{ $errors->has('telefono_fijo') ? ' is-invalid' : '' }}" value="{{old('telefono_fijo')}}" onkeypress="return isNumber(event)" maxlength="10">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Correo Corporativo</label>
                <input type="text" id="correo_corporativo" name="correo_corporativo" class="form-control {{ $errors->has('correo_corporativo') ? ' is-invalid' : '' }}" value="{{old('correo_corporativo')}}"  required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Telefono de Trabajo</label>
                <input type="text" id="telefono_trabajo" name="telefono_trabajo" class="form-control {{ $errors->has('telefono_trabajo') ? ' is-invalid' : '' }}" value="{{old('telefono_trabajo')}}"  required>
            </div>
        </div>
        <div class="row">
            <div class="m-t-md col-lg-8">
                <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (*) son obligatorios.</small>
            </div>
        </div>
    </fieldset>
    <h1>Datos Laborales</h1>
    <fieldset style="position: relative;">
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Área</label>
                <select id="area" name="area" class="select2_form form-control {{ $errors->has('area') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(areas() as $area)
                        <option value="{{ $area->simbolo }}" {{ (old('area') == $area->simbolo ? "selected" : "") }} >{{ $area->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Profesión</label>
                <select id="profesion" name="profesion" class="select2_form form-control {{ $errors->has('profesion') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(profesiones() as $profesion)
                        <option value="{{ $profesion->simbolo }}" {{ (old('profesion') == $profesion->simbolo ? "selected" : "") }} >{{ $profesion->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Cargo</label>
                <select id="cargo" name="cargo" class="select2_form form-control {{ $errors->has('cargo') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(cargos() as $cargo)
                        <option value="{{ $cargo->simbolo }}" {{ (old('cargo') == $cargo->simbolo ? "selected" : "") }} >{{ $cargo->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Sueldo</label>
                <input type="text" id="sueldo" name="sueldo" class="form-control {{ $errors->has('sueldo') ? ' is-invalid' : '' }}" value="{{old('sueldo')}}" maxlength="15" onkeypress="return filterFloat(event,this);" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Sueldo bruto</label>
                <input type="text" id="sueldo_bruto" name="sueldo_bruto" class="form-control {{ $errors->has('sueldo_bruto') ? ' is-invalid' : '' }}" value="{{old('sueldo_bruto')}}" onkeypress="return filterFloat(event,this);" maxlength="15" required>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Sueldo neto</label>
                <input type="text" id="sueldo_neto" name="sueldo_neto" class="form-control {{ $errors->has('sueldo_neto') ? ' is-invalid' : '' }}" value="{{old('sueldo_neto')}}" onkeypress="return filterFloat(event,this);" maxlength="15" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12">
                <label class="required">Moneda sueldo</label>
                <select id="moneda_sueldo" name="moneda_sueldo" class="select2_form form-control {{ $errors->has('moneda_sueldo') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(tipos_moneda() as $moneda)
                        <option value="{{ $moneda->simbolo }}" {{ (old('moneda_sueldo') == $moneda->simbolo ? "selected" : "") }}>{{ $moneda->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label>Banco</label>
                <select id="tipo_banco" name="tipo_banco" class="select2_form form-control {{ $errors->has('tipo_banco') ? ' is-invalid' : '' }}" style="width: 100%">
                    <option></option>
                    @foreach(bancos() as $banco)
                        <option value="{{ $banco->simbolo }}" {{ (old('tipo_banco') == $banco->simbolo ? "selected" : "") }} >{{ $banco->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <label>Número de cuenta</label>
                <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control {{ $errors->has('numero_cuenta') ? ' is-invalid' : '' }}" value="{{old('numero_cuenta')}}" maxlength="20" onkeypress="return isNroCuenta(event)" onkeyup="return mayus(this)">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12" id="fecha_inicio_actividad">
                <label class="required">Fecha inicio actividad</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_inicio_actividad" name="fecha_inicio_actividad" class="form-control {{ $errors->has('fecha_inicio_actividad') ? ' is-invalid' : '' }}" value="{{old('fecha_inicio_actividad')}}" required>
                </div>
            </div>
            <div class="form-group col-lg-4 col-xs-12" id="fecha_fin_actividad">
                <label>Fecha fin actividad</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_fin_actividad" name="fecha_fin_actividad" class="form-control {{ $errors->has('fecha_fin_actividad') ? ' is-invalid' : '' }}" value="{{old('fecha_fin_actividad')}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12" id="fecha_inicio_planilla">
                <label>Fecha inicio planilla</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_inicio_planilla" name="fecha_inicio_planilla" class="form-control {{ $errors->has('fecha_inicio_planilla') ? ' is-invalid' : '' }}" value="{{old('fecha_inicio_planilla')}}">
                </div>
            </div>
            <div class="form-group col-lg-4 col-xs-12" id="fecha_fin_planilla">
                <label>Fecha fin planilla</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_fin_planilla" name="fecha_fin_planilla" class="form-control {{ $errors->has('fecha_fin_planilla') ? ' is-invalid' : '' }}" value="{{old('fecha_fin_planilla')}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="m-t-md col-lg-8">
                <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (*) son obligatorios.</small>
            </div>
        </div>
    </fieldset>
    <h1>Datos Adicionales</h1>
    <fieldset>
        <div class="row">
            <div class="col-lg-8 col-xs-12">
                <div class="row">
                    <div class="form-group col-lg-6 col-xs-12">
                        <label>Teléfono de referencia</label>
                        <input type="text" id="telefono_referencia" name="telefono_referencia" class="form-control {{ $errors->has('telefono_referencia') ? ' is-invalid' : '' }}" value="{{old('telefono_referencia')}}" maxlength="50" onkeyup="return mayus(this)">
                    </div>
                    <div class="form-group col-lg-6 col-xs-12">
                        <label>Contacto de referencia</label>
                        <input type="text" id="contacto_referencia" name="contacto_referencia" class="form-control {{ $errors->has('contacto_referencia') ? ' is-invalid' : '' }}" value="{{old('contacto_referencia')}}" maxlength="191" onkeyup="return mayus(this)">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-xs-12">
                        <label>Número de hijos</label>
                        <input type="text" id="numero_hijos" name="numero_hijos" class="form-control {{ $errors->has('numero_hijos') ? ' is-invalid' : '' }}" value="{{old('numero_hijos')}}" onkeypress="return isNumber(event)" maxlength="2" >
                    </div>
                    <div class="form-group col-lg-6 col-xs-12">
                        <label>Grupo sanguíneo</label>
                        <select id="grupo_sanguineo" name="grupo_sanguineo" class="select2_form form-control {{ $errors->has('grupo_sanguineo') ? ' is-invalid' : '' }}" style="width: 100%">
                            <option></option>
                            @foreach(grupos_sanguineos() as $grupo_sanguineo)
                                <option value="{{ $grupo_sanguineo->simbolo }}" {{ (old('grupo_sanguineo') == $grupo_sanguineo->simbolo ? "selected" : "") }} >{{ $grupo_sanguineo->descripcion }} ({{ $grupo_sanguineo->simbolo }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-12 col-xs-12">
                        <label>Alergias</label>
                        <textarea type="text" id="alergias" name="alergias" class="form-control {{ $errors->has('alergias') ? ' is-invalid' : '' }}" value="{{old('alergias')}}" rows="3" onkeyup="return mayus(this)"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-xs-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Imagen:</label>
                            <div class="custom-file">
                                <input id="imagen" type="file" name="imagen" class="custom-file-input {{ $errors->has('imagen') ? ' is-invalid' : '' }}"  accept="image/*">
                                <label for="imagen" id="ruta_imagen" class="custom-file-label selected {{ $errors->has('ruta_imagen') ? ' is-invalid' : '' }}">Seleccionar</label>
                                @if ($errors->has('imagen'))
                                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('imagen') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group justify-content-center">
                        <div class="col-6 align-content-center">
                            <div class="row justify-content-end">
                                <a href="javascript:void(0);" id="limpiar_logo">
                                    <span class="badge badge-danger">x</span>
                                </a>
                            </div>
                            <div class="row justify-content-center">
                                <p>
                                    <img class="logo" src="{{asset('storage/empresas/logos/default.png')}}" alt="">
                                    <input id="url_logo" name="url_logo" type="hidden" value="">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="m-t-md col-lg-8">
                <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (*) son obligatorios.</small>
            </div>
        </div>
    </fieldset>
</form>