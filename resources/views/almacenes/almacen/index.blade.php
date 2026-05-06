@extends('layout')
@section('almacenes-active', 'active')
@section('almacen-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Almacénes')
@section('hero-title', 'Lista de Almacénes')
@section('hero-subtitle', 'Almacénes')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreateAlmacen()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('almacenes.almacen.modals.mdl_create')
    @include('almacenes.almacen.modals.mdl_edit')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">

                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('almacenes.almacen.tables.tbl_list_almacenes')
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
            z-index: 3000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let dtAlmacenes = null;
        document.addEventListener('DOMContentLoaded', (e) => {
            events();
            loadDtAlmacenes();
        })

        function events() {
            eventsMdlCreateAlmacen();
            eventsMdlEditAlmacen();
        }

        function loadDtAlmacenes() {

            const url = '{{ route('almacenes.almacen.getRepository') }}';

            dtAlmacenes = new DataTable('.dataTables-almacenes', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,
                    type: 'GET',
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id',
                        searchable: false,
                        orderable: true,
                        className: "text-center",
                        visible: false
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion',
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: 'ubicacion',
                        name: 'ubicacion',
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                    {
                        data: 'sede_direccion',
                        name: 'sede_direccion',
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                    {
                        data: 'tipo_almacen',
                        name: 'tipo_almacen',
                        searchable: true,
                        orderable: false,
                        className: "text-center",
                        render: function(data, type, row) {

                            if (data === 'PRINCIPAL') {
                                return '<span class="badge badge-success">PRINCIPAL</span>';
                            }

                            if (data === 'SECUNDARIO') {
                                return '<span class="badge badge-warning">SECUNDARIO</span>';
                            }

                            return data;
                        }
                    },
                    {
                        data: 'creado',
                        name: 'created_at',
                        className: "text-center",
                        searchable: true,
                        orderable: true
                    },
                    {
                        data: null,
                        name: 'almacenes.descripcion',
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm" type="button"
                                onclick="openMdlEditAlmacen(${data.id})" title="Modificar">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm" type="button"
                                onclick="destroyAlmacen(${data.id})" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                        }
                    }
                ],
                language: {
                    url: "{{ asset('Spanish.json') }}"
                }
            });
        }

        function destroyAlmacen(id) {
            toastr.clear();
            let row = getRowById(dtAlmacenes, id);
            let message = '';

            Swal.fire({
                title: `Eliminar almacén?`,
                text: `ALMACÉN: ${row.descripcion}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando almacén...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let url =
                            `{{ route('almacenes.almacen.destroy', ['id' => ':id']) }}`;
                        url = url.replace(':id', id);
                        const token = document.querySelector('input[name="_token"]').value;

                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const res = await response.json();

                        if (res.success) {
                            dtAlmacenes.ajax.reload();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'Error en el servidor - eliminar almacén');
                        }

                    } catch (error) {
                        toastr.error(error, 'Error en la petición eliminar almacén');
                    } finally {
                        Swal.close();
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }
    </script>
@endpush
