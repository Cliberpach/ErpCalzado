@extends('layout') 
@section('content')

@section('almacenes-active', 'active')
@section('solicitudes_traslado-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Solicitudes de traslado</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Solicitudes de traslado</strong>
            </li>
        </ol>
    </div>
  
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                       @include('almacenes.solicitudes_traslado.tables.tbl_sol_tr_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>

let dtSolicitudesTraslado    =   null;

document.addEventListener('DOMContentLoaded',()=>{
    iniciarDTSolicitudesTraslado();
})

function iniciarDTSolicitudesTraslado(){
    const urlGetSolicitudesTraslado = '{{ route('almacenes.solicitud_traslado.getSolicitudesTraslado') }}';

    dtSolicitudesTraslado  =   new DataTable('#tbl_sol_tr_list',{
        serverSide: true,
        processing: true,
        ajax: {
            url: urlGetSolicitudesTraslado,
            type: 'GET',
        },
        columns: [
            { 
                data: 'simbolo', 
                className: "text-center", 
                render: function(data, type, row) {
                    return `<div style="width:100px;">
                                <p style="margin:0;padding:0;font-weight:bold;">${data}</p>
                            </div>`; 
                }
            },
            { data: 'almacen_origen_nombre',className: "text-center"},
            { data: 'almacen_destino_nombre',className: "text-center"},
            { data: 'sede_origen_direccion',className: "text-center"},
            { data: 'sede_destino_direccion',className: "text-center"}, 
            { data: 'observacion',className: "text-center"}, 
            { data: 'fecha_registro',className: "text-center"}, 
            { data: 'fecha_traslado',className: "text-center"}, 
            { data: 'registrador_nombre',className: "text-center"}, 
            {
                data: 'estado',
                className: "text-center",
                render: function(data, type, row) {
                    if (data === 'PENDIENTE') {
                        return '<span class="badge badge-danger">PENDIENTE</span>';
                    } else if (data === 'RECIBIDO') {
                        return '<span class="badge badge-primary">RECIBIDO</span>';
                    }
                    return data;
                }
            },
            {
                data: null, 
                render: function(data, type, row) {

                    let urlConfirmar   =   `{{ route('almacenes.solicitud_traslado.confirmarShow', ['id' => ':id']) }}`;
                    urlConfirmar       =   urlConfirmar.replace(':id', data.id); 

                    let urlVer   =   `{{ route('almacenes.solicitud_traslado.show', ['id' => ':id']) }}`;
                    urlVer       =   urlVer.replace(':id', data.id); 

                    let acciones    =   `<div class='btn-group' style='text-transform:capitalize;'>
                                            <button data-toggle='dropdown' class='btn btn-primary btn-sm dropdown-toggle'>
                                            <i class='fa fa-bars'></i>
                                            </button>
                                            <ul class='dropdown-menu'>`;

                    if(data.estado === 'PENDIENTE'){
                       acciones += `<li>
                                        <a class='dropdown-item' href='${urlConfirmar}' title='Confirmar'>
                                        <b><i class="fas fa-check"></i> Confirmar</b>
                                        </a>
                                    </li>`; 
                    }

                    acciones    +=  `
                                    <li>
                                        <a class='dropdown-item' href='${urlVer}' title='Ver'>
                                        <b><i class="fas fa-eye"></i> Ver</b>
                                        </a>
                                    </li>
                                    </ul>
                                    </div>`;
                                    
                    return acciones;
                },
                name: 'actions', 
                orderable: false, 
                searchable: false 
            }
        ],
        language: {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
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


</script>
@endpush