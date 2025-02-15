@extends('layout') @section('content')

@section('seguridad-active', 'active')
@section('users-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-xs-12">
       <h2  style="text-transform:uppercase"><b>Listado de Usuarios</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Usuarios</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-xs-12">
        @can('haveaccess', 'user.create')
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('user.create')}}">
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
                        <table id="usuarios" class="table dataTables-user table-striped table-bordered table-hover"  style="text-transform:uppercase" id="tbl_users">
                            <thead>
                            <tr>
                                <th class="text-center">SEDE</th>
                                <th class="text-center">USUARIO</th>
                                <th class="text-center">CORREO</th>
                                <th class="text-center">COLABORADOR</th>
                                <th class="text-center">ROLES</th>
                                <th class="text-center">OPCIONES</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td class="text-center">{{$user->sede->nombre}}</td>
                                        <td class="text-center">{{$user->usuario}}</td>
                                        <td class="text-center">{{$user->email}}</td>
                                        <td class="text-center">@if(empty($user->colaborador)) NO ASIGNADO @else {{$user->colaborador->persona->getApellidosYNombres() }} @endif</td>
                                        <td class="text-center">
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-info">{{$role->name}}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @can('view', [$user,['user.show','userown.show']])
                                                <a class="btn btn-success btn-sm Ver" href="{{route('user.show',$user->id)}}" title="Detalle"><i class="fa fa-eye"></i></a>
                                                @endcan
                                                @can('view', [$user,['user.edit','userown.edit']])
                                                <a class="btn btn-warning btn-sm modificarDetalle" href="{{route('user.edit',$user->id)}}" title="Actualizar"><i class="fa fa-edit"></i></a>
                                                @endcan
                                                @can('haveaccess', 'user.destroy')
                                                <a class="btn btn-danger btn-sm @if ($user->id == 1){{'d-none'}}@endif" href="#" onclick="eliminar({{$user->id}})" title="Eliminar"><i class="fa fa-trash"></i></a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
                    var url_eliminar = '{{ route("user.destroy", ":id")}}';
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
                $('.dataTables-user').DataTable({
                    "dom": '<"html5buttons"B>lTfgitp',
                    "buttons": [
                        {
                            //extend:    'excelHtml5',
                            text:      '<i class="fa fa-file-excel-o"></i> Excel',
                            titleAttr: 'Excel',
                            title: 'Users',
                            action: function (e, dt, node, config ){
                                $(location).attr('href','/export/users/excel');
                                return false;
                            },
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
                    "language": {
                        "url": "{{asset('Spanish.json')}}"
                    },
                    "order": [],
                });


            }
    </script>
@endpush
