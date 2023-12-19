@extends('layout') @section('content')
@section('mantenimiento-active', 'active')
@section('actividades-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Actividades de Registros</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Actividades</strong>
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
                    <table class="table dataTables-actividades table-striped table-bordered table-hover"  style="text-transform:uppercase">
                    <thead>
                        <tr>
                            <th class="text-center">USUARIO</th>
                            <th class="text-center">OPERACION</th>
                            <th class="text-center">GESTION</th>
                            <th class="text-center">DESCRIPCION</th>
                            <th class="text-center">REALIZADO</th>
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
        $('buttons-html5').removeClass('.btn-default');
        $('#table_actividades_wrapper').removeClass('');
        $('.dataTables-actividades').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [
   
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'Actividades de Registros'
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
            "serverSide":true,
            "processing":true,
            "ajax": '{{ route("actividad.getActivity")}}',
            "columns": [
                //Actividades
                {data: 'usuario' },
                {data: 'propiedades.operacion',className: "text-center" },
                {data: 'propiedades.gestion', className: "text-center" },
                {data: 'descripcion' },
                {data: 'fecha_creacion', className: "text-center" },


            ],
            "language": {
                        "url": "{{asset('Spanish.json')}}"
            },
            "order": [],

        });

    });

    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

</script>
@endpush