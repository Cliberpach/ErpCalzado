@extends('layout')

@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Mantenimiento')
@section('hero-title', 'Editar Sede')
@section('hero-subtitle', 'Mantenimiento')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">

                <div class="ibox ">
                    <div class="ibox-content">
                        @include('mantenimiento.sedes.forms.form_edit_sede')
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
    <link href="{{ mix('css/filepond.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ mix('js/filepond.js') }}"></script>
    <script>
        const btnGetComprobantes = document.querySelector('#btn-get-comprobantes');
        const bodyTableSearchComprobantes = document.querySelector('.table-search-comprobantes tbody');
        let tableResumenes = null;

        let pondImage = null;
        let fecha_comprobantes = null;
        let listComprobantes = [];

        document.addEventListener('DOMContentLoaded', () => {
            initFilePondImage();
            events();
            iniciarSelect2();
            setUbigeoPrevio();
        })

        function events() {
            document.querySelector('#formActualizarSede').addEventListener('submit', (e) => {
                e.preventDefault();
                actualizarSede(e.target);
            })
        }

        function iniciarSelect2() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
        }

        async function actualizarSede(formActualizarSede) {
            Swal.fire({
                title: "Desea actualizar la sede?",
                text: "Se producirán cambios!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí actualizar!",
                cancelButtonText: "No!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {

                    limpiarErroresValidacion('msgError');

                    Swal.fire({
                        title: "Actualizando...",
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

                        const formData = new FormData(formActualizarSede);
                        const sede_id = @json($sede->id);

                        const res = await axios.post(route('mantenimiento.sedes.update', sede_id),
                            formData, {
                                headers: {
                                    "X-HTTP-Method-Override": "PUT"
                                }
                            });

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            window.location.href = "{{ route('mantenimiento.sedes.index') }}";
                        } else {
                            Swal.close();
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {

                        Swal.close();

                        if (error.response) { // Verifica si error.response existe
                            if (error.response.status === 422) {
                                toastr.error('VALIDACIÓN CON ERRORES!!!', 'ERROR EN EL SERVIDOR');
                                const lstErrors = error.response.data.errors;
                                pintarErroresValidacion(lstErrors, 'error');
                                return;
                            }

                            toastr.error(error.response.data.message || 'Error desconocido',
                                'ERROR EN LA PETICIÓN ACTUALIZAR SEDE!!!');
                            return;
                        }

                        toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR SEDE!!!');
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

        function initFilePondImage() {

            const sede = @json($sede);

            const inputElement = document.querySelector('#img_empresa');

            pondImage = FilePond.create(inputElement, {

                allowMultiple: false,
                instantUpload: false,
                maxFileSize: '2MB',
                storeAsFile: true,

                acceptedFileTypes: [
                    "image/jpeg",
                    "image/png",
                    "image/webp",
                    "image/avif"
                ],

                files: sede.logo_ruta ? [{
                    source: `/storage/${sede.logo_ruta}`
                }] : [],

                labelIdle: `
                    <div style="padding:10px;">
                        <i class="fas fa-cloud-upload-alt"
                            style="font-size:40px;color:#2563eb;margin-bottom:10px;">
                        </i>

                        <div style="font-size:15px;font-weight:bold;">
                            Arrastra una imagen o
                            <span style="color:#2563eb;">haz click aquí</span>
                        </div>

                        <small style="color:#6b7280;">
                            JPG, JPEG, WEBP, AVIF | Máx. 2MB
                        </small>
                    </div>
                `,

                fileValidateTypeLabelExpectedTypes: 'Solo se permiten JPG, JPEG, WEBP y AVIF',

                labelMaxFileSizeExceeded: 'El archivo es demasiado grande',

                labelMaxFileSize: 'El tamaño máximo permitido es 2MB'

            });

        }

        function changeDepartment(department_id) {

            const lstProvinces = @json($provincias);
            const lstDistricts = @json($distritos);

            let lstProvincesFiltered = [];

            if (department_id) {

                departamento_id = String(department_id).padStart(2, '0');

                lstProvincesFiltered = lstProvinces.filter((province) => {
                    return province.departamento_id == department_id;
                })

                $('#province').empty().trigger('change');

                lstProvincesFiltered.forEach((province) => {
                    $('#province').append(new Option(province.nombre, province.id, false, false));
                })

                $('#province').select2({
                    placeholder: 'Seleccione una provincia',
                    width: '100%'
                });

                $('#province').trigger('change');
            }

        }

        function changeProvince(province_id) {

            const lstDistricts = @json($distritos);

            let lstDistrictsFiltered = [];

            if (province_id) {

                province_id = String(province_id).padStart(4, '0');

                lstDistrictsFiltered = lstDistricts.filter((district) => {
                    return district.provincia_id == province_id;
                })

                $('#district').empty().trigger('change');

                lstDistrictsFiltered.forEach((district) => {
                    $('#district').append(new Option(district.nombre, district.id, false, false));
                })

                $('#district').select2({
                    placeholder: 'Seleccione un distrito',
                    width: '100%'
                });
            }

        }

        function setUbigeoPrevio() {

            const sede = @json($sede);
            console.log(sede);

            const departamento = String(sede.departamento_id).padStart(2, '0');
            const provincia = String(sede.provincia_id).padStart(4, '0');
            const distrito = String(sede.distrito_id).padStart(6, '0');
            console.log({
                departamento,
                provincia,
                distrito
            });
            $('#department').val(departamento).trigger('change');
            $('#province').val(provincia).trigger('change');
            $('#district').val(distrito).trigger('change');
        }
    </script>
@endpush
