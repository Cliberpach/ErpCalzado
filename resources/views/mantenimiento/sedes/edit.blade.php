@extends('layout')
@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Sedes')
@section('hero-title', 'Editar Sede')
@section('hero-subtitle', 'Sedes')

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
    document.addEventListener('DOMContentLoaded', () => {
        events();
        iniciarSelect2();
        setUbigeoPrevio();
        loadFpImagen();
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

    function loadFpImagen() {
        const input = document.querySelector('#img_empresa');
        const sede = @json($sede);
        const pond = FilePond.create(input, {
            allowImagePreview: true,
            imagePreviewHeight: 120,
            imageCropAspectRatio: '1:1',
            styleLayout: 'compact',
            stylePanelAspectRatio: 0.5,
            storeAsFile: true,
            maxFileSize: '1MB',
            acceptedFileTypes: ['image/jpeg', 'image/webp'],
            labelFileTypeNotAllowed: 'Solo se permiten JPG, JPEG y WEBP',
            labelMaxFileSizeExceeded: 'El archivo supera 1 MB',
        });

        if (sede.logo_ruta) {
            pond.addFile(@json(asset('storage')) + '/' + sede.logo_ruta);
        }
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



    function changeDepartment(department_id) {

        const lstProvinces = @json($provincias);
        const lstDistricts = @json($distritos);

        let lstProvincesFiltered = [];

        if (department_id) {

            departamento_id = String(department_id).padStart(2, '0');

            lstProvincesFiltered = lstProvinces.filter((province) => {
                return province.departamento_id == department_id;
            })

            $('#provincia').empty().trigger('change');

            lstProvincesFiltered.forEach((province) => {
                $('#provincia').append(new Option(province.nombre, province.id, false, false));
            })

            $('#provincia').select2({
                placeholder: 'Seleccione una provincia',
                width: '100%'
            });

            $('#provincia').trigger('change');
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

            $('#distrito').empty().trigger('change');

            lstDistrictsFiltered.forEach((district) => {
                $('#distrito').append(new Option(district.nombre, district.id, false, false));
            })

            $('#distrito').select2({
                placeholder: 'Seleccione un distrito',
                width: '100%'
            });
        }
    }

    function setUbigeoPrevio() {
        const sede = @json($sede);
        $('#departamento').val(sede.departamento_id).trigger('change');
        $('#provincia').val(sede.provincia_id).trigger('change');
        $('#distrito').val(sede.distrito_id).trigger('change');
    }
</script>
@endpush
