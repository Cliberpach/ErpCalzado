@extends('layout') @section('content')
@section('compras-active', 'active')
@section('proveedor-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVO PROVEEDOR</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('compras.proveedor.index')}}">Proveedores</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">

                <div class="ibox-content">

                    <form action="{{route('compras.proveedor.store')}}" method="POST" id="enviar_proveedor">
                        {{csrf_field()}}

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="tabs-container">
                                    <ul class="nav nav-tabs">
                                        <li title="Datos del Proveedor">
                                            <a class="nav-link active" data-toggle="tab" href="#tab-5"><i class="fa fa-check-square"></i> Proveedor</a>
                                        </li>
                                        <li title="Datos de Entidades Financieras">
                                            <a class="nav-link" data-toggle="tab"href="#tab-6" id="bancos_link"> <i class="fa fa-user"></i> Bancos</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="tab-5" class="tab-pane active">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-6 b-r">
                                                        <h4 class=""><b>Proveedor</b></h4>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <p>Registrar datos del nuevo Proveedor:</p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">

                                                            <div class="col-md-6">
                                                                <label class="required">Documento: </label>
                                                                <div class="row mt-1" align="center">
                                                                    <div class="col">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="tipo_documento"
                                                                                id="tipo_documento_ruc" value="RUC"
                                                                                @if(old('tipo_documento')=="RUC" ) {{'checked'}}@endif>
                                                                            <label class="form-check-label">Ruc</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="tipo_documento"
                                                                                id="tipo_documento_dni" value="DNI"
                                                                                @if(old('tipo_documento')=="DNI" ) {{'checked'}}@endif>
                                                                            <label class="form-check-label">Dni</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>


                                                            <div class="col-md-6">
                                                                <label class="required">Tipo: </label>
                                                                <select
                                                                    class="select2_form form-control {{ $errors->has('tipo_persona') ? ' is-invalid' : '' }}"
                                                                    style="text-transform: uppercase; width:100%"
                                                                    value="{{old('tipo_persona')}}" name="tipo_persona" id="tipo_persona"
                                                                    required>
                                                                    <option></option>
                                                                    @foreach ($tipos as $tipo)
                                                                    <option value="{{$tipo->descripcion}}" @if(old('tipo_persona')==$tipo->
                                                                        descripcion ) {{'selected'}} @endif >{{$tipo->descripcion}}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback"><b><span id="error-tipo"></span></b></div>

                                                                <input type="text" disabled id="tipo_persona_dni" class="form-control"
                                                                    name="tipo_persona_dni" value="PERSONA CON DNI">

                                                                @if ($errors->has('tipo_persona'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('tipo_persona') }}</strong>
                                                                </span>
                                                                @endif

                                                            </div>


                                                        </div>

                                                        <div class="form-group row">

                                                            <div class="col-md-6" id="ruc_requerido">

                                                                <label class="required">Ruc: </label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control {{ $errors->has('ruc') ? ' is-invalid' : '' }}"  name="ruc" id="ruc" maxlength="11" value="{{old('ruc')}}" required> 
                                                                    <span class="input-group-append"><a style="color:white" onclick="consultarRuc()" class="btn btn-primary"><i class="fa fa-search"></i> Sunat</a></span>
                                                                    @if ($errors->has('ruc'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('ruc') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-ruc"></span></b></div>
                                                                </div>

                                                            </div>

                                                            <div class="col-md-6" id="dni_requerido" style="display:none;">
                                                                <label class="required">Dni: </label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control {{ $errors->has('dni') ? ' is-invalid' : '' }}"  name="dni" id="dni" maxlength="8" value="{{old('dni')}}"> 
                                                                    <span class="input-group-append"><a style="color:white" onclick="consultarDni()" class="btn btn-primary"><i class="fa fa-search"></i> Reniec</a></span>
                                                                    @if ($errors->has('dni'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('dni') }}</strong>
                                                                    </span>
                                                                    @endif

                                                                    <div class="invalid-feedback"><b><span id="error-dni"></span></b></div>
                                                                </div>

                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="">Estado: </label>
                                                                <input type="text" id="estado"
                                                                    class="form-control text-center {{ $errors->has('estado') ? ' is-invalid' : '' }}"
                                                                    name="estado" value="{{old('estado',"SIN VERIFICAR")}}"
                                                                    onkeyup="return mayus(this)" disabled>
                                                                @if ($errors->has('estado'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('estado') }}</strong>
                                                                </span>
                                                                @endif

                                                            </div>

                                                        </div>
                                                        
                                                        <div class="form-group row">
                                                            <div class="col-md-12">
                                                                <label class="required">Descripción: </label>
                                                                <input type="text"
                                                                    class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
                                                                    name="descripcion" value="{{ old('descripcion')}}" id="descripcion"
                                                                    onkeyup="return mayus(this)" required>

                                                                @if ($errors->has('descripcion'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-descripcion"></span></b></div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-12">
                                                                <label class="required">Dirección:</label>
                                                                <textarea type="text" id="direccion" name="direccion"
                                                                    class="form-control {{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                                                                    value="{{old('direccion')}}"  onkeyup="return mayus(this)"
                                                                    required>{{old('direccion')}}</textarea>
                                                                @if ($errors->has('direccion'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('direccion') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-direccion"></span></b></div>
                                                            </div>

                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label class="required">Zona: </label>
                                                                <select
                                                                    class="select2_form form-control {{ $errors->has('zona') ? ' is-invalid' : '' }}"
                                                                    style="text-transform: uppercase; width:100%" value="{{old('zona')}}"
                                                                    name="zona" id="zona" required>
                                                                    <option></option>
                                                                    @foreach ($zonas as $zona)
                                                                    <option value="{{$zona->descripcion}}" @if(old('zona')==$zona->descripcion )
                                                                        {{'selected'}} @endif >{{$zona->descripcion}}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('zona'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('zona') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-zona"></span></b></div>

                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="required">Correo:</label>
                                                                <input type="email" placeholder=""
                                                                    class="form-control {{ $errors->has('correo') ? ' is-invalid' : '' }}"
                                                                    name="correo" id="correo"  onkeyup="return mayus(this)"
                                                                    value="{{old('correo')}}" required>
                                                                @if ($errors->has('correo'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('correo') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-correo"></span></b></div>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label class="required">Teléfono:</label>
                                                                <input type="text" placeholder=""
                                                                    class="form-control {{ $errors->has('telefono') ? ' is-invalid' : '' }}" required
                                                                    name="telefono" id="telefono"  onkeyup="return mayus(this)"
                                                                    value="{{old('telefono')}}">
                                                                @if ($errors->has('telefono'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('telefono') }}</strong>
                                                                </span>
                                                                @endif
                                                                <div class="invalid-feedback"><b><span id="error-telefono"></span></b></div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label>Celular:</label>
                                                                <input type="text" placeholder=""
                                                                    class="form-control {{ $errors->has('celular') ? ' is-invalid' : '' }}"
                                                                    name="celular" id="celular"  onkeyup="return mayus(this)"
                                                                    value="{{old('celular')}}">
                                                                @if ($errors->has('celular'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('celular') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>

                                                        </div>


                                                    </div>

                                                    <div class="col-sm-6">
                                                        <h4><b>Datos Adicionales</b></h4>
                                                        <p>Registrar datos adicionales del proveedor:</p>

                                                        

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="tabs-container">
                                                                    <ul class="nav nav-tabs">
                                                                        <li title="Datos de Calidad"><a class="nav-link active"
                                                                                data-toggle="tab" href="#tab-1"><i
                                                                                    class="fa fa-check-square"></i> Calidad</a></li>
                                                                        <li title="Datos de Contacto"><a class="nav-link" data-toggle="tab"
                                                                                href="#tab-2" id="contacto_link"> <i class="fa fa-user"></i> Contacto</a></li>
                                                                        <li title="Datos de Tranporte"><a class="nav-link" data-toggle="tab"
                                                                                href="#tab-3" id="transporte_link"> <i class="fa fa-bus"></i> Transporte</a></li>
                                                                        <li title="Datos de Almacen"><a class="nav-link" data-toggle="tab"
                                                                                href="#tab-4"> <i class="fa fa-building"></i> Almacen</a></li>
                                                                        
                                                                    </ul>
                                                                    <div class="tab-content">
                                                                        <div id="tab-1" class="tab-pane active">
                                                                            <div class="panel-body">
                                                                                <p><b>Registrar datos del calidad:</b></p>
                                                                                <div class="form-group">

                                                                                    <label>Nombre Completo:</label>
                                                                                    <input type="text" placeholder=""
                                                                                        class="form-control {{ $errors->has('calidad') ? ' is-invalid' : '' }}"
                                                                                        name="calidad" id="calidad"
                                                                                        onkeyup="return mayus(this)"
                                                                                        value="{{old('calidad')}}">
                                                                                    @if ($errors->has('calidad'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('calidad') }}</strong>
                                                                                    </span>
                                                                                    @endif

                                                                                </div>

                                                                                <div class="form-group row">
                                                                                    <div class="col-md-6">
                                                                                        <label>Teléfono:</label>
                                                                                        <input type="text" placeholder=""
                                                                                            class="form-control {{ $errors->has('telefono_calidad') ? ' is-invalid' : '' }}"
                                                                                            name="telefono_calidad" id="telefono_calidad"
                                                                                            onkeyup="return mayus(this)"
                                                                                            value="{{old('telefono_calidad')}}">
                                                                                        @if ($errors->has('telefono_calidad'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('telefono_calidad') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label>Celular:</label>
                                                                                        <input type="text" placeholder=""
                                                                                            class="form-control {{ $errors->has('celular_calidad') ? ' is-invalid' : '' }}"
                                                                                            name="celular_calidad" id="celular_calidad"
                                                                                            onkeyup="return mayus(this)"
                                                                                            value="{{old('celular_calidad')}}">
                                                                                        @if ($errors->has('celular_calidad'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('celular_calidad') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="form-group">

                                                                                    <label>Correo:</label>
                                                                                    <input type="email" placeholder=""
                                                                                        class="form-control {{ $errors->has('correo_calidad') ? ' is-invalid' : '' }}"
                                                                                        name="correo_calidad" id="correo_calidad"
                                                                                        onkeyup="return mayus(this)"
                                                                                        value="{{old('correo_calidad')}}">
                                                                                    @if ($errors->has('correo_calidad'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('correo_calidad') }}</strong>
                                                                                    </span>
                                                                                    @endif

                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <div id="tab-2" class="tab-pane">
                                                                            <div class="panel-body">
                                                                                <p><b>Registrar datos del contacto:</b></p>
                                                                                <div class="form-group">

                                                                                    <label class="required">Nombre Completo:</label>
                                                                                    <input type="text" placeholder=""
                                                                                        class="form-control {{ $errors->has('web') ? ' is-invalid' : '' }}"
                                                                                        name="contacto" id="contacto"
                                                                                        onkeyup="return mayus(this)"
                                                                                        value="{{old('contacto')}}">
                                                                                    @if ($errors->has('contacto'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('contacto') }}</strong>
                                                                                    </span>
                                                                                    @endif
                                                                                    <div class="invalid-feedback"><b><span id="error-nombre_contacto"></span></b></div> 

                                                                                </div>

                                                                                <div class="form-group">

                                                                                    <label class="required">Correo:</label>
                                                                                    <input type="text" placeholder=""
                                                                                        class="form-control {{ $errors->has('web') ? ' is-invalid' : '' }}"
                                                                                        name="correo_contacto" id="correo_contacto"
                                                                                        onkeyup="return mayus(this)"
                                                                                        value="{{old('correo_contacto')}}">
                                                                                    @if ($errors->has('correo_contacto'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('correo_contacto') }}</strong>
                                                                                    </span>
                                                                                    @endif
                                                                                    <div class="invalid-feedback"><b><span id="error-correo_contacto"></span></b></div> 

                                                                                </div>

                                                                                <div class="form-group row">
                                                                                    <div class="col-md-6">
                                                                                        <label class="required">Teléfono:</label>
                                                                                        <input type="text" placeholder=""
                                                                                            class="form-control {{ $errors->has('telefono_contacto') ? ' is-invalid' : '' }}"
                                                                                            name="telefono_contacto" id="telefono_contacto"
                                                                                            onkeyup="return mayus(this)"
                                                                                            value="{{old('telefono_contacto')}}">
                                                                                        @if ($errors->has('telefono_contacto'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('telefono_contacto') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                        <div class="invalid-feedback"><b><span id="error-telefono_contacto"></span></b></div> 
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label>Celular:</label>
                                                                                        <input type="text" placeholder=""
                                                                                            class="form-control {{ $errors->has('celular_contacto') ? ' is-invalid' : '' }}"
                                                                                            name="celular_contacto" id="celular_contacto"
                                                                                            onkeyup="return mayus(this)"
                                                                                            value="{{old('celular_contacto')}}">
                                                                                        @if ($errors->has('celular_contacto'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('celular_contacto') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <div id="tab-3" class="tab-pane">
                                                                            <div class="panel-body">

                                                                                <p><b>Registrar datos del transporte:</b></p>


                                                                                
                                                                                <div class="form-group row">

                                                                                    <div class="col-md-6">

                                                                                        <label>Ruc:</label>
                                                                                        <div class="input-group">
                                                                                            <input type="text" class="form-control {{ $errors->has('ruc_transporte') ? ' is-invalid' : '' }}"  name="ruc_transporte" id="ruc_transporte" maxlength="11" value="{{old('ruc_transporte')}}"> 
                                                                                            <span class="input-group-append"><a style="color:white" onclick="consultarRuctransporte()" class="btn btn-primary"><i class="fa fa-search"></i> Sunat</a></span>
                                                                                            @if ($errors->has('ruc_transporte'))
                                                                                            <span class="invalid-feedback" role="alert">
                                                                                                <strong>{{ $errors->first('ruc_transporte') }}</strong>
                                                                                            </span>
                                                                                            @endif

                                                                                            <div class="invalid-feedback"><b><span id="error-ruc_transporte"></span></b></div>
                                                                                        </div>
                                                                                 
                                                                                    
                                                                                    </div>

                                                                                    <div class="col-md-6">

                                                                                    <label>Estado: </label>
                                                                                        <input type="text" id="estado_transporte"
                                                                                            class="form-control text-center {{ $errors->has('estado_transporte') ? ' is-invalid' : '' }}"
                                                                                            name="estado_transporte" value="{{old('estado_transporte',"SIN VERIFICAR")}}"
                                                                                            onkeyup="return mayus(this)" disabled>
                                                                                        @if ($errors->has('estado_transporte'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('estado_transporte') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                        <!-- <div class="invalid-feedback"><b><span id="error-estado_transporte"></span></b></div>  -->
                                                                                
                                                                                    </div>




                                                                                </div>

                                                                                <div class="form-group">
                                                                                
                                                                                        <label>Nombre Completo:</label>
                                                                                        <input type="text" placeholder=""
                                                                                            class="form-control {{ $errors->has('transporte') ? ' is-invalid' : '' }}" 
                                                                                            name="transporte" id="transporte"
                                                                                            onkeyup="return mayus(this)"
                                                                                            value="{{old('transporte')}}">
                                                                                        @if ($errors->has('transporte'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('transporte') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                        <div class="invalid-feedback"><b><span id="error-transporte"></span></b></div> 
                                                                                </div>


                                                                                <div class="form-group">
                                                                                    <label>Dirección:</label>
                                                                                    <textarea type="text" placeholder=""
                                                                                        class="form-control {{ $errors->has('direccion_transporte') ? ' is-invalid' : '' }}" 
                                                                                        name="direccion_transporte" id="direccion_transporte"
                                                                                        onkeyup="return mayus(this)" rows='3'
                                                                                        value="{{old('direccion_transporte')}}">{{old('direccion_transporte')}}</textarea>
                                                                                    @if ($errors->has('direccion_transporte'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('direccion_transporte') }}</strong>
                                                                                    </span>
                                                                                    @endif

                                                                                    <div class="invalid-feedback"><b><span id="error-direccion_transporte"></span></b></div> 


                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                        <div id="tab-4" class="tab-pane">
                                                                            <div class="panel-body">
                                                                                <p><b>Registrar datos del almacen:</b></p>


                                                                                <div class="form-group">
                                                                                    <label>Dirección:</label>
                                                                                    <textarea type="text" placeholder=""
                                                                                        class="form-control {{ $errors->has('direccion_almacen') ? ' is-invalid' : '' }}"
                                                                                        name="direccion_almacen" id="direccion_almacen"
                                                                                        onkeyup="return mayus(this)"
                                                                                        value="{{old('direccion_almacen')}}">{{old('direccion_almacen')}}</textarea>
                                                                                    @if ($errors->has('direccion_almacen'))
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $errors->first('direccion_almacen') }}</strong>
                                                                                    </span>
                                                                                    @endif
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-12">


                                                                <div class="form-group">
                                                                    <h4><b>Redes Sociales</b></h4>
                                                                    <p>Registrar redes sociales del proveedor:</p>
                                                                </div>

                                                                <div class="form-group">
                                                                        
                                                                        <label class="">Facebook:</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon">
                                                                                    <i class="fa fa-facebook"></i>
                                                                                </span>
                                                                                <input type="text" id="facebook" name="facebook"
                                                                                    class="form-control {{ $errors->has('facebook') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('facebook')}}">

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
                                                                                class="form-control {{ $errors->has('instagram') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('instagram')}}">

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
                                                                                class="form-control {{ $errors->has('web') ? ' is-invalid' : '' }}" onkeyup="return mayus(this)"  value="{{old('web')}}">

                                                                                @if ($errors->has('web'))
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $errors->first('web') }}</strong>
                                                                                </span>
                                                                                @endif
                                                                        </div>
                                                                    
                                                                </div>


                                                            </div>



                                                        </div>


                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div id="tab-6" class="tab-pane">

                                            <div class="panel-body">

                                                <div class="form-group row">
                                                    <div class="col-md-9">
                                                        <h4><b>Entidades Financieras</b></h4>
                                                        <p>Registrar entidad financiera del proveedor:</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <a class="btn btn-block btn-primary m-t-md"
                                                            href="#" onclick="agregarEntidad()">
                                                            <i class="fa fa-plus-square"></i> Añadir entidad
                                                        </a>
                                                    </div>
                                                </div>

                                                <input type="hidden" id="entidades_tabla" name="entidades_tabla[]">
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
                                <a href="{{route('compras.proveedor.index')}}" id="btn_cancelar"
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
@include('compras.proveedores.modal')
@stop

@push('styles')
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

<style>
.logo {
    width: 200px;
    height: 200px;
    border-radius: 10%;
}

div.dataTables_wrapper div.dataTables_paginate ul.pagination {  
    margin-left:2px;
}
</style>
@endpush

@push('scripts')

<!-- Select2 -->
<script src="{{asset('Inspinia/js/plugins/select2/select2.full.min.js')}}"></script>

<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>

<script>
$(document).ready(function() {

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
                "width": "20%",
                className: "text-center",
            },

        ],

    });

})

$.fn.select2.defaults.set('language', 'es');

//Select2
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    height: '200px',
    width: '100%',
});

// Solo campos numericos
$('#ruc').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#ruc_transporte').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#dni').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

//Old tipo de Documento
@if(!old('tipo_documento'))
$('#tipo_documento_ruc').prop("checked", true)
$('#tipo_persona_dni').hide();
@endif

@if(old('tipo_documento') == "RUC")
//Ocultar Tipo de Persona
$('#ruc_requerido').show();
$("#tipo_persona").select2().next().show();
$("#tipo_persona").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    height: '200px',
    width: '100%',
});
//Mostrar Campos Dni
$('#dni_requerido').hide();
$('#tipo_persona_dni').hide();
$("#tipo_persona").prop('required', true);

@endif

@if(old('tipo_documento') == "DNI")

//Ocultar Tipo de Persona
$('#ruc_requerido').hide();
$("#tipo_persona").select2().next().hide();
//Mostrar Campos Dni
$('#dni_requerido').show();
$('#tipo_persona_dni').show();
$("#tipo_persona").prop('required', false);

@endif


$("input[name=tipo_documento]").click(function () { 
    //Limpiar comentarios de errores
    limpiarErrores()
    // Cambiar el estado
    $('#estado').val('SIN VERIFICAR')
    ///////////////////////////////////    
    if ($(this).val() == 'RUC') {
        //Limpiar Inputs
        $('#ruc').val('');
        $('#dni').val('');
        $('#descripcion').val('');
        $('#direccion').val('');
        $("#tipo_persona").val('');

        //////////////////////////

        //Ocultar Tipo de Persona
        $('#ruc_requerido').show();
        $("#tipo_persona").select2().next().show();
        $("#tipo_persona").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
        //Mostrar Campos Dni
        $('#dni_requerido').hide();
        $('#tipo_persona_dni').hide();
        //Campos requeridos
        $("#dni").prop('required', false);
        $("#ruc").prop('required', true);
        $("#tipo_persona").prop('required', true);
    } else {
        //Limpiar Inputs
      
        $('#ruc').val('');
        $('#dni').val('');
        $('#descripcion').val('');
        $('#direccion').val('');
        //////////////////////////
        //Ocultar Tipo de Persona
        $('#ruc_requerido').hide();
        $("#tipo_persona").select2().next().hide();
        //Mostrar Campos Dni
        $('#dni_requerido').show();
        $('#tipo_persona_dni').show();
        //Campos requeridos
        $("#dni").prop('required', true);
        $("#ruc").prop('required', false);
        $("#tipo_persona").prop('required', false);

    }

})



$("#ruc").keyup(function() {
    if ($('#estado').val('ACTIVO')) {
        $('#estado').val('SIN VERIFICAR');
    }
})

$("#descripcion").keyup(function() {
    if ($('#estado').val('ACTIVO')) {
        $('#estado').val('SIN VERIFICAR');
    }
})

$("#direccion").keyup(function() {
    if ($('input:radio[name=tipo_documento]:checked').val() == "RUC") {
        if ($('#estado').val('ACTIVO')) {
            $('#estado').val('SIN VERIFICAR');
        }
    }

})


//Validar datos del tranportista
$("#ruc_transporte").keyup(function() {
    if ($('#estado_transporte').val('ACTIVO')) {
        $('#estado_transporte').val('SIN VERIFICAR');
    }
})

$("#transporte").keyup(function() {
    $('#estado_transporte').val('SIN VERIFICAR');
})

$("#direccion_transporte").keyup(function() {
    $('#estado_transporte').val('SIN VERIFICAR');
})


$("#dni").keyup(function() {
    if ($('#estado').val('ACTIVO')) {
        $('#estado').val('SIN VERIFICAR');
    }
})

$('#enviar_proveedor').submit(function(e) {
    e.preventDefault();
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: 'Opción Guardar',
        text: "¿Seguro que desea guardar cambios?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var error = false
            limpiarErrores()
            // //Transportista
            // var enviar = enviarDatosTransportista()
            // if (enviar != false) {
            //     toastr.error('Campos incompletos en Transportista', 'Error');
            //     $('#transporte_link').click();
            //     error = true
            // }

            //Contacto
            var enviar = enviarDatosContacto()
            if (enviar != false) {
                toastr.error('Campos incompletos en Contacto', 'Error');
                $('#contacto_link').click();
                error = true
            }


            if ($('#estado_transporte').val() != "ACTIVO" ) {
                    if ($('#estado_transporte').val() != "SIN VERIFICAR" ) {
                        toastr.error('Ingrese un transporte activo', 'Error');
                        error = true 
                    }

            } 

            if ($('#estado').val() != "ACTIVO") {
                    if ($('#estado').val() != "SIN VERIFICAR" ) {
                        toastr.error('Ingrese un proveedor activo', 'Error');
                        error = true 
                    }
            } 

            if (error == false) {
                
                var existe = entidadFinanciera()
            
                if (existe == false) {
                    
                    Swal.fire({
                        title: 'Entidad Financiera',
                        text: "¿Seguro que desea agregar Proveedor sin ninguna entidad financiera?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: "#1ab394",
                        confirmButtonText: 'Si, Confirmar',
                        cancelButtonText: "No, Cancelar",
                    }).then((result) => {
                        
                        if (result.isConfirmed) {
                            $("#estado").prop('disabled', false)
                            $("#tipo_persona").prop('disabled', false);
                            $("#tipo_persona_dni").prop('disabled', false);
                            $("#estado_transporte").prop('disabled', false)
                            this.submit();
                            //Cargar Entidades en modal
                            cargarEntidades()
                        } else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ) {
                            $('#bancos_link').click();
                        }
                    })
                }else{
                    $("#estado").prop('disabled', false)
                    $("#tipo_persona").prop('disabled', false);
                    $("#tipo_persona_dni").prop('disabled', false);
                    $("#estado_transporte").prop('disabled', false)
                    this.submit();
                    //Cargar Entidades en modal
                    cargarEntidades()
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



function consultarRuc() {
    limpiarErrores()
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
                        $('#estado').val('SIN VERIFICAR')
                        Swal.showValidationMessage(
                            `Ruc Inválido`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            console.log(result)
           
            camposRuc(result)
            consultaExitosa()
        })
    } else {
        toastr.error('El campo Ruc debe de contar con 11 dígitos', 'Error');
    }
}

function consultarRuctransporte() {
    limpiarErroresTransportista()
    var ruc = $('#ruc_transporte').val()
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
            console.log(result)
            // $('#ruc_transporte').removeClass('is-invalid')
            camposRuctransporte(result)
            consultaExitosa()
        })
    } else {
        toastr.error('El campo Ruc debe de contar con 11 dígitos', 'Error');
    }


}

function camposRuctransporte(objeto) {
    var razonsocial = objeto.value.razonSocial;
    var direccion = objeto.value.direccion;
    var departamento = objeto.value.departamento;
    var provincia = objeto.value.provincia;
    var distrito = objeto.value.distrito;
    var estado = objeto.value.estado;

    if (razonsocial != '-' && razonsocial != "NULL") {
        $('#transporte').val(razonsocial)
    }

    if (estado == "ACTIVO") {
        $('#estado_transporte').val(estado)
    } else {
        $('#estado_transporte').val('INACTIVO')
        toastr.error('Transportista no se encuentra "Activo"', 'Error');
    }

    if (direccion != '-' && direccion != "NULL") {
        $('#direccion_transporte').val(direccion + " - " + departamento + " - " + provincia + " - " + distrito)
    }
}

function camposRuc(objeto) {
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

        $('#descripcion').val(razonsocial)

        if (estado == "ACTIVO") {
            $('#estado').val(estado)
        } else {
            $('#estado').val('INACTIVO')
            toastr.error('Proveedor no se encuentra "Activo"', 'Error');
        }
        $('#direccion').val(direccion)
    }    
    else
    {
        toastr.error('No se encontraron datos.')
    }
}

// Consulta Dni
function consultarDni() {
    var dni = $('#dni').val()
    limpiarErrores()
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
                        $('#estado').val('SIN VERIFICAR')
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

    if (objeto.value === undefined)
                return;

    if(objeto.value.success)
    {
        var nombres = objeto.value.data.nombres;
        var apellido_paterno = objeto.value.data.apellido_paterno;
        var apellido_materno = objeto.value.data.apellido_materno;
        var codigo_verificacion = objeto.value.data.codigo_verificacion;

        $('#estado').val('ACTIVO')
        $('#descripcion').val(nombres + ' ' + apellido_paterno + ' ' + apellido_materno)
    }
    else{
        toastr.error('No se encontraron datos.')
    }

}

//Añadir Entidad Financiera
function agregarEntidad() {
    $('#modal_agregar_entidad').modal('show');
}

function limpiarErroresTransportista(){
    $('#ruc_transporte').removeClass( "is-invalid" )
    $('#error-ruc_transporte').text('')
    
    $('#transporte').removeClass( "is-invalid" )
    $('#error-transporte').text('')

    $('#direccion_transporte').removeClass( "is-invalid" )
    $('#error-direccion_transporte').text('')

    $('#estado_transporte').removeClass( "is-invalid" )
    $('#error-estado_transporte').text('')
}

// Validar tabs
//Transportista
function enviarDatosTransportista() {
    
    limpiarErroresTransportista();
    var enviar = false;
    if ($('#estado_transporte').val() != 'ACTIVO') {
        if ($('#estado_transporte').val()!='SIN VERIFICAR'){
            toastr.error('EL Ruc del transportista debe de estar Activo.','Error');
            enviar = true;
            $('#estado_transporte').addClass( "is-invalid" )
            $('#error-estado_transporte').text('El campo Estado debe estar Activo.')
        }            
    
    }

    if ($('#ruc_transporte').val()==''){
        toastr.error('Ingresar Ruc del transportista.','Error');
        enviar = true;            
        $('#ruc_transporte').addClass( "is-invalid" )
        $('#error-ruc_transporte').text('El campo Ruc es obligatorio.')
    }

    if ($('#transporte').val()==''){
        toastr.error('Ingresar Nombre Completo del transportista.','Error');
        enviar = true;            
        $('#transporte').addClass( "is-invalid" )
        $('#error-transporte').text('El campo Nombre Completo es obligatorio.')
    }

    if ($('#direccion_transporte').val()==''){
        toastr.error('Ingresar Dirección del transportista.','Error');
        enviar = true;            
        $('#direccion_transporte').addClass( "is-invalid" )
        $('#error-direccion_transporte').text('El campo Dirección es obligatorio.')
    }
    return enviar
}

//Contacto
function limpiarErroresContacto(){
    $('#contacto').removeClass( "is-invalid" )
    $('#error-nombre_contacto').text('')
    
    $('#correo_contacto').removeClass( "is-invalid" )
    $('#error-correo_contacto').text('')

    $('#telefono_contacto').removeClass( "is-invalid" )
    $('#error-telefono_contacto').text('')
}

function enviarDatosContacto() {
    
    limpiarErroresContacto();
    var enviar = false;
    if ($('#contacto').val()==''){
        toastr.error('Ingresar Nombre Completo del contacto.','Error');
        enviar = true;            
        $('#contacto').addClass( "is-invalid" )
        $('#error-nombre_contacto').text('El campo Nombre Completo es obligatorio.')
    }

    if ($('#correo_contacto').val()==''){
        toastr.error('Ingresar Correo del contacto.','Error');
        enviar = true;            
        $('#correo_contacto').addClass( "is-invalid" )
        $('#error-correo_contacto').text('El campo Correo es obligatorio.')
    }

    if ($('#telefono_contacto').val()==''){
        toastr.error('Ingresar Telefono del contacto.','Error');
        enviar = true;            
        $('#telefono_contacto').addClass( "is-invalid" )
        $('#error-telefono_contacto').text('El campo Telefono es obligatorio.')
    }
    return enviar
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

$('.tabs-container .nav-tabs #bancos_link').click(function() {
    limpiarErrores()
    var enviar = true;
    
    if ($('input:radio[name=tipo_documento]:checked').val() == "RUC") {
        if ($('#ruc').val() == '') {
            enviar = false
            $('#ruc').addClass("is-invalid")
            toastr.error("Ingrese Ruc del proveedor.", 'Error');
            $('#error-ruc').text("El campo Ruc es obligatorio.")
        }
        if ($('#tipo_persona').val() == '') {
            enviar = false
            $('#tipo_persona').addClass("is-invalid")
            toastr.error("Ingrese el Tipo de persona del proveedor.", 'Error');
            $('#error-tipo').text("El campo Tipo es obligatorio.")
        }
    }else{
        if ($('#dni').val() == '') {
            enviar = false
            $('#dni').addClass("is-invalid")
            toastr.error("Ingrese Dni del proveedor.", 'Error');
            $('#error-dni').text("El campo Dni es obligatorio.")
        }
    }



    if ($('#descripcion').val() == '') {
        enviar = false
        $('#descripcion').addClass("is-invalid")
        toastr.error("Ingrese Descripción del Proveedor.", 'Error');
        $('#error-descripcion').text("El campo Descripción es obligatorio.")
    }

    if ($('#direccion').val() == '') {
        enviar = false
        $('#direccion').addClass("is-invalid")
        toastr.error("Ingrese la Dirección del Proveedor.", 'Error');
        $('#error-direccion').text("El campo Dirección es obligatorio.")
    }

    
    if ($('#zona').val() == '') {
        enviar = false
        $('#zona').addClass("is-invalid")
        toastr.error("Ingrese la Zona del Proveedor.", 'Error');
        $('#error-zona').text("El campo Zona es obligatorio.")
    }

    if ($('#correo').val() == '') {
        enviar = false
        $('#correo').addClass("is-invalid")
        toastr.error("Ingrese el Correo del Proveedor.", 'Error');
        $('#error-correo').text("El campo Correo es obligatorio.")
    }

    if ($('#telefono').val() == '') {
        enviar = false
        $('#telefono').addClass("is-invalid")
        toastr.error("Ingrese el Telefono del Proveedor.", 'Error');
        $('#error-telefono').text("El campo Telefono es obligatorio.")
    }

    $('#tipo_documento_ruc').val("RUC")


    //Transportista
    // var error = enviarDatosTransportista()
    // if (error != false) {
    //     toastr.error('Campos incompletos en Transportista', 'Error');
    //     $('#transporte_link').click();
    //     enviar = false
    // }
    //Contacto
    var contacto = enviarDatosContacto()
    if (contacto != false) {
        toastr.error('Campos incompletos en Contacto', 'Error');
        $('#contacto_link').click();
        enviar = false
    }


    return enviar

})

function limpiarErrores() {
    $('#ruc').removeClass("is-invalid")
    $('#error-ruc').text("")

    $('#tipo_persona').removeClass("is-invalid")
    $('#error-tipo').text("")

    $('#dni').removeClass("is-invalid")
    $('#error-dni').text("")

    $('#descripcion').removeClass("is-invalid")
    $('#error-descripcion').text("")

    $('#direccion').removeClass("is-invalid")
    $('#error-direccion').text("")

    $('#zona').removeClass("is-invalid")
    $('#error-zona').text("")

    $('#correo').removeClass("is-invalid")
    $('#error-correo').text("")

    $('#telefono').removeClass("is-invalid")
    $('#error-telefono').text("")
}


</script>

@endpush