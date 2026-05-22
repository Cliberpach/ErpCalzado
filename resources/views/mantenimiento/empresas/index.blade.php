@extends('layout')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Empresa')
@section('hero-title', 'Lista de Empresas')
@section('hero-subtitle', 'Empresa')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">

                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('mantenimiento.empresas.tables.tbl_list')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@stop
@push('styles')
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('buttons-html5').removeClass('.btn-default');
            $('#table_empresas_wrapper').removeClass('');
            $('.dataTables-empresas').DataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false,
                "serverSide": true,
                "processing": true,
                "ajax": '{{ route('getBusiness') }}',
                "columns": [
                    //Empresa
                    {
                        data: 'id',
                        className: "text-center",
                        visible: false,
                        name: 'empresas.razon_social'
                    },
                    {
                        data: 'ruc',
                        className: "text-center",
                        name: 'empresas.ruc'
                    },
                    {
                        data: 'razon_social',
                        name: 'empresas.razon_social'
                    },
                    {
                        data: 'razon_social_abreviada',
                        name: 'empresas.razon_social_abreviada'
                    },
                    {
                        data: null,
                        className: "text-center",
                        searchable: false,
                        orderable: false,
                        render: function(data) {

                            let url_detalle = '{{ route('mantenimiento.empresas.show', ':id') }}';
                            url_detalle = url_detalle.replace(':id', data.id);

                            let url_edit = '{{ route('mantenimiento.empresas.edit', ':id') }}';
                            url_edit = url_edit.replace(':id', data.id);

                            let urlEditFacturacion = '{{ route('mantenimiento.empresas.editFacturacion', ':id') }}';
                            urlEditFacturacion = urlEditFacturacion.replace(':id', data.id);

                            let urlNumeracion = '{{ route('mantenimiento.empresas.numeracionCreate', ':id') }}';
                            urlNumeracion = urlNumeracion.replace(':id', data.id);

                            return `
                                <div class="dropdown">

                                    <button class="btn btn-success btn-sm dropdown-toggle"
                                        type="button"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">

                                        <i class="fas fa-cog"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right">

                                        <a class="dropdown-item" href="${url_detalle}">
                                            <i class="fa fa-eye text-success"></i> Detalle
                                        </a>

                                        <a class="dropdown-item" href="${url_edit}">
                                            <i class="fa fa-edit text-warning"></i> Editar
                                        </a>

                                        <a class="dropdown-item" href="${urlEditFacturacion}">
                                            <i class="fas fa-sort-numeric-up"></i> Facturacion
                                        </a>

                                        <a class="dropdown-item" href="${urlNumeracion}">
                                            <i class="fas fa-hashtag text-primary"></i> Numeracion
                                        </a>

                                        ${
                                            data.id != 1
                                            ? `<div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#" onclick="eliminar(${data.id})">
                                                            <i class="fa fa-trash"></i> Eliminar
                                                        </a>`
                                            : ''
                                        }

                                    </div>

                                </div>
                            `;
                        }
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [],

            });

        });


        function eliminar(id) {
            Swal.fire({
                title: 'Opción Eliminar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                allowOutsideClick: () => !Swal.isLoading(),
                // onBeforeOpen: () => {
                //     Swal.showLoading()
                // }
            }).then((result) => {
                if (result.isConfirmed) {
                    //Ruta Eliminar
                    var url_eliminar = '{{ route('mantenimiento.empresas.destroy', ':id') }}';
                    url_eliminar = url_eliminar.replace(':id', id);

                    Swal.fire({
                        title: '¡Cargando!',
                        type: 'info',
                        icon: 'info',
                        text: 'Eliminando Registro',
                        showConfirmButton: false,
                        onBeforeOpen: () => {
                            Swal.showLoading()
                        }
                    })

                    return $.ajax({

                        url: url_eliminar,

                        success: function(respuesta) {

                            if (respuesta.success == true) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: '¡Acción realizada satisfactoriamente!',
                                    showConfirmButton: false,
                                    timer: 1500
                                })

                                toastr.success(respuesta.mensaje)

                                $('.dataTables-empresas').DataTable().ajax.reload();
                            }

                        },

                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }


                    });

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
