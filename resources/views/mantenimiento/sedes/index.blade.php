@extends('layout')

@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Sedes')
@section('hero-title', 'Lista de Sedes')
@section('hero-subtitle', 'Sedes')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="goToSedeCreate()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">

                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="table-responsive">

                            @include('mantenimiento.sedes.tables.tbl_lst_sedes')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
@endpush


@push('scripts')
    <script>
        const btnGetComprobantes = document.querySelector('#btn-get-comprobantes');
        const bodyTableSearchComprobantes = document.querySelector('.table-search-comprobantes tbody');
        let tblLstSedes = null;

        let fecha_comprobantes = null;
        let listComprobantes = [];

        document.addEventListener('DOMContentLoaded', () => {
            events();
            cargarDataTable();
            loadDataTableDetallesResumen();
        })

        function events() {

        }

        function goToSedeCreate() {
            window.location.href = route('mantenimiento.sedes.create');
        }


        function cargarDataTable() {
            const getSedes = "{{ route('mantenimiento.sedes.getSedes') }}";

            tblLstSedes = new DataTable('#tbl_lst_sedes', {
                serverSide: true,
                ajax: {
                    url: getSedes,
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'direccion'
                    },
                    {
                        data: 'ubigeo'
                    },
                    {
                        data: 'codigo_local'
                    },
                    {
                        data: 'tipo_sede'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {

                            let acciones = ``;
                            const ruta_numeracion =
                                `{{ route('mantenimiento.sedes.numeracionCreate', ':sede_id') }}`.replace(
                                    ':sede_id', row.id);
                            const ruta_editar = `{{ route('mantenimiento.sedes.edit', ':id') }}`.replace(
                                ':id', row.id);

                            if (data.tipo_sede == 'SECUNDARIA') {
                                acciones = `
                                    <div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle"
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-th-large"></i>
                                        </button>

                                        <div class="dropdown-menu">

                                            <a class="dropdown-item" href="${ruta_editar}">
                                                <i class="fas fa-edit text-warning mr-2"></i>
                                                Editar
                                            </a>

                                            <a class="dropdown-item" href="${ruta_numeracion}">
                                                <i class="fas fa-hashtag text-primary mr-2"></i>
                                                Numeración
                                            </a>

                                        </div>
                                    </div>
                                `;
                            }

                            return acciones;
                        }
                    }
                ],
                language: {
                    processing: "Cargando sedes",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ SEDES",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ RESÚMENES",
                    infoEmpty: "MOSTRANDO 0 RESÚMENES",
                    infoFiltered: "(FILTRADO de _MAX_ SEDES)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "NO HAY SEDES DISPONIBLES",
                    paginate: {
                        first: "PRIMERO",
                        previous: "ANTERIOR",
                        next: "SIGUIENTE",
                        last: "ÚLTIMO"
                    },
                    aria: {
                        sortAscending: ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                },
                "order": [
                    [0, "desc"]
                ]
            });
        }
    </script>
@endpush
