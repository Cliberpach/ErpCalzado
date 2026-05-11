<div class="modal inmodal" id="mdlProductosPromocion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated fadeIn">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white">

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <i class="fas fa-tags modal-icon"></i>

                <h4 class="modal-title">Asignar productos</h4>
                <small>Gestión de productos para la promoción</small>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- INFO PROMOCIÓN -->
                <div class="card mb-3 border-left-success">
                    <div class="card-body py-2">

                        <div class="row">

                            <div class="col-md-6">
                                <small class="text-muted">Promoción</small>
                                <h5 class="mb-0 font-weight-bold" id="promo_nombre">

                                </h5>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted">Descuento</small>
                                <h5 class="mb-0" id="promo_descuento">

                                </h5>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted">Mín. pares</small>
                                <h5 class="mb-0" id="promo_minimo">

                                </h5>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="table-responsive">
                    @include('mantenimiento.promociones.tables.tbl_products')
                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-success btn-sm" id="btn-add-products">
                    <i class="fas fa-save"></i> Guardar selección
                </button>

                <button class="btn btn-danger btn-sm" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>

            </div>

        </div>
    </div>
</div>
<script>
    let paramsMdlProducts = {
        id: null,
        dtProducts: null,
        lstProductsSelected: []
    };

    function eventsMdlProducts() {
        loadDtProducts();

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('chk-producto')) {
                handleProductCheckboxChange(e);
            }
        });

        document.querySelector('#btn-add-products').addEventListener('click', (e) => {
            saveProductsPromocion(paramsMdlProducts.id);
        })
    }

    async function openMdlProducts(id) {
        paramsMdlProducts.id = id;
        const row = getRowById(dtPromociones, id);
        if (row) {
            setPromoHeader(row);
        }

        await getPromocionProducts(id);
        $('#mdlProductosPromocion').modal('show');
    }

    function setPromoHeader(row) {
        document.getElementById('promo_nombre').innerText = row.nombre ?? '';

        document.getElementById('promo_descuento').innerText =
            row.descuento ?? '';

        document.getElementById('promo_minimo').innerText =
            row.cantidad_minima ?? '';
    }

    function handleProductCheckboxChange(e) {

        const checkbox = e.target;

        if (!checkbox || !checkbox.dataset.id) return;

        const id = Number(checkbox.dataset.id);

        if (Number.isNaN(id)) return;

        if (checkbox.checked) {

            if (!paramsMdlProducts.lstProductsSelected.includes(id)) {
                paramsMdlProducts.lstProductsSelected.push(id);
            }

        } else {

            paramsMdlProducts.lstProductsSelected =
                paramsMdlProducts.lstProductsSelected.filter(item => item !== id);
        }

        console.log('Seleccionados:', paramsMdlProducts.lstProductsSelected);
    }

    function loadDtProducts() {
        const url = '{{ route('utilidades.dataTableProducts') }}';

        paramsMdlProducts.dtProducts = new DataTable('#dt-products', {
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                type: 'GET',
            },
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'id',
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const checked = paramsMdlProducts.lstProductsSelected.includes(parseInt(data)) ?
                            'checked' :
                            '';

                        return `
                            <input type="checkbox"
                                class="chk-producto"
                                data-id="${data}"
                                ${checked}>
                        `;
                    }
                },
                {
                    data: 'nombre',
                    className: "text-left",
                    name: "p.nombre"
                },
            ],
            language: {
                url: "{{ asset('Spanish.json') }}"
            }
        });
    }

    async function saveProductsPromocion(promocionId) {
        toastr.clear();
        Swal.fire({
            title: "¿Guardar productos en la promoción?",
            text: "Se actualizará la lista de productos",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#007bff",
            confirmButtonText: "Sí",
            cancelButtonText: "Cancelar"
        }).then(async (result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: "Guardando...",
                    text: "Procesando información",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {

                    let formData = new FormData();
                    formData.append('productos', JSON.stringify(paramsMdlProducts.lstProductsSelected));
                    formData.append('_method', 'PUT');

                    const res = await axios.post(
                        route('mantenimiento.promociones.addProducts', {
                            id: promocionId
                        }),
                        formData
                    );

                    if (res.data.success) {
                        toastr.success(res.data.message, 'Operación completada');
                        $('#mdlProductosPromocion').modal('hide');
                    } else {
                        toastr.error(res.data.message);
                    }

                } catch (error) {

                    if (error.response?.status === 422) {
                        toastr.error('Errores de validación');
                    } else {
                        toastr.error(error.message);
                    }

                } finally {
                    Swal.close();
                }
            }
        });
    }

    async function getPromocionProducts(promocionId) {

        try {

            mostrarAnimacion();

            const url = route('mantenimiento.promociones.getProductsPromocion', promocionId);

            const res = await axios.get(url);

            if (res.data.success) {

                paramsMdlProducts.lstProductsSelected = res.data.productos;

                paramsMdlProducts.dtProducts.ajax.reload(null, false);

            } else {
                toastr.error(res.data.message || 'Error al cargar productos');
            }

        } catch (error) {

            toastr.error(
                error.response?.data?.message || 'Error de conexión',
                'ERROR'
            );

        } finally {
            ocultarAnimacion();
        }
    }
</script>
