@extends('layout')

@section('almacenes-active', 'active')
@section('producto-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Editar Producto')
@section('hero-title', 'Editar Producto')
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
                        @include('almacenes.productos.forms.form_producto_edit')
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

        //====== VARIABLES ===============
        const formActualizarProducto = document.querySelector('#form_actualizar_producto');
        const inputColoresJSON = document.querySelector('#coloresJSON');
        const tableColores = document.querySelector('#table-colores');
        const coloresPrevios = @json($colores_asignados);

        const btnAddFeature = document.getElementById('btn-add-feature');
        const tbodyFeatures = document.getElementById('tbody-features');

        let coloresAsignados = [];
        let dtColores = null;

        //=========== CUANDO SE CARGUE EL HTML Y CSS HACER ESTO =========
        document.addEventListener('DOMContentLoaded', () => {

            setFeaturesPreview(@json($features));

            loadFpImg();
            loadSelectProducts();
            loadFeatureIconsSelect();

            limpiarTabla('tbl_producto_colores');
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

            //========== FORM ACTUALIZAR PRODUCTO ==============
            formActualizarProducto.addEventListener('submit', (e) => {
                e.preventDefault();
                actualizarProducto(e.target);
            })

            btnAddFeature.addEventListener('click', addFeature);
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


        //========= marcamos los colores que ya tenía asignado el producto ===========
        const marcarColoresPrevios = () => {
            //===== obtener todas las filas del datatable colores como jquery formato =========
            const rows = dtColores.rows().nodes().to$();
            //======= recorrer las filas =========
            rows.each(function() {
                //========= obtener el data-color-id de cada checkbox y convertirlo a number =========
                const idColorCheck = parseInt($(this).children().eq(1).children().eq(0).children().eq(0).attr(
                    'data-color-id'));
                //======= si está incluido en el array de colores asignados previos entonces marcalo ========
                if (coloresAsignados.includes(idColorCheck)) {
                    $(this).children().eq(1).children().eq(0).children().eq(0).prop('checked', true);
                }
            });
        }


        //======= AÑADIR NUEVO COLOR AL DATATABLE =========
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

        //======= AGREGAR COLORES AL ARRAY ASIGNADOS ======
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

        //load colores previos
        function loadColoresPrevios() {
            coloresPrevios.forEach((c) => {
                addColor(c.id);
            })
        }

        //===== ACTUALIZAR PRODUCTO ========
        async function actualizarProducto(formActualizarProducto) {
            toastr.clear();
            Swal.fire({
                title: 'Actualizar producto',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Actualizando producto...",
                        text: "Por favor, espera mientras procesamos la solicitud.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {

                        limpiarErroresValidacion('msgErrorProducto');

                        const producto_id = @json($producto->id);
                        const formData = new FormData(formActualizarProducto);

                        formData.append('coloresJSON', JSON.stringify(coloresAsignados));
                        formData.append('features', JSON.stringify(features));

                        let urlUpdateProducto = `{{ route('almacenes.producto.update', ['id' => ':id']) }}`;
                        urlUpdateProducto = urlUpdateProducto.replace(':id', producto_id);

                        const res = await axios.post(urlUpdateProducto, formData);

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
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        }

        function pintarTablaColores(lstColores) {
            const tbody = document.querySelector('#tbl_producto_colores tbody');

            let filas = '';

            lstColores.forEach((color) => {

                let indiceProductoColor = coloresAsignados.findIndex((c) => {
                    return c == color.id;
                })

                const marcar = indiceProductoColor !== -1 ? true : false;

                filas += `<tr>
                            <td>${color.id}</td>
                            <td>
                                <div class="form-check">
                                    <input ${marcar?'checked':''}  class="form-check-input checkColor" type="checkbox" value="" id="checkColor_${color.id}" data-color-id="${color.id}">
                                    <label class="form-check-label" for="checkColor_${color.id}">
                                        ${color.descripcion}
                                    </label>
                                </div>
                            </td>
                        </tr>`;
            })

            tbody.innerHTML = filas;

        }

        async function getColoresProducto(almacen_id) {
            toastr.clear();
            mostrarAnimacion();

            try {

                const producto_id = @json($producto->id);

                if (!producto_id || !almacen_id) {
                    destruirDataTable(dtColores);
                    limpiarTabla('tbl_producto_colores');
                    cargarDatatables();
                    coloresAsignados = [];
                    return;
                }

                const res = await axios.get(route('almacenes.producto.getColores', {
                    almacen_id,
                    producto_id
                }));

                if (res.data.success) {

                    coloresAsignados = [];
                    const producto_colores = res.data.data;
                    producto_colores.forEach((c) => {
                        addColor(c.color_id);
                    })

                    destruirDataTable(dtColores);
                    limpiarTabla('tbl_producto_colores');
                    pintarTablaColores(@json($colores));
                    cargarDatatables();

                    toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');

                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA PETICIÓN OBTENER COLORES DEL PRODUCTO EN ALMACÉN');
            } finally {
                ocultarAnimacion();
            }
        }

        function loadFpImg() {
            const inputs = document.querySelectorAll(".filepond");
            const producto = @json($producto);

            inputs.forEach((input, index) => {

                const campo = `img${index + 1}_ruta`;
                const url = producto[campo] ? @json(asset('')) + `${producto[campo]}` : null;

                const pond = FilePond.create(input, {
                    allowMultiple: false,
                    instantUpload: false,

                    acceptedFileTypes: [
                        'image/jpeg',
                        'image/webp',
                        'image/avif'
                    ],

                    maxFileSize: '2MB',

                    labelMaxFileSizeExceeded: 'El archivo es demasiado grande',
                    labelMaxFileSize: 'El tamaño máximo permitido es 2MB',

                    labelFileTypeNotAllowed: 'Formato no permitido',
                    fileValidateTypeLabelExpectedTypes: 'Solo JPG, JPEG, WEBP o AVIF',

                    files: url ? [{
                        source: url
                    }] : []
                });

                ponds.push(pond);
            });
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


        // ======================================
        // SET FEATURES PREVIEW
        // ======================================
        function setFeaturesPreview(productFeatures = []) {

            // VALIDAR ARRAY
            if (!Array.isArray(productFeatures)) {
                features = [];
                paintTblFeatures();
                return;
            }

            // MAPEAR DATA
            features = productFeatures.map(feature => ({

                id: feature.id,

                title: feature.title,

                icon: feature.icon,

                description: feature.description,

                sort_order: feature.sort_order ?? 0,

                status: feature.status ?? 1,
            }));

            // ORDENAR
            features.sort((a, b) => a.sort_order - b.sort_order);

            // PINTAR
            paintTblFeatures();
        }
    </script>
@endpush
