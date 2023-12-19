@extends('layout') @section('content')
@section('caja-active', 'active')
@section('caja-chica-active', 'active')
@include('pos.Cajas.modalcreate')
@include('pos.Cajas.modaledit')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>LISTADO DE CAJAS</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cajas</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-modal btn-primary m-t-md" href="#">
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
                        <table class="table dataTables-cajas table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">CAJA</th>
                                    <th class="text-center">FECHA CREACION</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
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
<style>
    .my-swal {
        z-index: 3000 !important;
    }

</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script>
    $('.dataTables-cajas').DataTable({
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
        "serverSide": true,
        "ajax": '{{ route('Caja.getCajas') }}',
        "columns": [
            //Caja chica
            {
                data: 'id',
                className: "text-center",
            },
            {
                data: 'nombre',
                className: "text-center"
            },
            {
                data: 'created_at',
                className: "text-center"
            },
            {
                data: null,
                className: "text-center",
                "render": function(data, type, row, meta) {
                    return "<div class='btn-group'><a class='btn btn-warning btn-sm' id='edit" + data
                        .id + "' href='#' data-nombre='" + data.nombre + "' data-id='" + data.id +
                        "'  onclick='editar(" +
                        data.id +
                        ")' title='Modificar'><i class='fa fa-edit'></i></a><a class='btn btn-danger btn-sm' href='#' onclick='eliminar(" +
                        data.id + ")' title='Eliminar'><i class='fa fa-trash'></i></a></div>"
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
    $(".btn-modal").click(function(e) {
        e.preventDefault();
        $("#modal_create_caja").modal("show");
    });
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    function editar(id) {
        var a = $("#edit" + id)
        var url = "{{ route('Caja.update', ':id') }}"
        url = url.replace(':id', id)
        $("#frm_editar_caja").attr('action', url);
        $("#modal_edit_caja #nombre_editar").val(a.data('nombre'))
        $("#modal_edit_caja").modal('show');
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
                var url_eliminar = '{{ route('Caja.destroy', ':id') }}';
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
</script>
@endpush
