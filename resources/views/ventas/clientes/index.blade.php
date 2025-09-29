@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('clientes-active', 'active')
@include('ventas.clientes.modalfile')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Clientes</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Clientes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_cliente" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
        <button id="btn_file_cliente" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-file-excel-o"></i> Importar Excel
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-cliente table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>

                                <tr>

                                    <th class="text-center">TIPO DOC</th>
                                    <th class="text-center">N° DOC</th>
                                    <th class="text-center">NOMBRE</th>
                                    <th class="text-center">TELÉFONO MÓVIL</th>
                                    <th class="text-center">DEPARTAMENTOS</th>
                                    <th class="text-center">PROVINCIAS</th>
                                    <th class="text-center">DISTRITOS</th>
                                    <th class="text-center">ZONAS</th>
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
@push('styles')
<style>
    .letrapequeña {
        font-size: 11px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {

        // DataTables
        let table = $('.dataTables-cliente').DataTable({
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'Clientes'
                },
                {
                    titleAttr: 'Imprimir',
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            serverSide: true,
            "ajax": "{{ route('ventas.cliente.getTable') }}",
            initComplete: function() {
                let api = this.api();
                $(api.table().container())
                    .find('.dt-search input')
                    .after(
                        '<small class="form-text text-muted d-block">Puedes buscar por: tipo doc, documento, nombre, teléfono, departamento, provincia, distrito.</small>'
                        );
            },
            "columns": [{
                    data: 'tipo_documento',
                    name: 'c.tipo_documento',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'documento',
                    name: 'c.documento',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'nombre',
                    name: 'c.nombre',
                    className: "text-left letrapequeña"
                },
                {
                    data: 'telefono_movil',
                    name: 'c.telefono_movil',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'departamento',
                    name: 'd.nombre',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'provincia',
                    name: 'p.nombre',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'distrito',
                    name: 'd.nombre',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'zona',
                    searchable: false,
                    className: "text-center letrapequeña"
                },
                {
                    searchable: false,
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        var url_detalle = '{{ route('ventas.cliente.show', ':id') }}';
                        url_detalle = url_detalle.replace(':id', data.id);

                        //Ruta Modificar
                        var url_editar = '{{ route('ventas.cliente.edit', ':id') }}';
                        url_editar = url_editar.replace(':id', data.id);

                        //Ruta Tiendas
                        var url_tienda = '{{ route('clientes.tienda.index', ':id') }}';
                        url_tienda = url_tienda.replace(':id', data.id);

                        /* return "<div class='btn-group'>" +
                             "<a class='btn btn-primary btn-sm' href='" + url_tienda +
                             "' title='Tiendas'><i class='fa fa-shopping-cart'></i></a>" +
                             "<a class='btn btn-success btn-sm' href='" + url_detalle +
                             "' title='Detalle'><i class='fa fa-eye'></i></a>" +
                             "<a class='btn btn-warning btn-sm modificarDetalle' href='" +
                             url_editar + "' title='Modificar'><i class='fa fa-edit'></i></a>" +
                             "<a class='btn btn-danger btn-sm' href='#' onclick='eliminar(" +
                             data.id + ")' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                             "</div>";*/

                        return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +

                            "<li><a class='dropdown-item' href='" + url_detalle +
                            "' title='Modificar' ><b><i class='fa fa-eye'></i>Detalle</a></b></li>" +

                            "<li><a class='dropdown-item' href='" + url_editar +
                            "' title='Modificar' ><b><i class='fa fa-edit'></i>Editar</a></b></li>" +

                            "<li><a class='dropdown-item' onclick='eliminar(" + data.id +
                            ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>" +

                            "<li class='dropdown-divider'></li>" +
                            "</ul></div>"
                    }
                }

            ],
            language: {
                decimal: ",",
                thousands: ".",
                processing: "Procesando...",
                search: "Buscar:",
                searchPlaceholder: "Doc, nombre, teléfono, distrito...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos disponibles en la tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                },
                aria: {
                    sortAscending: ": activar para ordenar la columna de manera ascendente",
                    sortDescending: ": activar para ordenar la columna de manera descendente"
                }
            },
            "order": [],
        });

        // Eventos
        $('#btn_añadir_cliente').on('click', añadirCliente);
    });

    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

    //Modal Eliminar
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    // Funciones de Eventos
    function añadirCliente() {
        window.location = "{{ route('ventas.cliente.create') }}";
    }

    function editarCliente(url) {
        window.location = url;
    }

    function eliminar(id) {
        Swal.fire({
            title: 'Opción Eliminar',
            text: "¿Seguro que desea guardar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                //Ruta Eliminar
                var url_eliminar = '{{ route('ventas.cliente.destroy', ':id') }}';
                url_eliminar = url_eliminar.replace(':id', id);
                $(location).attr('href', url_eliminar);

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
    }
    $("#btn_file_cliente").on('click', function() {
        $("#modal_file").modal('show');
    });
</script>
@endpush
