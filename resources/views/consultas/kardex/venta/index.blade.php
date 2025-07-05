@extends('layout')

@section('content')
@section('kardex-active', 'active')
@section('venta_kardex-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12">
       <h2  style="text-transform:uppercase"><b>Listado de Productos Vendidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Productos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="filtrar()"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('consultas.kardex.venta.tables.tbl_list_kventas')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop


@push('styles')
<link href="https://cdn.datatables.net/v/bs4/dt-2.3.2/r-3.0.5/sc-2.4.3/datatables.min.css" rel="stylesheet" integrity="sha384-Y3z8QWg2WxeGcCzoUszKioTOl/t5nsuw04Ovug3XmL1vq/3169xaFbunr4cHL2NG" crossorigin="anonymous">
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/v/bs4/dt-2.3.2/r-3.0.5/sc-2.4.3/datatables.min.js" integrity="sha384-4iaAiW9Gi2uzZr7WGtxLB2ax39LxR04skeQ7solBQcyk0k55Q+fY2hjec0EHc5kd" crossorigin="anonymous"></script>

<script>

    let dtCVentas = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDtKVentas();
    });


    function iniciarDtKVentas(){
        const url = '{{ route('consultas.kardex.venta.getTable') }}';

        dtCVentas  =   new DataTable('.dt-kventas-list',{
            serverSide: true,
            processing: true,
            responsive:true,
            "order": [[0, 'desc'], [1, 'desc']],
            ajax: {
                url: url,
                type: 'GET',
                data: function (d) {
                    d.fecha_inicio      =   document.querySelector('#fecha_inicio').value;
                    d.fecha_fin         =   document.querySelector('#fecha_fin').value;
                }
            },
            initComplete: function () {
                $('.dt-search').append(`
                    <div class="text-muted small mt-1">
                        <strong>Buscar por: Fecha, Código, Modelo, Producto, Color, Talla, Sede, Registrador</strong>
                    </div>
                `);
            },
            columns: [
                { data: 'fecha_registro', name: 'cd.created_at' },
                { data: 'codigo', name: 'codigo' },
                { data: 'almacen_nombre', name: 'cdd.almacen_nombre' },
                { data: 'nombre_modelo', name: 'cdd.nombre_modelo' },
                { data: 'nombre_producto', name: 'cdd.nombre_producto' },
                { data: 'nombre_color', name: 'cdd.nombre_color' },
                { data: 'nombre_talla', name: 'cdd.nombre_talla' },
                { data: 'cantidad', name: 'cantidad',searchable:false },
                { data: 'sede', name: 'es.nombre' },
                { data: 'registrador_nombre', name: 'u.usuario' },
            ],
            language: {
                "lengthMenu": "Mostrar _MENU_ items por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ items",
                "infoEmpty": "Mostrando 0 a 0 de 0 items",
                "infoFiltered": "(filtrado de _MAX_ items totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function filtrar(){

        toastr.clear();
        const fecha_inicio      =   document.querySelector('#fecha_inicio').value;
        const fecha_fin         =   document.querySelector('#fecha_fin').value;

        if(fecha_inicio > fecha_fin && fecha_fin && fecha_inicio){
            toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
            document.querySelector('#fecha_inicio').focus();
            return;
        }
        dtCVentas.draw();

    }


</script>
@endpush
