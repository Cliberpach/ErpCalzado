@extends('layout')
@section('content')
    {{-- @include('pos.caja_chica.edit') --}}
@section('egreso-active', 'active')
@section('caja-chica-active', 'active')
@include('Egreso.modals.mdl_create_egreso')
@include('Egreso.modals.mdl_edit_egreso')
@include('Egreso.modalImpreso')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>lista de Egresos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Egreso</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-modal btn-success m-t-md" href="javascript:void(0);"
            onclick="openMdlCreateEgreso()">
            <i class="fa fa-plus-square"></i> NUEVO
        </a>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('Egreso.tables.tbl_egresos_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/textSpinners/spinners.css') }}" rel="stylesheet">
<style>
    .my-swal {
        z-index: 3000 !important;
    }
</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script>
    let dtEgresos = null;

    document.addEventListener('DOMContentLoaded', () => {
        events();
        iniciarDtEgresos();
    })

    function events() {
        eventsMdlCreateEgreso();
        eventsMdlEditEgreso();
    }

    function iniciarDtEgresos() {

        dtEgresos = new DataTable('#tbl_egresos_list', {
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
            "serverSide": true,
            "ajax": '{{ route('Egreso.getEgresos') }}',
            "columns": [
                {
                    searchable: false,
                    data: 'id',
                    className: "text-center",
                },
                {
                    searchable: false,
                    data: 'cuenta_nombre',
                    name: 'td2.descripcion',
                    className: "text-center",
                },
                {
                    searchable: false,
                    data: 'descripcion',
                    className: "text-center"
                },
                {
                    searchable:false,
                    data: 'tipoDocumento',
                    name: 'td.descripcion',
                    className: "text-center"
                },
                {
                    data: 'documento',
                    name: 'e.documento',
                    className: "text-center"
                },
                {
                    searchable: false,
                    data: 'monto',
                    className: "text-center"
                },
                {
                    data: 'usuario',
                    name: 'e.usuario',
                    className: "text-center",
                },
                {
                    data: 'created_at',
                    name: 'e.created_at',
                    className: "text-center"
                },
                {
                    searchable: false,
                    data: null,
                    className: "text-center",
                    render: function(data) {

                        //Ruta Modificar
                        var url_edit = '{{ route('clientes.tienda.edit', ':id') }}';
                        url_edit = url_edit.replace(':id', data.id);

                        return `
                            <div class="btn-group">
                                <a class="btn btn-success btn-sm" style="color:white;" onclick="imprimir(${data.id})" title="Imprimir">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" style="color:white;" onclick="openMdlEditEgreso(${data.id})" title="Modificar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" onclick="eliminar(${data.id})" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        `;

                    }
                }
            ],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar por documento,usuario,fecha:",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad",
                    "excel": "Excel",
                    "pdf": "PDF",
                    "print": "Imprimir"
                }
            },
            "order": [
                [0, "desc"]
            ],


        });

    }

    function imprimir(id) {

        $("#frm_imprimir #egreso_id").val(id)
        $("#modal_imprimir").modal("show");
        //  var url = "{{ route('Egreso.recibo', ':id') }}"
        // window.location.href= url.replace(":id", id)
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
        }).then(async (result) => {
            if (result.isConfirmed) {

                try {

                    Swal.fire({
                        title: 'Eliminando egreso...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const res = await axios.post(route('Egreso.destroy', id));
                    if (res.data.success) {
                        dtEgresos.ajax.reload();
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETA');
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR EGRESO');
                } finally {
                    Swal.close();
                }


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


    @if (Session::has('egreso_error_mov'))
        toastr.error('{{ Session::get('egreso_error_mov') }}', 'ERROR');
    @endif

    @if (Session::has('egreso_success'))
        toastr.success('{{ Session::get('egreso_success') }}', 'OPERACIÓN COMPLETADA');
    @endif
</script>
@endpush
