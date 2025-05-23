@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Notas de Salida</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Nota de Salida</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('almacenes.nota_salidad.create')}}">
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
                        @include('almacenes.nota_salidad.tables.tbl_ns_list')
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
    $('.dataTables-ingreso_mercaderia').DataTable({
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
        "ajax": "{{ route('almacenes.nota_salidad.data')}}",
        "columns": [
            //ingreso_mercaderia INTERNA
            //{ data: 'id',className: "text-center"},
             
            { data: 'id',className: "text-center"},
            { data: 'registrador_nombre',className: "text-center"},
            { data: 'created_at',className: "text-center"},
            { data: 'almacen_origen_nombre',className: "text-center"}, 
            { data: 'almacen_destino_nombre',className: "text-center"}, 
            { data: 'cadena_detalles',className: "text-center"},
            { data: 'observacion',className: "text-center"}, 
            {
                data: null,
                className: "text-center",
                render: function(data) {
                    //Ruta Detalle

                    //Ruta Modificar
                    var url_editar = '{{ route("almacenes.nota_salidad.edit", ":id")}}';
                    url_editar = url_editar.replace(':id', data.id);

                    var url_detalles = '{{ route("almacenes.nota_salidad.show", ":id")}}';
                    url_detalles = url_detalles.replace(':id', data.id);

                    return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +
                        "<li><a class='dropdown-item' href='" + url_detalles +
                        "' title='Detalles' ><b><i class='fa fa-eye'></i> Detalles</a></b></li>" +
                        "<li class=''><a class='dropdown-item' href='#' title='Comprobante' onclick='comprobante("+data.id+")'><b><i class='fa fa-file-pdf-o'></i> Comprobante</a></b></li>" +
                        "<li class='d-none'><a class='dropdown-item' href='" + url_editar +
                        "' title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li>" +
                        "<li class='d-none'><a class='dropdown-item' onclick='eliminar(" + data.id +
                        ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>" +
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

function comprobante(id) {
    var url = '{{ route("almacenes.nota_salidad.getPdf", ":id")}}';
    url = url.replace(':id',id+'-100');
    window.open(url, "Comprobante SISCOM", "width=900, height=600")
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
            var url_eliminar = '{{ route("almacenes.nota_salidad.destroy", ":id")}}';
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