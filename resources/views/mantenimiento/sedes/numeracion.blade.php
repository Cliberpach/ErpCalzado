@extends('layout')

@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')

@section('bread-module', 'Manteniemiento')
@section('bread-submodule', 'Sedes')
@section('hero-title', 'Numeración Sede')
@section('hero-subtitle', 'Sedes')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlAddNumeracion();">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('mantenimiento.sedes.modals.mdl_numeracion_add')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">

                <div class="ibox ">
                    <div class="ibox-content">

                        <div class="table-responsive">
                            @include('mantenimiento.sedes.tables.tbl_numeracion')
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
        let tableNumeracion = null;

        let fecha_comprobantes = null;
        let listComprobantes = [];

        document.addEventListener('DOMContentLoaded', () => {
            events();
            iniciarSelect2();
            cargarDataTable();

        })

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

        async function registrarSede(formStoreSede) {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Desea registrar la sede?",
                text: "Se afiliará a la empresa!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí registrar!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    limpiarErroresValidacion('msgError');

                    Swal.fire({
                        title: "Registrando...",
                        text: "Por favor, espere mientras procesamos la solicitud.",
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const formData = new FormData(formStoreSede);

                        const res = await axios.post(route('mantenimiento.sedes.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            window.location.href = "{{ route('mantenimiento.sedes.index') }}";
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        console.log(error);

                        if (error.response.status === 422) {
                            toastr.error('VALIDACIÓN CON ERRORES!!!', 'ERROR EN EL SERVIDOR');
                            const lstErrors = error.response.data.errors;
                            pintarErroresValidacion(lstErrors, 'error')
                            return;
                        }

                        toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR SEDE!!!');
                    } finally {
                        Swal.close();
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });
        }


        function cargarDataTable() {
            const getNumeracion = "{{ route('mantenimiento.sedes.getNumeracion') }}";

            tableNumeracion = new DataTable('#tbl_numeracion', {
                serverSide: true,
                ajax: {
                    url: getNumeracion,
                    type: 'GET',
                    data: function(d) {
                        d.sede_id = @json($sede_id);
                    }
                },
                columns: [{
                        data: 'comprobante'
                    },
                    {
                        data: 'serie'
                    },
                    {
                        data: 'nro_inicio'
                    },
                    {
                        data: 'iniciado'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '';
                        }
                    }
                ],
                language: {
                    processing: "Cargando resúmenes",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ RESÚMENES",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ RESÚMENES",
                    infoEmpty: "MOSTRANDO 0 RESÚMENES",
                    infoFiltered: "(FILTRADO de _MAX_ RESÚMENES)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "NO HAY RESÚMENES DISPONIBLES",
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
