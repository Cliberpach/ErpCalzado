@extends('layout')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Empresa')
@section('hero-title', 'Editar Empresa')
@section('hero-subtitle', 'Empresa')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">

                    <div class="ibox-content">
                        @include('mantenimiento.empresas.forms.form_edit')
                    </div>

                    <!-- FOOTER DEL IBOX -->
                    <div class="ibox-footer">
                        <div class="row">

                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i>
                                <small>
                                    Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.
                                </small>
                            </div>

                            <div class="col-md-6 text-right">
                                <a href="{{ route('mantenimiento.empresas.index') }}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>

                                <button form="form_edit_company" type="submit" id="btn_grabar"
                                    class="btn btn-w-m btn-success">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .logo {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }

        .custom-file-label::after {
            content: "Buscar";
        }
    </style>
    <link href="{{ mix('css/filepond.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ mix('js/filepond.js') }}"></script>
    <script src="{{ mix('js/tomselect.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            loadSelectsCompany();
            loadFpLogo();
            eventsCompanyEdit();
            loadUbigeo();
        })

        function loadSelectsCompany() {
            window.departmentSelect = loadSimpleSelect('departamento');
            window.provinceSelect = loadSimpleSelect('provincia');
            window.districtSelect = loadSimpleSelect('distrito');
        }

        function loadUbigeo() {
            window.departmentSelect.setValue(13);
            window.provinceSelect.setValue(1301);
            window.districtSelect.setValue(130101);
        }


        function consultarDni(dni) {
            var dni = $('#dni_representante').val()
            if (dni.length == 8) {

                Swal.fire({
                    title: 'Consultar',
                    text: "¿Desea consultar Dni a Reniec?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        var url = '{{ route('getApidni', ':dni') }}';
                        url = url.replace(':dni', dni);

                        return fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                console.log(error)
                                $('#dni_representante').val('SIN VERIFICAR')
                                Swal.showValidationMessage(
                                    `Dni Inválido`
                                )
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    camposDni(result)
                    consultaExitosa()
                })
            } else {
                toastr.error('El campo Dni debe de contar con 8 dígitos', 'Error');
            }
        }

        function eventsCompanyEdit() {
            document.querySelector('#form_edit_company').addEventListener('submit', function(e) {
                e.preventDefault();
                updateCompany(e.target);
            });

            window.departmentSelect.on('change', function(value) {
                console.log('Departamento seleccionado:', value);
                changeDepartment(value);
            });

            window.provinceSelect.on('change', function(value) {
                console.log('Provincia seleccionado:', value);
                changeProvince(value);
            });
        }

        function loadFpLogo() {
            const input = document.querySelector('#logo');
            const empresa = @json($empresa);
            const rutaLogo = @json(asset('storage')) + '/' + empresa.ruta_logo;


            const pond = FilePond.create(input, {
                allowImagePreview: true,
                imagePreviewHeight: 120,
                imageCropAspectRatio: '1:1',
                styleLayout: 'compact',
                stylePanelAspectRatio: 0.5,
                storeAsFile: true,

                maxFileSize: '2MB',
                acceptedFileTypes: [
                    'image/jpeg',
                    'image/webp',
                    'image/avif'
                ],
                labelFileTypeNotAllowed: 'Formato no permitido',
                labelMaxFileSizeExceeded: 'El archivo supera los 2 MB',
            });

            if (empresa.ruta_logo) {
                pond.addFile(rutaLogo);
            }
        }

        function updateCompany(form) {

            limpiarErroresValidacion('msgError');

            Swal.fire({
                title: 'Opción Modificar',
                text: "¿Seguro que desea modificar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#5b8bd9",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const id = @json($empresa->id);
                        const formData = new FormData(form);
                        formData.append('_method', 'PUT');

                        const res = await axios.post(route('mantenimiento.empresas.update', {
                            id: id
                        }), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            const routeIndex = route('mantenimiento.empresas.index');
                            window.location.href = routeIndex;
                        } else {
                            Swal.close();
                            toastr.error(res.data.message, 'Error en el servidor');
                        }

                    } catch (error) {
                        if (error.response && error.response.status === 422) {
                            pintarErroresValidacion(error.response.data.errors, 'error');
                            toastr.error('Errores de validación');
                        } else {
                            toastr.error(error, 'Error en la petición actualizar empresa');
                        }
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    );
                }

            });
        }

        function changeDepartment(departmentId) {
            const departmentIdLeft = String(departmentId).padStart(2, '0');

            const lstProvinces = @json($provinces);
            console.log('lstProvinces', lstProvinces);
            const lstProvincesFiltered = lstProvinces.filter(p => p.departamento_id == departmentIdLeft);
            console.log('lstProvincesFiltered', lstProvincesFiltered);
            paintProvinces(lstProvincesFiltered);

        }

        function paintProvinces(provinces) {

            const select = window.provinceSelect;

            select.clear();
            select.clearOptions();
            const options = provinces.map(p => ({
                id: p.id,
                text: p.nombre
            }));

            select.addOptions(options);
            select.refreshOptions(false);
        }

        function changeProvince(provinceId) {
            const provinceIdLeft = String(provinceId).padStart(4, '0');

            const lstDistricts = @json($districts);

            const lstDistrictsFiltered = lstDistricts.filter(
                d => d.provincia_id == provinceIdLeft
            );

            paintDistricts(lstDistrictsFiltered);
        }

        function paintDistricts(districts) {
            const select = window.districtSelect;

            select.clear();
            select.clearOptions();

            const options = districts.map(d => ({
                id: d.id,
                text: d.nombre
            }));

            select.addOptions(options);
            select.refreshOptions(false);
        }
    </script>
@endpush
