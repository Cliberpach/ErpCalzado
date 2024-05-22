@extends('layout') @section('content')
    {{-- @include('pos.caja_chica.edit') --}}
@section('egreso-active', 'active')
@section('caja-chica-active', 'active')
@include('Egreso.create')
@include('Egreso.edit')
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
                                    <th class="text-center">ID</th>
                                    <th class="text-center">DESCRIPCION</th>
                                    <th class="text-center">TIPO DOCUMENTO</th>
                                    <th class="text-center">DOCUMENTO</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">FECHA</th>
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
<link href="{{ asset('Inspinia/css/plugins/textSpinners/spinners.css') }}" rel="stylesheet">
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
        "ajax": '{{ route('Egreso.getEgresos') }}',
        "columns": [
            //Caja chica
            {
                data: 'id',
                className: "text-center",
            },
            {
                data: 'descripcion',
                className: "text-center"
            },
            {
                data: 'tipoDocumento',
                className: "text-center"
            },
            {
                data: 'documento',
                className: "text-center"
            },
            {
                data: 'monto',
                className: "text-center"
            },
            {
                data: 'created_at',
                className: "text-center"
            },
            {
                data: null,
                className: "text-center",
                render: function(data) {
                    //Ruta Detalle
                    // var url_detalle = '{{ route('clientes.tienda.show', ':id') }}';
                    // url_detalle = url_detalle.replace(':id',data.id);

                    //Ruta Modificar
                    var url_edit = '{{ route('clientes.tienda.edit', ':id') }}';
                    url_edit = url_edit.replace(':id', data.id);


                    return "<div class='btn-group'>" +
                        "<a class='btn btn-primary btn-sm' style='color:white;' onclick='imprimir(" +
                        data.id + ")' title='Modificar'><i class='fa fa-file-pdf-o'></i></a>" +
                        "<a class='btn btn-warning btn-sm' style='color:white;' onclick='editar(" + data
                        .id + ")' title='Modificar'><i class='fa fa-edit'></i></a>" +
                        "<a class='btn btn-danger btn-sm' href='#' onclick='eliminar(" + data.id +
                        ")' title='Eliminar'><i class='fa fa-trash'></i></a></div>";
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

    function imprimir(id) {

        $("#frm_imprimir #egreso_id").val(id)
        $("#modal_imprimir").modal("show");
        //  var url = "{{ route('Egreso.recibo', ':id') }}"
        // window.location.href= url.replace(":id", id)
    }

    function editar(id) {
        axios.get("{{ route('Egreso.getEgreso') }}", {
            params: {
                id: id
            }
        }).then((value) => {
            console.log(value)
            var url = "{{ route('Egreso.update', ':id') }}"
            url = url.replace(':id', id)
            $("#frm_editar_egreso").attr('action', url);
            $("#modal_editar_egreso #descripcion_editar").html(value.data.descripcion)
            $("#modal_editar_egreso #importe_editar").val(value.data.importe)
            $("#modal_editar_egreso #monto_editar").val(value.data.monto)
            $("#modal_editar_egreso #efectivo_editar").val(value.data.efectivo)
            $("#modal_editar_egreso #documento_editar").val(value.data.documento)
            $("#modal_editar_egreso #cuenta_editar").val(value.data.cuenta_id).trigger('change');
            $("#modal_editar_egreso #modo_pago_editar").val(value.data.tipo_pago_id).trigger('change');
            //$("#modal_editar_egreso #tipo_documento_editar").val(value.data.tipodocumento_id).trigger('change');
            $("#modal_editar_egreso").modal("show");
        }).catch((value) => {

        })

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
                var url_eliminar = '{{ route('Egreso.destroy', ':id') }}';
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
    $(".btn-modal").click(function(e) {
        e.preventDefault();
        $("#modal_crear_egreso").modal("show");
    });


    @if(Session::has('egreso_error_mov'))
            toastr.error('{{ Session::get('egreso_error_mov') }}', 'ERROR');
    @endif

    @if(Session::has('egreso_success'))
            toastr.success('{{ Session::get('egreso_success') }}', 'OPERACIÓN COMPLETADA');
    @endif

</script>
@endpush
