<style>
.inputCantidadValido {
    border-color: rgb(59, 63, 255) !important;
}

.inputCantidadIncorrecto {
    border-color: red !important;
}

.inputCantidadColor {
    border-color: rgb(48, 48, 88);
}

.colorStockLogico {
    background-color: rgb(243, 248, 255);
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.fulfilling-bouncing-circle-spinner,
.fulfilling-bouncing-circle-spinner * {
    box-sizing: border-box;
}

.fulfilling-bouncing-circle-spinner {
    height: 60px;
    width: 60px;
    position: relative;
    animation: fulfilling-bouncing-circle-spinner-animation infinite 4000ms ease;
}

.fulfilling-bouncing-circle-spinner .orbit {
    height: 60px;
    width: 60px;
    position: absolute;
    top: 0;
    left: 0;
    border-radius: 50%;
    border: calc(60px * 0.03) solid #ff1d5e;
    animation: fulfilling-bouncing-circle-spinner-orbit-animation infinite 4000ms ease;
}

.fulfilling-bouncing-circle-spinner .circle {
    height: 60px;
    width: 60px;
    color: #ff1d5e;
    display: block;
    border-radius: 50%;
    position: relative;
    border: calc(60px * 0.1) solid #ff1d5e;
    animation: fulfilling-bouncing-circle-spinner-circle-animation infinite 4000ms ease;
    transform: rotate(0deg) scale(1);
}

@keyframes fulfilling-bouncing-circle-spinner-animation {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

@keyframes fulfilling-bouncing-circle-spinner-orbit-animation {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1);
    }

    62.5% {
        transform: scale(0.8);
    }

    75% {
        transform: scale(1);
    }

    87.5% {
        transform: scale(0.8);
    }

    100% {
        transform: scale(1);
    }
}

@keyframes fulfilling-bouncing-circle-spinner-circle-animation {
    0% {
        transform: scale(1);
        border-color: transparent;
        border-top-color: inherit;
    }

    16.7% {
        border-color: transparent;
        border-top-color: initial;
        border-right-color: initial;
    }

    33.4% {
        border-color: transparent;
        border-top-color: inherit;
        border-right-color: inherit;
        border-bottom-color: inherit;
    }

    50% {
        border-color: inherit;
        transform: scale(1);
    }

    62.5% {
        border-color: inherit;
        transform: scale(1.4);
    }

    75% {
        border-color: inherit;
        transform: scale(1);
        opacity: 1;
    }

    87.5% {
        border-color: inherit;
        transform: scale(1.4);
    }

    100% {
        border-color: transparent;
        border-top-color: inherit;
        transform: scale(1);
    }
}
</style>
<template>


    <div class="row">

        <div class="col-lg-12">

            <!-- PRIMER PANEL  -->
            <div class="panel panel-primary">
                <div id="overlay" class="overlay">
                    <div class="fulfilling-bouncing-circle-spinner">
                        <div class="circle"></div>
                        <div class="orbit"></div>
                    </div>
                </div>
                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><b>Seleccionar productos</b></h4>
                    <button type="button" class="btn btn-success btn-sm"
                        data-toggle="modal" data-target="#modal_consultar_stock">
                        <i class="fas fa-boxes"></i> Consultar Stocks
                    </button>
                </div>
                <div class="panel-body ibox-content">

                    <div class="row" v-if="idcotizacion == 0">

                        <div class="col-lg-6 col-md-8 col-sm-12 mt-2">
                            <label style="font-weight:bold; font-size:11px;">CATEGORÍA - MARCA - MODELO - PRODUCTO</label>
                            <v-select
                                v-model="productoSeleccionado"
                                :options="lst_productos"
                                :reduce="p => p.id"
                                label="producto_completo"
                                :filterable="false"
                                placeholder="Escribe para buscar..."
                                @search="onSearchProducto">
                                <template #no-options="{ search }">
                                    <span style="font-size:11px;">{{ search ? 'Sin resultados' : 'Escribe para buscar...' }}</span>
                                </template>
                            </v-select>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mt-3">
                            <label class="required" style="font-weight: bold;">PRECIO VENTA</label>
                            <v-select v-model="precioVentaSeleccionado" :options="preciosVenta" label="text"
                                :reduce="pv => pv.sale_price" placeholder="Seleccionar">
                            </v-select>
                        </div>

                        <div class="col-12 mt-3">
                            <label style="font-weight: bold;">CÓDIGO BARRA</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                </div>
                                <input v-model="buscarCodigoBarra" class="inputBarCode form-control" maxlength="8"
                                    type="text" placeholder="Escriba el código de barra" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>

                        <input type="hidden" name="producto_id" id="producto_id">
                        <input type="hidden" name="producto_unidad" id="producto_unidad">
                        <input type="hidden" name="producto_json" id="producto_json">

                    </div>
                    <hr>
                    <div class="row mb-2" v-if="productosPorModelo.producto_colores && productosPorModelo.producto_colores.length">
                        <div class="col-lg-3 col-md-4 col-sm-6 mt-1">
                            <label style="font-weight:bold; font-size:11px;">FILTRAR POR COLOR</label>
                            <v-select
                                v-model="filtroColor"
                                :options="lst_colores_matriz"
                                :reduce="c => c.color_id"
                                label="color_nombre"
                                placeholder="Todos los colores"
                                :clearable="true">
                            </v-select>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mt-1">
                            <label style="font-weight:bold; font-size:11px;">FILTRAR POR TALLA</label>
                            <v-select
                                v-model="filtroTalla"
                                :options="lst_tallas_matriz"
                                :reduce="t => t.id"
                                label="descripcion"
                                placeholder="Todas las tallas"
                                :clearable="true">
                            </v-select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase" id="table-stocks">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>
                                    <th>COLOR</th>
                                    <template v-for="talla in tallasFiltradas">
                                        <th class="colorStockLogico">
                                            {{ talla.descripcion }}
                                        </th>
                                        <th>CANT</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pc in productoColoresFiltrados"
                                    :key="`${pc.producto_id}${pc.color_id}`">
                                    <td>{{ pc.producto_nombre }}</td>
                                    <td>{{ pc.color_nombre }}</td>

                                    <template v-for="t in tallasFiltradas">
                                        <td class="colorStockLogico">
                                            <span>
                                                {{ printStockLogico(pc.producto_id, pc.color_id, t.id) }}
                                            </span>
                                        </td>
                                        <td style="width: 5%;"
                                            v-if="printStockLogico(pc.producto_id, pc.color_id, t.id) > 0">
                                            <input type="text" class="form-control inputCantidad inputCantidadColor"
                                                :data-almacen-id="almacenSeleccionado"
                                                :data-producto-id="pc.producto_id"
                                                :data-producto-nombre="pc.producto_nombre"
                                                :data-color-nombre="pc.color_nombre" :data-talla-nombre="t.descripcion"
                                                :data-color-id="pc.color_id" :data-talla-id="t.id"
                                                @input="validarContenidoInput($event)"
                                                :disabled="printStockLogico(pc.producto_id, pc.color_id, t.id) === 0" />
                                        </td>
                                        <td v-else>

                                        </td>
                                    </template>
                                </tr>
                            </tbody>

                            <tfoot>

                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group row mt-1">
                        <div class="col-lg-2 col-xs-12">
                            <button :disabled="deshabilitarBtnAgregar" type="button" id="btn_agregar_detalle"
                                @click="agregarProducto" class="btn btn-warning btn-block">
                                <i class="fa fa-plus"></i> AGREGAR
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- FIN PRIMER PANEL -->

            <ModalEnvioVue :cliente="cliente" @addDataEnvio="addDataEnvio" @borrarDataEnvio="borrarDataEnvio" />

        </div>
        <!-- FIN COLUMNA -->
    </div>
    <!-- FIN FILA -->
</template>
<script>

import ModalEnvioVue from "./ModalEnvio.vue";
import { Empresa } from "../../interfaces/Empresa.js";
import ModalCodigoPrecioMenorVue from './ModalCodigoPrecioMenor.vue';
import ModalEditaDetalleVue from "./ModalEditDetalle.vue";

export default {
    name: "TablaProductos",
    components: {
        ModalEnvioVue,
        ModalCodigoPrecioMenorVue,
        ModalEditaDetalleVue,
    },
    props: [
        "fullaccessTable",
        "btnDisabled",
        "parametros",
        "productoTabla",
        "TotalesObj",
        'idcotizacion',
        'modelos',
        'categorias',
        'marcas',
        'tallas',
        'cliente',
        'almacenSeleccionado',
        'lst_departamentos_base',
        'lst_provincias_base',
        'lst_distritos_base'],
    data() {
        return {
            hayDatosEnvio: false,

            monto_embalaje: 0,
            monto_envio: 0,
            monto_descuento: 0,
            porcentaje_descuento_global: 0,
            descuentos: [],
            flujo: [],
            monto_subtotal: 0,
            monto_igv: 0,
            monto_total: 0,
            monto_total_pagar: 0,
            carrito: [],
            deshabilitarBtnAgregar: true,
            productosPorModelo: {},
            preciosVenta: [],
            precioVentaSeleccionado: null,
            buscarCodigoBarra: null,
            asegurarCierre: 5,
            formDetalles: {
                producto_id: "",
                producto_unidad: "",
                cantidad: "",
                precio: "",
                producto_lote: "",
                igv: 18,
                igv_int: "",
                monto_sub_total: 0,
                monto_total_igv: 0,
                monto_total: 0
            },
            searchInput: "",
            dataEmpresa: new Empresa(),
            codigo_precio_menor: "",
            estadoPrecioMenor: "",
            tablaDetalles: [],
            itemLote: {
                cantidad: 0,
                cantidadMax: 0,
                precio: 0,
                precioMinimo: 0,
                unidadMedida: "",
                id: 0,
                producto: "",
                estado_precio_menor: "",
                codigo_precio_menor: "",
                igv: 0
            },
            Igv: 18,
            modalLote: false,

            //======== FILTROS PRODUCTOS ========
            productoSeleccionado: null,
            lst_productos: [],
            filtroColor: null,
            filtroTalla: null,
            lst_colores_matriz: [],
            lst_tallas_matriz: [],
            searchTimer: null,
        }
    },
    watch: {
        monto_total_pagar: {
            handler(value) {
                this.$emit('actualizarMontoPago', value);
            },
            deep: true
        },
        monto_envio: {
            handler(value) {
                let valor = value;

                if (!/^\d*\.?\d*$/.test(valor)) {
                    //========= PERMITIR ENTEROS Y DECIMALES ========
                    valor = valor.replace(/^0+/, '0');
                    //======= ELIMINAR CARACTERES NO NUMÉRICOS, EXCEPTO EL PUNTO DECIMAL =======
                    valor = valor.replace(/[^\d.]/g, '');
                    //========= PERMITIR SOLO 1 PUNTO DECIMAL =====
                    valor = valor.replace(/(\..*)\./g, '$1');
                    this.monto_envio = valor;
                    return;
                }

                //======== RECALCULAR MONTOS =======
                this.calcularMontos();

                const monto_embalaje_aux = this.monto_embalaje.length > 0 ? this.monto_embalaje : 0;
                const monto_envio_aux = this.monto_envio.length > 0 ? this.monto_envio : 0;
                const montos = {
                    monto_sub_total: parseFloat(this.monto_subtotal),
                    monto_total_igv: parseFloat(this.monto_igv),
                    monto_total: parseFloat(this.monto_total),
                    monto_embalaje: parseFloat(monto_embalaje_aux),
                    monto_envio: parseFloat(monto_envio_aux),
                    monto_total_pagar: parseFloat(this.monto_total_pagar)
                };

                this.$emit('addProductoDetalle', {
                    detalles: this.carrito,
                    totales: montos
                });
            },
            deep: true
        },
        monto_embalaje: {
            handler(value) {
                let valor = value;

                if (!/^\d*\.?\d*$/.test(valor)) {
                    //========= PERMITIR ENTEROS Y DECIMALES ========
                    valor = valor.replace(/^0+/, '0');
                    //======= ELIMINAR CARACTERES NO NUMÉRICOS, EXCEPTO EL PUNTO DECIMAL =======
                    valor = valor.replace(/[^\d.]/g, '');
                    //========= PERMITIR SOLO 1 PUNTO DECIMAL =====
                    valor = valor.replace(/(\..*)\./g, '$1');
                    this.monto_embalaje = valor;
                    return;
                }

                //======== RECALCULAR MONTOS =======
                this.calcularMontos();

                const monto_embalaje_aux = this.monto_embalaje.length > 0 ? this.monto_embalaje : 0;
                const monto_envio_aux = this.monto_envio.length > 0 ? this.monto_envio : 0;
                const montos = {
                    monto_sub_total: parseFloat(this.monto_subtotal),
                    monto_total_igv: parseFloat(this.monto_igv),
                    monto_total: parseFloat(this.monto_total),
                    monto_embalaje: parseFloat(monto_embalaje_aux),
                    monto_envio: parseFloat(monto_envio_aux),
                    monto_total_pagar: parseFloat(this.monto_total_pagar)
                };

                this.$emit('addProductoDetalle', {
                    detalles: this.carrito,
                    totales: montos
                });
            },
            deep: true
        },
        carrito: {
            handler(value) {

                //======== si aún quedan items en el carrito , asegurar cierre será 1 =========
                if (this.carrito.length > 0) {
                    this.asegurarCierre = 1;
                }
                const montos = {
                    monto_sub_total: parseFloat(this.monto_subtotal),
                    monto_total_igv: parseFloat(this.monto_igv),
                    monto_total: parseFloat(this.monto_total),
                    monto_embalaje: parseFloat(this.monto_embalaje),
                    monto_envio: parseFloat(this.monto_envio),
                    monto_total_pagar: parseFloat(this.monto_total_pagar),
                    monto_descuento: parseFloat(this.monto_descuento)
                };

                this.$emit('addProductoDetalle', {
                    detalles: this.carrito,
                    totales: montos
                });

            },
            deep: true,
        },
        buscarCodigoBarra: {
            handler(value) {

                //======= VALIDAR QUE HAYA SELECCIONADO UN ALMACÉN PREVIAMENTE =======
                if (!this.almacenSeleccionado) {
                    toastr.clear();
                    toastr.error('DEBES SELECCIONAR UN ALMACÉN!!!');
                    //======= FOCUS AL VSELECT ALMACÉN ========
                    this.$nextTick(() => {
                        this.$parent.$refs.selectAlmacen.$el.querySelector("input").focus();
                    });
                    this.$parent.ocultarAnimacionVenta();
                    return;
                }

                if (value.trim().length === 8) {
                    this.getProductoBarCode(value);
                }
            },
            deep: true,
        },
        productoSeleccionado(val) {
            if (val) {
                this.buscarStocks();
            } else {
                this.productosPorModelo = {};
                this.preciosVenta = [];
                this.precioVentaSeleccionado = null;
                this.filtroColor = null;
                this.filtroTalla = null;
                this.lst_colores_matriz = [];
                this.lst_tallas_matriz = [];
                this.clearInputsCantidad();
            }
        },

        codigoPrecioMenor: {
            handler(value) {
                this.estadoPrecioMenor = value.estado_precio_menor;
            },
            deep: true,
        },
        searchInput(value) {
            this.paramsLotes.search = value;
            let timeout;
            clearTimeout(timeout)
            timeout = setTimeout(() => {
                this.$nextTick(this.ObtenerLotes);
                clearTimeout(timeout);
            }, 1000);
        },
        formDetalles: {
            handler(value) {

                let cant = !isNaN(Number(value.cantidad)) ? Number(value.cantidad) : 0;
                let stock = Number(this.productoJson.cantidad_logica);
                if (cant > stock) {
                    toastr.error("La cantidad ingresa supera el stock del producto. MAX(" + stock + ")")
                    value.cantidad = stock;
                }
            },
            deep: true
        }
    },
    created() {

    },
    computed: {
        productoColoresFiltrados() {
            if (!this.productosPorModelo.producto_colores) return [];
            if (!this.filtroColor) return this.productosPorModelo.producto_colores;
            return this.productosPorModelo.producto_colores.filter(pc => pc.color_id === this.filtroColor);
        },
        tallasFiltradas() {
            if (!this.filtroTalla) return this.lst_tallas_matriz;
            return this.lst_tallas_matriz.filter(t => t.id === this.filtroTalla);
        },
    },
    methods: {
        formatoNumero(valor) {
            return parseFloat(valor).toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        actualizarItemCarrito(productoEditado) {
            const indexProducto = this.carrito.findIndex((c) => {
                return c.producto_id == productoEditado.producto_id && c.color_id == productoEditado.color_id;
            })
            if (indexProducto === -1) {
                toastr.error('ERROR AL ACTUALIZAR PRODUCTO');
                return;
            }
            if (productoEditado.tallas.length === 0) {
                this.carrito.splice(indexProducto, 1);
            } else {
                this.$set(this.carrito, indexProducto, productoEditado);
            }
            this.calcularMontos();
            this.recargarTablaStocks();
        },
        borrarDataEnvio() {
            this.$emit('borrarDataEnvio');
            this.hayDatosEnvio = false;
        },
        async buscarStocks() {
            if (!this.almacenSeleccionado) {
                toastr.clear();
                toastr.error('DEBES SELECCIONAR UN ALMACÉN!!!');
                return;
            }
            this.$parent.mostrarAnimacionVenta();
            try {
                const res = await this.axios.get(route('ventas.documento.getStocksMatriz'), {
                    params: {
                        almacen_id:  this.almacenSeleccionado,
                        producto_id: this.productoSeleccionado || '',
                    }
                });
                if (res.data.success) {
                    this.productosPorModelo = res.data;
                    this.preciosVenta = res.data.precios_venta || [];
                    this.precioVentaSeleccionado = null;
                    // populate matrix color/talla filter options
                    const coloresSeen = new Set();
                    this.lst_colores_matriz = (res.data.producto_colores || []).filter(pc => {
                        if (coloresSeen.has(pc.color_id)) return false;
                        coloresSeen.add(pc.color_id);
                        return true;
                    });
                    const tallaIdsConStock = new Set((res.data.stocks || []).filter(st => st.stock_logico > 0).map(st => st.talla_id));
                    this.lst_tallas_matriz = (this.tallas || []).filter(t => tallaIdsConStock.has(t.id));
                    this.filtroColor = null;
                    this.filtroTalla = null;
                    this.clearInputsCantidad();
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                toastr.error('Error al buscar stocks', 'ERROR');
            } finally {
                this.$parent.ocultarAnimacionVenta();
            }
        },

        limpiarFiltrosProducto() {
            this.productoSeleccionado = null;
            this.lst_productos = [];
            this.productosPorModelo = {};
            this.preciosVenta = [];
            this.precioVentaSeleccionado = null;
            this.filtroColor = null;
            this.filtroTalla = null;
            this.lst_colores_matriz = [];
            this.lst_tallas_matriz = [];
            this.clearInputsCantidad();
        },
        openMdlEditItem(producto) {
            this.$parent.openMdlEditItem(producto);
        },
        closeModal() {
            this.$parent.closeModal(producto);
        },
        closeModal() {
            this.modalVisible = false;
            this.selectedProducto = null;
        },
        addDataEnvio(value) {
            this.hayDatosEnvio = true;
            this.$emit('addDataEnvio', value);
        },
        NuevoCliente() {
            $("#modal_cliente").modal("show");
        },
        setDataEnvio() {
            $("#modal_envio").modal("show");
        },
        validarDescuento(producto_id, color_id, event) {

            //==== CONTROLANDO DE QUE EL VALOR SEA UN NÚMERO ====
            const valor = event.target.value;

            //==== SI EL INPUT ESTA VACÍO ====
            if (valor.trim().length === 0) {
                this.calcularDescuento(producto_id, color_id, 0);
                return;
            }

            //===== EXPRESION REGULAR PARA EVITAR CARACTERES NO NUMÉRICOS EN LA CADENA ====
            const regex = /^[0-9]+(\.[0-9]{0,2})?$/;
            //==== BORRAR CARACTER NO NUMÉRICO ====
            if (!regex.test(valor)) {
                event.target.value = valor.slice(0, -1);
                return;
            }

            //==== EN CASO SEA NUMÉRICO ====
            let porcentaje_desc = parseFloat(event.target.value);

            //==== EL MÁXIMO DESCUENTO ES 100% ====
            if (porcentaje_desc > 100) {
                event.target.value = 100;
                porcentaje_desc = event.target.value;
            }

            //==== CALCULAR DESCUENTO ====
            this.calcularDescuento(producto_id, color_id, porcentaje_desc)

        },
        calcularDescuento(producto_id, color_id, porcentaje_descuento) {

            //==== BUSCANDO PRODUCTO COLOR EN EL CARRITO ====
            const indiceProductoColor = this.carrito.findIndex((producto) => {
                return producto.producto_id == producto_id && producto.color_id == color_id;
            })

            const producto_color_editar = this.carrito[indiceProductoColor];

            //===== APLICANDO DESCUENTO ======
            producto_color_editar.porcentaje_descuento = porcentaje_descuento;
            producto_color_editar.monto_descuento = producto_color_editar.subtotal * (porcentaje_descuento / 100);
            producto_color_editar.precio_venta_nuevo = (producto_color_editar.precio_venta * (1 - porcentaje_descuento / 100));
            producto_color_editar.subtotal_nuevo = (producto_color_editar.subtotal * (1 - porcentaje_descuento / 100));

            this.carrito[indiceProductoColor] = producto_color_editar;

            //==== RECALCULANDO MONTOS ====
            this.calcularMontos();

        },
        async eliminarCarrito() {
            if (this.carrito.length !== 0) {
                this.$parent.mostrarAnimacionVenta();
                try {
                    await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                        carrito: JSON.stringify(this.carrito)
                    })
                    this.carrito = [];
                    this.monto_subtotal = 0;
                    this.monto_igv = 0;
                    this.monto_total = 0;
                    this.recargarTablaStocks();
                    this.$parent.ocultarAnimacionVenta();
                    toastr.success('Detalle eliminado', 'Completado');
                } catch (error) {
                    toastr.error('Ocurrio un error al eliminar el detalle', 'Error');
                } finally {
                    this.$parent.ocultarAnimacionVenta();
                }
            } else {
                toastr.warning('El detalle no tiene productos', 'Advertencia');
            }

        },
        calcularMontos() {
            let subtotal = 0;
            let totalSinIgv = 0;
            let igv = 0;
            let totalConIgv = 0;
            let descuento = 0;

            //==== RECORRIENDO CARRITO ====
            this.carrito.forEach((producto) => {
                if (producto.porcentaje_descuento === 0) {
                    subtotal += producto.subtotal;
                } else {
                    subtotal += parseFloat(producto.subtotal_nuevo);
                }
                descuento += parseFloat(producto.monto_descuento);
            })

            //========= PRECIO PRODUCTOS EN CARRITO + PRECIO EMBALAJE + PRECIO ENVÍO =============
            //====== PERMITIR BORRAR EL 0 DEL INPUT EMBALAJE, EVITAR ERROR DE VALOR NAN =======
            const monto_embalaje_aux = this.monto_embalaje.length > 0 ? parseFloat(this.monto_embalaje) : 0;
            const monto_envio_aux = this.monto_envio.length > 0 ? parseFloat(this.monto_envio) : 0;

            totalConIgv = subtotal + parseFloat(monto_embalaje_aux) + parseFloat(monto_envio_aux);
            totalSinIgv = totalConIgv / 1.18
            igv = totalConIgv - totalSinIgv;

            this.monto_total_pagar = totalConIgv;
            this.monto_igv = igv;
            this.monto_total = totalSinIgv;
            this.monto_subtotal = subtotal;
            this.monto_descuento = descuento;
        },
        async getStockLogico(inputCantidad) {
            const almacen_id = inputCantidad.getAttribute('data-almacen-id');
            const producto_id = inputCantidad.getAttribute('data-producto-id');
            const color_id = inputCantidad.getAttribute('data-color-id');
            const talla_id = inputCantidad.getAttribute('data-talla-id');

            try {
                const url = `/get-stocklogico/${almacen_id}/${producto_id}/${color_id}/${talla_id}`;
                const response = await axios.get(url);
                if (response.data.message == 'success') {
                    const stock_logico = response.data.data[0].stock_logico;
                    return stock_logico;
                }

            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`, "Error");
                event.target.value = '';
                console.error('Error al obtener stock logico:', error);
                return null;
            }
        },
        calcularSubTotal() {
            //======= calculando el subtotal de cada producto color =========
            this.carrito.forEach((item, index) => {
                const precio_venta = item.precio_venta;
                //===== sumando las cantidades de todas las tallas que tiene el producto color ======
                let subtotal = 0;
                item.tallas.forEach((talla) => {
                    subtotal += (precio_venta * talla.cantidad);
                })
                //======= actualizamos el subtotal de ese producto_color
                this.carrito[index].subtotal = subtotal;
            })
        },
        reordenarCarrito() {
            this.carrito.sort(function (a, b) {
                if (a.producto_id === b.producto_id) {
                    return a.color_id - b.color_id;
                } else {
                    return a.producto_id - b.producto_id;
                }
            });
        },
        async validarCantidadCarrito(inputCantidad) {
            const stockLogico = await this.getStockLogico(inputCantidad);
            const cantidadSolicitada = inputCantidad.value;
            return stockLogico >= cantidadSolicitada;
        },
        clearInputsCantidad() {
            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            inputsCantidad.forEach((ic) => {
                ic.value = '';
            })
        },
        async agregarProducto() {

            toastr.clear();

            //====== VALIDACIONES =======
            if (!this.precioVentaSeleccionado) {
                toastr.error('DEBE SELECCIONAR UN PRECIO DE VENTA!!!');
                return;
            }

            //======= DEBE SELECCIONARSE UN ALMACÉN =======
            if (!this.almacenSeleccionado) {
                toastr.clear();
                toastr.error('DEBES SELECCIONAR UN ALMACÉN!!!');
                //======= FOCUS AL VSELECT ALMACÉN ========
                this.$nextTick(() => {
                    this.$parent.$refs.selectAlmacen.$el.querySelector("input").focus();
                });
                this.$parent.ocultarAnimacionVenta();
                return;
            }

            //======== ANIMACIÓN =======
            this.$parent.mostrarAnimacionVenta();

            //========= GET INPUTS TO PRODUCTS ARRAY ======
            const resInputProducts = this.getInputProductos();

            if (!resInputProducts.validacion) {
                this.$parent.ocultarAnimacionVenta();
                return;
            }

            try {

                const res_actualizar_stock = await this.actualizarStockLogicoVentas(this.almacenSeleccionado, resInputProducts.lstInputProducts);

                if (!res_actualizar_stock.data.success) {
                    toastr.error(res_actualizar_stock.data.message, 'ERROR EN EL SERVIDOR');
                    this.$parent.ocultarAnimacionVenta();
                    return;
                }

                //======== ACTIVAR LA DEVOLUCIÓN DE STOCK ======
                this.asegurarCierre = 1;
                toastr.success(res_actualizar_stock.data.message, 'OPERACIÓN COMPLETADA');

                //====== AGREGAR AL CARRITO =======
                this.agregarCarrito(resInputProducts.lstInputProducts);

                //======= LIMPIAR INPUTS CANTIDAD ======
                this.clearInputsCantidad();

                //======== ACTUALIZAR TABLERO STOCKS =======
                this.recargarTablaStocks();

                this.reordenarCarrito();
                this.calcularSubTotal();
                this.calcularMontos();

                //===== RECALCULANDO DESCUENTOS =====
                this.carrito.forEach((c) => {
                    this.calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
                })

            } catch (error) {
                toastr.error(error, 'ERROR AL AGREGAR PRODUCTO!!!');
            }

            this.$parent.ocultarAnimacionVenta();
        },
        async validarStockVentas(almacenId, lstInputProducts) {

            const res = await axios.post(route('ventas.documento.validarStockVentas'),
                { almacenId, lstInputProducts: JSON.stringify(lstInputProducts) });

            return res;

        },
        async actualizarStockLogicoVentas(almacenId, lstInputProducts) {

            //===== RESTAR STOCK LÓGICO ======
            const res = await axios.post(route('ventas.documento.actualizarStockAdd')
                , { almacenId, lstInputProducts: JSON.stringify(lstInputProducts) });

            return res;
        },
        agregarCarrito(lstInputProducts) {
            lstInputProducts.forEach((producto) => {

                //========= REVIZAR SI EXISTE EL PRODUCTO COLOR EN EL CARRITO ======
                const indiceExiste = this.carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                //======= EN CASO EL PRODUCTO COLOR SEA NUEVO ======
                if (indiceExiste == -1) {

                    const objProduct = {
                        almacen_id: producto.almacen_id,
                        producto_id: producto.producto_id,
                        color_id: producto.color_id,
                        producto_nombre: producto.producto_nombre,
                        color_nombre: producto.color_nombre,
                        precio_venta: producto.precio_venta,
                        monto_descuento: 0,
                        porcentaje_descuento: 0,
                        precio_venta_nuevo: producto.precio_venta,
                        subtotal_nuevo: 0,
                        tallas: [{
                            talla_id: producto.talla_id,
                            talla_nombre: producto.talla_nombre,
                            cantidad: producto.cantidad
                        }]
                    };

                    this.carrito.push(objProduct);
                } else {

                    //======== PRODUCTO COLOR EXISTE =========
                    const productoModificar = this.carrito[indiceExiste];
                    productoModificar.precio_venta = producto.precio_venta;
                    productoModificar.precio_venta_nuevo = producto.precio_venta;

                    //===== PREGUNTANDO SI EXISTE LA TALLA EN EL CARRITO ======
                    const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                    //========== TALLA NUEVA ======
                    if (indexTalla === -1) {

                        const objTallaProduct = {
                            talla_id: producto.talla_id,
                            talla_nombre: producto.talla_nombre,
                            cantidad: producto.cantidad
                        };

                        this.carrito[indiceExiste].tallas.push(objTallaProduct);

                    }
                }

            })
        },
        getInputProductos() {

            //========= OBTENER TODOS LOS INPUT CANTIDAD =======
            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            const lstInputProducts = [];
            let validacion = true;

            for (let i = 0; i < inputsCantidad.length; i++) {
                const ic = inputsCantidad[i];

                if (ic.value) {
                    const producto = this.formarProducto(ic);

                    //========= VERIFICAR SI EL COLOR YA EXISTE EN EL CARRITO =====
                    const indiceProductoColor = this.carrito.findIndex((c) => {
                        return c.producto_id == producto.producto_id && c.color_id == producto.color_id;
                    });

                    if (indiceProductoColor !== -1) {
                        //======== VERIFICAR SI LA TALLA YA EXISTE EN EL CARRITO ====
                        const indiceTalla = this.carrito[indiceProductoColor].tallas.findIndex((t) => {
                            return t.talla_id == producto.talla_id;
                        });

                        if (indiceTalla !== -1) {

                            //======== ROMPER EL BUCLE EN CASO EXISTA LA TALLA EN EL CARRITO ======
                            toastr.error(
                                `YA FUE AGREGADO!! ${this.carrito[indiceProductoColor].producto_nombre}-${this.carrito[indiceProductoColor].color_nombre}-${this.carrito[indiceProductoColor].tallas[indiceTalla].talla_nombre}`
                            );
                            validacion = false;
                            break;
                        }
                    }

                    lstInputProducts.push(producto);
                }
            }

            return { validacion, lstInputProducts };

        },
        async actualizarStockLogico(producto, modo, cantidadAnterior) {

            modo == "eliminar" ? this.asegurarCierre = 0 : this.asegurarCierre = 1;

            try {
                await this.axios.post(route('ventas.documento.cantidad'), {
                    'producto_id': producto.producto_id,
                    'color_id': producto.color_id,
                    'talla_id': producto.talla_id,
                    'cantidad': producto.cantidad,
                    'condicion': this.asegurarCierre,
                    'modo': modo,
                    'cantidadAnterior': cantidadAnterior,
                    'tallas': producto.tallas,
                });


            } catch (ex) {

            }
        },
        formarProducto(ic) {
            const almacen_id = ic.getAttribute('data-almacen-id');
            const producto_id = ic.getAttribute('data-producto-id');
            const producto_nombre = ic.getAttribute('data-producto-nombre');
            const color_id = ic.getAttribute('data-color-id');
            const color_nombre = ic.getAttribute('data-color-nombre');
            const talla_id = ic.getAttribute('data-talla-id');
            const talla_nombre = ic.getAttribute('data-talla-nombre');
            const precio_venta = parseFloat(this.precioVentaSeleccionado);
            const cantidad = ic.value ? ic.value : 0;

            const monto_descuento = 0.0;
            const porcentaje_descuento = 0.0;
            const precio_venta_nuevo = parseFloat(this.precioVentaSeleccionado);
            const subtotal_nuevo = 0.0;

            const producto = {
                almacen_id, producto_id, producto_nombre, color_id, color_nombre,
                talla_id, talla_nombre, cantidad, precio_venta,
                monto_descuento, porcentaje_descuento, precio_venta_nuevo, subtotal_nuevo
            };
            return producto;
        },
        validarContenidoInput(e) {
            e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            this.validarCantidadInstantanea(e);
        },


        async validarCantidadInstantanea(event) {
            const cantidadSolicitada = event.target.value;
            try {
                if (cantidadSolicitada !== '') {
                    const stock_logico = await this.getStockLogico(event.target);
                    if (stock_logico < cantidadSolicitada) {
                        event.target.classList.add('inputCantidadIncorrecto');
                        event.target.classList.remove('inputCantidadValido');
                        event.target.focus();
                        this.deshabilitarBtnAgregar = true;
                        toastr.error(`Cantidad solicitada: ${cantidadSolicitada}, debe ser menor o igual
                            al stock lógico: ${stock_logico}`, "Error");
                    } else {
                        event.target.classList.add('inputCantidadValido');
                        event.target.classList.remove('inputCantidadIncorrecto');
                        this.deshabilitarBtnAgregar = false;
                    }
                } else {
                    this.deshabilitarBtnAgregar = false;
                    event.target.classList.remove('inputCantidadIncorrecto');
                    event.target.classList.remove('inputCantidadValido');
                }
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`, "Error");
                event.target.value = '';
                console.error('Error al obtener stock logico:', error);
            }
        },
        recargarTablaStocks() {
            this.buscarStocks();
        },

        printStockLogico(productoId, colorId, tallaId) {
            if (!this.productosPorModelo.stocks) return 0;
            const stock = this.productosPorModelo.stocks.find(
                st => st.producto_id === productoId && st.color_id === colorId && st.talla_id === tallaId
            );
            return stock ? stock.stock_logico : 0;
        },

        onSearchProducto(search, loading) {
            clearTimeout(this.searchTimer);
            if (!search || search.length < 1) { loading(false); return; }
            const vm = this;
            this.searchTimer = setTimeout(async () => {
                loading(true);
                try {
                    const res = await vm.axios.get(route('utilidades.getProductosSimple'), {
                        params: {
                            q:          search,
                            almacen_id: vm.almacenSeleccionado || '',
                        }
                    });
                    vm.lst_productos = res.data;
                } catch (e) {
                    // silent
                } finally {
                    loading(false);
                }
            }, 400);
        },
        async devolverCantidades() {
            console.log('ASEGURAR CIERRE', this.asegurarCierre);
            if (this.asegurarCierre == 1) {
                console.log('DEVOLVIENDO STOCK');
                await this.axios.post(route('ventas.documento.devolverCantidades'), {
                    carrito: JSON.stringify(this.carrito),
                });
                this.ChangeAsegurarCierre();
            }

        },
        async ObtenerCodigoPrecioMenor() {

            try {
                const { data } = await this.axios.get(route("consulta.ajax.getCodigoPrecioMenor"));
                this.dataEmpresa = data;
            } catch (ex) {

            }
        },
        AgregarDatos(item) {
            this.tablaDetalles.push(item);
            this.formDetalles.precio = "";
            this.formDetalles.cantidad = "";
            this.formDetalles.producto_id = "";
            this.formDetalles.producto_unidad = "";
            this.formDetalles.producto_lote = "";
        },
        buscarProductoAdded(id) {
            let obj = this.tablaDetalles.find(item => Number(item.producto_id) == Number(id));
            return obj ? true : false;
        },
        TransformarNumber(item) {
            if (item && !isNaN(Number(item))) {

                return parseFloat(item);
            } else {
                return item;
            }
        },
        async actualizarStockDelete(producto) {
            const res = await axios.post(route('ventas.documento.actualizarStockDelete'),
                {
                    almacen_id: producto.almacen_id,
                    producto_id: producto.producto_id,
                    color_id: producto.color_id,
                    tallas: JSON.stringify(producto.tallas)
                });

            return res;
        },
        async EliminarItem(item, index) {

            try {
                toastr.clear();
                this.$parent.mostrarAnimacionVenta();

                //==== devolvemos el stock logico separado ========
                const res_delete = await this.actualizarStockDelete(item);

                if (!res_delete.data.success) {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR AL DEVOLVER STOCK LÓGICO!!!');
                }

                //this.actualizarStockLogico(item, "eliminar");
                //======== renderizamos la tabla de stocks ==============
                this.recargarTablaStocks();
                //========== eliminar el item del carrito ========
                //============ renderizamos la tabla detalle =======
                this.carrito.splice(index, 1);
                //========= recalcular subtotal,igv,total =========
                this.calcularMontos();

                //======= alerta ======================
                this.$parent.ocultarAnimacionVenta();
                toastr.info('ITEM ELIMINADO', "CANTIDAD DEVUELTA");
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR ITEM!!!');
            }
        },
        ChangeAsegurarCierre() {
            this.asegurarCierre = 5;
            console.log('devuelto', this.asegurarCierre);
        },
        async getProductoBarCode(barcode) {
            try {
                toastr.clear();
                this.$parent.mostrarAnimacionVenta();

                const res = await axios.get(route('ventas.documento.getProductoBarCode', { barcode }));

                if (res.data.success) {  //====== PRODUCTO EXISTE ====

                    toastr.info('BUSCANDO PRODUCTO');

                    res.data.producto.cantidad = 1;

                    res.data.producto.almacen_id = this.almacenSeleccionado;  //======= AGREGAR ALMACEN ID ======
                    const lstInputProducts = [res.data.producto];
                    let indiceProductoColor = -1;
                    let indiceTalla = -1;

                    //======= VALIDAR QUE EL PRODUCTO NO EXISTA EN EL DETALLE ======
                    indiceProductoColor = this.carrito.findIndex((c) => {
                        return c.producto_id == res.data.producto.producto_id && c.color_id == res.data.producto.color_id;
                    });

                    if (indiceProductoColor !== -1) {
                        indiceTalla = this.carrito[indiceProductoColor].tallas.findIndex((t) => {
                            return t.talla_id == res.data.producto.talla_id;
                        });
                    }

                    if (indiceTalla !== -1) {
                        toastr.error(`${res.data.producto.producto_nombre}-${res.data.producto.color_nombre}-${res.data.producto.talla_nombre}`, 'ERROR, YA EXISTE EN EL DETALLE!!!');
                        return;
                    }

                    //======= RESTAR STOCK LÓGICO ======
                    const res_actualizar_stock = await this.actualizarStockLogicoVentas(this.almacenSeleccionado, lstInputProducts);

                    if (!res_actualizar_stock.data.success) {
                        toastr.error(res_actualizar_stock.data.message, 'ERROR EN EL SERVIDOR');
                        return;
                    }

                    //======== ACTIVAR LA DEVOLUCIÓN DE STOCK ======
                    this.asegurarCierre = 1;

                    //====== AGREGAR AL CARRITO =======
                    this.agregarCarrito(lstInputProducts);

                    //======= LIMPIAR INPUTS CANTIDAD ======
                    this.clearInputsCantidad();

                    //======== ACTUALIZAR TABLERO STOCKS =======
                    this.recargarTablaStocks();

                    this.reordenarCarrito();
                    this.calcularSubTotal();
                    this.calcularMontos();

                    //===== RECALCULANDO DESCUENTOS =====
                    this.carrito.forEach((c) => {
                        this.calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
                    })

                    toastr.info('AGREGADO AL DETALLE!!!', `${res.data.producto.producto_nombre} - ${res.data.producto.color_nombre} - ${res.data.producto.talla_nombre}
                                PRECIO: ${res.data.producto.precio_venta}`, { timeOut: 0 });

                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER PRODUCTO POR CÓDIGO DE BARRA');
            } finally {
                this.$parent.ocultarAnimacionVenta();
            }
        },
        async limpiarFormularioDetalle() {
            this.$parent.mostrarAnimacionVenta();
            await this.devolverCantidades();
            this.carrito = [];
            this.productosPorModelo = {};
            this.precioVentaSeleccionado = '';
            this.productoSeleccionado = null;
            this.clearInputsCantidad();
            this.$parent.ocultarAnimacionVenta();
        }

    }
}
</script>
