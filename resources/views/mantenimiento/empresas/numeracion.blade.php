@extends('layout')

@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Empresas')
@section('hero-title', 'Numeración Empresa')
@section('hero-subtitle', 'Empresas')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlAddNumeracion();">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('mantenimiento.empresas.modals.mdl_numeracion_add')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">

                <div class="ibox ">
                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('mantenimiento.empresas.tables.tbl_numeracion_empresa')
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
        let tableNumeracionEmpresa = null;

        document.addEventListener('DOMContentLoaded', () => {
            events();
            iniciarSelect2();
            cargarDataTable();
        });

        function events() {
            eventsMdlAddNumeracion();
        }

        function iniciarSelect2() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
        }

        function openMdlAddNumeracion() {
            $('#mdlNumeracionAdd').modal('show');
        }

        function cargarDataTable() {
            const getNumeracion = "{{ route('mantenimiento.sedes.getNumeracion') }}";

            tableNumeracionEmpresa = new DataTable('#tbl_numeracion_empresa', {
                serverSide: true,
                ajax: {
                    url: getNumeracion,
                    type: 'GET',
                    data: function(d) {
                        d.sede_id = @json($sede_principal->id);
                    }
                },
                columns: [
                    { data: 'comprobante' },
                    { data: 'serie' },
                    { data: 'nro_inicio' },
                    { data: 'iniciado' },
                ],
                language: {
                    processing: "Cargando numeraciones",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ REGISTROS",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ REGISTROS",
                    infoEmpty: "MOSTRANDO 0 REGISTROS",
                    infoFiltered: "(FILTRADO de _MAX_ REGISTROS)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Sin registros para mostrar",
                    emptyTable: "NO HAY NUMERACIONES DISPONIBLES",
                    paginate: {
                        first: "PRIMERO",
                        previous: "ANTERIOR",
                        next: "SIGUIENTE",
                        last: "ULTIMO"
                    }
                },
                order: [[0, "asc"]]
            });
        }
    </script>
@endpush
