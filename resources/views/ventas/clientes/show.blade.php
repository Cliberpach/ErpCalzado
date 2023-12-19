@extends('layout')

@section('content')

@section('ventas-active', 'active')
@section('clientes-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Datos del Cliente: {{ $cliente->nombre }}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('ventas.cliente.index') }}">Clientes</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Datos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div>
                        <a href="{{ route('ventas.cliente.edit', $cliente->id) }}" class="btn btn-warning btn-xs float-right">
                            <i class="fa fa-edit"></i>EDITAR CLIENTE
                        </a>
                       <h2  style="text-transform:uppercase">{{ $cliente->nombre }}</h2>
                    </div>

                    <div class="tabs-container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li><a class="nav-link active" data-toggle="tab" href="#tab-personales"> DATOS DEL CLIENTE  </a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-contacto"> DATOS DEL NEGOCIO   </a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-laborales"> DATOS DEL PROPIETARIO </a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" id="tab-personales" class="tab-pane active">
                                <div class="panel-body">

                                    <h4><b><i class="fa fa-caret-right"></i> DATOS DEL CLIENTE</b></h4><br>
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>TIPO DE DOCUMENTO</strong></label>
                                            <p>{{ $cliente->tipo_documento }}</p>
                                        </div>
                                        <div class="form-group col-lg-3 col-xs-12">
                                            <label><strong>DOCUMENTO</strong></label>
                                            <p>{{ $cliente->documento }}</p>
                                        </div>
                                        <div class="form-group col-lg-3 col-xs-12">
                                            <label><strong>TIPO CLIENTE</strong></label>
                                            @php
                                            foreach (tipo_clientes() as $tipo_cliente) {
                                                if ($cliente->tabladetalles_id == $tipo_cliente->id) {
                                                    echo ("<p>".$tipo_cliente->descripcion."</p>");
                                                }
                                            }
                                            @endphp
                                        </div>
                                        <div class="form-group col-lg-2 col-xs-12">
                                            <label><strong>ESTADO</strong></label>
                                            <p>{{ $cliente->activo }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>NOMBRE</strong></label>
                                            <p>{{ $cliente->nombre }}</p>
                                        </div>
                                        <div class="form-group col-lg-6 col-xs-12">
                                            <label><strong>DIRECCION COMPLETA</strong></label>
                                            <p>{{ $cliente->direccion }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>DEPARTAMENTO</strong></label>
                                            <p>{{ $cliente->getDepartamento() }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>PROVINCIA</strong></label>
                                            <p>{{ $cliente->getProvincia() }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>DISTRITO</strong></label>
                                            <p>{{ $cliente->getDistrito() }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>TELÉFONO MÓVIL</strong></label>
                                            <p>{{ $cliente->telefono_movil }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>TELÉFONO FIJO</strong></label>
                                            <p>{{ ($cliente->telefono_fijo) ? $cliente->telefono_fijo : '-' }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>CORREO ELECTRÓNICO</strong></label>
                                            <p>{{ ($cliente->correo_electronico) ? $cliente->correo_electronico : '-' }}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div role="tabpanel" id="tab-contacto" class="tab-pane">
                                <div class="panel-body">
                                    <h4><b><i class="fa fa-caret-right"></i> DATOS DEL NEGOCIO</b></h4><br>
                                    <div class="row">
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>DIRECCION DE NEGOCIO</strong></label>
                                            <p>{{ $cliente->direccion_negocio }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>FECHA DE ANIVERSARIO</strong></label>
                                            <p>{{ $cliente->fecha_aniversario }}</p>
                                        </div>
                                        <div class="form-group col-lg-4 col-xs-12">
                                            <label><strong>OBSERVACIONES</strong></label>
                                            <p>{{ $cliente->observaciones }}</p>
                                        </div>
                                    </div>

                                    <div class="row">

                                            <div class="col-md-6 b-r">
                                            <h4><b><i class="fa fa-caret-right"></i> REDES SOCIALES</b></h4><br>

                                                <div class="form-group">
                                                    <label><strong>FACEBOOK: </strong></label>
                                                    @if($cliente->facebook != "")
                                                        <p>{{$cliente->facebook}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>INSTAGRAM: </strong></label>
                                                    @if($cliente->instagram != "")
                                                        <p>{{$cliente->instagram}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>WEB: </strong></label>
                                                    @if($cliente->web != "")
                                                        <p>{{$cliente->web}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>


                                            </div>

                                            <div class="col-md-6">
                                            <h4><b><i class="fa fa-caret-right"></i> HORARIOS DE ATENCION</b></h4><br>

                                            <div class="row" style="text-transform:uppercase;">
                                                <div class="col-md-6">
                                                    <label><strong>Hora de Inicio: </strong></label> 
                                                    @if($cliente->hora_inicio != "")
                                                        <p>{{$cliente->hora_inicio}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>Hora de Termino: </strong></label> 
                                                    @if($cliente->hora_termino != "")
                                                        <p>{{$cliente->hora_termino}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>
                                            </div>

                                            
                                            </div>
                                    </div>

                                </div>
                            </div>
                            <div role="tabpanel" id="tab-laborales" class="tab-pane">
                                <div class="panel-body">
                                    <h4><b><i class="fa fa-caret-right"></i> DATOS DEL PROPIETARIO </b></h4><br>

                                    <div class="form-group row" style="text-transform:uppercase;">
                                        
                                        <div class="col-md-6 b-r">

                                                <div class="form-group">
                                                    <label><strong>NOMBRE: </strong></label>
                                                    @if($cliente->nombre_propietario != "")
                                                        <p>{{$cliente->nombre_propietario}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label><strong>DIRECCION: </strong></label>
                                                    @if($cliente->direccion_propietario != "")
                                                        <p>{{$cliente->direccion_propietario}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>

                                                
                                                <div class="form-group">
                                                    <label><strong>CORREO: </strong></label>
                                                    @if($cliente->correo_propietario != "")
                                                        <p>{{$cliente->correo_propietario}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>




                                        </div>

                                        <div class="col-md-6">

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <label><strong>FECHA DE NACIMIENTO: </strong></label> 
                                                    @if($cliente->fecha_nacimiento_prop != "")
                                                        <p>{{ Carbon\Carbon::parse($cliente->fecha_nacimiento_prop)->format('d/m/y') }}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label><strong>CELULAR: </strong></label> 
                                                    @if($cliente->celular != "")
                                                        <p>{{$cliente->celular}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                </div>



                                            </div>

                                        </div>



                                    </div>
 
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- iCheck -->
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
@endpush
