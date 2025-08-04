@extends('layout')
@section('content')

@section('almacenes-active', 'active')
@section('traslados-active', 'active')

@csrf
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Lista de Traslados</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Traslados</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{ route('almacenes.traslados.create') }}">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('almacenes.traslados.tables.tbl_traslados_index')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
@push('styles')
@endpush

@push('scripts')
<script>
    let dtTraslados = null;

    $(document).ready(function() {

        // DataTables
        dtTraslados = $('.dataTables-traslados').DataTable({
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
            "responsive": true,
            "processing": true,
            "ajax": "{{ route('almacenes.traslados.getTraslados') }}",
            "order": [
                [0, 'desc']
            ],
            "columns": [
                {
                    data: 'id',
                    className: "text-center",
                    visible: false
                },
                {
                    data: 'simbolo',
                    className: "text-center",
                    render: function(data, type, row) {
                        return `<div style="width:100px;">
                                <p style="margin:0;padding:0;font-weight:bold;">${data}</p>
                            </div>`;
                    }
                },
                {
                    data: 'guia',
                    className: "text-center"
                },
                {
                    data: 'almacen_origen_nombre',
                    className: "text-center"
                },
                {
                    data: 'almacen_destino_nombre',
                    className: "text-center"
                },
                {
                    data: 'sede_origen_direccion',
                    className: "text-center"
                },
                {
                    data: 'sede_destino_direccion',
                    className: "text-center"
                },
                {
                    data: 'observacion',
                    className: "text-center"
                },
                {
                    data: 'fecha_registro',
                    className: "text-center"
                },
                {
                    data: 'fecha_traslado',
                    className: "text-center"
                },
                {
                    data: 'registrador_nombre',
                    className: "text-center"
                },
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
                    className: "text-center",
                    render: function(data) {

                        let url_detalles = '{{ route('almacenes.traslados.show', ':id') }}';
                        url_detalles = url_detalles.replace(':id', data.id);

                        let acciones = `<div class='btn-group' style='text-transform:capitalize;'>
                                        <button data-toggle='dropdown' class='btn btn-primary btn-sm dropdown-toggle'>
                                        <i class='fa fa-bars'></i>
                                        </button>
                                        <ul class='dropdown-menu'>
                                            <li>
                                                <a class='dropdown-item' href='${url_detalles}' title='Detalles'>
                                                <b><i class='fa fa-eye'></i> Detalles</b>
                                                </a>
                                            </li>`;

                        if (!data.guia_id) {
                            acciones += ` <li>
                                                <a class='dropdown-item' href='javascript:void(0);' title='Guía Remisión' onclick='generarGuia(${data.id})'>
                                                    <b><i class='fa fa-file-pdf-o'></i> Guía Remisión</b>
                                                </a>
                                            </li>`;
                        }

                        if (data.estado === 'PENDIENTE') {
                            acciones += ` <li>
                                                <a class='dropdown-item' href='javascript:void(0);' title='Eliminar Traslado' onclick='eliminarTraslado(${data.id})'>
                                                    <b><i class='fa fa-trash'></i> Eliminar</b>
                                                </a>
                                            </li>`;
                        }

                        acciones += `</ul></div>`;

                        return acciones;
                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [
                [0, "desc"]
            ],
        });

    });

    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    function comprobante(id) {
        var url = '{{ route('almacenes.nota_salidad.getPdf', ':id') }}';
        url = url.replace(':id', id + '-100');
        window.open(url, "Comprobante SISCOM", "width=900, height=600")
    }


    function generarGuia(id) {
        Swal.fire({
            title: 'Desea generar una guía de remisión',
            text: "Será redirigido a un formulario de guía de remisión",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {

                //==== RUTA GUÍA REMISIÓN ====
                let guia_create = '{{ route('almacenes.traslados.generarGuiaCreate', ':id') }}';
                guia_create = guia_create.replace(':id', id);

                window.location.href = guia_create;

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

    function eliminarTraslado(id) {
        toastr.clear();
        let row = getRowById(dtTraslados, id);
        let message = '';
        let tipo_documento = '';

        message = `Desea eliminar el traslado: ${row.simbolo}`;

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: message,
            text: `Se devolverá stock en almacén origen: ${row.almacen_origen_nombre}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: `Eliminando traslado y devolviendo stock en almacén origen ${row.almacen_origen_nombre}`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let urlDelete =
                        `{{ route('almacenes.traslados.anularTrasladoPendiente', ['id' => ':id']) }}`;
                    urlDelete = urlDelete.replace(':id', id);
                    const token = document.querySelector('input[name="_token"]').value;

                    const response = await fetch(urlDelete, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    });

                    const res = await response.json();

                    if (res.success) {
                        dtTraslados.ajax.reload();
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR TRASLADO');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR TRASLADO');
                } finally {
                    Swal.close();
                }

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire({
                    title: "Operación cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                });
            }
        });
    }
</script>
@endpush
