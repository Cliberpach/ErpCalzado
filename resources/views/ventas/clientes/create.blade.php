@extends('layout')

@section('content')

@section('ventas-active', 'active')
@section('clientes-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVO CLIENTE</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('ventas.cliente.index') }}">Clientes</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            @include('ventas.clientes.forms.form_create')
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('ventas.cliente.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>

                                    <button type="submit" id="btn_grabar" form="formRegistrarCliente"
                                        class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                </div>
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
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAS6qv64RYCHFJOygheJS7DvBDYB0iV2wI&libraries=geometry">
</script>
<script src="{{ mix('js/tomselect.js') }}"></script>
<link rel="stylesheet" href="{{ mix('css/filepond.css') }}">
<script src="{{ mix('js/filepond.js') }}"></script>

<script>
    const paramsCustomerCreate = {
        fpImg: null,
        departamento_api: '',
        provincia_api: '',
        distrito_api: '',
        map: null,
        onemarker: 0
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadSelectCustomers();
        loadFpCustomerCreate();
        //loadMap();
        events();
        configDefault();
    })

    function events() {

        //===== CHECK PERMITIR VENTAS AL CRÉDITO =======
        /*document.querySelector('#control_credito').addEventListener('change', (e) => {
            const marcado = e.target.checked;
            const inputLimite = document.querySelector('#limite_credito');
            if (marcado) {
                inputLimite.readOnly = false;
                inputLimite.classList.add('colorReadOnly');
            } else {
                inputLimite.readOnly = true;
                inputLimite.classList.remove('colorReadOnly');
                inputLimite.value = 0;
            }
        })*/

        window.departmentSelect.on('change', function(value) {
            changeDepartment(value);
        });

        window.provinceSelect.on('change', function(value) {
            changeProvince(value);
        });

        //======= CONSULTAR API DOCUMENTO DNI ========
        document.querySelector('#btn_consultar_documento').addEventListener('click', () => {

            const nro_documento = document.querySelector('#nro_document').value;
            const tipo_documento = document.querySelector('#type_identity_document').value;
            toastr.clear();

            if (tipo_documento != 6 && tipo_documento != 8) {
                toastr.error('SOLO SE PUEDE CONSULTAR DNI Y RUC');
                return;
            }

            if (tipo_documento == 6 && nro_documento.length != 8) {
                toastr.error('NRO DE DNI DEBE CONTAR CON 8 DÍGITOS');
                return;
            }

            if (tipo_documento == 8 && nro_documento.length != 11) {
                toastr.error('NRO DE RUC DEBE CONTAR CON 11 DÍGITOS');
                return;
            }

            consultarDocumento(tipo_documento, nro_documento);

        })

        document.querySelector('#formRegistrarCliente').addEventListener('submit', function(e) {

            const form = this;
            e.preventDefault();
            if (!validationFormCreate(form)) return;
            storeCustomer();
        });

        document.addEventListener('click', (e) => {
            if (e.target.closest('.btnVolver')) {
                const rutaIndex = '{{ route('ventas.cliente.index') }}';
                window.location.href = rutaIndex;
            }
        })

    }

    function loadFpCustomerCreate() {
        const inputImg = document.querySelector('#logo');

        paramsCustomerCreate.fpImg = FilePond.create(inputImg, {
            allowImagePreview: true,
            imagePreviewHeight: 120,
            imageCropAspectRatio: '1:1',
            styleLayout: 'compact',
            stylePanelAspectRatio: 0.5,
            storeAsFile: true,

            allowFileTypeValidation: true,
            acceptedFileTypes: [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/avif'
            ],

            allowFileSizeValidation: true,
            maxFileSize: '2MB',

            labelIdle: 'Arrastra una imagen o <span class="filepond--label-action">Buscar</span>',

            labelFileTypeNotAllowed: 'Solo se permiten imágenes PNG, JPG, WEBP o AVIF',
            fileValidateTypeLabelExpectedTypes: 'Formatos válidos: PNG, JPG, WEBP, AVIF',

            labelMaxFileSizeExceeded: 'El archivo es demasiado grande',
            labelMaxFileSize: 'El tamaño máximo permitido es 2 MB'
        });
    }

    function loadSelectCustomers() {

        const typeCustomerSelect = document.getElementById('type_customer');
        if (typeCustomerSelect && !typeCustomerSelect.tomselect) {
            window.typeCustomerSelect = new TomSelect(typeCustomerSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const typeDocumentSelect = document.getElementById('type_identity_document');
        if (typeDocumentSelect && !typeDocumentSelect.tomselect) {
            window.typeDocumentSelect = new TomSelect(typeDocumentSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const departmentSelect = document.getElementById('department');
        if (departmentSelect && !departmentSelect.tomselect) {
            window.departmentSelect = new TomSelect(departmentSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const provinceSelect = document.getElementById('province');
        if (provinceSelect && !provinceSelect.tomselect) {
            window.provinceSelect = new TomSelect(provinceSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }

        const districtSelect = document.getElementById('district');
        if (districtSelect && !districtSelect.tomselect) {
            window.districtSelect = new TomSelect(districtSelect, {
                valueField: 'id',
                labelField: 'description',
                searchField: ['description', 'id'],
                create: false,
                sortField: {
                    field: 'id',
                    direction: 'desc'
                },
                plugins: ['clear_button'],
                render: {
                    option: (item, escape) => `
                            <div>
                                ${escape(item.description)}
                            </div>
                        `,
                    item: (item, escape) => `
                            <div>${escape(item.description)}</div>
                        `
                }
            });
        }
    }

    function configDefault() {
        //======== SELECT TIPO DOCUMENTO ======
        window.typeDocumentSelect.setValue(6);
    }

    function storeCustomer() {
        Swal.fire({
            title: "DESEA REGISTRAR EL CLIENTE?",
            text: "Se creará un nuevo cliente!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, REGISTRAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                limpiarErroresValidacion('msgError');
                const token = document.querySelector('input[name="_token"]').value;
                const formRegistrarCliente = document.querySelector('#formRegistrarCliente');
                const formData = new FormData(formRegistrarCliente);
                const urlStore = route('ventas.cliente.store');

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nuevo cliente...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlStore, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    console.log(res);

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        const cliente_index = @json(route('ventas.cliente.index'));
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = cliente_index;
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR CLIENTE');
                    Swal.close();
                } finally {
                    Swal.close();
                }
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "OPERACIÓN CANCELADA",
                    text: "NO SE REALIZARON ACCIONES",
                    icon: "error"
                });
            }
        });
    }

    //======== CHANGE TIPO DOCUMENTO ======
    function changeTipoDoc(params) {
        const tipo_documento = document.querySelector('#type_identity_document').value;
        const inputNroDoc = document.querySelector('#nro_document');
        const btnConsultarDocumento = document.querySelector('#btn_consultar_documento');

        //======== DNI =======
        if (tipo_documento == 6) {
            inputNroDoc.value = '';
            inputNroDoc.readOnly = false;
            inputNroDoc.maxLength = 8;
            btnConsultarDocumento.disabled = false;
            inputNroDoc.classList.add('inputEnteroPositivo');
        }

        //======== RUC =======
        if (tipo_documento == 8) {
            inputNroDoc.value = '';
            inputNroDoc.readOnly = false;
            inputNroDoc.maxLength = 11;
            btnConsultarDocumento.disabled = false;
            inputNroDoc.classList.add('inputEnteroPositivo');
        }

        //====== CARNET EXTRANJERÍA =====
        if (![6, 8].includes(parseInt(tipo_documento))) {
            inputNroDoc.value = '';
            inputNroDoc.readOnly = false;
            inputNroDoc.maxLength = 20;
            btnConsultarDocumento.disabled = true;
            inputNroDoc.classList.remove('inputEnteroPositivo');
        }
    }

    //======= CONSULTAR DOCUMENTO IDENTIDAD =====
    async function consultarDocumento(typeDocument, nroDocument) {
        mostrarAnimacion();
        try {
            const token = document.querySelector('input[name="_token"]').value;

            const url = route('utilidades.consultarDocumento', {
                type_identity_document: typeDocument,
                nro_document: nroDocument
            })

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': token
                },
            });

            const res = await response.json();

            if (res.success) {

                if (!res.success) {
                    toastr.error(res.data.message);
                    return;
                }
                if (typeDocument == 6) {
                    setDatosDni(res.data);
                }
                if (typeDocument == 8) {
                    setDatosRuc(res.data);
                }

                toastr.info('OPERACIÓN COMPLETADA', res.message);
            } else {
                toastr.error(res.message, 'ERROR EN EL SERVIDOR AL CONSULTAR DOCUMENTO');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN CONSULTAR DOCUMENTO');
        } finally {
            ocultarAnimacion();
        }
    }

    /*
    ubigeo:
    [
      "01",
      "0101",
      "010101"
    ]
    */
    function setDatosRuc(data) {
        const nombre_o_razon_social = `${data.nombre_o_razon_social}`;
        const direccion_completa = data.direccion_completa;
        const ubigeo = data.ubigeo;

        document.querySelector('#name').value = nombre_o_razon_social;
        document.querySelector('#address').value = direccion_completa;

        //======= COLOCANDO UBIGEO =======
        let departmentId = parseInt(ubigeo[0]);
        let provinceId = parseInt(ubigeo[1]);
        let districtId = parseInt(ubigeo[2]);


        if (!isNaN(departmentId)) {
            window.departmentSelect.setValue(departmentId);
        }

        if (!isNaN(provinceId)) {
            window.provinceSelect.setValue(provinceId);
        }

        if (!isNaN(districtId)) {
            window.districtSelect.setValue(districtId);
        }

    }

    function setDatosDni(data) {
        const nombre_completo = `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;
        const direccion = data.direccion;

        document.querySelector('#name').value = nombre_completo;
        document.querySelector('#address').value = direccion;
    }

    function changeDepartment(department_id) {
        const lstProvinces = @json($provinces);
        const lstDistricts = @json($districts);
        let lstProvincesFiltered = [];

        if (department_id) {

            department_id = String(department_id).padStart(2, '0');

            lstProvincesFiltered = lstProvinces.filter((province) => {
                return parseInt(province.departamento_id) == parseInt(department_id);
            })

            window.provinceSelect.clear();
            window.provinceSelect.clearOptions();
            window.provinceSelect.addOptions(
                lstProvincesFiltered.map(province => ({
                    id: province.id,
                    description: province.nombre,
                }))
            );
            window.provinceSelect.refreshOptions(false);

            window.districtSelect.clear();
            window.districtSelect.clearOptions();
        }
    }

    function changeProvince(province_id) {

        const lstDistricts = @json($districts);

        let lstDistrictsFiltered = [];

        if (province_id) {

            province_id = String(province_id).padStart(4, '0');

            lstDistrictsFiltered = lstDistricts.filter((district) => {
                return parseInt(district.provincia_id) == parseInt(province_id);
            })

            window.districtSelect.clear();
            window.districtSelect.clearOptions();
            window.districtSelect.addOptions(
                lstDistrictsFiltered.map(district => ({
                    id: district.id,
                    description: district.nombre,
                }))
            );
            window.districtSelect.refreshOptions(false);
        }
    }

    function loadMap() {
        paramsCustomerCreate.map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: {
                lat: -8.1092027,
                lng: -79.0244529
            },
            gestureHandling: "greedy",
            zoomControl: false,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });
    }

    function validationFormCreate(form) {
        if (!form.checkValidity()) {

            const firstInvalid = form.querySelector(':invalid');

            if (firstInvalid) {

                const tabPane = firstInvalid.closest('.tab-pane');
                if (tabPane) {

                    const tabId = tabPane.id;
                    const tabButton = document.querySelector(`[data-target="#${tabId}"]`);

                    if (tabButton) {
                        tabButton.click();

                        setTimeout(() => {
                            firstInvalid.focus();
                            firstInvalid.reportValidity();
                        }, 200);
                    }
                }

                firstInvalid.focus();
                firstInvalid.reportValidity();
            }

            return false;
        }
        return true;
    }
</script>
@endpush
