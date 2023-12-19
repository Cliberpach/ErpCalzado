@extends('layout') @section('content')

@section('compras-active', 'active')
@section('documento-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Documentos de Compra</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Documentos de Compra</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('compras.documento.create')}}">
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

                                    <th colspan="7" class="text-center">DOCUMENTO DE COMPRA</th>
                                    <th colspan="2" class="text-center">FORMAS DE PAGO</th>
                                    <th colspan="3" class="text-center"></th>

                                </tr>
                                <tr>
                                    <th style="text-center">ID</th>
                                    <th style="display:none;"></th>
                                    <th class="text-center">O.C</th>
                                    <th class="text-center">#Doc</th>
                                    <th class="text-center">EMISION</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">MODO</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">MONEDA</th>
                                    <th class="text-center">TIPO_CAMBIO</th>
                                    <th class="text-center">A CUENTA</th>
                                    <th class="text-center">SALDO</th>
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
                title: 'DOC_COMPRAS'
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
        "ajax": "{{ route('getDocument')}}",
        "columns": [
            //DOCUMENTO DE COMPRA
            {
                data: 'id',
                className: "text-center",
            },
            {
                data: 'tipo_pago',
                className: "text-center",
                visible: false
            },
            {
                data: null,
                className: "text-center",
                render: function(data) {
                    if (data.orden_compra) {
                        return "<input type='checkbox' disabled checked>"
                    }else{
                        return "<input type='checkbox' disabled>"
                    }
                }

            },
            {
                data: 'numero_doc',
                className: "text-center"
            },
            {
                data: 'fecha_emision',
                className: "text-center"
            },
            {
                data: 'tipo',
                className: "text-center",
            },
            {
                data: 'proveedor',
                className: "text-left"
            },
            {
                data: 'modo',
                className: "text-left"
            },
            {
                data: 'total_pagar',
                className: "text-center"
            },
            {
                data: 'moneda',
                className: "text-center",
                visible: false,
            },
            {
                data: 'tipo_cambio',
                className: "text-center",
                visible: false,
            },
            {
                data: 'acuenta',
                className: "text-center"
            },

            {
                data: 'saldo',
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
                            return "<span class='badge badge-primary' d-block>" + data.estado +
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
                    var url_detalle = '{{ route("compras.documento.show", ":id")}}';
                    url_detalle = url_detalle.replace(':id', data.id);

                    var url_nota = '{{ route("compras.notas", ":id") }}';
                    url_nota = url_nota.replace(':id', data.id);

                    var cadena = "<a class='btn btn-sm btn-primary m-1' href='#' style='color:white' onclick='modificar("+data.orden_compra+","+data.id +")' title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a>" + 
                        "<a class='btn btn-sm btn-success m-1' style='color:white' href='" + url_detalle + "' title='Detalle'><b><i class='fa fa-eye'></i> Detalle</a>" + 
                        "<a class='btn btn-sm btn-danger m-1 d-none'  style='color:white' href='#' onclick='eliminar(" + data.id + ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a>" +
                        "<a class='btn btn-sm btn-warning m-1'  style='color:white' href='"+ url_nota +"'  title='Notas'><i class='fa fa-file-o'></i> Notas</a>" ;
                            
                    return cadena
                }
            }

        ],
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

            if(aData.notas > 0)
            {
                $('td', nRow).css('background-color', '#FDEBD0');
            }
        },
        "order": [
            [0, "desc"]
        ],
    });

    tablaDatos = $('.dataTables-enviados').DataTable();

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
            var url_eliminar = '{{ route("compras.documento.destroy", ":id")}}';
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

function modificar(orden,id) {
    if (orden) {
        toastr.error('El documento de compra fue generado por una orden (Opción "Editar" en orden de compra).', 'Error');
    }else{
        Swal.fire({
            title: 'Opción Modificar',
            text: "¿Seguro que desea modificar registro?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {

                //Ruta Modificar
                var url_editar = '{{ route("compras.documento.edit", ":id")}}';
                url_editar = url_editar.replace(':id', id);
                $(location).attr('href', url_editar);

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
}

</script>
@endpush
