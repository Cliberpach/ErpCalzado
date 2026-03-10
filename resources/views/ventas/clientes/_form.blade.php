<div class="wrapper wrapper-content animated fadeIn">
    <form class="wizard-big" action="{{ $action }}" method="POST" id="form_registrar_cliente"
        enctype="multipart/form-data">
        @csrf
        <h1>Datos Del Cliente</h1>
        <fieldset style="position: relative;">

            <div class="row">
                <div class="col-md-6 b-r">

                    <div class="form-group row">

                        <div class="col-lg-6 col-xs-12 select-required">
                            <label class="required">Tipo de documento</label>
                            <select id="tipo_documento" name="tipo_documento"
                                class="select2_form form-control {{ $errors->has('tipo_documento') ? ' is-invalid' : '' }}">
                                <option></option>
                                @foreach (tipos_documento() as $tipo_documento)
                                    <option value="{{ $tipo_documento->simbolo }}"
                                        {{ $tipo_documento->simbolo == 'DNI' ? 'selected' : '' }}
                                        {{ old('tipo_documento') ? (old('tipo_documento') == $tipo_documento->simbolo ? 'selected' : '') : ($cliente->tipo_documento == $tipo_documento->simbolo ? 'selected' : '') }}>
                                        {{ $tipo_documento->simbolo }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tipo_documento'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tipo_documento') }}</strong>
                                </span>
                            @endif
                        </div>


                        <div class="col-lg-6 col-xs-12">
                            <label class="required">Nro. Documento</label>

                            <div class="input-group">
                                <input type="text" id="documento" name="documento"
                                    class="form-control input-required {{ $errors->has('documento') ? ' is-invalid' : '' }}"
                                    value="{{ old('documento') ? old('documento') : $cliente->documento }}"
                                    maxlength="8" onkeypress="return isNumber(event)" required>
                                <span class="input-group-append"><a style="color:white"
                                        @if ($cliente->estado != '') onclick="consultarDocumento2()" @else onclick="consultarDocumento()" @endif
                                        class="btn btn-primary"><i class="fa fa-search"></i> <span
                                            id="entidad">Entidad</span></a></span>
                                @if ($errors->has('documento'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('documento') }}</strong>
                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>


                    <div class="form-group d-none">
                        <div class="border p-4 align-items-center">
                            <div class="form-group">
                                <label> <input type="checkbox" id="check_retencion" class="i-checks" value="1"
                                        disabled> <b class="text-danger">¿Es agente de retención?</b> </label>
                                <input type="hidden" name="retencion" id="retencion" class="form-control"
                                    value="0">
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="" class="col-12 col-md-4">Tasa (%): </label>
                                <input type="namber" class="form-control col-12 col-md-8" name="tasa_retencion"
                                    id="tasa_retencion"
                                    value="{{ old('tasa_retencion') ? old('tasa_retencion') : (empty($cliente) ? '0.00' : $cliente->tasa_retencion) }}"
                                    readonly>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="" class="col-12 col-md-4">Monto que sobrepase: </label>
                                <input type="namber" class="form-control col-12 col-md-8" name="monto_mayor"
                                    id="monto_mayor"
                                    value="{{ old('monto_mayor') ? old('monto_mayor') : (empty($cliente) ? '0.00' : $cliente->monto_mayor) }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-lg-6 col-xs-12 select-required">
                            <label class="required">Tipo Cliente</label>
                            <select id="tipo_cliente" name="tipo_cliente"
                                class="select2_form form-control {{ $errors->has('tipo_cliente') ? ' is-invalid' : '' }}"
                                style="width: 100%">
                                <option></option>
                                @foreach ($tipos_clientes as $tipo_cliente)
                                    <option value="{{ $tipo_cliente->id }}"
                                        {{ $tipo_cliente->nombre == 'UNIDAD' ? 'selected' : '' }}
                                        {{ old('tipo_cliente') ? (old('tipo_cliente') == $tipo_cliente->id ? 'selected' : '') : ($cliente->tipo_cliente_id == $tipo_cliente->id ? 'selected' : '') }}>
                                        {{ $tipo_cliente->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tipo_cliente'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tipo_cliente') }}</strong>
                                </span>
                            @endif
                        </div>

                        <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">

                        <div class="col-lg-6 col-xs-12">
                            <label class="">Estado</label>
                            <input type="text" id="activo" name="activo"
                                class="form-control text-center {{ $errors->has('activo') ? ' is-invalid' : '' }}"
                                value="{{ old('activo') ? old('activo') : $cliente->activo }}" readonly>
                            @if ($errors->has('activo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('activo') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>

                    <div class="form-group row d-none">
                        <div class="col-md-6 col-xs-12">
                            <label class="" id="">Código de Cliente</label>
                            <input type="text" id="codigo" name="codigo"
                                class="form-control {{ $errors->has('codigo') ? ' is-invalid' : '' }}"
                                value="{{ old('codigo') ? old('codigo') : $cliente->codigo }}" maxlength="191"
                                onkeyup="return mayus(this)">
                            @if ($errors->has('codigo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <label class="" id="">Nombre Comercial</label>
                            <input type="text" id="nombre_comercial" name="nombre_comercial"
                                class="form-control {{ $errors->has('nombre_comercial') ? ' is-invalid' : '' }}"
                                value="{{ old('nombre_comercial') ? old('nombre_comercial') : $cliente->nombre_comercial }}"
                                maxlength="191" onkeyup="return mayus(this)">
                            @if ($errors->has('nombre_comercial'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('nombre_comercial') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required"
                            id="lblNombre">{{ old('documento', $cliente->documento) == 'RUC' ? 'Razón social' : 'Nombre' }}</label>
                        <input type="text" id="nombre" name="nombre"
                            class="form-control input-required {{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                            value="{{ old('nombre') ? old('nombre') : $cliente->nombre }}" maxlength="191"
                            onkeyup="return mayus(this)" required>
                        @if ($errors->has('nombre'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required">Dirección Fiscal</label>
                        <input type="text" id="direccion" name="direccion"
                            class="form-control input-required {{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                            value="{{ old('direccion') ? old('direccion') : $cliente->direccion }}" maxlength="191"
                            onkeyup="return mayus(this)" required>
                        @if ($errors->has('direccion'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-6 col-xs-12 select-required">
                            <label class="required">Departamento</label>
                            <select id="departamento" name="departamento"
                                class="select2_form form-control {{ $errors->has('departamento') ? ' is-invalid' : '' }}"
                                style="width: 100%" onchange="zonaDepartamento(this)">
                                <option></option>
                                @foreach (departamentos() as $departamento)
                                    <option value="{{ $departamento->id }}"
                                        {{ $departamento->nombre == 'LA LIBERTAD' ? 'selected' : '' }}
                                        {{ old('departamento') ? (old('departamento') == $departamento->id ? 'selected' : '') : ($cliente->departamento_id == $departamento->id ? 'selected' : '') }}>
                                        {{ $departamento->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('departamento'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('departamento') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-6 col-xs-12 select-required">
                            <label class="required">Provincia</label>
                            <select id="provincia" name="provincia"
                                class="select2_form form-control {{ $errors->has('provincia') ? ' is-invalid' : '' }}"
                                style="width: 100%">
                                <option></option>
                                @foreach (provincias() as $provincia)
                                    <option value="{{ $provincia->id }}"
                                        {{ $provincia->nombre == 'TRUJILLO' ? 'selected' : '' }}
                                        {{ old('provincia') ? (old('provincia') == $provincia->id ? 'selected' : '') : ($cliente->provincia_id == $provincia->id ? 'selected' : '') }}>
                                        {{ $provincia->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('provincia'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('provincia') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-lg-6 col-xs-12 select-required">
                            <label class="required">Distrito</label>
                            <select id="distrito" name="distrito"
                                class="select2_form form-control {{ $errors->has('distrito') ? ' is-invalid' : '' }}"
                                style="width: 100%">
                                <option></option>
                                @foreach (distritos() as $distrito)
                                    <option value="{{ $distrito->id }}"
                                        {{ $distrito->nombre == 'TRUJILLO' ? 'selected' : '' }}
                                        {{ old('distrito') ? (old('distrito') == $distrito->id ? 'selected' : '') : ($cliente->distrito_id == $distrito->id ? 'selected' : '') }}>
                                        {{ $distrito->nombre }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('distrito'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('distrito') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6 col-xs-12">
                            <label class="required">Zona</label>
                            <input type="text" id="zona" name="zona"
                                class=" text-center form-control {{ $errors->has('zona') ? ' is-invalid' : '' }}"
                                value="{{ old('zona') ? old('zona') : ($cliente->zona ? $cliente->zona : 'NORTE') }}"
                                readonly>
                            @if ($errors->has('zona'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('zona') }}</strong>
                                </span>
                            @endif
                        </div>


                    </div>

                    <div class="form-group row">
                        <div class="col-lg-6 col-xs-12">
                            <label class="required">Teléfono móvil</label>
                            <input type="text" id="telefono_movil" name="telefono_movil"
                                class="form-control input-required {{ $errors->has('telefono_movil') ? ' is-invalid' : '' }}"
                                value="{{ old('telefono_movil') ? old('telefono_movil') : $cliente->telefono_movil }}"
                                onkeypress="return isNumber(event)" maxlength="9" required>
                            @if ($errors->has('telefono_movil'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('telefono_movil') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-6 col-xs-12">
                            <label>Teléfono fijo</label>
                            <input type="text" id="telefono_fijo" name="telefono_fijo"
                                class="form-control {{ $errors->has('telefono_fijo') ? ' is-invalid' : '' }}"
                                value="{{ old('telefono_fijo') ? old('telefono_fijo') : $cliente->telefono_fijo }}"
                                onkeypress="return isNumber(event)" maxlength="10">
                            @if ($errors->has('telefono_fijo'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('telefono_fijo') }}</strong>
                                </span>
                            @endif
                        </div>


                    </div>

                    <div class="form-group">
                        <label class="required">Correo electrónico</label>
                        <input type="email" id="correo_electronico" name="correo_electronico"
                            class="form-control input-required {{ $errors->has('correo_electronico') ? ' is-invalid' : '' }}"
                            value="{{ old('correo_electronico') ? old('correo_electronico') : $cliente->correo_electronico }}"
                            maxlength="100" onkeyup="return mayus(this)" required>
                        @if ($errors->has('correo_electronico'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('correo_electronico') }}</strong>
                            </span>
                        @endif
                    </div>



                </div>



            </div>

            <div class="row">
                <div class="m-t-md col-lg-8">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
            </div>

        </fieldset>

        <h1>Datos Del Negocio</h1>
        <fieldset style="position: relative;">
            <h3>DATOS DE NEGOCIO</h3>
            <div class="row">
                <div class="col-md-6 b-r">
                    <div class="form-group">
                        <label>Direccion de Negocio (Direccion de Llegada)</label>
                        <input type="text" id="direccion_negocio" name="direccion_negocio"
                            class="form-control {{ $errors->has('direccion_negocio') ? ' is-invalid' : '' }}"
                            value="{{ old('direccion_negocio') ? old('direccion_negocio') : $cliente->direccion_negocio }}"
                            maxlength="191" onkeyup="return mayus(this)">
                        @if ($errors->has('direccion_negocio'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('direccion_negocio') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group row" id="fecha_aniversario">

                        <div class="col-md-6">
                            <label>Fecha de Aniversario</label>
                            <div class="input-group date">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="text" id="fecha_aniversario" name="fecha_aniversario"
                                    class="form-control {{ $errors->has('fecha_aniversario') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_aniversario', '') ? old('fecha_aniversario', getFechaFormato($cliente->fecha_aniversario, 'd/m/Y')) : getFechaFormato($cliente->fecha_aniversario, 'd/m/Y') }}"
                                    readonly>
                            </div>
                        </div>


                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea type="text" id="observaciones" name="observaciones"
                            class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}"
                            value="{{ old('observaciones') ? old('observaciones') : $cliente->observaciones }}" rows="3"
                            onkeyup="return mayus(this)">{{ old('observaciones') ? old('observaciones') : $cliente->observaciones }}</textarea>
                    </div>

                </div>



            </div>


            <div class="row">
                <div class="col-md-6 b-r">
                    <h3>REDES SOCIALES</h3>
                    <div class="form-group">

                        <label class="">Facebook:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-facebook"></i>
                            </span>
                            <input type="text" id="facebook" name="facebook"
                                class="form-control {{ $errors->has('facebook') ? ' is-invalid' : '' }}"
                                onkeyup="return mayus(this)"
                                value="{{ old('facebook') ? old('facebook') : $cliente->facebook }}">

                            @if ($errors->has('facebook'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('facebook') }}</strong>
                                </span>
                            @endif
                        </div>


                    </div>

                    <div class="form-group">

                        <label class="">Instagram:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-instagram"></i>
                            </span>
                            <input type="text" id="instagram" name="instagram"
                                class="form-control {{ $errors->has('instagram') ? ' is-invalid' : '' }}"
                                onkeyup="return mayus(this)"
                                value="{{ old('instagram') ? old('instagram') : $cliente->instagram }}">

                            @if ($errors->has('instagram'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('instagram') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>


                    <div class="form-group">

                        <label class="">Web:</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-globe"></i>
                            </span>
                            <input type="text" id="web" name="web"
                                class="form-control {{ $errors->has('web') ? ' is-invalid' : '' }}"
                                onkeyup="return mayus(this)" value="{{ old('web') ? old('web') : $cliente->web }}">

                            @if ($errors->has('web'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('web') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>




                </div>
                <div class="col-md-6">
                    <h3>HORARIO DE ATENCION</h3>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>Horario Inicio:</label>
                            <input type="time" name="hora_inicio" class="form-control"
                                value="{{ old('hora_inicio') ? old('hora_inicio') : $cliente->hora_inicio }}"
                                max="24:00:00" min="00:00:00" step="1">
                            @if ($errors->has('horario_inicio'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('horario_inicio') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>Horario Termino:</label>
                            <input type="time" name="hora_termino" class="form-control"
                                value="{{ old('hora_termino') ? old('hora_termino') : $cliente->hora_termino }}"
                                max="24:00:00" min="00:00:00" step="1">
                            @if ($errors->has('horario_termino'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('horario_termino') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>


                </div>



            </div>

            <div class="row">
                <div class="m-t-md col-lg-8">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
            </div>
        </fieldset>

        <h1>Datos Del Propietario</h1>
        <fieldset style="position: relative;">
            <div class="row">
                <div class="col-md-6 b-r">
                    <div class="form-group">
                        <label class="">Nombre</label>
                        <input type="text" id="nombre_propietario" name="nombre_propietario"
                            class="form-control {{ $errors->has('nombre_propietario') ? ' is-invalid' : '' }}"
                            value="{{ old('nombre_propietario') ? old('nombre_propietario') : $cliente->nombre_propietario }}"
                            maxlength="191" onkeyup="return mayus(this)">
                        @if ($errors->has('nombre_propietario'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('nombre_propietario') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="">Dirección</label>
                        <input type="text" id="direccion_propietario" name="direccion_propietario"
                            class="form-control {{ $errors->has('direccion_propietario') ? ' is-invalid' : '' }}"
                            value="{{ old('direccion_propietario') ? old('direccion_propietario') : $cliente->direccion_propietario }}"
                            maxlength="191" onkeyup="return mayus(this)">
                        @if ($errors->has('nombre_propietario'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('nombre_propietario') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group row" id="fecha_nacimiento_propietario">

                        <div class="col-md-6">
                            <label>Fecha de Nacimiento</label>
                            <div class="input-group date">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="text" id="fecha_nacimiento_prop" name="fecha_nacimiento_prop"
                                    class="form-control {{ $errors->has('fecha_nacimiento_prop') ? ' is-invalid' : '' }}"
                                    value="{{ old('fecha_nacimiento_prop') ? old('fecha_nacimiento_prop', getFechaFormato($cliente->fecha_nacimiento_prop, 'd/m/Y')) : getFechaFormato($cliente->fecha_nacimiento_prop, 'd/m/Y') }}"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Celular</label>
                            <input type="text" id="celular_propietario" name="celular_propietario"
                                class="form-control {{ $errors->has('celular_propietario') ? ' is-invalid' : '' }}"
                                value="{{ old('celular_propietario') ? old('celular_propietario') : $cliente->celular_propietario }}"
                                onkeypress="return isNumber(event)">
                            @if ($errors->has('celular_propietario'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('celular_propietario') }}</strong>
                                </span>
                            @endif


                        </div>


                    </div>

                    <div class="form-group">
                        <label class="">Correo electrónico</label>
                        <input type="email" id="correo_propietario" name="correo_propietario"
                            class="form-control {{ $errors->has('correo_propietario') ? ' is-invalid' : '' }}"
                            value="{{ old('correo_propietario') ? old('correo_propietario') : $cliente->correo_propietario }}"
                            onkeyup="return mayus(this)">
                        @if ($errors->has('correo_propietario'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('correo_propietario') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">Ubicacion gps</label>
                        </div>
                        <div class="col-lg-12">
                            <div id="map" style="width:350px;height:300px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">

                    <label id="logo_label">Imagen:</label>
                    <div class="custom-file">
                        <input id="logo" type="file" name="logo" onchange="seleccionarimagen()"
                            class="custom-file-input {{ $errors->has('logo') ? ' is-invalid' : '' }}"
                            accept="image/*"
                            {{ $cliente->ruta_logo ? 'src=' . Storage::url($cliente->ruta_logo) . '' : '' }}>
                        <label for="logo" id="logo_txt" name="logo_txt"
                            class="custom-file-label selected {{ $errors->has('ruta') ? ' is-invalid' : '' }}">{{ $cliente->nombre_logo ? $cliente->nombre_logo : 'Seleccionar' }}</label>
                        @if ($errors->has('logo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('logo') }}</strong>
                            </span>
                        @endif
                        <div class="invalid-feedback"><b><span id="error-logo_mensaje"></span></b></div>
                    </div>
                    <div class="row">
                        <a href="javascript:void(0);" id="limpiar_logo" onclick="limpiar()">
                            <span class="badge badge-danger">x</span>
                        </a>
                    </div>
                    <div>

                        @if ($cliente->ruta_logo)
                            <img class="logo" src="{{ Storage::url($cliente->ruta_logo) }}" alt="">
                            <input id="url_logo" name="url_logo" type="hidden"
                                value="{{ $cliente->ruta_logo }}">
                        @else
                            <img class="logo" src="{{ asset('storage/Clientes/img/default.png') }}"
                                alt="">
                            <input id="url_logo" name="url_logo" type="hidden" value="">
                        @endif

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="m-t-md col-lg-8">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
            </div>
        </fieldset>

        @if (!empty($put))
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="onPosition" id="onPosition" data-lat="{{ $cliente->lat }}"
                data-lng="{{ $cliente->lng }}">
        @endif
        <input type="hidden" id="lat" name="lat">
        <input type="hidden" id="lng" name="lng">
    </form>
</div>

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
        rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <style>
        .logo {
            width: 190px;
            height: 190px;
            border-radius: 10%;
            position: absolute;
        }
    </style>
@endpush
