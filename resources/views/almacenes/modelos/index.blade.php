@extends('layout')

@section('almacenes-active', 'active')
@section('modelo-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Modelos')
@section('hero-title', 'Lista de Modelos')
@section('hero-subtitle', 'Modelos')
@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreateModelo()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')

    @include('almacenes.modelos.modals.mdl_create')
    @include('almacenes.modelos.modals.mdl_edit')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            @include('almacenes.modelos.tables.tbl_list')
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
        let dtModelos = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtModelos();
            events();
        });

        function events() {
            eventsMdlCreateModelo();
            eventsMdlEditModelo();
        }

        function loadDtModelos() {

            const url = '{{ route('almacenes.modelos.getRepository') }}';

            dtModelos = new DataTable('.dt-modelos', {
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
                        visible: false
                    },
                    {
                        data: 'descripcion',
                        name: 'm.descripcion',
                        className: "text-center"
                    },
                    {
                        data: 'created_at',
                        name: 'm.created_at',
                        className: "text-center"
                    },
                    {
                        data: null,
                        className: "text-center",
                        orderable: false,
                        render: function(data) {
                            return `
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm"
                                onclick="openMdlEditModelo(${data.id})">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm"
                                onclick="destroyModelo(${data.id})">
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

        function destroyModelo(id) {

            let row = getRowById(dtModelos, id);

            Swal.fire({
                title: `Eliminar modelo?`,
                text: `MODELO: ${row.descripcion}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando modelo...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        let url = `{{ route('almacenes.modelos.destroy', ['id' => ':id']) }}`;
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
                            dtModelos.ajax.reload();
                            toastr.success(res.message, 'OK');
                        } else {
                            toastr.error(res.message, 'Error');
                        }

                    } catch (error) {
                        toastr.error(error, 'Error petición');
                    } finally {
                        Swal.close();
                    }

                } else {
                    Swal.fire("Cancelado", "No se realizaron cambios", "error");
                }
            });
        }
    </script>
@endpush
