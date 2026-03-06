@extends('layout')


@section('ventas-active', 'active')
@section('tipo_cliente-active', 'active')


@section('content')
    @include('ventas.tipos_clientes.modals.mdl_create')
    @include('ventas.tipos_clientes.modals.mdl_edit')

    {{-- @include('category.modals.mdl_create')
    @include('category.modals.mdl_edit')
    @include('category.modals.mdl_import') --}}

    <div class="row wrapper border-bottom white-bg page-heading">

        <div class="col-lg-10 col-md-10">
            <h2 style="text-transform:uppercase"><b>Lista Tipos Clientes</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Panel de Control</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Tipos Clientes</strong>
                </li>
            </ol>
        </div>

        <div class="col-lg-2 col-md-2 text-right d-flex align-items-center justify-content-end">
            <a onclick="openMdlCreate()" class="btn btn-primary text-white">
                <i class="fas fa-plus-circle"></i> Nuevo
            </a>
        </div>

    </div>


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('ventas.tipos_clientes.tables.tbl_list')
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let dtTipoCliente = null;


        document.addEventListener('DOMContentLoaded', function() {
            loadDtTipoCliente();
            events();
        });

        function events() {
            eventsMdlCreate();
            eventsMdlEdit();
        }

        function loadDtTipoCliente() {
            const url = '{{ route('ventas.tipo_cliente.get-all') }}';

            dtTipoCliente = new DataTable('#tbl-list', {
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: url,
                    type: 'GET',
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nombre',
                        name: 'nombre'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            return `
                                <div class="btn-group dropup">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa-solid fa-grip"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right" style="max-height:150px; overflow-y:auto;">

                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEdit(${data.id})">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <div class="dropdown-divider"></div>

                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteTipoCliente(${data.id})">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>

                                    </div>
                                </div>
                            `;
                        },
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    "lengthMenu": "Mostrar _MENU_ categorías por página",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ categorías",
                    "infoEmpty": "Mostrando 0 a 0 de 0 categorías",
                    "infoFiltered": "(filtrado de _MAX_ categorías totales)",
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

        function deleteTipoCliente(id) {
            toastr.clear();
            let row = getRowById(dtTipoCliente, id);
            let message = '';

            Swal.fire({
                title: `Eliminar tipo cliente?`,
                text: `${row.nombre}`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Cargando...',
                        html: 'Eliminando tipo cliente...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let url =
                            `{{ route('ventas.tipo_cliente.destroy', ['id' => ':id']) }}`;
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
                            dtTipoCliente.draw();
                            toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(res.message, 'Error en el servidor tipo cliente eliminar');
                        }

                    } catch (error) {
                        toastr.error(error, 'Error en la petición tipo cliente eliminar');
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
