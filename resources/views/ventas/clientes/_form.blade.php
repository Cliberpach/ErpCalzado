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
                                <span class="input-group-append"><a style="color:white" @if ($cliente->estado != '') onclick="consultarDocumento2()" @else onclick="consultarDocumento()" @endif class="btn btn-primary"><i class="fa fa-search"></i> <span
                                            id="entidad">Entidad</span></a></span>
                                @if ($errors->has('documento'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('documento') }}</strong>
                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>


                    <div class="form-group">
                        <div class="border p-4 align-items-center">
                            <div class="form-group">
                                <label> <input type="checkbox" id="check_retencion" class="i-checks" value="1" disabled> <b class="text-danger">¿Es agente de retención?</b> </label>
                                <input type="hidden" name="retencion" id="retencion" class="form-control" value="0">
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="" class="col-12 col-md-4">Tasa (%): </label>
                                <input type="namber" class="form-control col-12 col-md-8" name="tasa_retencion" id="tasa_retencion" value="{{ old('tasa_retencion') ? old('tasa_retencion') : (empty($cliente) ? '0.00' : $cliente->tasa_retencion) }}" readonly>
                            </div>

                            <div class="form-group row align-items-center">
                                <label for="" class="col-12 col-md-4">Monto que sobrepase: </label>
                                <input type="namber" class="form-control col-12 col-md-8" name="monto_mayor" id="monto_mayor" value="{{ old('monto_mayor') ? old('monto_mayor') : (empty($cliente) ? '0.00' : $cliente->monto_mayor) }}" readonly>
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
                                @foreach (tipo_clientes() as $tipo_cliente)
                                    <option value="{{ $tipo_cliente->id }}"
                                        {{ old('tipo_cliente') ? (old('tipo_cliente') == $tipo_cliente->id ? 'selected' : '') : ($cliente->tabladetalles_id == $tipo_cliente->id ? 'selected' : '') }}>
                                        {{ $tipo_cliente->descripcion }}</option>
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
                                value="{{ old('zona') ? old('zona') : $cliente->zona }}" readonly>
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
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
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
                            value="{{ old('observaciones') ? old('observaciones') : $cliente->observaciones }}"
                            rows="3"
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
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
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
                     <div >

                                  @if($cliente->ruta_logo)
                                  <img class="logo" src="{{Storage::url($cliente->ruta_logo)}}" alt="">
                                  <input id="url_logo" name="url_logo" type="hidden"
                                      value="{{$cliente->ruta_logo}}">
                                  @else
                                  <img class="logo" src="{{asset('storage/Clientes/img/default.png')}}"
                                      alt="">
                                  <input id="url_logo" name="url_logo" type="hidden" value="">
                                  @endif

                      </div>
                </div>
            </div>
            <div class="row">
                <div class="m-t-md col-lg-8">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
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
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/iCheck/custom.css' )}}" rel="stylesheet">
    <style>
        .logo {
            width: 190px;
            height: 190px;
            border-radius: 10%;
            position: absolute;
        }

    </style>
@endpush

@push('scripts')
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/steps/jquery.steps.min.js') }}"></script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAS6qv64RYCHFJOygheJS7DvBDYB0iV2wI&libraries=geometry">
    </script>
    
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        var departamento_api = '';
        var provincia_api = '';
        var distrito_api = '';
        var map;
        var onemarker = 0;

        $(document).ready(function() {
            if ($("#activo").val() == '') {
                $("#activo").val("SIN VERIFICAR");
            }


            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
            
            cambiarTipoDocumento()

            @if ($cliente->agente_retencion == '1')
                $('#check_retencion').attr('checked', true);
                $('#retencion').val('1');
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    activeClass: 'active'
                });

                $('#tasa_retencion').val('{{$cliente->tasa_retencion}}');
                $('#monto_mayor').val('{{$cliente->monto_mayor}}');
                $('#tasa_retencion').attr('readonly', false);
                $('#monto_mayor').attr('readonly', false);
            @else
                $('#check_retencion').removeAttr('checked');
                $('#retencion').val('0');
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                });

                $('#tasa_retencion').attr('readonly', true);
                $('#monto_mayor').attr('readonly', true);
            @endif  

            $("#tipo_documento").on("change", cambiarTipoDocumento);

            $("#departamento").on("change", cargarProvincias);

            $('#provincia').on("change", cargarDistritos);

            $("input[type='email']").on('change', function() {
                if (!emailIsValid($(this).val())) {
                    toastr.error('El formato del email es incorrecto', 'Error');
                    $(this).focus();
                }
            });

            $('#fecha_aniversario .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                language: 'es',
                format: "dd/mm/yyyy",

            });

            $('#fecha_nacimiento_propietario .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                language: 'es',
                format: "dd/mm/yyyy",

            });
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {
                    lat: -8.1092027,
                    lng: -79.0244529
                },
                gestureHandling: "greedy",
                zoomControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });
            google.maps.event.addListener(map, "click", function(event) {
                if (onemarker == 0) {
                    var marker = new google.maps.Marker({
                        position: event.latLng,
                        map: map,
                        draggable: true
                    });
                    $("#lat").val(marker.getPosition().lat());
                    $("#lng").val(marker.getPosition().lng());
                    google.maps.event.addListener(marker, "dragend", function(event) {
                        $("#lat").val(this.getPosition().lat());
                        $("#lng").val(this.getPosition().lng());
                    });
                    onemarker = 1;
                }
            });
            if ($("#onPosition").val() != undefined) {
                var lat = $("#onPosition").data("lat");
                var lng = $("#onPosition").data("lng");
                if (lat != "") {

                    editmarker(lat, lng);
                }

            }
        
        });
        function limpiar() {
            $('.logo').attr("src", "{{asset('storage/Clientes/img/default.png')}}")
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#logo').val('')
        }
        function seleccionarimagen() {
            var fileInput = document.getElementById('logo');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            $imagenPrevisualizacion = document.querySelector(".logo");
            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('logo');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                var data = userFile.src;
                $imagenPrevisualizacion.src = data
                let fileName = $('#logo').val().split('\\').pop();
                $('#logo').next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.logo').attr("src", "{{asset('storage/Clientes/img/default.png')}}")
            }
        }

        function editmarker(lat, lng) {
            var marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: map,
                draggable: true
            })
            $("#lat").val(marker.getPosition().lat());
            $("#lng").val(marker.getPosition().lng());
            google.maps.event.addListener(marker, "dragend", function(event) {
                $("#lat").val(this.getPosition().lat());
                $("#lng").val(this.getPosition().lng());
            });
        }

        function setLongitudDocumento() {

            var tipo_documento = $('#tipo_documento').val();
            if (tipo_documento !== undefined && tipo_documento !== null && tipo_documento !== "" && tipo_documento.length >
                0) {

                @if (!$cliente)
                    clearDatosPersona(true);                                
                @endif

                switch (tipo_documento) {
                    case 'DNI':
                        $('#entidad').text('Reniec')
                        $('#lblNombre').text('Nombre')
                        $("#documento").attr('maxlength', 8);
                        @if (!$cliente)
                            $("#activo").val("SIN VERIFICAR");
                        @endif
                        break;

                    case 'RUC':
                        $('#entidad').text('Sunat')
                        $('#lblNombre').text('Razón social')
                        $("#documento").attr('maxlength', 11);
                        @if (!$cliente)
                            $("#activo").val("SIN VERIFICAR");
                        @endif
                        break;

                    case 'CARNET EXT.':
                        $("#documento").attr('maxlength', 20);
                        $('#lblNombre').text('Nombre')
                        $("#activo").val("SIN VERIFICAR");
                        @if (!$cliente)
                            $("#activo").val("SIN VERIFICAR");
                        @endif
                        break;

                    case 'PASAPORTE':
                        $("#documento").attr('maxlength', 20);
                        $('#lblNombre').text('Nombre')
                        $("#activo").val("SIN VERIFICAR");
                        @if (!$cliente)
                            $("#activo").val("SIN VERIFICAR");
                        @endif
                        break;

                    case 'P. NAC.':
                        $("#documento").attr('maxlength', 25);
                        $('#lblNombre').text('Nombre')
                        @if (!$cliente)
                            $("#activo").val("SIN VERIFICAR");
                        @endif
                        break;
                }
            }
        }

        function consultarDocumento() {

            var tipo_documento = $('#tipo_documento').val();
            var documento = $('#documento').val();

            // Consultamos nuestra BBDD
            $.ajax({
                dataType: 'json',
                type: 'post',
                url: '{{ route('ventas.cliente.getDocumento') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'tipo_documento': tipo_documento,
                    'documento': documento,
                    'id': null
                }
            }).done(function(result) {
                if (result.existe) {
                    toastr.error('El ' + tipo_documento + ' ingresado ya se encuentra registrado para un cliente',
                        'Error');
                    clearDatosPersona(true);
                } else {
                    if (tipo_documento === "DNI") {
                        if (documento.length === 8) {
                            consultarAPI(tipo_documento, documento);
                        } else {
                            toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                            clearDatosPersona(false);
                        }
                    } else if (tipo_documento === "RUC") {
                        if (documento.length === 11) {
                            consultarAPI(tipo_documento, documento);
                        } else {
                            toastr.error('El RUC debe de contar con 11 dígitos', 'Error');
                            clearDatosPersona(false);
                        }
                    }
                }
            });
        }

        function consultarAPI(tipo_documento, documento) {

            if (tipo_documento === 'DNI' || tipo_documento === 'RUC') {
                var url = (tipo_documento === 'DNI') ? '{{ route('getApidni', ':documento') }}' :
                    '{{ route('getApiruc', ':documento') }}';
                url = url.replace(':documento', documento);
                var textAlert = (tipo_documento === 'DNI') ? "¿Desea consultar DNI a RENIEC?" :
                    "¿Desea consultar RUC a SUNAT?";
                Swal.fire({
                    title: 'Consultar',
                    text: textAlert,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `El documento ingresado es incorrecto`
                                );
                                clearDatosPersona(true);
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value !== undefined && result.isConfirmed) {
                        $('#documento').removeClass('is-invalid')
                        if (tipo_documento === 'DNI')
                            camposDNI(result);
                        else
                            camposRUC(result);

                        consultaExitosa();
                    }
                });
            }
        }

        function camposDNI(objeto) {
            if (objeto.value === undefined)
                return;

            if(objeto.value.success)
            {
                var nombres = objeto.value.data.nombres;
                var apellido_paterno = objeto.value.data.apellido_paterno;
                var apellido_materno = objeto.value.data.apellido_materno;
                var codigo_verificacion = objeto.value.data.codigo_verificacion;

                var direccion = objeto.value.data.direccion_completa;
                var departamento = objeto.value.data.ubigeo[0];
                var provincia = objeto.value.data.ubigeo[1];
                var distrito = objeto.value.data.ubigeo[2];

                if (direccion != '-' && direccion != "NULL") {
                    $('#direccion').val(direccion);
                }

                if(departamento && provincia && distrito)
                {
                    camposUbigeoApi(departamento, provincia, distrito);
                }

                var nombre = "";
                if (nombres !== '-' && nombres !== "NULL") {
                    nombre += nombres;
                }
                if (apellido_paterno !== '-' && apellido_paterno !== "NULL") {
                    nombre += (nombre.length === 0) ? apellido_paterno : ' ' + apellido_paterno
                }
                if (apellido_materno !== '-' && apellido_materno !== "NULL") {
                    nombre += (nombre.length === 0) ? apellido_materno : ' ' + apellido_materno
                }
                $("#nombre").val(nombre);
                $("#activo").val("ACTIVO");
                if (codigo_verificacion !== '-' && codigo_verificacion !== "NULL") {
                    $('#codigo_verificacion').val(codigo_verificacion);
                }
            }
            else{
                toastr.error('No se encontraron datos.')
            }
        }

        function clearDatosPersona(limpiarDocumento) {
            if (limpiarDocumento)
                $('#documento').val("");

            $('#nombre').val("");
            $('#codigo_verificacion').val("");
            $('#direccion').val("");
            $('#departamento').val("").trigger("change");
            $('#provincia').val("").trigger("change");
            $('#distrito').val("").trigger("change");
            $("#provincia").empty();
            $("#distrito").empty();
            $('#correo_electronico').val("");
            $('#telefono_movil').val("");
            $('#telefono_fijo').val("");

            departamento_api = '';
            provincia_api = '';
            distrito_api = '';
        }

        $("#form_registrar_cliente").steps({
            bodyTag: "fieldset",
            transitionEffect: "fade",
            labels: {
                current: "actual paso:",
                pagination: "Paginación",
                finish: "Finalizar",
                next: "Siguiente",
                previous: "Anterior",
                loading: "Cargando ..."
            },
            onStepChanging: function(event, currentIndex, newIndex) {
                // Always allow going backward even if the current step contains invalid fields!
                if (currentIndex > newIndex) {
                    return true;
                }

                var form = $(this);

                // Clean up if user went backward before
                if (currentIndex < newIndex) {
                    // To remove error styles
                    $(".body:eq(" + newIndex + ") label.error", form).remove();
                    $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                }

                // Start validation; Prevent going forward if false
                return validarDatos(currentIndex + 1);
            },
            onStepChanged: function(event, currentIndex, priorIndex) {

            },
            onFinishing: function(event, currentIndex) {
                var form = $(this);

                // Start validation; Prevent form submission if false
                return true;
            },
            onFinished: function(event, currentIndex) {
                var form = $(this);

                // Submit form input
                form.submit();
            }
        });

        function validarDatos(paso) {
            switch (paso) {
                case 1:
                    return validarDatosPersonales();
                    //return validarDatosContacto();

                case 2:

                    return validarDatosContacto();
                    //return validarDatosLaborales();

                case 3:
                    return validarDatosLaborales();

                case 4:
                    return validarDatosAdicionales();

                default:
                    return false;
            }
        }

        function validarDatosPersonales() {
            debugger;
            var tipo_documento = $("#tipo_documento").val();
            var documento = $("#documento").val();
            var nombres = $("#nombre").val();
            var departamento = $("#departamento").val();
            var provincia = $("#provincia").val();
            var distrito = $("#distrito").val();
            var telefono_movil = $("#telefono_movil").val();
            var tipo_cliente = $("#tipo_cliente").val();
            var correo_electronico = $("#correo_electronico").val();

            $('.datepicker-days').removeAttr("style").hide();
            var sexo = $("#sexo_hombre").is(':checked') ? 'H' : 'M';

            if ((tipo_documento !== null && tipo_documento.length === 0) || correo_electronico.length === 0 || documento
                .length === 0 || nombres.length === 0 || departamento.length === 0 || provincia.length === 0 || sexo
                .length === 0 || distrito.length === 0 || telefono_movil.length === 0 || tipo_cliente.length === 0) {
                toastr.error('Complete la información de los campos obligatorios (*)', 'Error');
                return false;
            }

            switch (tipo_documento) {
                case 'RUC':

                    if (documento.length !== 11) {
                        toastr.error('El RUC debe de contar con 11 dígitos', 'Error');
                        return false;
                    }
                    break;

                case 'DNI':

                    if (documento.length !== 8) {
                        toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                        return false;
                    }
                    break;

                case 'CARNET EXT.':
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');

                    if (documento.length !== 20) {
                        toastr.error('El CARNET DE EXTRANJERIA debe de contar con 20 dígitos', 'Error');
                        return false;
                    }
                    break;

                case 'PASAPORTE':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    if (documento.length !== 20) {
                        toastr.error('El PASAPORTE debe de contar con 20 dígitos', 'Error');
                        return false;
                    }
                    break;

                case 'P. NAC.':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    if (documento.length !== 25) {
                        toastr.error('La PARTIDAD DE NACIMIENTO debe de contar con 25 dígitos', 'Error');
                        return false;
                    }
                    break;

                default:
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');

            }

            return true;
        }

        function validarDatosContacto() {
            var direccion_negocio = $("#direccion_negocio").val();

            // if (direccion_negocio == '') {
            //     toastr.error('Complete la información de los campos obligatorios (*)', 'Error');
            //     return false;
            // }
            return true;
        }

        function validarDatosLaborales() {

            debugger;

            if (!emailIsValid($('#correo_propietario').val())) {
                return false;
            }

            return true;
        }

        function validarEmail() {
            if (!emailIsValid($('#correo_electronico').val())) {
                toastr.error('El formato del email es incorrecto', 'Error');
                $('#correo_electronico').focus();
            }
        }

        function cambiarTipoDocumento() {
            var tipo_documento = $("#tipo_documento").val();
            $('#check_retencion').removeAttr('checked');    
            $('#retencion').val('0');
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                activeClass: '',
            });

            $('#tasa_retencion').val('0.00');
            $('#monto_mayor').val('0.00');
            
            $('#tasa_retencion').attr('readonly', true);
            $('#monto_mayor').attr('readonly', true);   
            setLongitudDocumento();
        }

        function cargarProvincias() {
            var departamento_id = $("#departamento").val();
            if (departamento_id !== "" || departamento_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        departamento_id: departamento_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.provincias') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#provincia").empty();
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.provincias != null) {
                                if (provincia_api != '') {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val(provincia_api).trigger('change');
                                    provincia_api='';
                                } else {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val($('#provincia').find(':selected').val()).trigger('change');

                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function cargarDistritos() {
            var provincia_id = $("#provincia").val();
            if (provincia_id !== "" || provincia_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        provincia_id: provincia_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.distritos') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.distritos != null) {
                                var selected = $('#distrito').find(':selected').val();
                                if (distrito_api != '') {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    }).val(distrito_api).trigger('change');
                                    distrito_api='';
                                } else {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    });
                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function zonaDepartamento(depar) {
            // alert(depar.value)
            @foreach (departamentos() as $departamento)
                if ("{{ $departamento->id }}" == depar.value){
                $('#zona').val("{{ $departamento->zona }}")
                }
            @endforeach


        }

        function camposRUC(objeto) {
            console.log(objeto);
            if (objeto.value === undefined)
                return;
            if(objeto.value.success)
            {
                var razonsocial = objeto.value.data.nombre_o_razon_social;
                var direccion = objeto.value.data.direccion;
                var departamento = objeto.value.data.ubigeo[0];
                var provincia = objeto.value.data.ubigeo[1];
                var distrito = objeto.value.data.ubigeo[2];
                var estado = objeto.value.data.estado;
                var condicion_retencion = objeto.value.data.es_agente_de_retencion;

                if (condicion_retencion == 'SI') {
                    $('#check_retencion').attr('checked', true);
                    $('#retencion').val('1');
                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        activeClass: 'active',
                    });

                    $('#tasa_retencion').val('3.00');
                    $('#monto_mayor').val('700.00');

                    $('#tasa_retencion').removeAttr('readonly');
                    $('#monto_mayor').removeAttr('readonly');
                }
                else {
                    $('#check_retencion').removeAttr('checked');
                    $('#retencion').val('0');
                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        activeClass: '',
                    });

                    $('#tasa_retencion').val('0.00');
                    $('#monto_mayor').val('0.00');

                    
                    $('#tasa_retencion').attr('readonly', true);
                    $('#monto_mayor').attr('readonly', true);
                }

                if (razonsocial != '-' && razonsocial != "NULL") {
                    $('#nombre').val(razonsocial);
                }

                if (estado == "ACTIVO") {
                    $('#activo').val(estado);
                } else {
                    toastr.error('Cliente con RUC no se encuentra "Activo"', 'Error');
                }

                if (direccion != '-' && direccion != "NULL") {
                    $('#direccion').val(direccion);
                }

                camposUbigeoApi(departamento, provincia, distrito);
            }
            else
            {
                toastr.error('No se encontraron datos.')
            }
        }

        function camposUbigeoApi(departamento, provincia, distrito) {
            departamento_api = '';
            provincia_api = '';
            distrito_api = '';

            if (departamento !== '-' && departamento !== null && provincia !== '-' && provincia !== null &&
                distrito !== '-' && distrito !== null) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        departamento: departamento,
                        provincia: provincia,
                        distrito: distrito
                    },
                    url: "{{ route('mantenimiento.ubigeo.api_ruc') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#provincia").empty();
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.ubigeo != null) {

                                departamento_api = data.ubigeo.departamento_id;
                                provincia_api = parseInt(data.ubigeo.provincia_id);
                                distrito_api = data.ubigeo.id;

                                $("#departamento").val(parseInt(departamento_api)).trigger('change');
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function consultar() {
            var tipo = $('#tipo_documento').val()
            switch (tipo) {
                case 'DNI':
                    // $('#entidad').text('Reniec')
                    // consultarDocumento()
                    break;
                case 'CARNET EXT.':
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    $('#entidad').text('Entidad')
                    break;
                case 'RUC':
                    // $('#entidad').text('Sunat')
                    // consultarDocumento()
                    break;
                case 'P. NAC.':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    break;
                case 'PASAPORTE':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    break;
                    // default:
                    //     $('#entidad').text('Entidad')
                    //     toastr.error('El tipo de documento no tiene entidad para consultar','Error');
            }

        }

        $("#documento").keyup(function() {
            $('#activo').val('SIN VERIFICAR');
            $('#nombre').val('');
            $('#codigo_verificacion').val('');
            $('#check_retencion').removeAttr('checked');
            $('#retencion').val('0');
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                activeClass: '',
            });

            $('#tasa_retencion').val('0.00');
            $('#monto_mayor').val('0.00');
            
            $('#tasa_retencion').attr('readonly', true);
            $('#monto_mayor').attr('readonly', true);
        })

        $("#nombre").keyup(function() {
            $('#activo').val('SIN VERIFICAR');
            $('#codigo_verificacion').val('');
            $('#check_retencion').removeAttr('checked');
            $('#retencion').val('0');
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                activeClass: '',
            });

            $('#tasa_retencion').val('0.00');
            $('#monto_mayor').val('0.00');
            
            $('#tasa_retencion').attr('readonly', true);
            $('#monto_mayor').attr('readonly', true);
        })

        $("#tipo_documento").on('change', function(e) {
            $('#activo').val('SIN VERIFICAR')
        })

    </script>
@endpush
