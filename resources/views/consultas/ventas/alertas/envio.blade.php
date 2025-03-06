@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('consulta-alertas-active', 'active')
@section('consulta-ventas-alertas-envio-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12">
       <h2  style="text-transform:uppercase"><b>Listado de Documentos de Venta No Enviados</b></h2>
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
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-envio table-striped table-bordered table-hover" style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th class="text-center"># DOC</th>
                                    <th class="text-center">FECHA DOC.</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">TIEMPO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">SUNAT</th>
                                    <th class="text-center">DESCRIPCION</th>
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

@if(Session::has('doc_error_get_xml'))
<script>
    toastr.error("{{ Session::get('doc_error_get_xml') }}",'ERROR AL OBTENER XML');
</script>
@endif

<script>
$(document).ready(function() {
    var ventas = [];
    // DataTables
    loadTable();

});

function loadTable()
{
    $('.dataTables-envio').dataTable().fnDestroy();
    $('.dataTables-envio').DataTable({
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
        "ajax": "{{ route('consultas.ventas.alerta.getTableEnvio') }}",
        "columns": [
            {
                data: "id",
                visible: false,
                name: "cotizacion_documento.id"
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data){
                    return data.numero_doc
                }
            },
            {
                data: "fecha_documento",
                className: "text-center letrapequeña",
                name: "cotizacion_documento.fecha_documento"
            },
            {
                data: "tipo",
                className: "text-center letrapequeña",
                name: "tabladetalles.descripcion"
            },
            {
                data: "cliente",
                className: "text-center letrapequeña",
                name: "clientes.nombre"
            },
            {
                data: "monto",
                className: "text-center letrapequeña",
                name: "cotizacion_documento.total"
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data){
                    return data.dias > 4 ? 0 : 4 - data.dias;
                }
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
                    if(data.getCdrResponse)
                    {
                        return data.code + "-" + data.description;
                    }
                    else{
                        return "-";
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

                    var dias = data.dias > 4 ? 0 : 4 - data.dias;
                    cadena = cadena + `
                        `;

                    // cadena = cadena + `
                    //      <button type='button' class='btn btn-sm btn-danger m-1' onclick='anularVenta(${data.id})'
                    //         title='ANULAR'>
                    //         <i class='fa fa-times'></i> ANULAR
                    //     </button>`;

                    if(data.code != '1033' && dias > 0)
                    {
                        cadena = cadena + `
                        <button type='button' class='btn btn-sm btn-success m-1' onclick='enviarSunat(${data.id})'
                            title='Enviar Sunat'>
                            <i class='fa fa-send'></i> Sunat
                        </button>`;
                    }
                    else {
                        cadena = cadena + "<span class='badge badge-warning'>FUERA DE FECHA</span>";
                    }

                    // cadena = cadena + `
                    //      <button type='button' class='btn btn-sm btn-success m-1' onclick='enviarSunat(${data.id})'
                    //          title='Enviar Sunat'>
                    //          <i class='fa fa-send'></i> Sunat
                    //      </button>`;

                    return cadena;
                }
            },

        ],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if(aData.code == '1033')
            {
                $('td', nRow).css('display', 'none');
            }
        },
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
    });
    return false;
}

$(".dataTables-envio").on('click','.btn-pdf',function(){
    var data = $(".dataTables-envio").dataTable().fnGetData($(this).closest('tr'));
    let fn_pdf = 'comprobanteElectronico(' + data.id + ')';
    let fn_ticket = 'comprobanteElectronicoTicket(' + data.id + ')';
    $('.descarga-title').html(data.serie + '-' + data.correlativo);
    $('.file-pdf').attr('onclick',fn_pdf);
    $('.file-ticket').attr('onclick',fn_ticket);
    $('#modal_descargas_pdf').modal('show');
});

function comprobanteElectronico(id) {
    const url = route("ventas.documento.comprobante", { id: id,size:100});

    window.open(url, "Comprobante SISCOM", "width=900, height=600")
}

function comprobanteElectronicoTicket(id) {
    const url = route("ventas.documento.comprobante", { id: id,size:80});

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
function anularVenta(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción anular venta",
        text: "¿Seguro que desea anular esta venta, esta acción es irreparable.?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {

            var url = '{{ route("consultas.ventas.alerta.anularVenta", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Anulando la venta',
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

function enviarSunat(id , sunat) {
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
    });

    Swal.fire({
        title: "Opción Enviar a Sunat",
        text: "¿Seguro que desea enviar documento de venta a Sunat?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar"
    }).then(async (result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Cargando!',
                text: 'Enviando documento de venta a Sunat',
                showConfirmButton: false,
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const res = await axios.get(route('ventas.documento.sunat', id));
                if (res.data.success) {
                    // Actualizar DataTable
                    $('.dataTables-envio').DataTable().ajax.reload();

                
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA, SE TRASLADÓ EL REGISTRO A DOCUMENTOS ENVIADOS');
                } else {
                   toastr.error(res.data.exception,res.data.message,{timeOut:0});
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la solicitud',
                    text: error.message,
                    footer: `<pre>${error.stack}</pre>`
                });
            }finally{
                Swal.close();
            }
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La solicitud se ha cancelado.',
                'error'
            );
        }
    });

}

@if(Session::has('anulado_exito'))
    Swal.fire({
        icon: 'success',        
        title: 'Anulación',
        text: '{{ Session::get("correcto_anulado") }}',
        showConfirmButton: false,
        timer: 2500
    })
@endif

@if(Session::has('anulado_error'))
    Swal.fire({
        icon: 'error',
        title: 'Anulación',
        text: '{{ Session::get("error_anulado") }}',
        showConfirmButton: false,
        timer: 5500
    })
@endif

@if(Session::has('sunat_exito'))
    Swal.fire({
        icon: 'success',        
        title: '{{ Session::get("id_sunat") }}',
        text: '{{ Session::get("descripcion_sunat") }}',
        showConfirmButton: false,
        timer: 2500
    })
@endif



@if(Session::has('sunat_error'))
    Swal.fire({
        icon: 'error',
        title: '{{ Session::get("id_sunat") }}',
        text: '{{ Session::get("descripcion_sunat") }}',
        showConfirmButton: false,
        timer: 5500
    })
@endif

@if(Session::has('documento_id'))
    let doc = '{{ Session::get("documento_id")}}';
    let id = doc+'-100';

    const url = route("ventas.documento.comprobante", { id: doc,size:100});
    // $('#nueva_ventana').attr('href',url);
    // document.getElementById('nueva_ventana').click;
    window.open(url, "Comprobante SISCOM", "width=900, height=600")
@endif

</script>
@endpush
