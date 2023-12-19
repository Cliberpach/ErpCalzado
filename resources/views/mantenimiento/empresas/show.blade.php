@extends('layout') @section('content')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
       <h2  style="text-transform:uppercase"><b>Detalle de la Empresa: {{$empresa->razon_social}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('mantenimiento.empresas.index')}}">Empresas</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detalle</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row" style="text-transform:uppercase">
            <div class="col-lg-9">
                <div class="wrapper wrapper-content animated fadeInUp">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <a href="{{route('mantenimiento.empresas.edit',$empresa->id)}}" class="btn btn-warning btn-xs float-right"><i class='fa fa-edit'></i>Editar Empresa</a>
                                       <h2  style="text-transform:uppercase">{{$empresa->razon_social}}</h2>
                                    </div>
                                    <p  onkeyup="return mayus(this)"><strong><i class="fa fa-caret-right"></i> Información general de la empresa:</strong></p>

                                </div>
                            </div>

                            <div class="row"  onkeyup="return mayus(this)">

                                <div class="col-md-6 b-r">

                                    <div class="form-group">
                                        <label><strong>Ruc: </strong></label>
                                        <p class="text-navy">{{$empresa->ruc}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Razón Social: </strong></label>
                                        <p >{{$empresa->razon_social}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Razón Social Abreviada: </strong></label> 
                                        @if($empresa->razon_social_abreviada != "")
                                            <p>{{$empresa->razon_social}}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                       
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Dirección Fiscal: </strong></label>
                                        <p >{{$empresa->direccion_fiscal}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Dirección de Llegada: </strong></label>
                                        <p >{{$empresa->direccion_llegada}}</p>
                                    </div>
                        
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label><strong>Telefono: </strong></label> 
                                            @if($empresa->telefono != "")
                                                <p>{{$empresa->telefono}}</p>
                                            @else
                                                <p>-</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label><strong>Celular: </strong></label> 
                                            @if($empresa->celular != "")
                                                <p>{{$empresa->celular}}</p>
                                            @else
                                                <p>-</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>FACEBOOK: </strong></label> 
                                        @if($empresa->facebook != "")
                                            <p>{{$empresa->facebook}}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                       
                                    </div>

                                    <div class="form-group">
                                        <label><strong>INSTAGRAM: </strong></label> 
                                        @if($empresa->instagram != "")
                                            <p>{{$empresa->instagram}}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                       
                                    </div>

                                    <div class="form-group">
                                        <label><strong>WEB: </strong></label> 
                                        @if($empresa->web != "")
                                            <p>{{$empresa->web}}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                       
                                    </div>



                                </div>


       
                                
                            </div>

                            <hr>
                            <div class="form-group">
                                <p  style="text-transform:uppercase"><strong><i class="fa fa-caret-right"></i> Informacion de las entidades Financieras asociadas a la empesa:</strong></p>
                                <div class="table-responsive">
                                    <table class="table dataTables-bancos table-striped table-bordered table-hover"
                                    style="text-transform:uppercase">
                                        <thead>
                                            <tr>
                                                <th class="text-left">DESCRIPCION</th>
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
                          
                            
                            <p  onkeyup="return mayus(this)"><strong> <i class="fa fa-caret-right"></i> Representante de la empresa:</strong></p>
                            <div class="row"  onkeyup="return mayus(this)">

                                    <div class="col-md-6 b-r">

                                        <div class="form-group">
                                            <label><strong>Dni: </strong></label>
                                            <p>{{$empresa->dni_representante}}</p>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label><strong>Nombre Completo: </strong></label>
                                            <p >{{$empresa->nombre_representante}}</p>
                                        </div>

                                    </div>

                            </div>

                            <hr>
                            <p  onkeyup="return mayus(this)"><strong><i class="fa fa-caret-right"></i> Registros Públicos:</strong></p>
                            <div class="row"  onkeyup="return mayus(this)">

                                    <div class="col-md-6 b-r">

                                        <div class="form-group">
                                            <label><strong>N° Asiento: </strong></label>
                                            <p >{{$empresa->num_asiento}}</p>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label><strong>N° Partida: </strong></label>
                                            <p >{{$empresa->num_partida}}</p>
                                        </div>

                                    </div>

                            </div>






                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="wrapper wrapper-content project-manager"  onkeyup="return mayus(this)">
                    <h4>Empresa</h4>
                    <p><b>Información adicional:</b><p>
                    <div class="text-center">
                        
                        @if($empresa->ruta_logo)
                            <img  src="{{Storage::url($empresa->ruta_logo)}}" class="img-fluid">
                        @else
                            <img  src="{{asset('storage/empresas/logos/default.png')}}" class="img-fluid">
                        @endif

                    <div>
                    <div class="text-center m-t-md">
                        @if($empresa->ruta_logo)
                            <a title="{{$empresa->nombre_logo}}" download="{{$empresa->nombre_logo}}" href="{{Storage::url($empresa->ruta_logo)}}" class="btn btn-xs btn-block btn-primary"><i class="fa fa-download"></i> Descargar Logo</a>
                        @else
                            <a title="Logo por defecto" download="Logo por defecto" href="{{asset('storage/empresas/logos/default.png')}}" class="btn btn-xs btn-block btn-primary"><i class="fa fa-download"></i> Descargar Logo</a>
                        @endif

                    
                    </div>
                    <hr>
                    <div class="row">
                                <div class="col-lg-12">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-left"><dt>CREADO:</dt> </div>
                                        <div class="col-sm-8 text-sm-right"><dd class="mb-1">  {{ Carbon\Carbon::parse($empresa->created_at)->format('d/m/y - G:i:s') }}</dd> </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-left"><dt>ACTUALIZADO:</dt> </div>
                                        <div class="col-sm-8 text-sm-right"> <dd class="mb-1">  {{ Carbon\Carbon::parse($empresa->updated_at)->format('d/m/y - G:i:s') }}</dd></div>
                                    </dl>

                                </div>
                    </div>
                </div>
            </div>
        </div>
@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<style>
div.dataTables_wrapper div.dataTables_paginate ul.pagination {  
    margin-left:2px;
}
</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>

<script>
$(document).ready(function() {

    // DataTables
    $('.dataTables-bancos').DataTable({
        "dom": 'Ttp',
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
                className: "text-left",
            },
            {
                "targets": [1],
                className: "text-center",
            },
            {
                "targets": [2],
                className: "text-center",
            },
            {
                "targets": [3],
                className: "text-center",
            },
            {
                "targets": [4],
                className: "text-center",
            },

        ],

    });
    obtenerTabla()

})

function obtenerTabla() {
    var t = $('.dataTables-bancos').DataTable();
    @foreach($banco as $ban)
    t.row.add([
        "{{$ban->descripcion}}",
        "{{$ban->tipo_moneda}}",
        "{{$ban->num_cuenta}}",
        "{{$ban->cci}}",
        "{{$ban->itf}}",
    ]).draw(false);
    @endforeach
}
</script>
@endpush