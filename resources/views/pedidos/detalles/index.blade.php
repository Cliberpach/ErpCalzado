@extends('layout') @section('content')

@section('pedidos-active', 'active')
@section('pedidos-detalles-active', 'active')



<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Detalles de Pedidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos Detalles</strong>
            </li>
        </ol>
    </div>
   
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mb-3">
        <div class="col-9">
            <div class="row">
                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_inicio" style="font-weight: bold;">Fecha desde:</label>
                    <input type="date" class="form-control" id="filtroFechaInicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" onchange="filtrarDespachoFechaInic(this.value)">
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_fin" style="font-weight: bold;">Fecha hasta:</label>
                    <input type="date" class="form-control" id="filtroFechaFin" value="{{ now()->format('Y-m-d') }}" onchange="filtrarDespachoFechaFin(this.value)">
                </div> --}}
                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="pedido_estado" style="font-weight: bold;">Estado</label>
                    <select  id="pedido_estado" class="form-control select2_form" onchange="filtrarDespachosEstado(this.value)">
                        <option value=""></option>
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIENDO">ATENDIENDO</option>
                        <option value="FINALIZADO">FINALIZADO</option>
                    </select>
                </div> --}}

            </div>
        </div>
       <div class="col-3 d-flex align-items-end justify-content-end">

       </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="pedidos_table"
                            style="text-transform:uppercase" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">FACTURADO</th>
                                    <th class="text-center">COT</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">USUARIO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">ACCIONES</th>
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

@stop