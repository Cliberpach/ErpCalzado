@extends('layout')
@section('content')

    @include('almacenes.colores.modalfile')
    @include('almacenes.colores.modals.mdl_create_color')
    @include('almacenes.colores.modals.mdl_edit_color')

@section('almacenes-active', 'active')
@section('color-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Colores</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Colores</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a data-toggle="modal" class="btn btn-block btn-w-m btn-primary m-t-md" href="javascript:void(0);"
            onclick="openMdlCreateColor()">
            <i class="fa fa-plus-square"></i> NUEVO
        </a>
        <a class="btn btn-block btn-w-m btn-primary m-t-md btn-modal-file" href="#">
            <i class="fa fa-plus-square"></i> Importar Excel
        </a>
    </div>

</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">

                <div class="ibox-content">

                    <div class="table-responsive">
                        @include('almacenes.colores.tables.tbl_list_colores')
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@stop
@push('styles')
<style>
    .my-swal {
        z-index: 3000 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let dtColores = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDtColores();
        events();
    })

    function events() {
        eventsMdlCreateColor();
        eventsMdlEditColor();
    }

    function iniciarDtColores() {
        dtColores = new DataTable('#dt-colores', {
            "processing": true,
            "ajax": '{{ route('almacenes.colores.getColores') }}',
            "columns": [{
                    data: 'id',
                    className: "text-center",
                    "visible": false
                },
                {
                    data: 'descripcion',
                    className: "text-center"
                },
                {
                    data: 'codigo',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (!data) {
                            return '<span class="text-muted">—</span>';
                        }
                        return `
                        <div style="
                            display: inline-block;
                            width: 25px;
                            height: 25px;
                            border-radius: 5px;
                            border: 1px solid #ccc;
                            background-color: ${data};
                        " title="${data}"></div>
                        <div style="font-size: 0.85rem; margin-top: 3px;">${data}</div>
                    `;
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button
                                    class="btn btn-warning btn-sm modificarDetalle"
                                    onclick="openMdlEditColor(${data.id})"
                                    type="button"
                                    title="Modificar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a
                                    class="btn btn-danger btn-sm"
                                    href="#"
                                    onclick="eliminar(${data.id})"
                                    title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        `;
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

    }

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    function eliminar(id) {
        const fila = getRowById(dtColores, id);
        const descripcion = fila?.descripcion || 'Sin descripción';
        const codigo = fila?.codigo || '#ffffff';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        });

        Swal.fire({
            title: '¿Desea eliminar el color?',
            html: `
            <div style="text-align: center; font-size: 15px;">
                <p><i class="fa fa-palette text-primary"></i>
                    <strong>Descripción:</strong> ${descripcion}
                </p>
                <p><i class="fa fa-square text-info"></i>
                    <strong>Código:</strong> ${codigo}
                    <span style="display:inline-block; width:20px; height:20px; background:${codigo}; border:1px solid #ccc; margin-left:6px; vertical-align:middle; border-radius:4px;"></span>
                </p>
                <hr>
                <p style="color: #d9534f; font-weight: bold;">
                    <i class="fa fa-exclamation-triangle"></i>
                    Se eliminarán los stocks que dependan de este color.
                </p>
            </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: "No, cancelar",
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando color...',
                    html: `
                    <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                        <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                    </div>
                `,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                try {
                    const res = await axios.delete(route('almacenes.colores.destroy', id));
                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        dtColores.ajax.reload();
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR COLOR');
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La solicitud ha sido cancelada.',
                    'error'
                );
            }
        });
    }


    $(".btn-modal-file").on('click', function() {
        $("#modal_file").modal("show");
    });
</script>
@endpush
