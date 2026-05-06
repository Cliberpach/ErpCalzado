@extends('layout')
@section('almacenes-active', 'active')
@section('talla-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Tallas')
@section('hero-title', 'Lista de Tallas')
@section('hero-subtitle', 'Tallas')
@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreateTalla()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')

    @include('almacenes.tallas.modals.mdl_create')
    @include('almacenes.tallas.modals.mdl_edit')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            @include('almacenes.tallas.tables.tbl_list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let dtTallas = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadDtTallas();
            events();
        });

        function events() {
            eventsMdlCreateTalla();
            eventsMdlEditTalla();
        }

        function loadDtTallas() {

            const url = '{{ route('almacenes.tallas.getRepository') }}';

            dtTallas = new DataTable('.dt-tallas', {
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
                        visible: false
                    },
                    {
                        data: 'descripcion',
                        className: "text-center"
                    },
                    {
                        data: 'fecha_creacion',
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
                                onclick="openMdlEditTalla(${data.id})">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm"
                                onclick="destroyTalla(${data.id})">
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

        function destroyTalla(id) {

            let row = getRowById(dtTallas, id);

            Swal.fire({
                title: `Eliminar talla?`,
                text: `TALLA: ${row.descripcion}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando talla...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {

                        let url = `{{ route('almacenes.tallas.destroy', ['id' => ':id']) }}`;
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
                            dtTallas.ajax.reload();
                            toastr.success(res.message, 'Operación completada');
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
