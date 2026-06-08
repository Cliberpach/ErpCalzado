@extends('layout')

@section('seguridad-active', 'active')
@section('users-active', 'active')

@section('bread-module', 'Seguridad')
@section('bread-submodule', 'Seguridad')
@section('hero-title', 'Lista de Usuarios')
@section('hero-subtitle', 'Seguridad')

@section('btn-add')
    <a class="main-btn-add" onclick="goToCrearUsuario()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            @include('seguridad.users.tables.tbl_list_users')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        let dtUsuarios = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarDataTableUsuarios();
        });

        function iniciarDataTableUsuarios() {
            const urlGetUsuarios = '{{ route('seguridad.user.getUsuarios') }}';

            dtUsuarios = new DataTable('#tbl_list_users', {
                serverSide: true,
                processing: true,
                ajax: {
                    url: urlGetUsuarios,
                    type: 'GET',
                },
                order: [[0, 'desc']],
                columns: [
                    { data: 'id',                 name: 'id',                 visible: false, searchable: false, orderable: true },
                    { data: 'sede_nombre',        name: 'sede_nombre',        searchable: true,  orderable: true },
                    { data: 'usuario',            name: 'usuario',            searchable: true,  orderable: true },
                    { data: 'email',              name: 'email',              searchable: true,  orderable: true },
                    { data: 'colaborador_nombre', name: 'colaborador_nombre', searchable: true,  orderable: false },
                    { data: 'roles_html',         name: 'roles_html',         searchable: false, orderable: false },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const baseUrlEdit = `{{ route('seguridad.user.edit', ['id' => ':id']) }}`;
                            const urlEdit     = baseUrlEdit.replace(':id', data.id);

                            const baseUrlShow = `{{ route('seguridad.user.show', ['id' => ':id']) }}`;
                            const urlShow     = baseUrlShow.replace(':id', data.id);

                            return `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="max-height: 150px; overflow-y: auto;">
                                        <a class="dropdown-item" href="${urlShow}">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a class="dropdown-item" href="${urlEdit}">
                                            <i class="fas fa-user-edit"></i> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarUsuario(${data.id})">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                initComplete: function() {
                    const searchFilter = document.querySelector('#dt-search-0');
                    if (searchFilter) {
                        const hint = document.createElement('small');
                        hint.className = 'text-muted d-block mt-1';
                        hint.innerHTML = 'Búsqueda por: Sede, Usuario, Correo, Colaborador';
                        searchFilter.appendChild(hint);
                    }
                },
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "aria": {
                        "sortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": activar para ordenar la columna de manera descendente"
                    }
                }
            });
        }

        function goToCrearUsuario() {
            window.location.href = @json(route('seguridad.user.create'));
        }

        function eliminarUsuario(id) {
            toastr.clear();
            const row = getRowById(dtUsuarios, id);
            const message = `Desea eliminar el usuario: ${row.usuario}`;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: message,
                text: 'Operación no reversible!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar!',
                cancelButtonText: 'No, cancelar!',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando usuario...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    try {
                        let urlEliminar = `{{ route('seguridad.user.destroy', ['id' => ':id']) }}`;
                        urlEliminar = urlEliminar.replace(':id', id);
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch(urlEliminar, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': token }
                        });

                        const res = await response.json();

                        if (res.success) {
                            dtUsuarios.draw();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR USUARIO');
                        }

                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR USUARIO');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: 'Operación cancelada',
                        text: 'No se realizaron acciones',
                        icon: 'error'
                    });
                }
            });
        }
    </script>
@endpush
