@extends('layout') @section('content')

@section('seguridad-active', 'active')
@section('roles-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-xs-12">
       <h2  style="text-transform:uppercase"><b>Listado de Roles</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Roles</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-xs-12">
        @can('haveaccess', 'role.create')
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('role.create')}}">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
        @endcan
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight" style="zoom: 90%;">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="roles" class="table dataTables-role table-striped table-bordered table-hover"  style="text-transform:uppercase">
                            <thead>
                            <tr>
                                <th class="text-center">ROL</th>
                                <th class="text-center">SLUG</th>
                                <th class="text-center">DESCRIPCIÓN</th>
                                <th class="text-center">FULL-ACCESS</th>
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
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <style>
        .my-swal {
            z-index: 2000;
          }
    </style>
@endpush

@push('scripts')
    <!-- DataTable -->
    <script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/select2/select2.full.min.js')}}"></script>
    <script>

        $(document).ready(function() {

            // DataTables
            refresh();

        });

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

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
                    var url_eliminar = '{{ route("role.destroy", ":id")}}';
                    url_eliminar = url_eliminar.replace(':id',id);
                    $(location).attr('href',url_eliminar);
                }else if (
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
        function refresh()
            {
                $('.dataTables-role').DataTable({
                    "dom": '<"html5buttons"B>lTfgitp',
                    "buttons": [
                        {
                            extend:    'excelHtml5',
                            text:      '<i class="fa fa-file-excel-o"></i> Excel',
                            titleAttr: 'Excel',
                            title: 'Roles'
                        },
                        {
                            titleAttr: 'Imprimir',
                            extend: 'print',
                            text:      '<i class="fa fa-print"></i> Imprimir',
                            customize: function (win){
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
                    "processing":true,
                    "ajax": "{{ route('role.getTable')}}",
                    "columns": [
                        {data: 'name', className:"text-center"},
                        {data: 'slug', className:"text-center"},
                        {data: 'description', className:"text-center"},
                        {data: 'full-access', className:"text-center"},
                        {
                            data: null,
                            className:"text-center",
                            render: function(data) {
                                var url_editar = '{{ route("role.edit", ":id")}}';
                                url_editar = url_editar.replace(':id',data.id);

                                var url_show = '{{route("role.show",":id")}}';
                                url_show = url_show.replace(':id',data.id);
                                return "<div class='btn-group'>" +
                                    "<a class='btn btn-success btn-sm Ver' href='"+url_show+"' title='Detalle'><i class='fa fa-eye'></i></a>" +
                                    "<a class='btn btn-warning btn-sm modificarDetalle' href='"+url_editar+"' title='Actualizar'><i class='fa fa-edit'></i></a>" +
                                    "<a class='btn btn-danger btn-sm' href='#' onclick='eliminar("+data.id+")' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                                    "</div>";
                            }
                        }
    
                    ],
                    "language": {
                        "url": "{{asset('Spanish.json')}}"
                    },
                    "order": [],
    
                });
            }
    </script>
@endpush
