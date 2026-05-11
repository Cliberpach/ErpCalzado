@extends('layout')

@section('almacenes-active', 'active')
@section('producto-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Productos')
@section('hero-title', 'Registrar Producto')
@section('hero-subtitle', 'Productos')

@section('content')
    @include('utils.modals.mdl_category.main')
    @include('utils.modals.mdl_brand.main')
    @include('utils.modals.mdl_model.main')
    @include('utils.modals.mdl_color.main')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        @include('almacenes.productos.forms.form_producto_create')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fancybox__container {
            z-index: 99999 !important;
        }

        .img-preview {
            max-width: 100%;
            max-height: 130px;
            object-fit: contain;
        }
    </style>
    <link href="{{ mix('css/filepond.css') }}" rel="stylesheet">
@endsection

@push('scripts')
    <script src="{{ mix('js/filepond.js') }}"></script>
    <script src="{{ mix('js/tomselect.js') }}"></script>
    <script>
        let ponds = [];
        let features = [];

        const btnAddFeature = document.getElementById('btn-add-feature');
        const tbodyFeatures = document.getElementById('tbody-features');

        const formRegProducto = document.querySelector('#form_registrar_producto');
        const inputColoresJSON = document.querySelector('#coloresJSON');
        const tableColores = document.querySelector('#table-colores');

        let coloresAsignados = [];
        let dtColores = null;

        //=========== CUANDO SE CARGUE EL HTML Y CSS HACER ESTO =========
        document.addEventListener('DOMContentLoaded', () => {
            const colores = @json($colores);
            loadFpImg();
            loadSelectProducts();
            loadFeatureIconsSelect();

            pintarTablaColores(colores);
            cargarDatatables();
            events();
            loadMdlCategory({
                categorySelect: window.categorySelect
            });
            loadMdlBrand({
                brandSelect: window.brandSelect
            });
            loadMdlModel({
                modelSelect: window.modelSelect
            });
            loadMdlColor({
                dtColores: dtColores
            });
        })

        function events() {

            //marcar check color
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('checkColor')) {
                    const colorId = e.target.getAttribute('data-color-id');
                    if (e.target.checked) {
                        addColor(colorId);
                    } else {
                        removeColor(colorId);
                    }
                }
            })

            //========== FORM REG PRODUCTO ==============
            formRegProducto.addEventListener('submit', (e) => {
                e.preventDefault();
                registrarProducto(e.target);
            })

            btnAddFeature.addEventListener('click', addFeature);

        }

        function loadFpImg() {
            const inputs = document.querySelectorAll(".filepond");

            inputs.forEach(input => {
                const pond = FilePond.create(input, {
                    allowMultiple: false,
                    instantUpload: false,
                    maxFileSize: "2MB",
                    acceptedFileTypes: [
                        "image/jpeg",
                        "image/png",
                        "image/webp",
                        "image/avif"
                    ],
                });

                ponds.push(pond);
            });
        }

        function loadSelectProducts() {
            window.categorySelect = loadSimpleSelect('categoria', '<i class="fas fa-tags text-primary"></i>');
            window.brandSelect = loadSimpleSelect('marca', '<i class="fas fa-certificate text-success"></i>');
            window.modelSelect = loadSimpleSelect('modelo', '<i class="fas fa-cubes text-primary"></i>');
            window.colorSelect = loadSimpleSelect('modelo', '<i class="fas fa-cubes text-primary"></i>');
        }

        function loadFeatureIconsSelect() {
            const simpleSelect = document.getElementById('icon');

            if (simpleSelect && !simpleSelect.tomselect) {

                const plugins = [];
                plugins.push('clear_button');

                const icons = [{
                        id: 'fas fa-star',
                        text: 'Estrella'
                    },
                    {
                        id: 'fas fa-gem',
                        text: 'Premium'
                    },
                    {
                        id: 'fas fa-shield-alt',
                        text: 'Protección'
                    },
                    {
                        id: 'fas fa-award',
                        text: 'Calidad'
                    },
                    {
                        id: 'fas fa-heart',
                        text: 'Favorito'
                    },
                    {
                        id: 'fas fa-fire',
                        text: 'Destacado'
                    },
                    {
                        id: 'fas fa-bolt',
                        text: 'Rápido'
                    },
                    {
                        id: 'fas fa-leaf',
                        text: 'Eco'
                    },
                    {
                        id: 'fas fa-globe',
                        text: 'Global'
                    },
                    {
                        id: 'fas fa-thumbs-up',
                        text: 'Recomendado'
                    },

                    {
                        id: 'fas fa-lock',
                        text: 'Seguro'
                    },
                    {
                        id: 'fas fa-truck',
                        text: 'Delivery'
                    },
                    {
                        id: 'fas fa-box-open',
                        text: 'Empaque'
                    },
                    {
                        id: 'fas fa-sync-alt',
                        text: 'Actualizado'
                    },
                    {
                        id: 'fas fa-check-circle',
                        text: 'Verificado'
                    },
                    {
                        id: 'fas fa-clock',
                        text: 'Rápido'
                    },
                    {
                        id: 'fas fa-medal',
                        text: 'Garantía'
                    },
                    {
                        id: 'fas fa-crown',
                        text: 'Exclusivo'
                    },
                    {
                        id: 'fas fa-magic',
                        text: 'Innovador'
                    },
                    {
                        id: 'fas fa-smile',
                        text: 'Cómodo'
                    },

                    {
                        id: 'fas fa-shoe-prints',
                        text: 'Calzado'
                    },
                    {
                        id: 'fas fa-tshirt',
                        text: 'Moda'
                    },
                    {
                        id: 'fas fa-shopping-bag',
                        text: 'Compra'
                    },
                    {
                        id: 'fas fa-tags',
                        text: 'Oferta'
                    },
                    {
                        id: 'fas fa-percentage',
                        text: 'Descuento'
                    },
                    {
                        id: 'fas fa-hand-holding-heart',
                        text: 'Confianza'
                    },
                    {
                        id: 'fas fa-wrench',
                        text: 'Resistente'
                    },
                    {
                        id: 'fas fa-sun',
                        text: 'Ligero'
                    },
                    {
                        id: 'fas fa-moon',
                        text: 'Elegante'
                    },
                    {
                        id: 'fas fa-battery-full',
                        text: 'Duradero'
                    }
                ];

                window.iconSelect = new TomSelect(simpleSelect, {

                    options: icons,

                    valueField: 'id',
                    labelField: 'text',
                    searchField: ['text', 'id'],

                    create: false,

                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    },

                    plugins: plugins,

                    render: {

                        option: (item, escape) => `
                    <div class="d-flex align-items-center">
                        <i class="${escape(item.id)} text-primary mr-2"></i>
                        <span>${escape(item.text)}</span>
                    </div>
                `,

                        item: (item, escape) => `
                    <div class="d-flex align-items-center">
                        <i class="${escape(item.id)} text-primary mr-2"></i>
                        <span>${escape(item.text)}</span>
                    </div>
                `
                    }
                });
            }
        }

        //============ guardar colores asignados ============
        const saveColorsAssigned = () => {
            //======== guardamos el array en el inputJSON de colores asignados ========
            inputColoresJSON.value = JSON.stringify(coloresAsignados);
        }

        //========== cargar datatables =======
        const cargarDataTablaColores = () => {
            dtColores = new DataTable('#tbl_producto_colores', {
                language: {
                    processing: "Cargando...",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ COLORES",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ COLORES",
                    infoEmpty: "MOSTRANDO 0 ELEMENTOS",
                    infoFiltered: "(FILTRADO de _MAX_ COLORES)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "NO HAY COLORES DISPONIBLES",
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
                }
            });
        }

        function addColorDataTable(color) {
            dtColores.row.add(
                [`<div style="text-align: left;font-weight:bold;">${color.id}</div>`,
                    `
                    <div class="form-check">
                        <input class="form-check-input checkColor" type="checkbox" value="" id="checkColor_${color.id}"
                        data-color-id="${color.id}">
                        <label class="form-check-label" for="checkColor_${color.id}">
                            ${color.descripcion}
                        </label>
                    </div>
                 `
                ]
            ).draw();
        }

        //agregar colores al array asignados
        function addColor(idColor) {
            if (!coloresAsignados.includes(idColor)) {
                coloresAsignados.push(idColor);
            }
        }

        function removeColor(idColor) {
            coloresAsignados = coloresAsignados.filter((c) => {
                return c != idColor
            })
        }


        function registrarProducto(formRegistrarProducto) {
            toastr.clear();
            Swal.fire({
                title: 'Opción Guardar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando producto...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {

                        limpiarErroresValidacion('msgErrorProducto');
                        const formData = new FormData(formRegistrarProducto);
                        formData.append('coloresJSON', JSON.stringify(coloresAsignados));
                        formData.append('features', JSON.stringify(features));

                        ponds.forEach((pond, index) => {
                            const files = pond.getFiles();

                            if (files.length > 0) {
                                formData.append(`imagen${index + 1}`, files[0].file);
                            }
                        });

                        const res = await axios.post(route('almacenes.producto.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            window.location = route('almacenes.producto.index');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                            Swal.close();
                        }

                    } catch (error) {
                        if (error.response) {
                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'error');
                                toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                            } else {
                                toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                            }
                        } else if (error.request) {
                            toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.',
                                'ERROR DE CONEXIÓN');
                        } else {
                            toastr.error(error.message, 'ERROR DESCONOCIDO');
                        }
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        }

        //========== cargar datatables =======
        const cargarDatatables = () => {
            dtColores = new DataTable('#tbl_producto_colores', {
                language: {
                    processing: "Cargando...",
                    search: "BUSCAR: ",
                    lengthMenu: "MOSTRAR _MENU_ COLORES",
                    info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ COLORES",
                    infoEmpty: "MOSTRANDO 0 ELEMENTOS",
                    infoFiltered: "(FILTRADO de _MAX_ COLORES)",
                    infoPostFix: "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "NO HAY COLORES DISPONIBLES",
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
                }
            });
        }

        function pintarTablaColores(lstColores) {
            const tbody = document.querySelector('#tbl_producto_colores tbody');

            let filas = '';

            lstColores.forEach((color) => {

                filas += `<tr>
                            <td style="text-align:start;font-weight:bold;">${color.id}</td>
                            <td>
                                <div class="form-check">
                                    <input  class="form-check-input checkColor" type="checkbox" value="" id="checkColor_${color.id}" data-color-id="${color.id}">
                                    <label class="form-check-label" for="checkColor_${color.id}">
                                        ${color.descripcion}
                                    </label>
                                </div>
                            </td>
                        </tr>`;
            })

            tbody.innerHTML = filas;
        }

        // ======================================
        // AGREGAR FEATURE
        // ======================================
        function addFeature() {
            toastr.clear();
            const featureItem = document.querySelector('.feature-item');

            // INPUTS
            const titleInput = featureItem.querySelector('input[name*="[title]"]');
            const descriptionInput = featureItem.querySelector('input[name*="[description]"]');

            // VALUES
            const title = titleInput.value.trim();

            const icon = window.iconSelect ?
                window.iconSelect.getValue() :
                '';

            const description = descriptionInput.value.trim();

            // VALIDAR TITULO
            if (!title) {
                toastr.error('Ingrea un título');
                return;
            }

            // VALIDAR REPETIDOS
            const exists = features.some(feature =>
                feature.title.trim().toLowerCase() === title.toLowerCase()
            );

            if (exists) {
                toastr.error('Ya existe una característica con ese título');
                return;
            }

            // OBJETO
            const feature = {
                id: Date.now(),
                title,
                icon,
                description,
                sort_order: features.length + 1
            };

            // AGREGAR
            features.push(feature);

            // PINTAR
            paintTblFeatures();

            // LIMPIAR
            clearFeatureInputs();
        }


        // ======================================
        // PINTAR TABLA
        // ======================================
        function paintTblFeatures() {

            // ACTUALIZAR ORDEN
            updateSortOrder();

            let html = '';

            // TABLA VACIA
            if (features.length === 0) {

                html = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No hay características registradas
                        </td>
                    </tr>
                `;

            } else {

                features.forEach((feature, index) => {

                    html += `
                <tr>

                    <!-- ACCION -->
                    <td class="text-center align-middle">

                        <div class="btn-group">

                            <!-- SUBIR -->
                            <button
                                type="button"
                                class="btn btn-info btn-sm"
                                onclick="moveFeatureUp(${index})"
                                ${index === 0 ? 'disabled' : ''}
                            >
                                <i class="fas fa-arrow-up"></i>
                            </button>

                            <!-- BAJAR -->
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                onclick="moveFeatureDown(${index})"
                                ${index === features.length - 1 ? 'disabled' : ''}
                            >
                                <i class="fas fa-arrow-down"></i>
                            </button>

                            <!-- ELIMINAR -->
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                onclick="removeFeature(${feature.id})"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>

                    </td>

                    <!-- TITULO -->
                    <td class="text-center align-middle">
                        ${feature.title}
                    </td>

                    <!-- ICONO -->
                    <td class="text-center align-middle">

                        ${
                            feature.icon
                                ? `<i class="${feature.icon}"></i>`
                                : '-'
                        }

                    </td>

                    <!-- DESCRIPCION -->
                    <td class="text-center align-middle">
                        ${feature.description || '-'}
                    </td>

                </tr>
            `;
                });
            }

            // PINTAR
            tbodyFeatures.innerHTML = html;

        }


        // ======================================
        // ACTUALIZAR SORT ORDER
        // ======================================
        function updateSortOrder() {

            features.forEach((feature, index) => {
                feature.sort_order = index + 1;
            });
        }


        // ======================================
        // SUBIR
        // ======================================
        function moveFeatureUp(index) {

            if (index === 0) return;

            [features[index - 1], features[index]] = [features[index], features[index - 1]];

            paintTblFeatures();
        }


        // ======================================
        // BAJAR
        // ======================================
        function moveFeatureDown(index) {

            if (index === features.length - 1) return;

            [features[index + 1], features[index]] = [features[index], features[index + 1]];

            paintTblFeatures();
        }


        // ======================================
        // ELIMINAR
        // ======================================
        function removeFeature(id) {

            features = features.filter(feature => feature.id !== id);

            paintTblFeatures();
        }




        // ======================================
        // LIMPIAR INPUTS
        // ======================================
        function clearFeatureInputs() {

            const featureItem = document.querySelector('.feature-item');

            // TITLE
            featureItem.querySelector('input[name*="[title]"]').value = '';

            // DESCRIPTION
            featureItem.querySelector('input[name*="[description]"]').value = '';

            // TOMSELECT
            if (window.iconSelect) {
                window.iconSelect.clear();
            }
        }
    </script>
@endpush
