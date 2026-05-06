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
        //====== VARIABLES ===============
        const formCrearCategoria = document.querySelector('#crear_categoria');
        const formCrearMarca = document.querySelector('#crear_marca');
        const formCrearModelo = document.querySelector('#crear_modelo');
        const formCrearColor = document.querySelector('#crear_color');
        const formActualizarProducto = document.querySelector('#form_actualizar_producto');
        const tokenValue = document.querySelector('input[name="_token"]').value;
        const selectCategorias = document.querySelector('#categoria');
        const selectMarcas = document.querySelector('#marca');
        const selectModelos = document.querySelector('#modelo');
        const inputColoresJSON = document.querySelector('#coloresJSON');
        const tableColores = document.querySelector('#table-colores');
        const coloresPrevios = @json($colores_asignados);

        let coloresAsignados = [];
        let dtColores = null;

        //=========== CUANDO SE CARGUE EL HTML Y CSS HACER ESTO =========
        document.addEventListener('DOMContentLoaded', () => {
            loadFpImg();
            loadSelectProducts();

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
        }

        function loadSelectProducts() {
            window.categorySelect = loadSimpleSelect('categoria', '<i class="fas fa-tags text-primary"></i>');
            window.brandSelect = loadSimpleSelect('marca', '<i class="fas fa-certificate text-success"></i>');
            window.modelSelect = loadSimpleSelect('modelo', '<i class="fas fa-cubes text-primary"></i>');
            window.colorSelect = loadSimpleSelect('modelo', '<i class="fas fa-cubes text-primary"></i>');
        }

        //===== PINTAR ERRORES AL CREAR COLOR =====
        const pintarErroresColor = (errores_color) => {
            let message = '';
            errores_color.forEach((m, index) => {
                message += m;
                if (index < errores_color.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR CATEGORÍA =====
        const pintarErroresCategoria = (errores_marca) => {
            let message = '';
            errores_marca.forEach((m, index) => {
                message += m;
                if (index < errores_marca.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR MARCA =====
        const pintarErroresMarca = (errores_marca) => {
            let message = '';
            errores_marca.forEach((m, index) => {
                message += m;
                if (index < errores_marca.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR MODELO =====
        const pintarErroresModelo = (errores_modelo) => {
            let message = '';
            errores_modelo.forEach((m, index) => {
                message += m;
                if (index < errores_modelo.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }


        //==== actualizar select de categorías ============
        const updateSelectCategorias = (categorias_actualizadas) => {
            let items = '<option></option>';
            categorias_actualizadas.forEach((c) => {
                const selected = "{{ old('categoria') == '" + c.id + "' ? 'selected' : '' }}";
                items += `<option value="${c.id}" ${selected}>${c.descripcion}</option>`;
            });
            selectCategorias.innerHTML = items;
        };

        //====== actualizar select de marcas =========
        const updateSelectMarcas = (marcas_actualizadas) => {
            let items = '<option></option>';
            marcas_actualizadas.forEach((m) => {
                const selected = "{{ old('marca') == '" + m.id + "' ? 'selected' : '' }}";
                items += `<option value="${m.id}" ${selected}>${m.marca}</option>`;
            });
            selectMarcas.innerHTML = items;
        };


        //========= actualizar select de modelos ===========
        const updateSelectModelos = (modelos_actualizados) => {
            let items = '<option></option>';
            modelos_actualizados.forEach((m) => {
                const selected = "{{ old('marca') == '" + m.id + "' ? 'selected' : '' }}";
                items += `<option value="${m.id}" ${selected}>${m.descripcion}</option>`;
            });
            selectModelos.innerHTML = items;
        };


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
    </script>
@endpush
