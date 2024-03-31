@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<style>
    .inputCantidadValido{
        border-color:rgb(59, 63, 255) !important;
    }
    .inputCantidadIncorrecto{
        border-color: red !important;
    }
    .inputCantidadColor{
        border-color: rgb(48, 48, 88);
    }
    .colorStockLogico{
        background-color: rgb(243, 248, 255);
    }

    .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    }

    .fulfilling-bouncing-circle-spinner, .fulfilling-bouncing-circle-spinner * {
      box-sizing: border-box;
    }

    .fulfilling-bouncing-circle-spinner {
      height: 60px;
      width: 60px;
      position: relative;
      animation: fulfilling-bouncing-circle-spinner-animation infinite 4000ms ease;
    }

    .fulfilling-bouncing-circle-spinner .orbit {
      height: 60px;
      width: 60px;
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 50%;
      border: calc(60px * 0.03) solid #ff1d5e;
      animation: fulfilling-bouncing-circle-spinner-orbit-animation infinite 4000ms ease;
    }

    .fulfilling-bouncing-circle-spinner .circle {
      height: 60px;
      width: 60px;
      color: #ff1d5e;
      display: block;
      border-radius: 50%;
      position: relative;
      border: calc(60px * 0.1) solid #ff1d5e;
      animation: fulfilling-bouncing-circle-spinner-circle-animation infinite 4000ms ease;
      transform: rotate(0deg) scale(1);
    }

    @keyframes fulfilling-bouncing-circle-spinner-animation {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    @keyframes fulfilling-bouncing-circle-spinner-orbit-animation {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1);
      }
      62.5% {
        transform: scale(0.8);
      }
      75% {
        transform: scale(1);
      }
      87.5% {
        transform: scale(0.8);
      }
      100% {
        transform: scale(1);
      }
    }

    @keyframes fulfilling-bouncing-circle-spinner-circle-animation {
      0% {
        transform: scale(1);
        border-color: transparent;
        border-top-color: inherit;
      }
      16.7% {
        border-color: transparent;
        border-top-color: initial;
        border-right-color: initial;
      }
      33.4% {
        border-color: transparent;
        border-top-color: inherit;
        border-right-color: inherit;
        border-bottom-color: inherit;
      }
      50% {
        border-color: inherit;
        transform: scale(1);
      }
      62.5% {
        border-color: inherit;
        transform: scale(1.4);
      }
      75% {
        border-color: inherit;
        transform: scale(1);
        opacity: 1;
      }
      87.5% {
        border-color: inherit;
        transform: scale(1.4);
      }
      100% {
        border-color: transparent;
        border-top-color: inherit;
        transform: scale(1);
      }
    }
    
</style>

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVA GUIA DE REMISION</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('ventas.guiasremision.index')}}">Guias de Remision</a>
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
                    <input type="hidden" id='asegurarCierre'>
                    <div class="row">
                        <div class="col-12">
                            <form action="{{route('ventas.guiasremision.store')}}" method="POST" id="enviar_documento">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-sm-6 b-r">
                                        <h4 class=""><b>Guia de Remision</b></h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Registrar datos de la guia de remision:</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">

                                            <div class="col-lg-6 col-xs-12" id="fecha_documento">
                                                <label class="required">Fecha de Documento</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>

                                                    <input type="date" id="fecha_documento_campo" name="fecha_documento"
                                                        class="form-control {{ $errors->has('fecha_documento') ? ' is-invalid' : '' }}"
                                                        value="{{old('fecha_documento',$hoy)}}" autocomplete="off"
                                                        required readonly>


                                                    @if ($errors->has('fecha_documento'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_documento') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-xs-12" id="fecha_entrega">
                                                <label class="required">Fecha de Atención</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>


                                                    <input type="date" id="fecha_atencion_campo"
                                                        name="fecha_atencion_campo"
                                                        class="form-control{{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                        value="{{old('fecha_atencion',$hoy)}}" autocomplete="off"
                                                        required readonly>



                                                    @if ($errors->has('fecha_atencion'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                    </span>
                                                    @endif

                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label class="required">Cliente: </label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%"
                                                value="{{old('cliente_id')}}" name="cliente_id" id="cliente_id"
                                                required>
                                                <option></option>
                                                @foreach ($clientes as $cliente)

                                                <option value="{{$cliente->id}}" @if(old('cliente_id')==$cliente->id )
                                                    {{'selected'}} @endif >{{$cliente->tipo_documento.':
                                                    '.$cliente->documento.' - '.$cliente->nombre}}
                                                </option>

                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="required">Cantidad de Productos: </label>
                                                <input type="number" name="cantidad_productos" id="cantidad_productos"
                                                    value="{{old('cantidad_productos')}}"
                                                    class="form-control {{ $errors->has('cantidad_productos') ? ' is-invalid' : '' }}"
                                                    readonly>
                                                @if ($errors->has('cantidad_productos'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('cantidad_productos') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                            <div class="col-md-6 col-xs-12">
                                                <label class="required">Peso Total de productos:</label>
                                                <input type="number" name="peso_productos" id="peso_productos" readonly
                                                    value="{{old('peso_productos')}}"
                                                    class="form-control {{ $errors->has('peso_productos') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('peso_productos'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('peso_productos') }}</strong>
                                                </span>
                                                @endif

                                            </div>

                                        </div>

                                        <hr>

                                        <h4 class=""><b>Dirección de Partida</b></h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Registrar dirección de partida de los productos:</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-8 col-xs-12">
                                                <label class="required">Empresa: </label>

                                                <select
                                                    class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%"
                                                    value="{{old('empresa_id')}}" name="empresa_id" id="empresa_id"
                                                    required disabled>
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                    <option value="{{$empresa->id}}" @if(old('empresa_id',
                                                        1)==$empresa->id )
                                                        {{'selected'}} @endif >{{$empresa->razon_social}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-lg-4 col-xs-12">
                                                <label class="required">Ubigeo: </label>
                                                <input type="text" id="ubigeo_partida"
                                                    class="form-control input-required {{ $errors->has('ubigeo_partida') ? ' is-invalid' : '' }}"
                                                    required name="ubigeo_partida"
                                                    value="{{ old('ubigeo_partida',$empresa->ubigeo)}}">
                                                @if ($errors->has('ubigeo_partida'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('ubigeo_partida') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>


                                        <div class="form-group">
                                            <label class="required">Dirección de la Empresa (Partida): </label>
                                            <textarea type="text" placeholder=""
                                                class="form-control input-required {{ $errors->has('direccion_tienda') ? ' is-invalid' : '' }}"
                                                name="direccion_empresa" id="direccion_empresa"
                                                value="{{old('direccion_empresa',$empresa->direccion_fiscal)}}"
                                                required>{{old('direccion_empresa',$empresa->direccion_fiscal)}}</textarea>


                                            @if ($errors->has('direccion_empresa'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('direccion_empresa') }}</strong>
                                            </span>
                                            @endif
                                        </div>



                                    </div>

                                    <div class="col-sm-6">
                                        <h4 class=""><b>Dirección de llegada</b></h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Registrar dirección de llegada de los productos:</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="">Tienda (Opcional): </label>
                                                    <input type="text" id="tienda"
                                                        class="form-control {{ $errors->has('tienda') ? ' is-invalid' : '' }}"
                                                        name="tienda" value="{{ old('tienda')}}">
                                                    @if ($errors->has('tienda'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('tienda') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Departamento</label>
                                                    <select id="departamento" name="departamento"
                                                        class="select2_form form-control {{ $errors->has('departamento') ? ' is-invalid' : '' }}"
                                                        style="width: 100%" required>
                                                        <option></option>
                                                        @foreach (departamentos() as $departamento)
                                                        <option value="{{ $departamento->id }}" {{
                                                            (old('departamento')==$departamento->id ? 'selected' : '')
                                                            }}>
                                                            {{ $departamento->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('departamento'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('departamento') }}</strong>
                                                    </span>
                                                    @endif
                                                    {{-- <label class="required">Ubigeo: </label>
                                                    <input type="text" id="ubigeo_llegada"
                                                        class="form-control input-required {{ $errors->has('ubigeo_llegada') ? ' is-invalid' : '' }}"
                                                        required name="ubigeo_llegada"
                                                        value="{{ old('ubigeo_llegada')}}">
                                                    @if ($errors->has('ubigeo_llegada'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('ubigeo_llegada') }}</strong>
                                                    </span>
                                                    @endif --}}
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Provincia</label>
                                                    <select id="provincia" name="provincia"
                                                        class="select2_form form-control {{ $errors->has('provincia') ? ' is-invalid' : '' }}"
                                                        style="width: 100%" required>
                                                        <option></option>
                                                        @foreach (provincias() as $provincia)
                                                        <option value="{{ $provincia->id }}" {{
                                                            (old('provincia')==$provincia->id ? 'selected' : '') }}>
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
                                            <div class="col-lg-6 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Distrito</label>
                                                    <select id="ubigeo_llegada" name="ubigeo_llegada"
                                                        class="select2_form form-control {{ $errors->has('ubigeo_llegada') ? ' is-invalid' : '' }}"
                                                        style="width: 100%" required>
                                                        <option></option>
                                                        @foreach (distritos() as $distrito)
                                                        <option value="{{ $distrito->id }}" {{
                                                            (old('ubigeo_llegada')==$distrito->id ? 'selected' : '') }}>
                                                            {{ $distrito->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('ubigeo_llegada'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('ubigeo_llegada') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label class="required">Dirección de la tienda o destino (Llegada): </label>
                                            <textarea type="text" placeholder=""
                                                class="form-control input-required {{ $errors->has('direccion_tienda') ? ' is-invalid' : '' }}"
                                                name="direccion_tienda" id="direccion_tienda"
                                                value="{{old('direccion_tienda')}}"
                                                required>{{old('direccion_tienda')}}</textarea>


                                            @if ($errors->has('direccion_tienda'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('direccion_tienda') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <hr>

                                        <h4 class=""><b>Detalles del envio</b></h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Registrar datos adicionales del envio:</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="">Dni del Conductor: </label>
                                                <input type="text" id="dni_conductor"
                                                    class="form-control {{ $errors->has('dni_conductor') ? ' is-invalid' : '' }}"
                                                    maxlength="8" name="dni_conductor"
                                                    value="{{ old('dni_conductor')}}">
                                                @if ($errors->has('dni_conductor'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('dni_conductor') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="">Placa del Vehículo: </label>
                                                <input type="text" id="placa_vehiculo"
                                                    class="form-control {{ $errors->has('placa_vehiculo') ? ' is-invalid' : '' }}"
                                                    name="placa_vehiculo" value="{{ old('placa_vehiculo')}}">
                                                @if ($errors->has('placa_vehiculo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('placa_vehiculo') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Motivo de Traslado</label>
                                            <select name="motivo_traslado" id="motivo_traslado"
                                                class="select2_form form-control" required>
                                                <option value=""></option>
                                                @foreach (motivo_traslado() as $motivo)
                                                <option value="{{ $motivo->id }}">{{ $motivo->descripcion }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('motivo_traslado'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('motivo_traslado') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>Observación:</label>

                                            <textarea type="text" placeholder=""
                                                class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                                name="observacion" id="observacion" onkeyup="return mayus(this)"
                                                value="{{old('observacion')}}"></textarea>


                                            @if ($errors->has('observacion'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('observacion') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <input type="hidden" class="form-control" name="productos_tabla[]"
                                            id="productos_tabla">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Seleccionar productos</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="sk-spinner sk-spinner-wave">
                                        <div class="sk-rect1"></div>
                                        <div class="sk-rect2"></div>
                                        <div class="sk-rect3"></div>
                                        <div class="sk-rect4"></div>
                                        <div class="sk-rect5"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-xs-12 mb-3">
                                            <label class="required">Modelo</label>
                                            <select id="modelo"
                                                class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                                onchange="getProductosByModelo(this.value)" >
                                                <option></option>
                                                @foreach ($modelos as $modelo)
                                                    <option value="{{ $modelo->id }}"
                                                        {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                                        {{ $modelo->descripcion }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"><b><span
                                                        id="error-producto"></span></b></div>
                                        </div>
                                        <div class="col-12 mb-5">
                                            @include('ventas.guias.table-stocks')
                                        </div>           
                                        <div class="col-lg-2 col-xs-12">
                                            <button  type="button" id="btn_agregar_detalle"
                                                class="btn btn-warning btn-block">
                                                    <i class="fa fa-plus"></i> AGREGAR
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle de la Guia de Remision</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="row">
                                        <div class="col-12 mb-5">
                                            @include('ventas.guias.table-detalle')
                                        </div>
                                        {{-- <div class="col-12">
                                            <form id="agregar_producto">
                                                <div class="row align-items-end">
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                class="col-form-label required">Producto-lote:</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    id="producto_lote_form" required readonly>
                                                                <span class="input-group-append">
                                                                    <button type="button" class="btn btn-primary"
                                                                        id="buscarLotes" data-toggle="modal"
                                                                        data-target="#modal_lote"><i
                                                                            class='fa fa-search'></i> Buscar
                                                                    </button>
                                                                </span>
                                                            </div>
                                                            <div class="invalid-feedback"><b><span
                                                                        id="error-producto_lote_form"></span></b></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label for="">Cantidad</label>
                                                            <input type="text" name="cantidad" id="cantidad_form"
                                                                class="form-control"
                                                                onkeypress="return filterFloat(event, this, false);"
                                                                placeholder="Cantidad" required>
                                                            <div class="invalid-feedback"><b><span
                                                                        id="error-cantidad_form"></span></b></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-success btn-block"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="producto_form">
                                                    <input type="hidden" id="peso_form">
                                                    <input type="hidden" id="unidad_form">
                                                    <input type="hidden" id="lote_form">
                                                    <input type="hidden" name="cantidad_actual_form"
                                                        id="cantidad_actual_form">
                                                </div>
                                            </form>
                                        </div> --}}
                                        {{-- <div class="col-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table dataTables-detalle-documento table-striped table-bordered table-hover"
                                                    style="text-transform:uppercase">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th class="text-center">ACCIONES</th>
                                                            <th class="text-center">CANTIDAD</th>
                                                            <th class="text-center">UNIDAD DE MEDIDA</th>
                                                            <th class="text-center">PESO (KG)</th>
                                                            <th class="text-center">DESCRIPCION DEL PRODUCTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> --}}
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

                            <a href="{{route('ventas.guiasremision.index')}}" id="btn_cancelar"
                                class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>

                            <button type="submit" id="btn_grabar" form="enviar_documento"
                                class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="overlay" class="overlay">
                <div class="fulfilling-bouncing-circle-spinner">
                    <div class="circle"></div>
                    <div class="orbit"></div>
                </div>
            </div>
        </div>

    </div>
    <input type="hidden" id="codigoProvincia">
    <input type="hidden" id="codigoDistrito">
</div>
@include('ventas.documentos.modal')
@include('ventas.guias.modalLote')
@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<!-- Data picker -->
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!-- Date range use moment.js same as full calendar plugin -->
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<!-- Date range picker -->
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script>
    $(document).ready(function() {
        //obtenerLotesproductos();
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });

        $("#departamento").on("change", cargarProvincias);

        $('#provincia').on("change", cargarDistritos);
    });

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
                            $("#provincia").empty().trigger("change");
                            let codigoProvincia = $("#codigoProvincia").val();
                            if(codigoProvincia == ""){
                                $("#provincia").select2({
                                    placeholder: "SELECCIONAR",
                                    allowClear: true,
                                    height: '200px',
                                    width: '100%',
                                    data: data.provincias
                                }).val($("#provincia").find(':selected').val()).trigger('change');
                            }else{
                                $("#provincia").select2({
                                    placeholder: "SELECCIONAR",
                                    allowClear: true,
                                    height: '200px',
                                    width: '100%',
                                    data: data.provincias
                                });
                                $("#provincia").select2("val", codigoProvincia);
                                $("#codigoProvincia").val("");
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
        if (provincia_id !== null && provincia_id.length > 0) {
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
                    $("#ubigeo_llegada").empty();

                    if (!data.error) {
                       // Mostramos la información
                        if (data.distritos != null) {
                            var selected = $('#ubigeo_llegada').find(':selected').val();
                           
                            let codigoDistrito = $("#codigoDistrito").val();
                            if(codigoDistrito == ""){
                                $("#ubigeo_llegada").select2({
                                    placeholder: "SELECCIONAR",
                                    allowClear: true,
                                    height: '200px',
                                    width: '100%',
                                    data: data.distritos
                                }).val($("#ubigeo_llegada").find(':selected').val()).trigger('change');
                            }else{
                                $("#ubigeo_llegada").select2({
                                    placeholder: "SELECCIONAR",
                                    allowClear: true,
                                    height: '200px',
                                    width: '100%',
                                    data: data.distritos
                                });
                                $("#ubigeo_llegada").select2("val", codigoDistrito);
                                $("#codigoDistrito").val("");
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

    // Solo campos numericos
    $('#ubigeo_partida, #dni_conductor').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // $(document).ready(function() {
    //     //DATATABLE - COTIZACION
    //     table = $('.dataTables-detalle-documento').DataTable({
    //         "dom": 'lTfgitp',
    //         "bPaginate": true,
    //         "bLengthChange": true,
    //         "responsive": true,
    //         "bFilter": true,
    //         "bInfo": false,
    //         "columnDefs": [
    //             {
    //                 "targets": 0,
    //                 "visible": false,
    //                 "searchable": false
    //             },
    //             {
    //                 searchable: false,
    //                 "targets": [1],
    //                 "className": 'text-center',
    //                 render: function(data){
    //                     return "<button type='button' class='btn btn-danger btn-delete'><i class='fa fa-trash'></i></button>"
    //                 }
    //             },
    //             {
    //                 "targets": [2],
    //                 "className": 'text-center'
    //             },
    //             {
    //                 "targets": [3],
    //                 "className": 'text-center'
    //             },
    //             {
    //                 "targets": [4],
    //                 "className": 'text-center'
    //             },
    //             {
    //                 "targets": [5],
    //             },
    //             {
    //                 "targets": [6],
    //                 "visible": false
    //             },
    //         ],
    //         'bAutoWidth': false,
    //         "language": {
    //             url: "{{asset('Spanish.json')}}"
    //         },
    //         "order": [[ 0, "desc" ]],
    //     });

    //     //DIRECCION DE LA TIENDA OLD

    //     //Controlar Error
    //     $.fn.DataTable.ext.errMode = 'throw';
    // });

    // $('#cantidad_form').on('input', function() {
    //     let max= convertFloat(this.max);
    //     let valor = convertFloat(this.value);
    //     if(valor>max){
    //         toastr.error('La cantidad ingresada supera al stock del producto Max('+max+').', 'Error');
    //         this.value = max;
    //     }
    // });

    // $('#agregar_producto').on('submit', function(e){
    //     e.preventDefault();
    //     let enviar = false;
    //     let cantidad = $('#cantidad_form').val();
    //     let lote = $('#lote_form').val();
    //     let producto = $('#producto_form').val();
    //     let producto_lote = $('#producto_lote_form').val();
    //     let unidad = $('#unidad_form').val();
    //     let peso = $('#peso_form').val();


    //     let cantidad_actual = convertFloat($('#cantidad_actual_form').val());


    //     if(convertFloat(cantidad) > cantidad_actual)
    //     {
    //         toastr.warning('La cantidad debe ser menor o igual al stock actual: ' + cantidad_actual);

    //         enviar=true;
    //     }

    //     if(convertFloat(cantidad) <= 0)
    //     {
    //         toastr.warning('La cantidad debe ser mayor a 0.');

    //         enviar=true;
    //     }

    //     if(cantidad.length == 0 || lote.length == 0 || producto.length == 0 || producto_lote.length == 0 || peso.length == 0 || unidad.length == 0)
    //     {
    //         toastr.error('Ingrese datos', 'Error');
    //         enviar=true;
    //     }
    //     else {
    //         var existe = buscarProducto($('#lote_form').val())
    //         if (existe == true) {
    //             toastr.error('Producto ya se encuentra ingresado.', 'Error');
    //             enviar = true;
    //         }
    //     }

    //     if (enviar != true) {
    //         const swalWithBootstrapButtons = Swal.mixin({
    //             customClass: {
    //                 confirmButton: 'btn btn-success',
    //                 cancelButton: 'btn btn-danger',
    //             },
    //             buttonsStyling: false
    //         })

    //         Swal.fire({
    //             title: 'Opción Agregar',
    //             text: "¿Seguro que desea agregar Producto?",
    //             icon: 'question',
    //             showCancelButton: true,
    //             confirmButtonColor: "#1ab394",
    //             confirmButtonText: 'Si, Confirmar',
    //             cancelButtonText: "No, Cancelar",
    //         }).then((result) => {
    //             if (result.isConfirmed) {

    //                 var detalle = {
    //                     cantidad: cantidad,
    //                     lote_id: lote,
    //                     producto_id: producto,
    //                     unidad: unidad,
    //                     peso: peso,
    //                     descripcion: $('#producto_lote_form').val()
    //                 }
    //                 agregarTabla(detalle);
    //                 cambiarCantidad(detalle,'1');
    //                 $('#asegurarCierre').val(1)
    //             } else if (
    //                 /* Read more about handling dismissals below */
    //                 result.dismiss === Swal.DismissReason.cancel
    //             ) {
    //                 swalWithBootstrapButtons.fire(
    //                     'Cancelado',
    //                     'La Solicitud se ha cancelado.',
    //                     'error'
    //                 )
    //             }
    //         })
    //     }
    // })

    // function buscarProducto(id) {
    //     var existe = false;
    //     var t = $('.dataTables-detalle-documento').DataTable();
    //     t.rows().data().each(function(el, index) {
    //         if (el[0] == id) {
    //             existe = true
    //         }
    //     });
    //     return existe
    // }

    //AGREGAR EL DETALLE A LA TABLA
    // function agregarTabla($detalle) {
    //     var t = $('.dataTables-detalle-documento').DataTable();
    //     t.row.add([
    //         $detalle.lote_id,
    //         '',
    //         $detalle.cantidad,
    //         $detalle.unidad,
    //         $detalle.peso,
    //         $detalle.descripcion,
    //         $detalle.producto_id,
    //     ]).draw(false);
    //     limpiarErrores();
    //     limpiarDetalle();
    //     cargarDetalle();
    //     $('#peso_productos').val(sumaPesos().toFixed(2))
    //     $('#cantidad_productos').val(registrosProductos().toFixed(2))
    // }

    // function cargarDetalle() {
    //     var notadetalle = [];
    //     var table = $('.dataTables-detalle-documento').DataTable();
    //     var data = table.rows().data();
    //     data.each(function(value, index) {
    //         let fila = {
    //             lote_id: value[0],
    //             cantidad: value[2],                
    //             producto_id: value[6],
    //         };

    //         notadetalle.push(fila);

    //     });
    //     $('#productos_tabla').val(JSON.stringify(notadetalle))
    // }

    //CAMBIAR LA CANTIDAD LOGICA DEL PRODUCTO
    // function cambiarCantidad(detalle, condicion) {
    //     $.ajax({
    //         type : 'POST',
    //         url : '{{ route('almacenes.nota_salidad.cantidad') }}',
    //         data : {
    //             '_token' : $('input[name=_token]').val(),
    //             'producto_id' : detalle.lote_id,
    //             'cantidad' : detalle.cantidad,
    //             'condicion' : condicion,
    //         }
    //     }).done(function (result){
    //         //alert('REVISAR')
    //     });
    // }

    // function limpiarErrores() {
    //     $('#cantidad_form').removeClass("is-invalid")
    //     $('#error-cantidad_form').text('')

    //     $('#producto_lote_form').removeClass("is-invalid")
    //     $('#error-producto_lote_form').text('')
    // }

    // function limpiarDetalle() {
    //     $('#producto_lote_form').val('');
    //     $('#producto_form').val('');
    //     $('#peso_form').val('');
    //     $('#unidad_form').val('');
    //     $('#cantidad_actual_form').val('');
    //     $('#cantidad_form').val('');
    // }


    // function registrosProductos() {
    //     var t = $('.dataTables-detalle-documento').DataTable();
    //     let total = 0.00;
    //     t.rows().data().each(function(el, index) {
    //         total = Number(el[2]) + total
    //     });

    //     return total
    // }


    // function validarFecha() {
    //     var enviar = false
    //     var productos = registrosProductos()
    //     if ($('#fecha_documento_campo').val() == '') {
    //         toastr.error('Ingrese Fecha de Documento.', 'Error');
    //         $("#fecha_documento_campo").focus();
    //         enviar = true;
    //     }

    //     if ($('#fecha_atencion_campo').val() == '') {
    //         toastr.error('Ingrese Fecha de Atención.', 'Error');
    //         $("#fecha_atencion_campo").focus();
    //         enviar = true;
    //     }

    //     if (productos == 0) {
    //         toastr.error('Ingrese al menos 1 Producto.', 'Error');
    //         enviar = true;
    //     }
    //     return enviar
    // }

    // function sumaPesos()
    // {
    //     var t = $('.dataTables-detalle-documento').DataTable();
    //     let total = 0.00;
    //     t.rows().data().each(function(el, index) {
    //         total=Number(el[4] * el[2]) + total
    //     });

    //     return total
    // }

    // $('#enviar_documento').submit(function(e) {
    //     e.preventDefault();
    //     const swalWithBootstrapButtons = Swal.mixin({
    //         customClass: {
    //             confirmButton: 'btn btn-success',
    //             cancelButton: 'btn btn-danger',
    //         },
    //         buttonsStyling: false
    //     })

    //     Swal.fire({
    //         title: 'Opción Guardar',
    //         text: "¿Seguro que desea guardar cambios?",
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonColor: "#1ab394",
    //         confirmButtonText: 'Si, Confirmar',
    //         cancelButtonText: "No, Cancelar",
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             if(registrosProductos() > 0)
    //             {
    //                 cargarDetalle();
    //                 $('#asegurarCierre').val('2');
    //                 this.submit();
    //             }
    //             else
    //             {
    //                 toastr.error('Debe tener al menos un detalle')
    //             }
    //         } else if (
    //             /* Read more about handling dismissals below */
    //             result.dismiss === Swal.DismissReason.cancel
    //         ) {
    //             swalWithBootstrapButtons.fire(
    //                 'Cancelado',
    //                 'La Solicitud se ha cancelado.',
    //                 'error'
    //             )
    //         }
    //     })
    // })

    //Borrar registro de articulos
    // $(".dataTables-detalle-documento").on('click','.btn-delete',function(){

    //     const swalWithBootstrapButtons = Swal.mixin({
    //         customClass: {
    //             confirmButton: 'btn btn-success',
    //             cancelButton: 'btn btn-danger',
    //         },
    //         buttonsStyling: false
    //     })

    //     Swal.fire({
    //         title: 'Opción Eliminar',
    //         text: "¿Seguro que desea eliminar Producto?",
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonColor: "#1ab394",
    //         confirmButtonText: 'Si, Confirmar',
    //         cancelButtonText: "No, Cancelar",
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             var table = $('.dataTables-detalle-documento').DataTable();
    //             var data = table.row($(this).parents('tr')).data();
    //             var detalle = {
    //                 lote_id: data[0],
    //                 cantidad: data[2],
    //             }
    //             //DEVOLVER LA CANTIDAD LOGICA
    //             cambiarCantidad(detalle,'0')
    //             table.row($(this).parents('tr')).remove().draw();
    //             $('#peso_productos').val(sumaPesos().toFixed(2))
    //             $('#cantidad_productos').val(registrosProductos().toFixed(2))

    //         } else if (
    //             /* Read more about handling dismissals below */
    //             result.dismiss === Swal.DismissReason.cancel
    //         ) {
    //             swalWithBootstrapButtons.fire(
    //                 'Cancelado',
    //                 'La Solicitud se ha cancelado.',
    //                 'error'
    //             )
    //         }
    //     })
    // });

    //DEVOLVER CANTIDADES A LOS LOTES
    // function devolverCantidades() {
    //     //CARGAR PRODUCTOS PARA DEVOLVER LOTE
    //     cargarDetalle()
    //     return $.ajax({
    //         dataType : 'json',
    //         type : 'post',
    //         url : '{{ route('almacenes.nota_salidad.devolver.cantidades') }}',
    //         data : {
    //             '_token' : $('input[name=_token]').val(),
    //             'cantidades' :  $('#productos_tabla').val(),
    //         },
    //         async: true
    //     }).responseText()
    // }
</script>
{{-- <script>
    window.onbeforeunload = () => {
        if ($('#asegurarCierre').val() == 1) {
            while (true) {
                devolverCantidades()
            }
        }
    }
</script> --}}
<script>
    $(function(){
        $("#cliente_id").on("change", function(){
            $("#tienda").val("");
            let cliente_id = $(this).val();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    _token: $('input[name=_token]').val(),
                    cliente_id
                },
                url: "{{ route('ventas.cliente.getcustomer') }}",
                success: function(data) {
                    const { departamento_id, provincia_id, distrito_id,direccion } = data;
                    $("#codigoProvincia").val(provincia_id);
                    $("#codigoDistrito").val(distrito_id);
                    $("#departamento").select2("val", departamento_id);
                    $("#direccion_tienda").val(direccion)
                }
            });
        });
    })
</script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script>
    const tallas                =   @json($tallas);
    const tableStocksBody       =   document.querySelector('#table-stocks tbody');
    const tokenValue            =   document.querySelector('input[name="_token"]').value;
    const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');
    const tableDetalleBody      =   document.querySelector('#table-detalle-guia tbody');      
    const tfoot_cantidadTotal   =   document.querySelector('#tfoot_cantidadTotal');   
    const formGuia              =   document.querySelector('#enviar_documento');
   

    let modelo_id       = null;
    let carrito         = [];
    let asegurarCierre  = 5;

    document.addEventListener('DOMContentLoaded',()=>{
        events();
    })

    function events(){
        formGuia.addEventListener('submit',(e)=>{
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
                    if( carrito.length > 0)
                    {
                        cargarProductos();
                        asegurarCierre = 2;    
                        formGuia.submit();                   
                    }
                    else
                    {
                        toastr.error('Debe tener al menos un detalle')
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error',
                    )
                }
            })
        })

        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                validarCantidadInstantanea(e);
            }
        })

        btnAgregarDetalle.addEventListener('click',()=>{
            this.agregarProducto();
        })

        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                const producto_id   =   e.target.getAttribute('data-producto');
                const color_id      =   e.target.getAttribute('data-color');

                const item          =   carrito.filter((p)=>{
                    return p.producto_id==producto_id && p.color_id==color_id;
                })[0];

                const index_item    =   carrito.findIndex((p)=> {
                    return p.producto_id==producto_id && p.color_id==color_id; 
                })
                console.log(item);
                
                eliminarItem(item,index_item);
            }
        })

    }

    function cargarProductos(){
        const inputProductosTabla   =   document.querySelector('#productos_tabla');
        const inputCantidadProductos=   document.querySelector('#cantidad_productos');
        const inputPesoProductos    =   document.querySelector('#peso_productos');

        inputProductosTabla.value   =   JSON.stringify(carrito);
        inputCantidadProductos.value=   carrito.length;
        inputPesoProductos.value    =   0;
    }

    async function getProductosByModelo(idModelo){
        modelo_id = idModelo;
        btnAgregarDetalle.disabled=true;

        if(modelo_id){
            try {
                const url = `/get-producto-by-modelo/${modelo_id}`;
                const response = await axios.get(url);
                    //console.log(response.data);
                //this.productosPorModelo = response.data;
                pintarTableStocks(response.data.stocks,tallas,response.data.producto_colores);
            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            }
        }else{
            tableStocksBody.innerHTML = ``;
        }
    }

    const pintarTableStocks = (stocks,tallas,producto_colores)=>{
        let options =``;
        let producto_color_procesados=[];
        
        producto_colores.forEach((pc)=>{
            options+=`  <tr>
                            <th scope="row" data-producto=${pc.producto_id} data-color=${pc.color_id} >
                                ${pc.producto_nombre} - ${pc.color_nombre}
                            </th>
                        `;

            let htmlTallas = ``;

            tallas.forEach((t)=>{
                const stock = stocks.filter(st => st.producto_id == pc.producto_id && st.color_id == pc.color_id && st.talla_id == t.id)[0]?.stock_logico || 0;

                htmlTallas +=   `
                                    <td style="background-color: rgb(210, 242, 242);font-weight:bold;">${stock}</td>
                                    <td width="8%">
                                        ${stock !== 0 ? `
                                            <input type="text" class="form-control inputCantidad inputCantidadColor" 
                                            data-producto-id="${pc.producto_id}"
                                            data-producto-nombre="${pc.producto_nombre}"
                                            data-color-nombre="${pc.color_nombre}"
                                            data-talla-nombre="${t.descripcion}"
                                            data-color-id="${pc.color_id}" data-talla-id="${t.id}"></input>` : ''}
                                    </td>
                                `;   
            })

            htmlTallas += `</tr>`;
            options += htmlTallas;
        })

        tableStocksBody.innerHTML = options;
        btnAgregarDetalle.disabled = false;
    }

    
    async function validarCantidadInstantanea(event) {
            const cantidadSolicitada    =   event.target.value;
            btnAgregarDetalle.disabled  =   true;
            try {
                if(cantidadSolicitada !== ''){
                    const stock_logico  =  await this.getStockLogico(event.target);
                    if(stock_logico < cantidadSolicitada){
                        event.target.classList.add('inputCantidadIncorrecto');
                        event.target.classList.remove('inputCantidadValido');
                        event.target.focus();
                        event.target.value  =   stock_logico;
                        toastr.error(`Cantidad solicitada: ${cantidadSolicitada}, debe ser menor o igual
                        al stock lógico: ${stock_logico}`,"Error");
                    }else{
                        event.target.classList.add('inputCantidadValido');
                        event.target.classList.remove('inputCantidadIncorrecto');
                    }                    
                }else{
                    this.deshabilitarBtnAgregar =   false;
                    event.target.classList.remove('inputCantidadIncorrecto');
                    event.target.classList.remove('inputCantidadValido');
                }   
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock logico:', error);
            }finally{
                btnAgregarDetalle.disabled  =   false;
            }
    }
       
    async function getStockLogico(inputCantidad){
            const producto_id           =   inputCantidad.getAttribute('data-producto-id');
            const color_id              =   inputCantidad.getAttribute('data-color-id');
            const talla_id              =   inputCantidad.getAttribute('data-talla-id');
            
            try {  
                const url = `/get-stocklogico/${producto_id}/${color_id}/${talla_id}`;
                const response = await axios.get(url);
                if(response.data.message=='success'){
                    const stock_logico  =   response.data.data[0].stock_logico;
                    return stock_logico;
                }
                 
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock logico:', error);
                return null;
            }
    }


    //============== AGREGAR PRODUCTOS AL CARRITO ===================
    async function agregarProducto() {
        document.getElementById('overlay').style.display = 'flex';

        const inputsCantidad = document.querySelectorAll('.inputCantidad:not([disabled])');

        for (const ic of inputsCantidad) {
                ic.classList.remove('inputCantidadIncorrecto');
                const cantidad = ic.value ? ic.value : null;

                if (cantidad) {
                    try {
                        console.log('Validando cantidad del producto...');
                        const cantidadValida = await validarCantidadCarrito(ic);
                        console.log('Cantidad válida:', cantidadValida);

                        if (cantidadValida) {
                            const producto = this.formarProducto(ic);
                            const indiceExiste = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);
                            
                            if (indiceExiste == -1) {
                                console.log('Producto no encontrado en el carrito. Agregando nuevo producto...');
                                const objProduct = {
                                    producto_id: producto.producto_id,
                                    color_id: producto.color_id,
                                    producto_nombre: producto.producto_nombre,
                                    color_nombre: producto.color_nombre,
                                    tallas: [{
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    }]
                                };

                                carrito.push(objProduct);
                                asegurarCierre = 1;
                                await this.actualizarStockLogico(producto, "nuevo");
                                console.log('Producto agregado al carrito:', objProduct);
                            } else {
                                console.log('Producto encontrado en el carrito. Modificando existente...');
                                const productoModificar = carrito[indiceExiste];
                                const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                                if (indexTalla !== -1) {
                                    const cantidadAnterior = productoModificar.tallas[indexTalla].cantidad;
                                    productoModificar.tallas[indexTalla].cantidad = producto.cantidad;
                                    carrito[indiceExiste] = productoModificar;
                                    asegurarCierre = 1;
                                    await actualizarStockLogico(producto, "editar", cantidadAnterior);
                                    console.log('Producto modificado:', productoModificar);
                                } else {
                                    console.log('Talla del producto no encontrada. Agregando nueva talla...');
                                    const objTallaProduct = {
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    };
                                    productoModificar.tallas.push(objTallaProduct);
                                    carrito[indiceExiste] = productoModificar;
                                    asegurarCierre = 1;
                                    await actualizarStockLogico(producto, "nuevo");
                                    console.log('Nueva talla agregada:', objTallaProduct);
                                }
                            }
                        } else {
                            ic.classList.add('inputCantidadIncorrecto');
                            console.log('Cantidad no válida. Marcando como incorrecto.');
                        }
                    } catch (error) {
                        console.error("Error:", error);
                    }
                } else {
                    const producto = this.formarProducto(ic);
                    const indiceProductoColor = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                    if (indiceProductoColor !== -1) {
                        const indiceTalla = carrito[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto.talla_id);

                        if (indiceTalla !== -1) {
                            const cantidadAnterior = carrito[indiceProductoColor].tallas[indiceTalla].cantidad;
                            carrito[indiceProductoColor].tallas.splice(indiceTalla, 1);
                            asegurarCierre = 1;
                            await actualizarStockLogico(producto, "editar", cantidadAnterior);
                            console.log('Talla del producto eliminada:', producto);
                            
                            const cantidadTallas = carrito[indiceProductoColor].tallas.length;

                            if (cantidadTallas == 0) {
                                carrito.splice(indiceProductoColor, 1);
                                console.log('Producto eliminado del carrito:', producto);
                            }
                        }
                    }
            }
        }

        await this.getProductosByModelo(modelo_id);
        console.log('Proceso de agregar producto completado.');
        document.getElementById('overlay').style.display = 'none';
        cargarCarritoPrevio();
        reordenarCarrito();
        pintarDetalle();
    }



    //========== CARGAR CARRITO PREVIO ===============
    function cargarCarritoPrevio (){
        //======= obteniendo inputs del modelo actual =======
        //============ colocando cantidades ========
        carrito.forEach((p)=>{
            p.tallas.forEach((t)=>{
                const selector = `input[data-producto-id="${p.producto_id}"][data-color-id="${p.color_id}"][data-talla-id="${t.talla_id}"]`;
                const inputSeleccionado = document.querySelector(selector);
                if(inputSeleccionado){
                    inputSeleccionado.value =   t.cantidad;
                }
            })
        })
    }

    //============  REORDENAR CARRITO ============
    function reordenarCarrito(){
            carrito.sort(function(a, b) {
                if (a.producto_id === b.producto_id) {
                    return a.color_id - b.color_id;
                } else {
                    return a.producto_id - b.producto_id;
                }
            });
        }


    //============== VALIDAR CANTIDAD CARRITO =============
    async function validarCantidadCarrito(inputCantidad){
        const stockLogico           =   await  this.getStockLogico(inputCantidad);
        const cantidadSolicitada    =   inputCantidad.value;
        return stockLogico>=cantidadSolicitada;
    }


    //============== formar objeto producto ================
    function formarProducto(ic){
        const producto_id = ic.getAttribute('data-producto-id');
        const producto_nombre = ic.getAttribute('data-producto-nombre');
        const color_id = ic.getAttribute('data-color-id');
        const color_nombre = ic.getAttribute('data-color-nombre');
        const talla_id = ic.getAttribute('data-talla-id');
        const talla_nombre = ic.getAttribute('data-talla-nombre');
        const cantidad     = ic.value?ic.value:0;
        const producto = {producto_id,producto_nombre,color_id,color_nombre,
                                talla_id,talla_nombre,cantidad};
        return producto;
    }

    //============= ACTUALIZAR STOCK LOGICO ==============
     async function actualizarStockLogico(producto,modo,cantidadAnterior){
         modo=="eliminar"?asegurarCierre=0:asegurarCierre=1;
        try {
            await this.axios.post(route('ventas.documento.cantidad'), {
                'producto_id'   :   producto.producto_id,
                'color_id'      :   producto.color_id,
                'talla_id'      :   producto.talla_id,
                'cantidad'      :   producto.cantidad,
                'condicion'     :   asegurarCierre,
                'modo'          :   modo,
                'cantidadAnterior'    :   cantidadAnterior,
                'tallas'        :   producto.tallas,
            });
                
        } catch (ex) {

        }
    }

    //=========== CALCULAR LA CANTIDAD DE PRODUCTOS ===============
    function calcularCantidad(lista){
        if(lista.length!==0){
            let cantidadTotal =   0;
            lista.forEach((p)=>{
                p.tallas.forEach((t)=>{
                    cantidadTotal += parseInt(t.cantidad);                
                })
            })
            return cantidadTotal;
        }else{
            return 0;
        }
    }

    //============== PINTAR DETALLE ===========
    function pintarDetalle(){
        let fila            =   ``;
        let cantidadTotal   =   calcularCantidad(carrito);
        
        carrito.forEach((p)=>{
            const carritoFiltrado = carrito.filter((c) => {
                return c.producto_id == p.producto_id && c.color_id == p.color_id;
            });

            const cantidadColor =   calcularCantidad(carritoFiltrado);
            fila += `   
                        <tr>
                            <th scope="row"> 
                                <i class="fas fa-trash-alt btn btn-primary delete-product"
                                data-producto="${p.producto_id}" data-color="${p.color_id}"></i>
                            </th>
                            <td>${cantidadColor}</td>
                            <td>${p.producto_nombre} - ${p.color_nombre}</td> 
                    `;
            let descripcion =   ``;
            p.tallas.forEach((t)=>{
                descripcion += `[${t.talla_nombre}/${t.cantidad}]`
            })

            fila+=  `
                            <td>${descripcion}</td>
                            <td>0.0</td>
                        </tr>
                    `;
        })
        tableDetalleBody.innerHTML          =   fila;
        tfoot_cantidadTotal.textContent     =   cantidadTotal;
    }

    //============= eliminar item producto_color del carrito ===============
    function eliminarItem(item,index) {
        document.getElementById('overlay').style.display = 'flex';
           try {
               //==== devolvemos el stock logico separado ========
               this.actualizarStockLogico(item, "eliminar");
               //====== obtenemos los stocks logicos actualizados de la bd ========
               //======== renderizamos la tabla de stocks ==============
               this.getProductosByModelo(modelo_id).then(()=>{
                    
                    //========== actualizamos el carrito =======
                    carrito.splice(index, 1);
                    carrito.length>0?asegurarCierre=1:0;
                    //======= reordenamos el carrito =======
                    reordenarCarrito();
                    //====== pintamos el carrito ==========
                    pintarDetalle();
                    //==== retiramos el overlay ======
                    document.getElementById('overlay').style.display = 'none';
                    //======= alerta ==========
                    toastr.success('Producto eliminado',"Cantidad devuelta")
                });               
           } catch (error) {
                //==== retiramos el overlay ======
                document.getElementById('overlay').style.display = 'none';
               console.error("Error en Eliminar item:", error);
               alert("Error en Eliminar item: " + error);
           } 
    }

     //============= devolver stock logico, ya que hay errores en la cotización ===================
     window.addEventListener('beforeunload', async () => {
        if (asegurarCierre == 1) {
            await this.DevolverCantidades();
            asegurarCierre = 10;
        } else {
            console.log("beforeunload", asegurarCierre);
        }
    });

    //================ devolver cantidades ===============
    async function DevolverCantidades() {
        await this.axios.post(route('ventas.documento.devolver.cantidades'), {
            carrito: JSON.stringify(carrito)
        });  
    }

</script>
@endpush