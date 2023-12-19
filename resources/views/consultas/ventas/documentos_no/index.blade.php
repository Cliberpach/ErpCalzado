@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('consulta-ventas-active', 'active')
@section('consulta-ventas-documento-no-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12">
       <h2  style="text-transform:uppercase"><b>Listado de Documentos de Venta</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Documentos de Ventas</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="initTable()"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-orden table-striped table-bordered table-hover" style="text-transform:uppercase">
                            <thead>
                                <tr>

                                    <th colspan="2" class="text-center"></th>
                                    <th colspan="5" class="text-center">DOCUMENTO DE VENTA</th>
                                    <th colspan="3" class="text-center">FORMAS DE PAGO</th>
                                    <th colspan="4" class="text-center"></th>

                                </tr>
                                <tr>

                                    <th style="display:none;"></th>
                                    <th class="text-center">C.O</th>
                                    <th class="text-center"># DOC</th>
                                    <th class="text-center">FECHA DOC.</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">TRANSF.</th>
                                    <th class="text-center">OTROS</th>
                                    <th class="text-center">EFECT.</th>
                                    <th class="text-center">TIEMPO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">SUNAT</th>
                                    <th class="text-center">DESCARGAS</th>
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

<div class="modal inmodal" id="modal_descargas_pdf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title descarga-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 text-center">
                        <div class="form-group">
                            <button class="btn btn-info file-pdf"><i class="fa fa-file-pdf-o"></i></button><br>
                            <b>Descargar A4</b>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        <div class="form-group">
                            <button class="btn btn-info file-ticket"><i class="fa fa-file-o"></i></button><br>
                            <b>Descargar Ticket</b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
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
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {
    var ventas = [];
    // DataTables
    initTable();

    tablaDatos = $('.dataTables-orden').DataTable();

});

function initTable()
{
    let verificar = true;
    var fecha_desde = $('#fecha_desde').val();
    var fecha_hasta = $('#fecha_hasta').val();
    if (fecha_desde !== '' && fecha_desde !== null && fecha_hasta == '') {
        verificar = false;
        toastr.error('Ingresar fecha hasta');
    }

    if (fecha_hasta !== '' && fecha_hasta !== null && fecha_desde == '') {
        verificar = false;
        toastr.error('Ingresar fecha desde');
    }

    if (fecha_desde > fecha_hasta && fecha_hasta !== '' && fecha_desde !== '') {
        verificar = false;
        toastr.error('Fecha desde debe ser menor que fecha hasta');
    }

    if(verificar)
    {
        let timerInterval;
        Swal.fire({
            title: 'Cargando...',
            icon: 'info',
            customClass: {
                container: 'my-swal'
            },
            timer: 10,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                Swal.stopTimer();
                $.ajax({
                    dataType : 'json',
                    type : 'post',
                    url : '{{ route('consultas.ventas.documento.no.getTable') }}',
                    data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta},
                    success: function(response) {
                        if (response.success) {
                            ventas = [];
                            ventas = response.ventas;
                            loadTable();
                            timerInterval = 0;
                            Swal.resumeTimer();
                            //console.log(colaboradores);
                        } else {
                            Swal.resumeTimer();
                            ventas = [];
                            loadTable();
                        }
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    }
    return false;
}

function loadTable()
{
    $('.dataTables-orden').dataTable().fnDestroy();
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
        "data": ventas,
        "columns": [
            {
                data: 'cotizacion_venta',
                className: "text-center letrapequeña",
                visible: false
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    if (data.cotizacion_venta) {
                        return "<input type='checkbox' disabled checked>"
                    }else{
                        return "<input type='checkbox' disabled>"
                    }
                }

            },
            {
                data: 'numero_doc',
                className: "text-center letrapequeña",
            },
            {
                data: 'fecha_documento',
                className: "text-center letrapequeña"
            },
            {
                data: 'tipo_venta',
                className: "text-center letrapequeña",
            },
            {
                data: 'cliente',
                className: "text-left letrapequeña"
            },
            {
                data: 'total',
                className: "text-center letrapequeña"
            },
            {
                data: 'transferencia',
                className: "text-center letrapequeña"
            },
            {
                data: 'otros',
                className: "text-center letrapequeña"
            },
            {
                data: 'efectivo',
                className: "text-center letrapequeña"
            },
            {
                data: 'dias',
                className: "text-center letrapequeña"
            },

            {
                data: null,
                className: "text-center letrapequeña",
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
                className: "text-center letrapequeña",
                render: function(data) {
                    switch (data.sunat) {
                        case "1":
                            return "<span class='badge badge-primary' d-block>ACEPTADO</span>";
                            break;
                        case "2":
                            return "<span class='badge badge-danger' d-block>NULA</span>";
                            break;
                        default:
                            return "<span class='badge badge-success' d-block>REGISTRADO</span>";
                    }
                },
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    return "<button class='btn btn-info btn-pdf mb-1' title='Detalle'>PDF</button>" +
                        "<button class='btn btn-info' onclick='xmlElectronico(" +data.id+ ")' title='Detalle'>XML</button>"
                }
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    //Ruta Detalle
                    var url_edit = '{{ route("consultas.ventas.documento.no.edit", ":id")}}';
                    url_edit = url_edit.replace(':id', data.id);

                    var url_nota = '{{ route("ventas.notas", ":id")}}';
                    url_nota = url_nota.replace(':id', data.id);

                    let cadena = "";

                    if(data.sunat != '2' && data.dias > 0)
                    {
                        cadena = cadena + "<a href='"+url_edit+"'  class='btn btn-sm btn-secondary m-1 btn-rounded'  title='Editar'><i class='fa fa-pencil'></i> Editar</a>"
                    }

                    if(data.sunat == '0' && data.dias > 0 && data.tipo_venta_id != 129)
                    {
                        cadena = cadena + "<button type='button' class='btn btn-sm btn-success m-1 d-none' onclick='enviarSunat(" +data.id+ ")'  title='Enviar Sunat'><i class='fa fa-send'></i> Sunat</button>";
                    }

                    if(data.sunat == '1')
                    {
                        cadena = cadena  +
                        "<button type='button' class='btn btn-sm btn-info m-1 d-none' onclick='guia(" +data.id+ ")'  title='Guia Remisión'><i class='fa fa-file'></i> Guia</button>"
                        + "<a class='btn btn-sm btn-warning m-1 d-none' href='"+ url_nota +"'  title='Notas'><i class='fa fa-file-o'></i> Notas</a>" ;
                    }

                    if(data.sunat == '2')
                    {
                        cadena = cadena +
                        "<button type='button' class='btn btn-sm btn-danger m-1 d-none' onclick='eliminar(" + data.id + ")' title='Eliminar'><i class='fa fa-trash'></i> Eliminar</button>";
                    }

                    return cadena;

                }
            }

        ],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            /*if (aData.sunat == 0 && aData.tipo_venta_id != 129) {
                $('td', nRow).css('background-color', '#D6EAF8');
            }

            if (aData.sunat == 1 && aData.tipo_venta_id != 129) {
                $('td', nRow).css('background-color', '#D1F2EB');
            }*/

            if(aData.notas > 0)
            {
                $('td', nRow).css('background-color', '#FDEBD0');
            }
        },
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
    });
    return false;
}

$(".dataTables-orden").on('click','.btn-pdf',function(){
    var data = $(".dataTables-orden").dataTable().fnGetData($(this).closest('tr'));
    let fn_pdf = 'comprobanteElectronico(' + data.id + ')';
    let fn_ticket = 'comprobanteElectronicoTicket(' + data.id + ')';
    $('.descarga-title').html(data.serie + '-' + data.correlativo);
    $('.file-pdf').attr('onclick',fn_pdf);
    $('.file-ticket').attr('onclick',fn_ticket);
    $('#modal_descargas_pdf').modal('show');
});

function modificar(cotizacion,id) {
    if (cotizacion) {
        toastr.error('El documento de venta fue generado por una cotización (Opción "Editar" en cotizaciones).', 'Error');
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
                var url_editar = '{{ route("ventas.documento.edit", ":id")}}';
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

function comprobanteElectronico(id) {
    var url = '{{ route("ventas.documento.comprobante", ":id")}}';
    url = url.replace(':id',id+'-100');
    window.open(url, "Comprobante SISCOM", "width=900, height=600")
}

function comprobanteElectronicoTicket(id) {
    var url = '{{ route("ventas.documento.comprobante", ":id")}}';
    url = url.replace(':id',id+'-80');
    window.open(url, "Comprobante SISCOM", "width=900, height=600");
}

function xmlElectronico(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    });

    Swal.fire({
        title: "Opción XML",
        text: "¿Seguro que desea obtener el documento de venta en xml?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {

            var url = '{{ route("ventas.documento.xml", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

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

function  guia(id) {
    Swal.fire({
        title: 'Opción Guia de Remision',
        text: "¿Seguro que desea crear una guia de remision?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta Guia
            var url = '{{ route("ventas.guiasremision.create", ":id")}}';
            url = url.replace(':id', id);
            $(location).attr('href', url);

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

function enviarSunat(id , sunat) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Enviar a Sunat",
        text: "¿Seguro que desea enviar documento de venta a Sunat?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {

            var url = '{{ route("ventas.documento.sunat", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Enviando documento de venta a Sunat',
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            })

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

@if(!empty($sunat_exito))
    Swal.fire({
        icon: 'success',
        title: '{{$id_sunat}}',
        text: '{{$descripcion_sunat}}',
        showConfirmButton: false,
        timer: 2500
    })
@endif

@if(!empty($sunat_error))
    Swal.fire({
        icon: 'error',
        title: '{{$id_sunat}}',
        text: '{{$descripcion_sunat}}',
        showConfirmButton: false,
        timer: 5500
    })
@endif

</script>
@endpush
