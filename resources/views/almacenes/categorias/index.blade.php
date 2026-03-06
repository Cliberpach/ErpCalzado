@extends('layout')
@section('content')
    @include('almacenes.categorias.modals.mdl_create')
    @include('almacenes.categorias.modals.mdl_edit')
@section('almacenes-active', 'active')
@section('categoria-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Categorias</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Categorias</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md text-white" onclick="openMdlCreate();">
            <i class="fa fa-plus-square"></i> NUEVO
        </a>
    </div>

</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">

                <div class="ibox-content">

                    <div class="table-responsive">
                        @include('almacenes.categorias.tables.tbl_list')
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ mix('css/filepond.css') }}">
<script src="{{ mix('js/filepond.js') }}"></script>

<script>
    let dtCategoria = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadDtCategorias();
        events();
    });

    function events() {
        eventsMdlCreate();
        eventsMdlEdit();
    }

    function loadDtCategorias() {
        const url = '{{ route('almacenes.categorias.getAll') }}';

        dtCategoria = new DataTable('#tbl-list', {
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
                    data: 'descripcion',
                    name: 'descripcion'
                },
                {
                    data: null,
                    render: function(data, type, row) {

                        return `
                                <div class="btn-group dropup">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                         <i class="fa fa-bars"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right" style="max-height:150px; overflow-y:auto;">

                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlEdit(${data.id})">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <div class="dropdown-divider"></div>

                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteCategoria(${data.id})">
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

    function deleteCategoria(id) {
        toastr.clear();
        let row = getRowById(dtCategoria, id);
        let message = '';

        Swal.fire({
            title: `Eliminar categoría?`,
            text: `${row.descripcion}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Eliminando categoría...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let url =
                        `{{ route('almacenes.categorias.destroy', ['id' => ':id']) }}`;
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
                        dtCategoria.draw();
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        toastr.error(res.message, 'Error en el servidor - eliminar categoría');
                    }

                } catch (error) {
                    toastr.error(error, 'Error en la petición eliminar categoría');
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
