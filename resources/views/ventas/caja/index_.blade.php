@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('ventas-caja-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-12">
       <h2  style="text-transform:uppercase"><b>Listado de Documentos de Venta de Hoy</b></h2>
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

<div>
    <a id="nueva_ventana" class="d-none" target="_blank"></a>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-documento table-striped table-bordered table-hover" style="text-transform:uppercase">
                            <thead>
                                <tr>

                                    <th colspan="2" class="text-center"></th>
                                    <th colspan="5" class="text-center">DOCUMENTO DE VENTA</th>
                                    <th colspan="4" class="text-center">FORMAS DE PAGO</th>
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

@include('ventas.caja.modalVentas')
@include('ventas.caja.modalPago')
@include('ventas.caja.modalPagoShow')

@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
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
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {

    $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
    });

    // DataTables
    $('.dataTables-documento').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'DOC_VENTAS'
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
        "ajax": "{{ route('ventas.caja.getDocument')}}",
        "columns": [
            //DOCUMENTO DE VENTA
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
                visible: false
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
                            return "<span class='badge badge-danger' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "PAGADA":
                            return "<span class='badge badge-primary verPago' style='cursor: pointer;' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "ADELANTO":
                            return "<span class='badge badge-success' d-block>" + data.estado +
                                "</span>";
                            break;
                        case "DEVUELTO":
                            return "<span class='badge badge-warning' d-block>" + data.estado +
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
                    return "<button class='btn btn-info btn-pdf mb-1' title='PDF'>PDF</button>" +
                        "<button class='btn btn-info' onclick='xmlElectronico(" +data.id+ ")' title='XML'>XML</button>"
                }
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {

                    let cadena = "";
                    if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE')
                    {
                        cadena = cadena +
                        "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                    }
                    else
                    {
                        cadena = cadena +
                        "<button type='button' class='btn btn-sm btn-success m-1 verPago' title='Ver'><i class='fa fa-eye'></i> Ver Pago</button>";
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
    tablaDatos = $('.dataTables-enviados').DataTable();

});

$(".dataTables-documento").on('click','.btn-pdf',function(){
    var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
    let fn_pdf = 'comprobanteElectronico(' + data.id + ')';
    let fn_ticket = 'comprobanteElectronicoTicket(' + data.id + ')';
    $('.descarga-title').html(data.serie + '-' + data.correlativo);
    $('.file-pdf').attr('onclick',fn_pdf);
    $('.file-ticket').attr('onclick',fn_ticket);
    $('#modal_descargas_pdf').modal('show');
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
            var url_eliminar = '{{ route("ventas.documento.destroy", ":id")}}';
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

            // Swal.fire({
            //     title: '¡Cargando!',
            //     type: 'info',
            //     text: 'Generando XML',
            //     showConfirmButton: false,
            //     onBeforeOpen: () => {
            //         Swal.showLoading()
            //     }
            // })

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

function cdr(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Regularizar CDR",
        text: "¿Seguro que desea regularizar CDR?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {

            var url = '{{ route("ventas.documento.cdr", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Regularizando CDR',
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

$(".dataTables-documento").on('click','.pagar',function(){
    var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
    $('.ventas-title').html(data.cliente);
    $('#modal_ventas').modal('show');

    initTable(data.cliente_id, data.condicion_id);
});

$(".dataTables-documento").on('click','.verPago',function(){
    var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
    $('#modal_pago_show .pago-title').html(data.numero_doc);
    $('#modal_pago_show #monto_venta').val(data.total);
    $('#modal_pago_show #venta_id').val(data.id);
    $('#modal_pago_show #div_cuentas').addClass('d-none');
    if(data.ruta_pago)
    {
        let ruta = data.ruta_pago;
        ruta = ruta.replace('public','');
        ruta = 'storage'+ruta;
        let ruta_final = "{{asset(':ruta')}}";
        ruta_final = ruta_final.replace(':ruta', ruta);
        $imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen");
        $imagenPrevisualizacion.src = ruta_final;
    }
    else
    {
        $imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen");
        $imagenPrevisualizacion.src = "{{asset('img/default.png')}}";
    }

    if(data.cuenta_id)
    {
        $('#modal_pago_show #div_cuentas').removeClass('d-none');
        initCuentasShow(data.empresa_id,data.cuenta_id);
    }
    $('#modal_pago_show #modo_pago').val(data.tipo_pago).trigger('change.select2');
    if(data.tipo_pago != 1)
    {
        $('#modal_pago_show #efectivo').val(data.efectivo);
        $('#modal_pago_show #importe').val(data.importe);
    }
    else
    {
        $('#modal_pago_show #efectivo').val('0.00');
        $('#modal_pago_show #importe').val(data.importe);
    }

    $('#modal_pago_show .pago-subtitle').html(data.cliente);
    $('#modal_pago_show').modal('show');
});

function initCuentasShow(empresa_id,cuenta_id)
{
    $("#cuenta_id_show").empty().trigger('change');
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
                url : '{{ route('ventas.documento.getCuentas') }}',
                data : {'_token': $('input[name=_token]').val(), 'empresa_id': empresa_id},
                success: function(response) {
                    if (response.success) {
                        if (response.cuentas.length > 0) {
                            $('#cuenta_id_show').append('<option></option>').trigger('change');
                            for(var i = 0;i < response.cuentas.length; i++)
                            {
                                var newOption = '<option value="'+response.cuentas[i].id+'">'+response.cuentas[i].descripcion + ': ' + response.cuentas[i].num_cuenta +'</option>';
                                $('#cuenta_id_show').append(newOption).trigger('change');
                            }

                            $('#cuenta_id_show').val(cuenta_id).trigger('change.select2');

                        } else {
                            //toastr.error('CuentaS no encontradas.', 'Error');
                        }
                        timerInterval = 0;
                        Swal.resumeTimer();
                    } else {
                        timerInterval = 0;
                        Swal.resumeTimer();
                    }
                }
            });
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
    });
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

@if(Session::has('documento_id'))
    let doc = '{{ Session::get("documento_id")}}';
    let id = doc+'-100';

    var url = '{{ route("ventas.documento.comprobante", ":id")}}';
    url = url.replace(':id', id);
    // $('#nueva_ventana').attr('href',url);
    // document.getElementById('nueva_ventana').click;
    window.open(url, "Comprobante SISCOM", "width=900, height=600")
@endif
</script>
@endpush
