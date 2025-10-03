<template>
    <transition name="fade" @before-enter="beforeEnter" @enter="enter" @before-leave="beforeLeave" @leave="leave">
        <div v-if="visible" class="modal-overlay" style="z-index: 99999 !important;">

            <div id="mdlEditItem" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5); z-index: 999999 !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; justify-content: center; align-items: center;">

                <div class="modal-dialog" style="max-height: 90vh;">
                    <div class="modal-content d-flex flex-column" style="max-height: 90vh; overflow: hidden;">

                        <!-- Encabezado -->
                        <div
                            class="modal-header d-flex flex-column align-items-center position-relative w-100 border-0">
                            <div class="text-center w-100 mb-2">
                                <i class="fas fa-shoe-prints fa-3x text-muted"></i>
                            </div>
                            <h3 class="mb-2 text-center w-100">Editar Ítem</h3>
                            <div class="text-muted text-center w-100">
                                <strong>Producto:</strong> {{ productoEditar.producto_nombre }}<br>
                                <strong>Color:</strong> {{ productoEditar.color_nombre }}
                            </div>
                            <button @click="closeModal" class="close-button btn btn-link p-0"
                                style="position: absolute; top: 10px; right: 10px; font-size: 1.5rem;">×</button>
                        </div>

                        <!-- Cuerpo -->
                        <div class="modal-body" style="overflow-y: auto; flex: 1 1 auto;">

                            <!-- <div class="row mb-3">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label for="" class="font-weight-bold">PRECIO VENTA</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <input :value="productoLocal.precio_venta_nuevo"
                                            @input="editarPrecioVenta($event)" type="text" class="form-control"
                                            placeholder="Precio" aria-label="Precio" aria-describedby="basic-addon1"
                                            ref="inputPrecioVentaEditar" />
                                    </div>
                                </div>
                            </div> -->

                            <div class="row">
                                <div class="col-12">
                                    <label for="" style="font-weight: bold;">TALLAS</label>
                                    <div class="table-responsive">
                                        <table ref="tablaTallas" id="tablaTallas"
                                            class="table table-sm table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-start">TALLA</th>
                                                    <th class="text-start">STOCK</th>
                                                    <th class="text-start">CANT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in cantidadPorTalla" :key="index">
                                                    <td class="text-start">{{ item.talla_nombre }}</td>
                                                    <td class="text-start">{{ item.stock_logico }}</td>
                                                    <td class="text-start">
                                                        <div v-if="item.stock_logico > 0">
                                                            <input min="0" type="text" style="width: 60px;"
                                                                class="form-control" v-model="item.cantidad"
                                                                @input="item.cantidad = (/^[0-9]+$/.test(item.cantidad)) ? parseInt(item.cantidad) : 0" />
                                                        </div>
                                                        <div v-else>

                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer border-0" style="flex-shrink: 0;">
                            <button class="btn btn-primary" @click="confirm">
                                <i class="fas fa-check mr-2"></i> Guardar
                            </button>
                            <button class="btn btn-secondary" @click="closeModal">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </transition>
</template>

<script>

export default {
    name: 'Modal',
    props: {
        title: {
            type: String,
            default: 'Modal Title',
        },
        visible: {
            type: Boolean,
            default: false,
        },
        tallas: {
            type: Array,
            default: []
        },
        tallasProducto: {
            type: Array,
            default: () => [],
        },
        productoEditar: {
            type: Object,
            default: () => { }
        },
        detalleVenta: {
            type: Array,
            default: () => [],
        }
    },
    data() {
        return {
            cantidadPorTalla: [],
            precioVentaNuevoEditado: null,
            productoLocal: {},
            dtTallas: null,
        }
    },
    emits: ['close'],
    watch: {
        visible(newVal) {
            if (newVal) {
                this.onModalOpen();
            }
        },
        productoEditar: {
            handler(nuevoProducto) {
                if (nuevoProducto) {
                    this.productoLocal = JSON.parse(JSON.stringify(nuevoProducto));
                }
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        inicializarDataTable() {
            this.$nextTick(() => {
                if (this.dtTallas) {
                    this.dtTallas.destroy();
                    this.dtTallas = null;
                }

                if (this.$refs.tablaTallas) {
                    this.dtTallas = $(this.$refs.tablaTallas).DataTable({
                        pageLength: 20,
                        "paging": false,
                        language: {
                            decimal: ",",
                            thousands: ".",
                            processing: "Procesando...",
                            search: "Buscar:",
                            lengthMenu: "Mostrar _MENU_ registros",
                            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Mostrando 0 a 0 de 0 registros",
                            infoFiltered: "(filtrado de _MAX_ registros totales)",
                            infoPostFix: "",
                            loadingRecords: "Cargando...",
                            zeroRecords: "No se encontraron registros coincidentes",
                            emptyTable: "No hay datos disponibles en la tabla",
                            paginate: {
                                first: "Primero",
                                previous: "Anterior",
                                next: "Siguiente",
                                last: "Último"
                            },
                            aria: {
                                sortAscending: ": activar para ordenar ascendente",
                                sortDescending: ": activar para ordenar descendente"
                            }
                        }
                    });
                } else {
                    console.warn("tablaTallas ref no encontrada");
                }
            });
        },
        async onModalOpen() {
            this.$parent.mostrarAnimacionVenta();
            const stocksTallas = await this.getTallas(this.productoEditar);
            this.setCantTallaOriginal(stocksTallas);
            this.$parent.ocultarAnimacionVenta();
        },
        editarPrecioVenta(event) {
            let valor = event.target.value;

            // Reemplaza caracteres no válidos (permite solo números y punto)
            valor = valor.replace(/[^0-9.]/g, '');

            if (valor.startsWith('.')) {
                valor = '';
            }

            // Limita a un solo punto decimal
            const partes = valor.split('.');
            if (partes.length > 2) {
                valor = partes[0] + '.' + partes[1];
            }

            // Limita a 2 decimales si existe parte decimal
            if (partes[1]) {
                partes[1] = partes[1].substring(0, 2);
                valor = partes[0] + '.' + partes[1];
            }

            event.target.value = valor;
            this.productoLocal.precio_venta_nuevo = parseFloat(valor);

        },

        getCantidadForTalla(tallaId) {
            const tallaEncontrada = this.tallasProducto.find(talla => talla.talla_id == tallaId);
            return tallaEncontrada ? tallaEncontrada.cantidad : '';
        },
        closeModal() {
            //this.setCantTallaOriginal();
            this.$emit('close');
        },
        async confirm() {

            //====== EDITAR ITEM =======
            toastr.clear();
            this.$parent.mostrarAnimacionVenta();

            const validacion = this.validacionEditarItem(this.productoLocal);
            if (!validacion) {
                this.$parent.ocultarAnimacionVenta();
                return;
            }

            //========= EDITAR PRECIO VENTA ========
            this.calcularMontosProducto(this.productoLocal);

            //======== EDITAR TALLAS =======
            const res = await this.editarTallas(this.productoLocal);
            if (!res) return;

            this.calcularSubTotalLinea(this.productoLocal);

            console.log('PRODUCTO EDITADO', this.productoLocal);
            this.$emit('update-producto', this.productoLocal);

            this.$parent.ocultarAnimacionVenta();
            this.closeModal();

        },
        async editarTallas(_producto) {

            let validacion = true;
            const almacen_id = _producto.almacen_id;

            //======= CANTIDADES NUEVAS =====
            const cantidadPorTallaSinCeros = this.cantidadPorTalla.filter(ct => ct.cantidad != '0' && ct.cantidad != '');

            let lstCantidadesEdit = [];

            this.cantidadPorTalla.forEach((dt) => {
                let producto = {};
                let indiceCantAnt = -1;

                if (_producto.tallas) {
                    indiceCantAnt = _producto.tallas.findIndex(t => t.talla_id == dt.talla_id);
                }

                if (indiceCantAnt === -1) {
                    producto = {
                        producto_id: _producto.producto_id,
                        color_id: _producto.color_id,
                        talla_id: dt.talla_id,
                        cantidad_actual: dt.cantidad,
                        cantidad_anterior: 0
                    };
                } else {
                    producto = {
                        producto_id: _producto.producto_id,
                        color_id: _producto.color_id,
                        talla_id: dt.talla_id,
                        cantidad_actual: dt.cantidad,
                        cantidad_anterior: _producto.tallas[indiceCantAnt].cantidad
                    };
                }

                lstCantidadesEdit.push(producto);
            });

            lstCantidadesEdit = lstCantidadesEdit.filter(ct =>
                !(ct.cantidad_actual == '0' && ct.cantidad_anterior == '0') &&
                !(ct.cantidad_actual == '' && ct.cantidad_anterior == '')
            );

            //======== ACTUALIZANDO STOCK EDIT =======
            const res = await axios.post(route('ventas.documento.actualizarStockEdit'),
                { almacenId: almacen_id, lstProductos: JSON.stringify(lstCantidadesEdit) });

            if (res.data.success) {
                toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
            } else {
                this.$parent.ocultarAnimacionVenta();
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                return false;
            }

            console.log(cantidadPorTallaSinCeros);

            if (cantidadPorTallaSinCeros.length === 0) {

                _producto.tallas.length = 0;

            } else {
                _producto.tallas = cantidadPorTallaSinCeros;
            }

            return validacion;

        },
        validacionEditarItem(producto) {
            let validacion = true;
            /*if (producto.precio_venta_nuevo > producto.precio_venta) {
                toastr.error('EL PRECIO DE VENTA DEBE SER MENOR O IGUAL AL ORIGINAL');
                validacion = false;
                this.$refs.inputPrecioVentaEditar.focus();
            }*/

            /*if (producto.precio_venta_nuevo <= 0) {
                toastr.error('EL PRECIO DE VENTA DEBE SER MAYOR A 0');
                validacion = false;
                this.$refs.inputPrecioVentaEditar.focus();
            }*/

            /*if (isNaN(producto.precio_venta_nuevo)) {
                toastr.error('PRECIO VENTA NO VÁLIDO');
                validacion = false;
                this.$refs.inputPrecioVentaEditar.focus();
            }*/
            return validacion;
        },
        calcularMontosProducto(producto) {
            producto.porcentaje_descuento = parseFloat(
                (100 * (1 - (producto.precio_venta_nuevo / producto.precio_venta))).toFixed(2)
            );
            producto.monto_descuento = producto.precio_venta - producto.precio_venta_nuevo;
        },
        calcularSubTotalLinea(producto) {
            let subtotal = 0;
            let subtotal_nuevo = 0;
            producto.tallas.forEach((talla) => {
                subtotal += parseFloat(producto.precio_venta * talla.cantidad);
                subtotal_nuevo += parseFloat(producto.precio_venta_nuevo * talla.cantidad);
            })
            producto.subtotal = subtotal;
            producto.subtotal_nuevo = subtotal_nuevo;
        },
        beforeEnter(el) {
            el.style.opacity = 0;
            el.style.transform = 'scale(0.8)';
        },
        enter(el, done) {
            el.offsetHeight;
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            el.style.opacity = 1;
            el.style.transform = 'scale(1)';
            done();
        },
        setCantTallaOriginal(stocksTallas) {
            const tallas = [];

            this.tallas.forEach((t) => {
                const talla_find = this.tallasProducto.find((p) => p.talla_id == t.id);
                const stockTalla = stocksTallas.find(st => st.talla_id == t.id);
                tallas.push({
                    talla_id: t.id,
                    talla_nombre: t.descripcion,
                    cantidad: talla_find ? talla_find.cantidad : 0,
                    stock_logico: parseInt(stockTalla ? stockTalla.stock_logico : 0)
                });
            });

            this.cantidadPorTalla = tallas;

            this.inicializarDataTable();

        },

        beforeLeave(el) {
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease'; // Set transition for leave
        },
        leave(el, done) {
            el.style.opacity = 0;
            el.style.transform = 'scale(0.8)';
            done();
        },
        async getTallas(producto) {
            console.log('TRAYENDO TALLAS', producto);
            try {
                const res = await axios.get(route('venta.cambiarTallas.getTallas',
                    { almacen_id: producto.almacen_id, producto_id: producto.producto_id, color_id: producto.color_id }));

                if (!res.data.success) {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    return null;
                } else {
                    return res.data.tallas;
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER TALLAS');
                return null;
            }
        }
    },
};
</script>

<style scoped>
.modal-overlay {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}


.modal-dialog {
    background: white;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 80vh;
    height: 70vh;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    opacity: 1;
    transform: scale(1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.close-button {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

.modal-body {
    margin-bottom: 15px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary {
    background: #007bff;
    color: #fff;
}

.btn-secondary {
    background: #6c757d;
    color: #fff;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter,
.fade-leave-to

/* .fade-leave-active in <2.1.8 */
    {
    opacity: 0;
    transform: scale(0.8);
}
</style>
