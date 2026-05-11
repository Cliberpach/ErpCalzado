@extends('layout')

@section('mantenimiento-active', 'active')
@section('promociones-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Promociones')

@section('hero-title', 'Lista de Promociones')
@section('hero-subtitle', 'Promociones')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreatePromocion()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('mantenimiento.promociones.modals.mdl_create')
    @include('mantenimiento.promociones.modals.mdl_edit')
    @include('mantenimiento.promociones.modals.mdl_products')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">

                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('mantenimiento.promociones.tables.tbl_list')
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection
@push('scripts')
    <script>
        let dtPromociones = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
            loadDtPromociones();
        });

        function events() {
            eventsMdlCreatePromocion();
            eventsMdlEditPromocion();
            eventsMdlProducts();
        }

        function loadDtPromociones() {

            const url = '{{ route('mantenimiento.promociones.getAll') }}';

            dtPromociones = new DataTable('.dataTables-promociones', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,
                    type: 'GET',
                },
                order: [
                    [0, 'desc']
                ],
                columns: [

                    {
                        data: 'id',
                        className: "text-center",
                        visible: false
                    },

                    {
                        data: 'nombre',
                        className: "text-center"
                    },

                    {
                        data: 'tipo_promocion',
                        className: "text-center",

                        render: function(data) {

                            // DESCUENTO FIJO
                            if (data === 'descuento_fijo') {

                                return `
                    <span class="badge badge-success">
                        DESC. FIJO
                    </span>
                `;
                            }

                            // DESCUENTO %
                            if (data === 'descuento_porcentaje') {

                                return `
                    <span class="badge badge-primary">
                        DESC. %
                    </span>
                `;
                            }

                            // PRECIO TOTAL
                            if (data === 'precio_total') {

                                return `
                    <span class="badge badge-warning">
                        PRECIO TOTAL
                    </span>
                `;
                            }

                            return data;
                        }
                    },

                    {
                        data: 'valor',
                        className: "text-center",

                        render: function(data, type, row) {

                            // DESCUENTO %
                            if (row.tipo_promocion === 'descuento_porcentaje') {

                                return `${data}%`;
                            }

                            // MONTO / PRECIO TOTAL
                            return `S/ ${data}`;
                        }
                    },

                    {
                        data: 'cantidad_minima',
                        className: "text-center",

                        render: function(data) {

                            return `${data} PAR(ES)`;
                        }
                    },

                    {
                        data: 'fecha_inicio',
                        className: "text-center",

                        render: function(data) {

                            return data ?? '-';
                        }
                    },

                    {
                        data: 'fecha_fin',
                        className: "text-center",

                        render: function(data) {

                            return data ?? '-';
                        }
                    },

                    {
                        data: 'estado',
                        className: "text-center",

                        render: function(data) {

                            if (data === 'ACTIVO') {

                                return `
                    <span class="badge badge-success">
                        ACTIVO
                    </span>
                `;
                            }

                            if (data === 'ANULADO') {

                                return `
                    <span class="badge badge-danger">
                        ANULADO
                    </span>
                `;
                            }

                            return data;
                        }
                    },

                    {
                        data: null,
                        className: "text-center",
                        orderable: false,
                        searchable: false,

                        render: function(data) {

                            return `
                                <div class="btn-group">

                                    <button type="button"
                                        class="btn btn-success btn-sm dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">

                                        <i class="fas fa-cogs"></i>

                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right">

                                        <a class="dropdown-item text-info"
                                            href="#"
                                            onclick="openMdlEditPromocion(${data.id})">

                                            <i class="fas fa-edit"></i>
                                            Editar

                                        </a>

                                        <a class="dropdown-item text-success"
                                            href="#"
                                            onclick="openMdlProducts(${data.id})">

                                            <i class="fas fa-box"></i>
                                            Productos

                                        </a>

                                        <div class="dropdown-divider"></div>

                                        <a class="dropdown-item text-danger"
                                            href="#"
                                            onclick="destroyPromocion(${data.id})">

                                            <i class="fas fa-trash"></i>
                                            Eliminar

                                        </a>

                                    </div>

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

        async function destroyPromocion(id) {

            toastr.clear();

            let row = getRowById(dtPromociones, id);

            Swal.fire({
                title: '¿Eliminar promoción?',
                text: `PROMOCIÓN: ${row.nombre}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Espere un momento',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {

                        let url = `{{ route('mantenimiento.promociones.destroy', ['id' => ':id']) }}`;
                        url = url.replace(':id', id);

                        const token = document.querySelector('input[name="_token"]').value;

                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const data = await res.json();

                        if (data.success) {
                            dtPromociones.ajax.reload();
                            toastr.success(data.message, 'Operación exitosa');
                        } else {
                            toastr.error(data.message, 'Error');
                        }

                    } catch (error) {
                        toastr.error(error, 'Error en la petición');
                    } finally {
                        Swal.close();
                    }

                } else {
                    Swal.fire('Cancelado', 'No se realizaron cambios', 'info');
                }

            });
        }
    </script>
@endpush
