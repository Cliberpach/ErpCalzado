@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Cotizaciones</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cotizaciones</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_cotizacion" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-cotizacion table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">EMPRESA</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">FECHA DOCUMENTO</th>
                                    <th class="text-center">TOTAL</th>
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
@push('styles')
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {

        // DataTables
        $('.dataTables-cotizacion').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'Tablas Generales'
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
            "ajax": "{{ route('ventas.cotizacion.getTable') }}",
            "columns": [{
                    data: 'id',
                    className: "text-center"
                },
                {
                    data: 'empresa',
                    className: "text-left"
                },
                {
                    data: 'cliente',
                    className: "text-left"
                },
                {
                    data: 'fecha_documento',
                    className: "text-center"
                },
                {
                    data: 'total',
                    className: "text-center"
                },

                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        switch (data.estado) {
                            case "PENDIENTE":
                                return "<span class='badge badge-warning' d-block>" + data
                                    .estado +
                                    "</span>";
                                break;
                            case "VENCIDA":
                                return "<span class='badge badge-danger' d-block>" + data
                                    .estado +
                                    "</span>";
                                break;
                            case "ATENDIDA":
                                return "<span class='badge badge-success' d-block>" + data
                                    .estado +
                                    "</span>";
                                break;
                            default:
                                return "<span class='badge badge-success' d-block>" + data
                                    .estado +
                                    "</span>";
                        }
                    },
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        var url_detalle = '{{ route('ventas.cotizacion.show', ':id') }}';
                        url_detalle = url_detalle.replace(':id', data.id);

                        //Ruta Modificar
                        var url_editar = '{{ route('ventas.cotizacion.edit', ':id') }}';
                        url_editar = url_editar.replace(':id', data.id);

                        var url_imprimir = '{{route("ventas.cotizacion.reporte", ":id")}}';
                        url_imprimir = url_imprimir.replace(':id', data.id);

                        return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +

                            "<li><a class='dropdown-item' target='_blank' href='" + url_imprimir +
                            "' title='Detalle'><b><i class='fa fa-file-pdf-o'></i> Pdf</a></b></li>" +
                            "<li><a class='dropdown-item' onclick='documento(" + data.id +
                            ")' title='Documento'><b><i class='fa fa-file'></i> Documento</a></b></li>" +
                            "<li><a class='dropdown-item' href='" + url_editar +
                            "' title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li>" +
                            "<li><a class='dropdown-item' onclick='eliminar(" + data.id +
                            ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>" +

                            "</ul></div>"


                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [],
        });

        // Eventos
        $('#btn_añadir_cotizacion').on('click', añadirCotizacion);
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
    });

    // Funciones de Eventos
    function añadirCotizacion() {
        window.location = "{{ route('ventas.cotizacion.create') }}";
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
                var url_eliminar = '{{ route('ventas.cotizacion.destroy', ':id') }}';
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

    function documento(id) {
        Swal.fire({
            title: 'Opción Documento de venta',
            text: "¿Seguro que desea crear un documento de venta?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                //Ruta Documento
                var url_concretar = '{{ route('ventas.cotizacion.documento', ':id') }}';
                url_concretar = url_concretar.replace(':id', id);
                $(location).attr('href', url_concretar);

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

    @if (!empty($id))
        Swal.fire({
        title: 'Documento de Venta duplicado',
        text: "¿Desea anular el documento y crear uno nuevo?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        }).then((result) => {
        if (result.isConfirmed) {
        //Ruta Nuevo Documento
        var url_nuevo = '{{ route('ventas.cotizacion.nuevodocumento', ':id') }}';
        url_nuevo = url_nuevo.replace(':id', "{{ $id }}");
        $(location).attr('href', url_nuevo);


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
    @endif
</script>
@endpush
