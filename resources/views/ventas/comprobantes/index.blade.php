@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('comprobantes-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12 col-xs-12">
       <h2  style="text-transform:uppercase"><b>Listado de Comprobantes Electronicos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Comprobantes Electronicos</strong>
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
                        <table class="table dataTables-orden table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">FEC.DOCUMENTO</th>
                                    <th class="text-center">EMPRESA</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">MONTO</th>
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
        "ajax": "{{ route('ventas.getVouchers')}}",
        "columns": [
            //COMPROBANTES ELECTRONICOS
            {
                data: 'id',
                className: "text-center",
                visible: false
            },

            {
                data: 'numero',
                className: "text-center",
            },
            {
                data: 'tipo_venta',
                className: "text-center",
            },
            {
                data: 'fecha_documento',
                className: "text-center"
            },
            {
                data: 'empresa',
                className: "text-left"
            },
            {
                data: 'cliente',
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

                    //Ruta Detalle
                    var url = '{{ Storage::url(":ruta") }}';
                    url = url.replace(':ruta', data.ruta_comprobante_archivo);
                    url = url.replace('public/', '');

                    return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +
                    
                        "<li><a class='dropdown-item' target='_blank'  title='"+data.nombre_comprobante_archivo+"'download='"+data.nombre_comprobante_archivo+"' href="+url+" ><b><i class='fa fa-download'></i> Descargar</a></b></li>" +
                        "<li><a class='dropdown-item' onclick='notaDebito(" +data.id+ ")'  title='Generar nota de Debito'><b><i class='fa fa-file'></i> Nota de Debito</a></b></li>"
                    
                    "</ul></div>"
                }
            }

        ],
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
    });
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


function notaDebito(id) {
 
    Swal.fire({
        title: 'Opción Nota debito',
        text: "¿Seguro que desea generar nota de debito ?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            //Ruta debito
            var url = "{{ route('ventas.notas.create', [ 'comprobante' =>'id', 'nota'=>  '1' ]) }}".replace('id', id);
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



</script>
@endpush