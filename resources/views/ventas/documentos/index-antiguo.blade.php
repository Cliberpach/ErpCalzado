@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading align-items-end">
    <div class="col-12 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Documentos de Venta</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Documentos de Ventas</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{ route('ventas.documento.create') }}">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
    </div>
</div>

<div>
    <a id="nueva_ventana" class="d-none" target="_blank"></a>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5><a style="color: #FDEBD0;" href="#"><i class="fa fa-square fa-2x"></i></a> Documento con
                        NOTAS DE CREDITO <a style="color: #EBDEF0;" href="#"><i
                                class="fa fa-square fa-2x"></i></a> Documento de CONTINGENCIA</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">Desde:</label>
                            <input type="date" id="fechaInicial" value="{{ FechaActual() }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label for="" class="text-white">-</label>
                            <input type="text" placeholder="buscar por n° de documento o nombres" id="cliente"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-1">
                            <label for="" class="text-white">-</label>
                            <button type="button" class="btn btn-primary btn-block" id="reload">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table dataTables-documento table-striped table-bordered table-hover"
                                    style="text-transform:uppercase">
                                    <thead>
                                        <tr>
                                            <th style="display:none;"></th>
                                            <th class="text-center">C.O</th>
                                            <th class="text-center"># DOC</th>
                                            <th class="text-center">FECHA DOC.</th>
                                            <th class="text-center">TIPO</th>
                                            <th class="text-center">CLIENTE</th>
                                            <th class="text-center">MONTO</th>
                                            <th class="text-center">TIEMPO</th>
                                            <th class="text-center">MODO</th>
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
                    <div class="col-12 col-md-6 text-center mb-4">
                        <div class="form-group">
                            <button class="btn btn-info file-ticket"><i class="fa fa-file-o"></i></button><br>
                            <b>Descargar Ticket</b>
                        </div>
                    </div>
                    <br>
                    <div class="col-12 mt-4">
                        <form id="frm_envio">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="hidden" id="id" name="id" placeholder="Id"
                                        class="form-control" required>
                                    <input type="email" id="correo" name="correo" placeholder="Correo electrónico"
                                        class="form-control" required>
                                    <span class="input-group-append"><button type="submit" class="btn btn-default"><i
                                                class="fa fa-envelope"></i> <span>Enviar</span></button></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('ventas.documentos.modalVentas')
@include('ventas.documentos.modalPago')
@include('ventas.documentos.modalPagoShow')
@stop
@push('styles')
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<style>
    .letrapequeña {
        font-size: 11px;
    }
</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script>
    $(document).ready(function() {

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
        // lTfgitp
        // lTrtip
        // DataTables
        var tableDocument = $('.dataTables-documento').DataTable({
            "dom": '<"html5buttons"B>lrtip',
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
            "bFilter": false,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('ventas.getDocumentAntiguo') }}",
                data: function(d) {
                    d.fechaInicial = $("#fechaInicial").val();
                    d.cliente = $("#cliente").val();
                }
            },
            "columns": [
                //DOCUMENTO DE VENTA
                {
                    data: 'id',
                    className: "text-center letrapequeña",
                    name: 'cotizacion_documento.id',
                    visible: false
                },

                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {
                        if (data.cotizacion_venta) {
                            return "<input type='checkbox' disabled checked>"
                        } else {
                            return "<input type='checkbox' disabled>"
                        }
                    }

                },

                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {
                        return data.numero_doc
                    }
                },

                {
                    data: 'fecha_documento',
                    className: "text-center letrapequeña",
                    name: 'cotizacion_documento.fecha_documento'
                },

                {
                    data: 'tipo_venta',
                    className: "text-center letrapequeña",
                    name: 'tabladetalles.nombre',
                    visible: false
                },

                {
                    data: 'cliente',
                    className: "text-left letrapequeña",
                    name: 'cotizacion_documento.cliente'
                },
                {
                    data: 'total',
                    className: "text-center letrapequeña",
                    name: 'cotizacion_documento.total'
                },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {
                        return data.dias > 4 ? 0 : 4 - data.dias;
                    }
                },
                {
                    data: 'condicion',
                    className: "text-center letrapequeña",
                    name: 'condicions.descripcion'
                },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {
                        switch (data.estado_pago) {
                            case "PENDIENTE":
                                return "<span class='badge badge-danger' d-block>" + data
                                    .estado_pago +
                                    "</span>";
                                break;
                            case "PAGADA":
                                return "<span class='badge badge-primary verPago' style='cursor: pointer;' d-block>" +
                                    data.estado_pago +
                                    "</span>";
                                break;
                            case "ADELANTO":
                                return "<span class='badge badge-success' d-block>" + data
                                    .estado_pago +
                                    "</span>";
                                break;
                            case "DEVUELTO":
                                return "<span class='badge badge-warning' d-block>" + data
                                    .estado_pago +
                                    "</span>";
                                break;
                            default:
                                return "<span class='badge badge-success' d-block>" + data
                                    .estado_pago +
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
                            "<button class='btn btn-info' onclick='xmlElectronico(" + data.id +
                            ")' title='XML'>XML</button>"
                    }
                },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {

                        var url_nota = '{{ route('ventas.notas', ':id') }}';
                        url_nota = url_nota.replace(':id', data.id);

                        var url_devolucion = '{{ route('ventas.notas_dev', ':id') }}';
                        url_devolucion = url_devolucion.replace(':id', data.id);

                        var url_edit = '{{ route('ventas.documento.edit', ':id') }}';
                        url_edit = url_edit.replace(':id', data.id);

                        var dias = data.dias > 4 ? 0 : 4 - data.dias;

                        let cadena = "";

                        if (data.sunat == '0' && data.tipo_venta_id != 129 && dias > 0 && data
                            .contingencia == '0') //&& data.dias > 0
                        {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-success m-1' onclick='enviarSunat(" +
                                data.id + "," + data.contingencia +
                                ")'  title='Enviar Sunat'><i class='fa fa-send'></i> Sunat</button>";
                        }

                        if (data.sunat_contingencia == '0' && data.tipo_venta_id != 129 && data
                            .contingencia == '1') //&& data.dias > 0
                        {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-success m-1' onclick='enviarSunat(" +
                                data.id + "," + data.contingencia +
                                ")'  title='Enviar Sunat'><i class='fa fa-send'></i> Sunat</button>";
                        }

                        if ((data.sunat == '1' || data.notas > 0 || data.sunat_contingencia ==
                                '1') && data.tipo_venta_id != 129) {
                            cadena = cadena +
                                "<a class='btn btn-sm btn-warning m-1' href='" + url_nota +
                                "'  title='Notas'><i class='fa fa-file-o'></i> Notas</a>";
                        }

                        if (data.sunat == '1' || data.sunat_contingencia == '1') {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-info m-1' onclick='guia(" +
                                data.id +
                                ")'  title='Guia Remisión'><i class='fa fa-file'></i> Guia</button>";
                        }

                        if ((data.tipo_venta_id == 129 && data.condicion == 'CONTADO' && data
                                .estado_pago == 'PAGADA') || (data.tipo_venta_id == 129 && (data
                                .condicion == 'CREDITO' || data.condicion == 'CRÉDITO'))) {
                            cadena = cadena +
                                "<a class='btn btn-sm btn-warning m-1' href='" +
                                url_devolucion +
                                "'  title='Devoluciones'><i class='fa fa-file-o'></i> Devoluciones</a>";
                        }

                        if (data.tipo_venta_id == 129 && data.estado_pago == 'PENDIENTE') {
                            cadena = cadena +
                                "<a class='btn btn-sm btn-warning m-1' href='" + url_edit +
                                "'  title='Editar'><i class='fa fa-pencil'></i> Editar</a>";
                        }

                        if ((data.sunat == '2' && data.tipo_venta_id == 129) || (data
                                .tipo_venta_id == 129 && data.estado_pago == 'PENDIENTE') &&
                            data.contingencia == '0') {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-danger m-1 d-none' onclick='eliminar(" +
                                data.id +
                                ")' title='Eliminar'><i class='fa fa-trash'></i> Eliminar</button>";
                        }

                        if (data.condicion == 'CONTADO' && data.estado_pago == 'PENDIENTE' &&
                            data.tipo_venta_id == '129') {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                        }

                        if (data.condicion == 'CONTADO' && data.estado_pago == 'PENDIENTE' &&
                            data.tipo_venta_id != 129 && (data.convertir == '' || data
                                .convertir == null)) {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                        }

                        if (data.code == '1033' && data.regularize == '1' && data.sunat !=
                            '2' && data.contingencia == '0') {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary-cdr m-1' onclick='cdr(" +
                                data.id + ")' title='CDR'>CDR</button>";
                        }

                        if (dias <= 0 && data.contingencia == '0' && data.tipo_venta_id !=
                            '129' && data.sunat == '0') {
                            cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-warning m-1' onclick='contingencia(" +
                                data.id +
                                ")'  title='Convertir a comprobante de contingencia'><i class='fa fa-exchange'></i> Contingencia</button>";
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

                if (aData.notas > 0) {
                    $('td', nRow).css('background-color', '#FDEBD0');
                }

                if (aData.contingencia == '1') {
                    $('td', nRow).css('background-color', '#EBDEF0');
                }
            },
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [],
        });
        tablaDatos = $('.dataTables-enviados').DataTable();
        $("#fechaInicial").on("change", function() {
            tableDocument.draw();
        });
        $("#reload").on("click",function(e){
            e.preventDefault();
            tableDocument.draw();
        });
    });

    $(".dataTables-documento").on('click', '.btn-pdf', function() {
        var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
        let fn_pdf = 'comprobanteElectronico(' + data.id + ')';
        let fn_ticket = 'comprobanteElectronicoTicket(' + data.id + ')';
        $('.descarga-title').html(data.serie + '-' + data.correlativo);
        $('.file-pdf').attr('onclick', fn_pdf);
        $('.file-ticket').attr('onclick', fn_ticket);
        $('#correo').val('');
        $('#id').val(data.id);
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
                var url_eliminar = '{{ route('ventas.documento.destroy', ':id') }}';
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

    function contingencia(id) {

        Swal.fire({
            title: 'Contingencia',
            text: "¿Seguro que desea convertir este documento a comprobante de contingencia?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                //Ruta Eliminar
                var url_contingencia = '{{ route('ventas.documento.contingencia', ':id') }}';
                url_contingencia = url_contingencia.replace(':id', id);
                $(location).attr('href', url_contingencia);

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

    function modificar(cotizacion, id) {
        if (cotizacion) {
            toastr.error('El documento de venta fue generado por una cotización (Opción "Editar" en cotizaciones).',
                'Error');
        } else {
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
                    var url_editar = '{{ route('ventas.documento.edit', ':id') }}';
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
        var url = '{{ route('ventas.documento.comprobante', ':id') }}';
        url = url.replace(':id', id + '-100');
        window.open(url, "Comprobante SISCOM", "width=900, height=600")
    }

    function comprobanteElectronicoTicket(id) {
        var url = '{{ route('ventas.documento.comprobante', ':id') }}';
        url = url.replace(':id', id + '-80');
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

                var url = '{{ route('ventas.documento.xml', ':id') }}';
                url = url.replace(':id', id);

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

                var url = '{{ route('ventas.documento.cdr', ':id') }}';
                url = url.replace(':id', id);

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

    function guia(id) {
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
                var url = '{{ route('ventas.guiasremision.create', ':id') }}';
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

    function enviarSunat(id, contingencia) {
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
            allowOutsideClick: false,
            // showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.value) {

                var url = '';

                if (contingencia == '1') {
                    url = '{{ route('ventas.documento.sunat.contingencia', ':id') }}';
                    url = url.replace(':id', id);
                } else {
                    url = '{{ route('ventas.documento.sunat', ':id') }}';
                    url = url.replace(':id', id);
                }

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

    $('#frm_envio').on('submit', function(e) {
        e.preventDefault();
        let timerInterval = 9999;
        Swal.fire({
            title: 'Enviando email...',
            icon: 'info',
            timer: 10,
            customClass: {
                container: 'my-swal'
            },
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                Swal.stopTimer();

                let frm_envio = document.getElementById('frm_envio');
                let formData = new FormData(frm_envio);

                axios.post("{{ route('ventas.documento.envio') }}", formData).then((value) => {
                    let response = value.data;
                    if (response.success) {
                        toastr.success(response.message);
                        timerInterval = 0;
                        $('#correo').val('');
                        Swal.resumeTimer();
                    } else {
                        toastr.error(response.message);
                        timerInterval = 0;
                        Swal.resumeTimer();
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    })

    $(".dataTables-documento").on('click', '.pagar', function() {
        var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
        $('.ventas-title').html(data.cliente);
        $('#modal_ventas').modal('show');

        initTable(data.cliente_id, data.condicion_id);
    });

    $(".dataTables-documento").on('click', '.verPago', function() {
        var data = $(".dataTables-documento").dataTable().fnGetData($(this).closest('tr'));
        if (data.condicion_id != 1) {
            toastr.warning("Este documento ha sido pagado en cuotas")
        } else {
            $('#modal_pago_show .pago-title').html(data.numero_doc);
            $('#modal_pago_show #monto_venta').val(data.total);
            $('#modal_pago_show #venta_id').val(data.id);
            $('#modal_pago_show #div_cuentas').addClass('d-none');

            $('#modal_pago_show #ruta_pago').val(data.ruta_pago);
            if (data.ruta_pago) {
                let ruta = data.ruta_pago;
                ruta = ruta.replace('public', '');
                ruta = 'storage' + ruta;
                let ruta_final = "{{ asset(':ruta') }}";
                ruta_final = ruta_final.replace(':ruta', ruta);
                $inputPrevisualizacion = document.querySelector("#modal_pago_show #imagen_update");
                $inputPrevisualizacion.src = ruta;
                $imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen_update");
                $imagenPrevisualizacion.src = ruta_final;
                $('#modal_pago_show .custom-file-label').addClass("selected").html(data.serie + '-' + data
                    .correlativo + '.jpg');
            } else {
                $('#modal_pago_show #imagen_update').val('');
                $imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen_update");
                $imagenPrevisualizacion.src = "{{ asset('img/default.png') }}";
            }

            if (data.cuenta_id) {
                $('#modal_pago_show #div_cuentas').removeClass('d-none');
                initCuentasShow(data.empresa_id, data.cuenta_id);
            }
            $('#modal_pago_show #modo_pago').val(data.tipo_pago_id).trigger('change.select2');
            if (data.tipo_pago_id != 1) {
                $('#modal_pago_show #efectivo').val(data.efectivo);
                $('#modal_pago_show #importe').val(data.importe);
            } else {
                $('#modal_pago_show #efectivo').val('0.00');
                $('#modal_pago_show #importe').val(data.importe);
            }

            $('#modal_pago_show .pago-subtitle').html(data.cliente);
            $('#modal_pago_show').modal('show');
        }
    });

    function initCuentasShow(empresa_id, cuenta_id) {
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
                    dataType: 'json',
                    type: 'post',
                    url: '{{ route('ventas.documento.getCuentas') }}',
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'empresa_id': empresa_id
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.cuentas.length > 0) {
                                $('#cuenta_id_show').append('<option></option>').trigger(
                                    'change');
                                for (var i = 0; i < response.cuentas.length; i++) {
                                    var newOption = '<option value="' + response.cuentas[i].id +
                                        '">' + response.cuentas[i].descripcion + ': ' + response
                                        .cuentas[i].num_cuenta + '</option>';
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

    @if (Session::has('sunat_exito'))
        Swal.fire({
            icon: 'success',
            title: '{{ Session::get('id_sunat') }}',
            text: '{{ Session::get('descripcion_sunat') }}',
            showConfirmButton: false,
            timer: 2500
        })
    @endif

    @if (Session::has('sunat_error'))
        Swal.fire({
            icon: 'error',
            title: '{{ Session::get('id_sunat') }}',
            text: '{{ Session::get('descripcion_sunat') }}',
            showConfirmButton: false,
            timer: 5500
        })
    @endif

    @if (Session::has('documento_id'))
        let doc = '{{ Session::get('documento_id') }}';
        let id = doc + '-100';

        var url = '{{ route('ventas.documento.comprobante', ':id') }}';
        url = url.replace(':id', id);
        // $('#nueva_ventana').attr('href',url);
        // document.getElementById('nueva_ventana').click;
        window.open(url, "Comprobante SISCOM", "width=900, height=600")
    @endif
</script>
@endpush
