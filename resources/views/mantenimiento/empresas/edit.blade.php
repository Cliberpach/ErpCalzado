@extends('layout') @section('content')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>MODIFICAR EMPRESA # {{$empresa->id}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('mantenimiento.empresas.index')}}">Empresas</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Modificar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">

                <div class="ibox-content">

                    <form action="{{route('mantenimiento.empresas.update', $empresa->id)}}" method="POST"
                        enctype="multipart/form-data" id="enviar_empresa_editar">

                        {{ csrf_field() }} {{method_field('PUT')}}

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tabs-container">
                                    <ul class="nav nav-tabs">
                                        <li title="Datos de la Empresa">
                                            <a class="nav-link active" data-toggle="tab" href="#tab-1" id="empresas_link"><i class="fa fa-check-square"></i> Empresa</a>
                                        </li>

                                        <li title="Datos de Contacto">
                                            <a class="nav-link" data-toggle="tab"href="#tab-2" id="bancos_link"> <i class="fa fa-user"></i> Bancos</a>
                                        </li>

                                        <li title="Datos sobre la Facturación Electrónica" id="facturacion_tab" >
                                            <a class="nav-link" data-toggle="tab"href="#tab-3" id="facturacion_link"> <i class="fa fa-file-pdf-o"></i> Facturación Electrónica</a>
                                        </li>
                                    </ul>


                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane active">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-6 b-r">
                                                        <h4 class=""><b>Empresa</b></h4>
                                                        <div class="row">
                                                            <div class="col-lg-6 col-xs-12">
                                                                <p>Modificar datos de empresa:</p>
                                                            </div>

                                                            @if(!$empresa->estado_fe)

                                                            <div class="col-lg-6 col-xs-12">
                                                                <label> Facturación Electrónica: &nbsp
                                                                @if(old('estado_fe',$empresa->estado_fe) == '1')
                                                                    <input type="checkbox" class="i-checks" id="estado_fe" name="estado_fe" value="{{old('estado_fe',$empresa->estado_fe)}}" checked></label>
                                                                @else
                                                                    <input type="checkbox" class="i-checks" id="estado_fe" name="estado_fe" value="{{old('estado_fe',$empresa->estado_fe)}}" ></label>
                                                                @endif
                                                            </div>
                                                            @else

                                                            <input type="hidden" name="estado_fe"  id="estado_fe" value="{{old('estado_fe',$empresa->estado_fe)}}" >

                                                            @endif

                                                        </div>

                                                        <div class="form-group row">

                                                            <div class="col-md-6">
                                                                <label class="required">Ruc: </label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control {{ $errors->has('ruc') ? ' is-invalid' : '' }}"  name="ruc" id="ruc" maxlength="11"  @if($empresa->estado_fe) @else '' @endif value="{{old('ruc',$empresa->ruc)}}" required>
                                                                    <span class="input-group-append"><a style="color:white"  onclick="consultarRuc()" class="btn btn-primary"><i class="fa fa-search"></i> Sunat</a></span>
                                                                    @if ($errors->has('ruc'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('ruc') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-ruc"></span></b></div>
                                                                </div>


                                                            </div>


                                                            <div class="col-md-6">
                                                                <label>Estado: </label>

                                                                <input type="text" id="estado"
                                                                    class="form-control text-center {{ $errors->has('estado') ? ' is-invalid' : '' }}"
                                                                    name="estado" value="{{ old('estado',$empresa->estado_ruc)}}"
                                                                   disabled>
                                                                @if ($errors->has('estado'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('estado') }}</strong>
                                                                </span>
                                                                @endif



                                                            </div>


                                                        </div>

                                                        <div class="form-group">
                                                            <label class="required">Razón social: </label>

                                                            <input type="text"
                                                                class="form-control {{ $errors->has('razon_social') ? ' is-invalid' : '' }}"
                                                                name="razon_social" value="{{ old('razon_social', $empresa->razon_social)}}"
                                                                id="razon_social"  onkeyup="return mayus(this)" >

                                                            @if ($errors->has('razon_social'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('razon_social') }}</strong>
                                                            </span>
                                                            @endif
                                                            <div class="invalid-feedback"><b><span id="error-razon_social"></span></b></div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-lg-8 col-xs-12">

                                                                <label>Razón Social Abreviada: </label>
                                                                <input type="text" id="razon_social_abreviada"
                                                                    class="form-control {{ $errors->has('razon_social_abreviada') ? ' is-invalid' : '' }}"
                                                                    name="razon_social_abreviada"
                                                                    value="{{ old('razon_social_abreviada',$empresa->razon_social_abreviada)}}"
                                                                    onkeyup="return mayus(this)">

                                                                @if ($errors->has('razon_social_abreviada'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('razon_social_abreviada') }}</strong>
                                                                </span>
                                                                @endif

                                                            </div>
                                                            <div class="col-lg-4 col-xs-12">
                                                                <label id="ubigeo_texto">Ubigeo: </label>
                                                                <input type="text" id="ubigeo_empresa" class="form-control {{ $errors->has('ubigeo_empresa') ? ' is-invalid' : '' }}"   name="ubigeo_empresa" value="{{ old('ubigeo_empresa',$empresa->ubigeo)}}">
                                                                <div class="invalid-feedback"><b><span id="error-empresa_ubigeo"></span></b></div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-12">
                                                                <label class="required">Dirección Fiscal:</label>
                                                                <textarea type="text" id="direccion_fiscal" name="direccion_fiscal"
                                                                    class="form-control {{ $errors->has('direccion_fiscal') ? ' is-invalid' : '' }}"
                                                                    value="{{old('direccion_fiscal',$empresa->direccion_fiscal)}}"
                                                                    onkeyup="return mayus(this)">{{old('direccion_fiscal',$empresa->direccion_fiscal)}}</textarea>
                                                                @if ($errors->has('direccion_fiscal'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('direccion_fiscal') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-direccion_fiscal"></span></b></div>
                                                            </div>

                                                        </div>


                                                        <div class="form-group row">
                                                            <div class="col-md-12">
                                                                <label class="required">Dirección de Planta:</label>
                                                                <textarea type="text" id="direccion_llegada" name="direccion_llegada"
                                                                    class="form-control {{ $errors->has('direccion_llegada') ? ' is-invalid' : '' }}"
                                                                    value="{{old('direccion_llegada', $empresa->direccion_llegada)}}"
                                                                    onkeyup="return mayus(this)">{{old('direccion_llegada',$empresa->direccion_llegada)}}</textarea>
                                                                @if ($errors->has('direccion_llegada'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('direccion_llegada') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-direccion_llegada"></span></b></div>
                                                            </div>

                                                        </div>

                                                        <div class="form-group">
                                                            <label class="">Correo: </label>

                                                            <input type="email"
                                                                class="form-control {{ $errors->has('correo') ? ' is-invalid' : '' }}"
                                                                name="correo" value="{{ old('correo', $empresa->correo)}}" id="correo"
                                                                onkeyup="return mayus(this)">

                                                            @if ($errors->has('correo'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('correo') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label>Teléfono:</label>
                                                                <input type="text" placeholder="" class="form-control" name="telefono"
                                                                    id="telefono"  onkeypress="return isNroPhone(event)"
                                                                    value="{{old('telefono', $empresa->telefono)}}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label>Celular:</label>
                                                                <input type="text" placeholder="" class="form-control" name="celular"
                                                                    id="celular"  onkeypress="return isNroPhone(event)"
                                                                    value="{{old('celular',$empresa->celular)}}">
                                                            </div>

                                                        </div>

                                                        <div class="form-group">
                                                            <label class="required">Condicion: </label>

                                                            <select class="select2_form form-control {{ $errors->has('condicion') ? ' is-invalid' : '' }}" name="condicion" required>
                                                                <option value="0" {{old('condicion') ? (old('condicion') == $empresa->condicion ? "selected" : "") : ($empresa->condicion == "0" ? "selected" : "")}}>PDF DE PRUEBA</option>
                                                                <option value="1" {{old('condicion') ? (old('condicion') == $empresa->condicion ? "selected" : "") : ($empresa->condicion == "1" ? "selected" : "")}}>PDF PERSONALIZADO</option>
                                                            </select>

                                                            @if ($errors->has('condicion'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('codicion') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>



                                                    </div>

                                                    <div class="col-sm-6">

                                                        <div class="form-group row">
                                                            <div class="col-md-12">
                                                                <label id="logo_label">Logo:</label>

                                                                <div class="custom-file">
                                                                    <input id="logo" type="file" name="logo" id="logo"
                                                                        class="custom-file-input {{ $errors->has('logo') ? ' is-invalid' : '' }}"
                                                                        accept="image/*" src="{{ Storage::url($empresa->ruta_logo)}}" @if($empresa->estado_fe) '' @else '' @endif >


                                                                    <label for="logo" id="logo_txt"
                                                                        class="custom-file-label selected {{ $errors->has('ruta') ? ' is-invalid' : '' }}">{{$empresa->nombre_logo ? $empresa->nombre_logo : 'Seleccionar'}}</label>

                                                                    @if ($errors->has('logo'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('logo') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-logo_empresa"></span></b></div>

                                                                </div>

                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="ruta_logo" id="ruta_logo" value="{{old('ruta_logo',$empresa->ruta_logo)}}">

                                                        <div class="form-group row justify-content-center">
                                                            <div class="col-6 align-content-center">
                                                                <div class="row justify-content-end">
                                                                    @if(!$empresa->estado_fe)
                                                                        <a href="javascript:void(0);" id="limpiar_logo">
                                                                            <span class="badge badge-danger">x</span>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                <div class="row justify-content-center">
                                                                    <p>
                                                                        @if($empresa->ruta_logo)
                                                                        <img class="logo" src="{{Storage::url($empresa->ruta_logo)}}" alt="">
                                                                        <input id="url_logo" name="url_logo" type="hidden"
                                                                            value="{{$empresa->ruta_logo}}">
                                                                        @else
                                                                        <img class="logo" src="{{asset('storage/empresas/logos/default.png')}}"
                                                                            alt="">
                                                                        <input id="url_logo" name="url_logo" type="hidden" value="">
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <div class="form-group">
                                                            <h4><b>Redes Sociales</b></h4>
                                                            <p>Modificar redes sociales del proveedor:</p>
                                                        </div>

                                                        <div class="form-group">

                                                                <label class="">Facebook:</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            <i class="fa fa-facebook"></i>
                                                                        </span>
                                                                        <input type="text" id="facebook" name="facebook"
                                                                            class="form-control {{ $errors->has('facebook') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('facebook',$empresa->facebook)}}">

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
                                                                        class="form-control {{ $errors->has('instagram') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('instagram',$empresa->instagram)}}">

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
                                                                        class="form-control {{ $errors->has('web') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('web',$empresa->web)}}">

                                                                        @if ($errors->has('web'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('web') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                </div>

                                                        </div>




                                                    </div>

                                                </div>
                                                <hr>
                                                <div class="row">


                                                    <div class="col-md-6 b-r">
                                                        <div class="col-sm-12">
                                                            <h4><b>Datos del Representante</b></h4>
                                                            <p>Modificar datos del representante:</p>

                                                            <div class="form-group row">
                                                                <div class="col-md-6">
                                                                    <label class="required">Dni:</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control {{ $errors->has('dni_representante') ? ' is-invalid' : '' }}"  name="dni_representante" id="dni_representante" maxlength="8" value="{{old('dni_representante',$empresa->dni_representante)}}" required>
                                                                        <span class="input-group-append"><a style="color:white" onclick="consultarDni()" class="btn btn-primary"><i class="fa fa-search"></i> Reniec</a></span>

                                                                        @if ($errors->has('dni_representante'))
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('dni_representante') }}</strong>
                                                                        </span>
                                                                        @endif
                                                                        <div class="invalid-feedback"><b><span id="error-dni_representante"></span></b></div>
                                                                    </div>

                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="">Estado:</label>
                                                                    <input type="text"
                                                                        class="form-control text-center {{ $errors->has('estado_dni_representante') ? ' is-invalid' : '' }}"
                                                                        name="estado_dni_representante" id="estado_dni_representante"
                                                                        onkeyup="return mayus(this)" value="{{old('estado_dni_representante',$empresa->estado_dni_representante)}}"
                                                                        disabled>

                                                                    @if ($errors->has('estado_dni_representante'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('estado_dni_representante') }}</strong>
                                                                    </span>
                                                                    @endif
                                                                </div>


                                                            </div>

                                                            <div class="form-group">

                                                                <label class="required">Nombre Completo:</label>
                                                                <input type="text"
                                                                    class="form-control {{ $errors->has('nombre_representante') ? ' is-invalid' : '' }}"
                                                                    name="nombre_representante" id="nombre_representante"
                                                                    onkeyup="return mayus(this)" value="{{old('nombre_representante',$empresa->nombre_representante)}}"
                                                                    required>

                                                                @if ($errors->has('nombre_representante'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('nombre_representante') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-nombre_representante"></span></b></div>

                                                            </div>

                                                        </div>
                                                    </div>



                                                    <div class="col-md-6">

                                                        <div class="col-sm-12">

                                                            <h4><b>Datos del R.R.P.P</b></h4>
                                                            <p>Modificar datos del R.R.P.P:</p>

                                                            <div class="form-group row">

                                                                <div class="col-md-6">
                                                                    <label class="required">N° de Asiento:</label>

                                                                    <input type="text"
                                                                        class="form-control {{ $errors->has('num_asiento') ? ' is-invalid' : '' }}"
                                                                        name="num_asiento" id="num_asiento"  onkeyup="return mayus(this)"
                                                                        value="{{old('num_asiento', $empresa->num_asiento)}}">

                                                                    @if ($errors->has('num_asiento'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('num_asiento') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-num_asiento"></span></b></div>

                                                                </div>

                                                                <div class="col-md-6">

                                                                    <label class="required">N° de Partida:</label>
                                                                    <input type="text"
                                                                        class="form-control {{ $errors->has('num_partida') ? ' is-invalid' : '' }}"
                                                                        name="num_partida" id="num_partida"  onkeyup="return mayus(this)"
                                                                        value="{{old('num_partida', $empresa->num_partida)}}">
                                                                    @if ($errors->has('num_partida'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('num_partida') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-num_partida"></span></b></div>


                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>


                                                </div>

                                            </div>
                                        </div>

                                        <div id="tab-2" class="tab-pane">
                                            <div class="panel-body">
                                                <input type="hidden" id="entidades_tabla" name="entidades_tabla[]">
                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <h4><b>Entidades Financieras</b></h4>
                                                        <p>Modificar entidad financiera del proveedor:</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <a class="btn btn-block btn-primary m-t-md"
                                                            style="color:white;" onclick="agregarEntidad()">
                                                            <i class="fa fa-plus-square"></i> Añadir entidad
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="table-responsive">
                                                        <table class="table dataTables-bancos table-striped table-bordered table-hover"
                                                        style="text-transform:uppercase">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">ACCIONES</th>
                                                                    <th class="text-center">DESCRIPCION</th>
                                                                    <th class="text-center">MONEDA</th>
                                                                    <th class="text-center">CUENTA</th>
                                                                    <th class="text-center">CCI</th>
                                                                    <th class="text-center">ITF</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>


                                            </div>
                                        </div>

                                        <div id="tab-3" class="tab-pane">

                                            <div class="panel-body">

                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <h4><b>Facturación Electrónica</b></h4>
                                                        <p>Registrar datos Sunat:</p>
                                                    </div>
                                                </div>

                                                @if ($facturacion)
                                                    <input type="hidden" name="certificado_base" id=certificado_base value="{{old('certificado_base',  $facturacion->certificado  )}}">

                                                @else
                                                    <input type="hidden" name="certificado_base" id=certificado_base value="{{old('certificado_base')}}">
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6 col-xs-12 b-r d-none">
                                                        <p>Certificado Sunat</p>
                                                        <div class="form-group row">
                                                            <div class="col-lg-6 col-xs-12">
                                                                <label for="">Certificado</label>
                                                                <a class="btn btn-primary btn-block  @if(!$empresa->estado_fe) modal_certificado @endif " @if($empresa->estado_fe) disabled @else '' @endif  style="color:white"><i class="fa fa-upload"></i> Subir Certificado (.pfx)</a>
                                                            </div>
                                                            <div class="col-lg-6 col-xs-12">
                                                                <label for="">Estado</label>
                                                                <input type="text" id="estado_certificado" class="form-control text-center" name="estado_certificado" value="{{old('estado_certificado', $empresa->estado_fe == '1' ? 'VERIFICADO' : 'SIN VERIFICAR' )}}" readonly>
                                                            </div>

                                                        </div>


                                                    </div>

                                                    <div class="col-md-6 col-xs-12">
                                                        <p>Usuario Secundario Sunat/OSE</p>

                                                        <div class="form-group row">
                                                            <div class="col-md-6 col-xs-12">
                                                                <label class="required">Soap Usuario:</label>
                                                                <input type="text" class="form-control {{ $errors->has('soap_usuario') ? ' is-invalid' : '' }}" @if($empresa->estado_fe) readonly @else '' @endif  name="soap_usuario" id="soap_usuario"  value="{{old('soap_usuario', $facturacion ? $facturacion->sol_user : '' )}}">
                                                                <div class="invalid-feedback"><b><span id="error-soap_usuario"></span></b></div>
                                                            </div>

                                                            <div class="col-md-6 col-xs-12">
                                                                <label class="required">Soap Contraseña:</label>
                                                                <input type="text" class="form-control {{ $errors->has('soap_password') ? ' is-invalid' : '' }}" @if($empresa->estado_fe) readonly @else '' @endif  name="soap_password" id="soap_password" value="{{old('soap_password', $facturacion ?  $facturacion->sol_pass  : '')}}">
                                                                <div class="invalid-feedback"><b><span id="error-soap_password"></span></b></div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="form-group row">
                                                        <div class="col-md-9">
                                                            <h4><b>Numeración de facturación</b></h4>
                                                            <p>Registrar Numeración de facturación de la Empresa:</p>
                                                            <input type="hidden" id="numeracion_tabla" name="numeracion_tabla[]">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <a class="btn btn-block btn-primary m-t-md"
                                                                style="color:white;" data-toggle="modal" data-target="#modal_numeracion_facturacion">
                                                                <i class="fa fa-plus-square"></i> Añadir
                                                            </a>
                                                        </div>
                                                    </div>



                                                    <div class="form-group">
                                                        <div class="table-responsive">
                                                            <table class="table dataTables-numeracion table-striped table-bordered table-hover"
                                                            style="text-transform:uppercase">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">ACCIONES</th>
                                                                        <th class="text-center">TIPO DE COMPROBANTE</th>
                                                                        <th class="text-center">SERIE</th>
                                                                        <th class="text-center">NÚMERO A INICIAR</th>
                                                                        <th class="text-center">EMISIÓN INICIALIZADA</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>


                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>



                                            </div>

                                        </div>



                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">

                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.</small>
                            </div>

                            <div class="col-md-6 text-right">
                                <a href="{{route('mantenimiento.empresas.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>

                        </div>

                    </form>

                </div>


            </div>
        </div>

    </div>

</div>
@include('mantenimiento.empresas.modal')
@stop

@push('styles')
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">

<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

<!-- iCheck -->
<link href="{{asset('Inspinia/css/plugins/iCheck/custom.css')}}" rel="stylesheet">

<style>
.logo {
    width: 200px;
    height: 200px;
    border-radius: 10%;
}

.custom-file-label::after {
    content: "Buscar";
}
</style>
@endpush

@push('scripts')
<script src="{{asset('Inspinia/js/plugins/select2/select2.full.min.js')}}"></script>

<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>

<!-- iCheck -->
<script src="{{asset('Inspinia/js/plugins/iCheck/icheck.min.js')}}"></script>


<script>

    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });
    // Solo campos numericos
    $('#ruc, #ubigeo_empresa, #dni_representante' ).on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#estado_fe').on('ifChanged', function(event) {
        document.getElementById("facturacion_tab").style.display = "";
        $('#logo_label').addClass("required")
        if ($('#logo_txt').text() == 'Seleccionar') {
            $('#logo').prop("required",true)
        }
        $('#estado_fe').val('1')

        $('#ubigeo_texto').addClass("required")
        $('#ubigeo_empresa').prop("required",true)


    });

    $('#estado_fe').on('ifUnchecked', function(event) {
        document.getElementById("facturacion_tab").style.display = "none";
        $('#logo_label').removeClass("required")
        $('#certificado_base').val('')
        $('#estado_certificado').val('SIN VERIFICAR')
        $('#logo').prop("required",false)
        $('#estado_fe').val('0')

        $('#ubigeo_texto').removeClass("required")
        $('#ubigeo_empresa').prop("required",false)
    });


    /* Limpiar imagen */
    $('#limpiar_logo').click(function() {
        $('.logo').attr("src", "{{asset('storage/empresas/logos/default.png')}}")
        var fileName = "Seleccionar"
        $('.custom-file-label').addClass("selected").html(fileName);
        $('#logo').val('')
        $('#ruta_logo').val('')

    })

    $('#logo').on('change', function() {
        var fileInput = document.getElementById('logo');
        var filePath = fileInput.value;
        var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
        $imagenPrevisualizacion = document.querySelector(".logo");

        if (allowedExtensions.exec(filePath)) {
            var userFile = document.getElementById('logo');
            userFile.src = URL.createObjectURL(event.target.files[0]);
            var data = userFile.src;
            $imagenPrevisualizacion.src = data
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        } else {
            toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
        }
    });

    $("#ruc").keyup(function() {
        $('#estado').val('INACTIVO');
    })

    $('#enviar_empresa_editar').submit(function(e) {
        e.preventDefault();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        Swal.fire({
            title: 'Opción Modificar',
            text: "¿Seguro que desea modificar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {

            if (result.isConfirmed) {

                var existe = entidadFinanciera()
                if (existe == false) {

                    Swal.fire({
                        title: 'Entidad Financiera',
                        text: "¿Seguro que desea modificar Empresa sin ninguna entidad financiera?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: "#1ab394",
                        confirmButtonText: 'Si, Confirmar',
                        cancelButtonText: "No, Cancelar",
                    }).then((result) => {

                        if (result.isConfirmed) {
                            if (validarCertificado() == true) {

                                if ($('#estado').val() == "ACTIVO" || $('#estado').val() == "SIN VERIFICAR" ) {
                                    $("#estado").prop('disabled', false)
                                    $("#estado_dni_representante").prop('disabled', false)
                                    //Cargar Entidades en modal
                                    cargarEntidades()
                                    cargarNumeracion()
                                    this.submit();
                                    Swal.fire({
                                        title: '¡Cargando!',
                                        type: 'info',
                                        icon: 'info',
                                        text: 'Modificando Registro',
                                        showConfirmButton: false,
                                        onBeforeOpen: () => {
                                            Swal.showLoading()
                                        }
                                    })
                                } else {
                                    $("#estado").prop('disabled', true)
                                    $("#estado_dni_representante").prop('disabled', true)
                                    toastr.error('Ingrese una empresa activa o sin verificar', 'Error');
                                }
                            }

                        } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ) {
                            $('#bancos_link').click();
                        }
                    })
                }else{
                    if (validarCertificado() == true) {

                        if ($('#estado').val() == "ACTIVO" || $('#estado').val() == "SIN VERIFICAR") {
                            $("#estado").prop('disabled', false)
                            $("#estado_dni_representante").prop('disabled', false)
                            cargarEntidades()
                            cargarNumeracion()
                            this.submit();
                            Swal.fire({
                                title: '¡Cargando!',
                                type: 'info',
                                icon: 'info',
                                text: 'Modificando Registro',
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading()
                                }
                            })
                        } else {
                            $("#estado").prop('disabled', true)
                            $("#estado_dni_representante").prop('disabled', true)
                            toastr.error('Ingrese una empresa activa', 'Error');
                        }
                    }
                }



            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    })


    $("#ruc").keyup(function() {
        if ($('#estado').val('ACTIVO')) {
            $('#estado').val('SIN VERIFICAR');
        }
    })

    $("#razon_social").keyup(function() {
        if ($('#estado').val('ACTIVO')) {
            $('#estado').val('SIN VERIFICAR');
        }
    })

    $("#razon_social_abreviada").keyup(function() {
        if ($('#estado').val('ACTIVO')) {
            $('#estado').val('SIN VERIFICAR');
        }
    })

    $("#direccion_fiscal").keyup(function() {
        if ($('#estado').val('ACTIVO')) {
            $('#estado').val('SIN VERIFICAR');
        }
    })

    $("#dni_representante").keyup(function() {
        if ($('#estado_dni_representante').val('ACTIVO')) {
            $('#estado_dni_representante').val('SIN VERIFICAR');
        }
    })

    $("#nombre_representante").keyup(function() {
        if ($('#estado_dni_representante').val('ACTIVO')) {
            $('#estado_dni_representante').val('SIN VERIFICAR');
        }
    })


    function camposRuc(objeto) {
        var razonsocial = objeto.value.data.nombre_o_razon_social;
        var nombrecorto = objeto.value.data.nombre_o_razon_social;
        var direccion = objeto.value.data.direccion;
        var departamento ="-";
        var provincia = "-";
        var distrito = "-";
        var estado = objeto.value.data.estado;
        var ubigeo = objeto.value.data.ubigeo[0];

        if (razonsocial != '-' && razonsocial != "NULL") {
            $('#razon_social').val(razonsocial)
        }

        if (nombrecorto != '-' && nombrecorto != "NULL") {
            $('#razon_social_abreviada').val(nombrecorto)
        } else {
            $('#razon_social_abreviada').val(razonsocial)
        }
        if (estado == "ACTIVO") {
            $('#estado').val(estado)
        } else {
            $('#estado').val('INACTIVO')
            toastr.error('Empresa no se encuentra "Activa"', 'Error');
        }

        if (direccion != '-' && direccion != "NULL") {
            $('#direccion_fiscal').val(direccion + " - " + departamento + " - " + provincia + " - " + distrito)
        }

        if (razonsocial != '-' && razonsocial != "NULL") {
            $('#ubigeo_empresa').val(ubigeo)
        }
    }

    // Consulta Dni
    $("#dni_representante").keypress(function() {
        if (event.which == 13) {
            event.preventDefault();
            var dni = $("#dni_representante").val()
            evaluarDni(dni);
        }
    })
    // Consulta Dni
    function consultarDni(dni) {
        var dni = $('#dni_representante').val()
        if (dni.length == 8) {

            Swal.fire({
                title: 'Consultar',
                text: "¿Desea consultar Dni a Reniec?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    var url = '{{ route("getApidni", ":dni")}}';
                    url = url.replace(':dni', dni);

                    return fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            console.log(error)
                            $('#dni_representante').val('SIN VERIFICAR')
                            Swal.showValidationMessage(
                                `Dni Inválido`
                            )
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                camposDni(result)
                consultaExitosa()
            })
        } else {
            toastr.error('El campo Dni debe de contar con 8 dígitos', 'Error');
        }


    }

    function camposDni(objeto) {

        var nombres = objeto.value.nombres;
        var apellidopa = objeto.value.apellidoPaterno;
        var apellidoma = objeto.value.apellidoMaterno;

        var nombre_completo = []

        if (nombres != "-" && nombres != null) {
            nombre_completo.push(nombres)
        }

        if (apellidopa != "-" && apellidopa != null) {
            nombre_completo.push(apellidopa)
        }

        if (apellidoma != "-" && apellidoma != null) {
            nombre_completo.push(apellidoma)
        }

        $('#nombre_representante').val(nombre_completo.join(' '))
        $('#estado_dni_representante').val('ACTIVO')

    }

    $(document).ready(function() {

        //Iniciar I-Checks
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });



        @if(old('estado_fe',$empresa->estado_fe) == '1')
            document.getElementById("facturacion_tab").style.display = "";
            $('#logo_label').addClass("required")
            if ($('#logo_txt').text() == 'Seleccionar') {
                $('#logo').prop("required",true)
            }

            $('#ubigeo_texto').addClass("required")
            $('#ubigeo_empresa').prop("required",true)

        @else
            document.getElementById("facturacion_tab").style.display = "none";
            $('#logo').prop("required",false)
            $('#logo_label').removeClass("required")
            $('#certificado_base').val('')
            $('#estado_certificado').val('SIN VERIFICAR')

            $('#ubigeo_texto').removeClass("required")
            $('#ubigeo_empresa').prop("required",false)

        @endif

        // DataTables
        $('.dataTables-bancos').DataTable({
            "dom": 'Tftp',
            "bPaginate": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },

            "columnDefs": [
                {

                    "targets": [0],
                    "width": "10%" ,
                    className: "text-center",
                    render: function(data, type, row) {
                        return "<div class='btn-group'>" +
                            "<a class='btn btn-warning btn-sm modificarDetalle' id='editar_entidad' style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                            "<a class='btn btn-danger btn-sm' id='borrar_entidad' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                            "</div>";
                    }
                },
                {
                    "targets": [1],
                    "width": "20%"
                },
                {
                    "targets": [2],
                    className: "text-center",
                    "width": "10%"
                },
                {
                    "targets": [3],
                    className: "text-center",
                    "width": "20%"
                },
                {
                    "targets": [4],
                    className: "text-center",
                    "width": "20%"
                },
                {
                    "targets": [5],
                    className: "text-center",
                    "width": "20%"
                },

            ],

        });
        obtenerTabla()

        //Ruta Numeracion
        var url = '{{ route("mantenimiento.empresas.obtenerNumeracion", ":id")}}';
        url = url.replace(':id', "{{$empresa->id}}");
        $('.dataTables-numeracion').DataTable({
            "dom": 'Tftp',
            "bPaginate": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "ajax": url,
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },

            "columnDefs": [
                {

                    "targets": [0],
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        if (data.emision == '1') {
                            return  '-';
                        }else{
                            return "<div class='btn-group'>" +
                            "<a class='btn btn-danger btn-sm' id='borrar_numeracion' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                            "</div>";
                        }

                    }
                },
                {
                    "targets": [1],
                    data: 'tipo_comprobante',
                },
                {
                    "targets": [2],
                    data: 'serie',
                    className: "text-center",

                },
                {
                    "targets": [3],
                    data: 'numero_iniciar',
                    className: "text-center",

                },
                {
                    "targets": [4],
                    data: null,
                    className: "text-center",
                    render: function(data) {

                        if (data.emision == '1') {
                            return  'SI';
                        }else{
                            return "NO";
                        }

                    }

                },
                {
                    "targets": [5],
                    visible: false,
                    data: 'tipo_id',

                },

            ],

        });

        // obtenerNumeracion()

    })

    function obtenerTabla() {
        var t = $('.dataTables-bancos').DataTable();
            @foreach($banco as $ban)
            t.row.add([
                '',
                "{{$ban->descripcion}}",
                "{{$ban->tipo_moneda}}",
                "{{$ban->num_cuenta}}",
                "{{$ban->cci}}",
                "{{$ban->itf}}",
            ]).draw(false);
            @endforeach
    }

    function obtenerNumeracion() {
        var t = $('.dataTables-numeracion').DataTable();
            @foreach($numeraciones as $numero)
            t.row.add([
                '',
                "{{$numero->comprobanteDescripcion()}}",
                "{{$numero->serie}}",
                "{{$numero->numero_iniciar}}",
                "{{$numero->emision_iniciada}}",
                "{{$numero->tipo_comprobante}}",
            ]).draw(false);
            @endforeach
    }

    //Añadir Entidad Financiera
    function agregarEntidad() {
        $('#modal_agregar_entidad').modal('show');
        limpiarForm();
    }

    //Consultar si ingesar entidad financiera
    function entidadFinanciera() {
        var existe = true
        var table = $('.dataTables-bancos').DataTable();
        var registros = table.rows().data().length;

        if (registros == 0) {
            existe = false
        }
        return existe
    }

    $('.modal_certificado').click(function(){
        $('#modal_certificado').modal('show');
    })

    $('.tabs-container .nav-tabs #bancos_link , .tabs-container .nav-tabs #facturacion_link').click(function() {
        limpiarErrores()
        var enviar = true;

        if ( $('#estado_fe').prop("checked") || !$('#estado_fe').prop("checked")) {
            if ($('#logo_txt').text() == 'Seleccionar') {
                enviar = false
                $('#logo').addClass("is-invalid")
                toastr.error("Ingrese Logo de la empresa.", 'Error');
                $('#error-logo_empresa').text("El campo Logo es obligatorio.")
            }

            if (!$('#ubigeo_empresa').val()) {
                enviar = false
                $('#ubigeo_empresa').addClass("is-invalid")
                toastr.error("Ingrese el ubigeo de la dirección fiscal de la empresa.", 'Error');
                $('#error-empresa_ubigeo').text("El campo Ubigeo es obligatorio.")
            }

        }else{
            enviar = true
            $('#logo').removeClass("is-invalid")
            $('#error-logo_empresa').text("")
        }


        if ($('#ruc').val() == '') {
            enviar = false
            $('#ruc').addClass("is-invalid")
            toastr.error("Ingrese Ruc de la empresa.", 'Error');
            $('#error-ruc').text("El campo Ruc es obligatorio.")
        }

        if ($('#razon_social').val() == '') {
            enviar = false
            $('#razon_social').addClass("is-invalid")
            toastr.error("Ingrese Razón Social de la empresa.", 'Error');
            $('#error-razon_social').text("El campo Razón Social es obligatorio.")
        }

        if ($('#direccion_fiscal').val() == '') {
            enviar = false
            $('#direccion_fiscal').addClass("is-invalid")
            toastr.error("Ingrese la Dirección Fiscal de la Empresa.", 'Error');
            $('#error-direccion_fiscal').text("El campo Dirección Fiscal es obligatorio.")
        }


        if ($('#direccion_llegada').val() == '') {
            enviar = false
            $('#direccion_llegada').addClass("is-invalid")
            toastr.error("Ingrese la Dirección de Llegada de la Empresa.", 'Error');
            $('#error-direccion_llegada').text("El campo Dirección de Llegada es obligatorio.")
        }

        if ($('#dni_representante').val() == '') {
            enviar = false
            $('#dni_representante').addClass("is-invalid")
            toastr.error("Ingrese el Dni del Representante de la Empresa.", 'Error');
            $('#error-dni_representante').text("El campo Dni es obligatorio.")
        }

        if ($('#nombre_representante').val() == '') {
            enviar = false
            $('#nombre_representante').addClass("is-invalid")
            toastr.error("Ingrese el Nombre Completo del Representante de la Empresa.", 'Error');
            $('#error-nombre_representante').text("El campo Nombre Completo es obligatorio.")
        }

        if ($('#num_asiento').val() == '') {
            enviar = false
            $('#num_asiento').addClass("is-invalid")
            toastr.error("Ingrese el N° de Asiento de la Empresa.", 'Error');
            $('#error-num_asiento').text("El campo N° de Asiento es obligatorio.")
        }

        if ($('#num_partida').val() == '') {
            enviar = false
            $('#num_partida').addClass("is-invalid")
            toastr.error("Ingrese el N° de Partida de la Empresa.", 'Error');
            $('#error-num_partida').text("El campo N° de Partida es obligatorio.")
        }

        return enviar

    })


    function limpiarErrores() {
        $('#ruc').removeClass("is-invalid")
        $('#error-ruc').text("")

        $('#razon_social').removeClass("is-invalid")
        $('#error-razon_social').text("")

        $('#direccion_fiscal').removeClass("is-invalid")
        $('#error-direccion_fiscal').text("")

        $('#direccion_llegada').removeClass("is-invalid")
        $('#error-direccion_llegada').text("")

        $('#dni_representante').removeClass("is-invalid")
        $('#error-dni_representante').text("")

        $('#nombre_representante').removeClass("is-invalid")
        $('#error-nombre_representante').text("")

        $('#num_asiento').removeClass("is-invalid")
        $('#error-num_asiento').text("")

        $('#num_partida').removeClass("is-invalid")
        $('#error-num_partida').text("")

        $('#logo').removeClass("is-invalid")
        $('#error-logo_empresa').text("")

        $('#ubigeo_empresa').removeClass("is-invalid")
        $('#error-empresa_ubigeo').text("")

    }

    function consultarRuc() {
        var ruc = $('#ruc').val()

        if (ruc.length == 11) {
            Swal.fire({
                title: 'Consultar',
                text: "¿Desea consultar Ruc a Sunat?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    var url = '{{ route("getApiruc", ":ruc")}}';
                    url = url.replace(':ruc', ruc);
                    return fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            console.log(error)
                            Swal.showValidationMessage(
                                `Ruc Inválido`
                            )
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                // $('#ruc').removeClass('is-invalid')
                console.log(result)
                camposRuc(result)
                consultaExitosa()
            })

        } else {
            toastr.error('El campo Ruc debe de contar con 11 dígitos', 'Error');
        }
    }

    //CERTIFICADO

    $('#certificado').on('change', function() {
        var fileInput = document.getElementById('certificado');
        var filePath = fileInput.value;
        var allowedExtensions = /(.pfx)$/i;

        if (allowedExtensions.exec(filePath)) {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        } else {
            toastr.error('Extensión inválida, formatos admitidos (.pfx)', 'Error');
        }

    });

    function validarCertificado() {
        var correcto = true

        if ($('#estado_fe').val()) {
            if ( $('#estado_fe').prop("checked")  ) {
                // if ($('#estado_certificado').val() != "VERIFICADO") {
                //     correcto = false
                //     toastr.error("Certitificado Incorrecto.", 'Error');
                //     $('#facturacion_link').click();
                // }

                if ($('#logo_txt').text() == 'Seleccionar') {
                    correcto = false
                    toastr.error("Ingrese Logo de la empresa.", 'Error');
                    $('#empresas_link').click();
                }

                if ($('#soap_usuario').val() == "") {
                    correcto = false
                    $('#soap_usuario').addClass("is-invalid")
                    toastr.error("Ingrese Soap Usuario Sunat.", 'Error');
                    $('#error-soap_usuario').text("El campo Soap Usuario es obligatorio.")

                    $('#facturacion_link').click();

                }

                if ($('#soap_password').val() == "") {
                    correcto = false
                    $('#soap_password').addClass("is-invalid")
                    toastr.error("Ingrese Soap Contraseña Sunat.", 'Error');
                    $('#error-soap_password').text("El campo Soap Contraseña es obligatorio.")

                    $('#facturacion_link').click();
                }
            }
        }else{
            $('#soap_usuario').val('')
            $('#soap_password').val('')
        }


        var table = $('.dataTables-numeracion').DataTable();
        var registros = table.rows().data().length;
        if ($('#estado_fe').val()) {
            if ( $('#estado_fe').prop("checked") ) {
                if (registros == 0) {
                    correcto = false
                    toastr.error("Debe de ingresar al menos 1 Numeración de facturación.", 'Error');
                    $('#facturacion_link').click();
                }
            }
        }
        return correcto
    }


</script>

@endpush
