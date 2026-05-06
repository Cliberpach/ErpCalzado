@extends('layout')
@section('almacenes-active', 'active')
@section('marca-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Marcas')
@section('hero-title', 'Lista de Marcas')
@section('hero-subtitle', 'Marcas')
@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreateBrand()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('almacenes.marcas.modals.mdl_create')
    @include('almacenes.marcas.modals.mdl_edit')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">

                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('almacenes.marcas.tables.tbl_list')
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
        let dtMarcas = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtMarcas();
            events();
        });

        function events() {
            evenstMdlCreateBrand();
            eventsMdlEditBrand();
        }

        function loadDtMarcas() {

            const url = '{{ route('almacenes.marcas.getRepository') }}';

            dtMarcas = new DataTable('.dt-marcas', {
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
                        name: 'm.id',
                        visible: false,
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'marca',
                        name: 'm.marca',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'procedencia',
                        name: 'm.procedencia',
                        className: "text-center",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'm.created_at',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: null,
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm"
                                onclick="openMdlEditBrand(${data.id})" title="Modificar">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm"
                                onclick="destroyBrand(${data.id})" title="Eliminar">
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

        function destroyBrand(id) {
            toastr.clear();
            let row = getRowById(dtMarcas, id);
            let message = '';

            Swal.fire({
                title: `Eliminar marca?`,
                text: `MARCA: ${row.marca}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando marca...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let url =
                            `{{ route('almacenes.marcas.destroy', ['id' => ':id']) }}`;
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
                            dtMarcas.ajax.reload();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'Error en el servidor - eliminar marca');
                        }

                    } catch (error) {
                        toastr.error(error, 'Error en la petición eliminar marca');
                    } finally {
                        Swal.close();
                    }

                } else if (
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
