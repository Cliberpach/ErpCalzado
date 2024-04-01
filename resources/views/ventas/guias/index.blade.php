@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Guias de Remision</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Guias de Remision</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('ventas.guiasremision.create_new')}}">
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
                        <table class="table dataTables-gui table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>


                                    <th colspan="4" class="text-center">DOCUMENTO DE VENTA</th>

                                    <th colspan="6" class="text-center">GUIA DE REMISION</th>

                                </tr>
                                <tr>
                                    <th class="text-center">Doc Afec.</th>
                                    <th class="text-center">FEC.DOCUMENTO</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">CLIENTE</th>

                                    <th class="text-center">N°</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">PESO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">SUNAT</th>

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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">

@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>

<script>
$(document).ready(function() {

    // DataTables
    $('.dataTables-gui').DataTable({
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
        "ajax": "{{ route('ventas.getGuia')}}",
        "columns": [
            //Guias de remision
            {
                data: 'numero',
                className: "text-center"
            },
            {
                data: 'fecha_documento',
                className: "text-center"
            },
            {
                data: 'tipo_venta',
                className: "text-center",
            },
            {
                data: 'cliente',
                className: "text-left"
            },

            {
                data: 'serie_guia',
                className: "text-center"
            },
            {
                data: 'cantidad',
                className: "text-center"
            },
            {
                data: 'peso',
                className: "text-center"
            },


            {
                data: null,
                className: "text-center",
                render: function(data) {
                    switch (data.estado_sunat) {
                        case "0":
                            return "<span class='badge badge-warning' d-block>ACEPTADO</span>";
                            break;
                        case "98":
                            return "<span class='badge badge-danger' d-block>EN PROCESO</span>";
                            break;
                        case "99":
                            return "<span class='badge badge-danger' d-block>CON ERRORES</span>";
                            break;
                        default:
                            return "<span class='badge badge-success' d-block>REGISTRADO</span>";
                    }
                },
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {

                    if (data.sunat == '1') {
                       //Ruta Detalle
                        var url = '{{ Storage::url(":ruta") }}';
                        url = url.replace(':ruta', data.ruta_comprobante_archivo);
                        url = url.replace('public/', '');
                        return "<a class='btn btn-danger btn-sm text-center' title='"+data.nombre_comprobante_archivo+"'download='"+data.nombre_comprobante_archivo+"' href="+url+"><i class='fa fa-file-pdf-o'></i></a>";
                    }else{
                        return "-";
                    }
                },
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {
                    //Ruta Detalle
                    var url_eliminar = '{{ route("ventas.guiasremision.delete", ":id")}}';
                    url_eliminar = url_eliminar.replace(':id', data.id);

                    return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +

                        "<li><a class='dropdown-item' onclick='detalle(" +data.id+ ")' title='Detalle'><b><i class='fa fa-eye'></i> Detalle</a></b></li>" +
                        "<li class='dropdown-divider'></li>" +
                        "<li><a class='dropdown-item' onclick='enviarSunat(" +data.id+ ")'  title='Enviar Sunat'><b><i class='fa fa-file'></i> Enviar Sunat</a></b></li>" +
                        "<li><a class='dropdown-item' onclick='consultarSunat(" +data.id+ ")'  title='Consultar Sunat'><b><i class='fa fa-file'></i> Consultar Sunat</a></b></li>" +
                        "<li><a class='dropdown-item' href='" +url_eliminar+ "'  title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>"

                    "</ul></div>"
                }
            }

        ],
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
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


function detalle(id) {
    var url = '{{ route("ventas.guiasremision.show", ":id")}}';
    url = url.replace(':id',id);

    window.open(url, "Comprobante SISCOM", "width=900, height=600")
}
function consultarSunat(id , sunat) {
    const url= `consulta_ticket/guia/${id}`;
    const tokenValue = document.querySelector('input[name="_token"]').value;
    toastr.info('Consultando envío...',"CONSULTANDO");
    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': tokenValue,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        })
        .then(response => response.json())
        .then(data => {
            toastr.remove()
            const type      =   data.type;
            const message   =   data.message;
            if(type === 'success'){
                toastr.options.closeDuration = 400;
                toastr.options.progressBar = true;
                toastr.success(`GUIA: ${id} | ESTADO: ${message.descripcion}`, 'Consulta completa');
            }
            if(type === 'error'){
                toastr.options.closeDuration = 400;
                toastr.options.progressBar = true;
                toastr.error(`GUIA: ${id} | ESTADO: ${message}`, 'Error');
            }
        })
        .catch(error => console.error('Error:', error));

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

            var url = '{{ route("ventas.guiasremision.sunat", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Enviando Guia de Remision a Sunat',
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

@if(Session::has('guia_exito'))
    toastr.error("{{ Session::get('guia_exito') }}", "", { timeOut: 0 });
@endif

@if(Session::has('guia_error'))
    toastr.error("{{ Session::get('guia_error') }}", "", { timeOut: 0 });
@endif

// @if(Session::has('sunat_exito'))
//         Swal.fire({
//             icon: 'success',        
//             title: '{{ Session::get("id_sunat") }}',
//             text: '{{ Session::get("descripcion_sunat") }}',
//             showConfirmButton: false,
//             timer: 2500
//         })
// @endif

// @if(Session::has('error'))
//         Swal.fire({
//             icon: 'error',
//             title: '{{ Session::get("id_sunat") }}',
//             text: '{{ Session::get("descripcion_sunat") }}',
//             showConfirmButton: false,
//             timer: 5500
//         })
// @endif




</script>
@endpush
