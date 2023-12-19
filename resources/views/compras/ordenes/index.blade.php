@extends('layout') @section('content')

@section('compras-active', 'active')
@section('orden-compra-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Ordenes de Compra</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Ordenes de Compra</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('compras.orden.create')}}">
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
                        <table class="table dataTables-orden table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>

                                    <th colspan="3" class="text-center">FECHAS</th>
                                    <th colspan="3" class="text-center">ORDEN DE COMPRA</th>
                                    <th colspan="1" class="text-center"></th>

                                </tr>
                                <tr>
                                    <th style="text-center">ID</th>
                                    <th class="text-center">EMISION</th>
                                    <th class="text-center">ENTREGA</th>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">MONTO</th>
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

<div class="modal inmodal" id="modal_listar_enviados" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Ordenes</h4>
                <small class="font-bold">Ultima Orden enviada.</small>
            </div>
            <div class="modal-body">
                <form role="form" id="">
                <div  id="enviado_usuario" style="display:none;">
                <p>Ultimo registro enviado por el usuario y al correo :</p>
                    <div class="form-group">
                        <label class="">Al Correo:</label>
                        <br>
                        <b><label id="correo_enviado"></label></b>

                    </div>

                    <div class="form-group">
                        <label class="">Usuario</label>
                        <br>
                        <b><label id="usuario_enviado"></label></b>
                    </div>
                </div>

                <div style="display:none;" id="no_enviado_usuario">
                    <p>Aun no se ha enviado ninguna orden de compra al proveedor.</p>
                </div>
            </div>


            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i>
                        Cancelar</button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>
@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {

    // DataTables
    $('.dataTables-orden').DataTable({
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
        "ajax": "{{ route('getOrder')}}",
        "columns": [
            //ORDEN DE COMPRA
            {
                data: 'id',
                className: "text-center",
            },
            {
                data: 'fecha_emision',
                className: "text-center"
            },
            {
                data: 'fecha_entrega',
                className: "text-center"
            },
            {
                data: 'proveedor',
                className: "text-left"
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
                            return "<span class='badge badge-warning' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "PAGADA":
                            return "<span class='badge badge-danger' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "ADELANTO":
                            return "<span class='badge badge-success' d-block>" + data.estado +
                                "</span>";
                            break;
                        default:
                            return "<span class='badge badge-success' d-block>" + data.estado +
                                "</span>";
                    }
                },
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {
                    //Ruta Detalle
                    var url_detalle = '{{ route("compras.orden.show", ":id")}}';
                    url_detalle = url_detalle.replace(':id', data.id);

                    //Ruta Modificar
                    var url_editar = '{{ route("compras.orden.edit", ":id")}}';
                    url_editar = url_editar.replace(':id', data.id);

                    return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +

                        "<li><a class='dropdown-item' href='" + url_editar +
                        "' title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li>" +
                        "<li><a class='dropdown-item' href='" + url_detalle +
                        "' title='Detalle'><b><i class='fa fa-eye'></i> Detalle</a></b></li>" +
                        "<li><a class='dropdown-item' onclick='eliminar(" + data.id +
                        ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>" +
                        "<li class='dropdown-divider'></li>" +
                        "<li><a class='dropdown-item' onclick='enviado(" + data.id +
                        ")' title='Ordenes Enviadas'><b><i class='fa fa-send'></i> Enviados</a></b></li>" +
                        "<li><a class='dropdown-item' onclick='documento("+data.id+")' title='Documento'><b><i class='fa fa-file'></i> Documento</a></b></li>" +
                        "<li><a class='dropdown-item' onclick='concretada(" + data.id +
                        ")' title='Concretada'><b><i class='fa fa-check'></i> Concretada</a></b></li>"
                    "</ul></div>"
                }
            }

        ],
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [
            [0, "desc"]
        ],
    });

    tablaDatos = $('.dataTables-enviados').DataTable();

});

//Controlar Error
$.fn.DataTable.ext.errMode = 'throw';




function eliminar(id) {
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})

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
            var url_eliminar = '{{ route("compras.orden.destroy", ":id")}}';
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

function concretada(id) {
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})

    Swal.fire({
        title: 'Opción Concretada',
        text: "¿Seguro que desea concretar orden de compra?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta Concretar
            var url_concretar = '{{ route("compras.orden.concretada", ":id")}}';
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

function enviado(id) {

    $("#modal_listar_enviados").on("shown.bs.modal", function () {
        $.get('/compras/ordenes/consultaEnvios/' + id, function(data) {
        if (data.length > 0) {
            enviado_usuario.style.display = "";
            no_enviado_usuario.style.display = "none";
            $('#correo_enviado').text(data[0].correo);
            $('#usuario_enviado').text(data[0].usuario);
        } else {
            enviado_usuario.style.display = "none";
            no_enviado_usuario.style.display = "";
        }
    });
    }).modal('show');

}

function documento(id) {
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})

    Swal.fire({
        title: 'Opción Documento de compra',
        text: "¿Seguro que desea crear un documento de compra?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta Documento
            var url_concretar = '{{ route("compras.orden.documento", ":id")}}';
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


@if(!empty($id))

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: 'Documento de Compra duplicado',
        text: "¿Desea anular el documento y crear uno nuevo?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta Nuevo Documento
            var url_nuevo = '{{ route("compras.orden.nuevodocumento", ":id")}}';
            url_nuevo = url_nuevo.replace(':id', "{{$id}}");
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

@if (!empty($id_eliminar))

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: 'Orden ingresado a documentos',
        text: "¿Desea eliminar la orden, recuerde que tambien eliminará el documento de compra?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta Nuevo Documento
            var url_nuevo = '{{ route("compras.orden.confirmDestroy", ":id")}}';
            url_nuevo = url_nuevo.replace(':id', "{{$id_eliminar}}");
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
