@extends('layout')

@section('seguridad-active', 'active')
@section('roles-active', 'active')

@section('bread-module', 'Seguridad')
@section('bread-submodule', 'Seguridad')
@section('hero-title', 'Lista de Roles')
@section('hero-subtitle', 'Seguridad')

@section('btn-add')
    <a class="main-btn-add" href="{{ route('seguridad.role.create') }}">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('seguridad.roles.modals.mdl_show')
    <div class="wrapper wrapper-content animated fadeInRight" style="zoom: 90%;">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            @include('seguridad.roles.table.tbl_list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .my-swal {
            z-index: 2000;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let dtRoles = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtRoles();
            cargarSelect2();
        });

        function cargarSelect2() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                width: '100%',
            });
        }

        function loadDtRoles() {
            dtRoles = $('.dataTables-role').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: "{{ route('seguridad.role.getTable') }}",
                columns: [{
                        data: 'name',
                        className: "text-center"
                    },
                    {
                        data: 'slug',
                        className: "text-center"
                    },
                    {
                        data: 'description',
                        className: "text-center"
                    },
                    {
                        data: 'full-access',
                        className: "text-center"
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        className: "text-center",

                        render: function(data) {

                            let urlEditar =
                                '{{ route('seguridad.role.edit', ':id') }}';

                            urlEditar = urlEditar.replace(':id', data.id);

                            let urlShow =
                                '{{ route('seguridad.role.show', ':id') }}';

                            urlShow = urlShow.replace(':id', data.id);

                            return `
                                <div class='btn-group'>

                                    <a class='btn btn-success btn-sm text-white'
                                        onclick="openMdlShowRole(${data.id})"
                                        title='Detalle'>

                                        <i class='fa fa-eye'></i>
                                    </a>

                                    <a class='btn btn-warning btn-sm'
                                        href='${urlEditar}'
                                        title='Actualizar'>

                                        <i class='fa fa-edit'></i>
                                    </a>

                                    <a class='btn btn-danger btn-sm'
                                        href='javascript:void(0)'
                                        onclick='eliminar(${data.id})'
                                        title='Eliminar'>

                                        <i class='fa fa-trash'></i>
                                    </a>

                                </div>
                            `;
                        }
                    }
                ],

                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron registros",
                    emptyTable: "No hay datos disponibles",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar ascendente",
                        sortDescending: ": activar para ordenar descendente"
                    }
                }
            });

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

                    let urlEliminar = '{{ route('seguridad.role.destroy', ':id') }}';

                    urlEliminar = urlEliminar.replace(':id', id);

                    window.location.href = urlEliminar;

                } else if (result.dismiss === Swal.DismissReason.cancel) {

                    Swal.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    );

                }

            });

        }
    </script>
@endpush
