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
                <div class="panel-heading">
                    <h4 class=""><b>Seleccionar productos</b></h4>
                </div>
                <div class="panel-body ibox-content">

                    <div class="row" v-if="idcotizacion == 0">

                        <!-- <div class="col-12 mb-3 d-flex justify-content-end">
                            <button class="btn btn-danger" @click="eliminarCarrito"> ELIMINAR TODO </button>
                        </div> -->

                        <!-- <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" >
                            <label class="required" style="font-weight: bold;">CATEGORÍA</label>

                            <v-select
                                v-model="categoriaSeleccionada"
                                :options="categorias"
                                :reduce="categoria => categoria.id"
                                label="descripcion"
                                placeholder="Seleccionar"
                            >
                            </v-select>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" >
                            <label class="required" style="font-weight: bold;">MARCA</label>
                            <v-select
                                v-model="marcaSeleccionada"
                                :options="marcas"
                                :reduce="marca => marca.id"
                                label="marca"
                                placeholder="Seleccionar">
                            </v-select>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" >
                            <label class="required" style="font-weight: bold;">MODELO</label>
                            <v-select
                                v-model="modeloSeleccionado"
                                :options="modelos"
                                :reduce="modelo => modelo.id"
                                label="descripcion"
                                placeholder="Seleccionar">
                            </v-select>
                        </div> -->

                        <div class="col-lg-12 col-md-3 col-sm-12 col-xs-12 mt-3">
                            <label class="required" style="font-weight: bold;">CATEGORÍA - MARCA - MODELO -
                                PRODUCTO</label>

                            <v-select v-model="productoSeleccionado" :options="productos" label="producto_completo"
                                placeholder="Seleccionar" @search="buscarProducto" :loading="loadingProductos">
                            </v-select>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mt-3">
                            <label class="required" style="font-weight: bold;">PRECIO VENTA</label>
                            <v-select v-model="precioVentaSeleccionado" :options="preciosVenta"
                                placeholder="Seleccionar">
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
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase" id="table-stocks">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>
                                    <template v-for="talla in tallas">
                                        <th class="colorStockLogico">
                                            {{ talla.descripcion }}
                                        </th>
                                        <th>CANT</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pc in productosPorModelo.producto_colores"
                                    :key="`${pc.producto_id}${pc.color_id}`">
                                    <td>{{ pc.producto_nombre }} - {{ pc.color_nombre }}</td>

                                    <template v-for="t in tallas">
                                        <td class="colorStockLogico">
                                            <span>
                                                {{ printStockLogico(pc.producto_id, pc.color_id, t.id) }}
                                            </span>
                                        </td>
                                        <td style="width: 5%;"
                                            v-if="printStockLogico(pc.producto_id, pc.color_id, t.id) > 0">
                                            <input type="text" class="form-control inputCantidad inputCantidadColor"
                                                :data-almacen-id="pc.almacen_id" :data-producto-id="pc.producto_id"
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

            <!-- SEGUNDO PANEL -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class=""><b>Detalle del documento de venta</b></h4>
                </div>
                <div class="panel-body ibox-content">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <i class="fa fa-dashboard"></i>
                                    </th>
                                    <th class="text-center">PRODUCTO</th>
                                    <template v-for="t in tallas">
                                        <th class="text-center">{{ t.descripcion }}</th>
                                    </template>

                                    <th class="text-center">P. VENTA</th>
                                    <th class="text-center">SUBTOTAL</th>
                                    <th class="text-center">DSCTO %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="carrito.length > 0">
                                    <tr v-for="(item, index) in carrito" :key="index">
                                        <td class="text-center">
                                            <div class='btn-group'>
                                                <button type="button" class='btn btn-sm btn-warning btn-edit'
                                                    style='color:white' @click.prevent="openModal(item)">
                                                    <i class='fa fa-edit'></i>
                                                </button>
                                                <button type="button" class='btn btn-sm btn-danger btn-delete'
                                                    style='color:white' @click.prevent="EliminarItem(item, index)">
                                                    <i class='fa fa-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div style="width: 160px;">
                                                {{ item.producto_nombre }}-{{ item.color_nombre }}
                                            </div>
                                        </td>
                                        <td v-for="t in tallas">
                                            <p style="font-weight: bold;">{{ printTallaDetalle(t.id, item) }}</p>
                                        </td>
                                        <td>{{ item.porcentaje_descuento != 0 ? item.precio_venta_nuevo :
                                            item.precio_venta }}</td>
                                        <td>{{ item.porcentaje_descuento != 0 ? item.subtotal_nuevo : item.subtotal }}
                                        </td>
                                        <td>
                                            <input @input="validarDescuento(item.producto_id, item.color_id, $event)"
                                                type="text" :value="item.porcentaje_descuento" style="width: 100px;"
                                                class="form-control">
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="tallas.length + 5" class="text-center"><strong>No hay
                                                detalles</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <div class="table-responsive">
                            <table style="margin:0 0 0 auto;">
                                <tfoot>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            SUBTOTAL:</td>
                                        <td class="subtotal" colspan="1" style="font-weight: bold; text-align:end;">
                                            {{ `S/. ${Number(monto_subtotal).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",")}` }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            EMBALAJE:</td>
                                        <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg style="width: 20px;" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 640 512">
                                                        <!Font Awesome Free 6.5.1 by @fontawesome -
                                                            https://fontawesome.com License -
                                                            https://fontawesome.com/license/free Copyright 2024
                                                            Fonticons, Inc.>
                                                            <path
                                                                d="M425.7 256c-16.9 0-32.8-9-41.4-23.4L320 126l-64.2 106.6c-8.7 14.5-24.6 23.5-41.5 23.5-4.5 0-9-.6-13.3-1.9L64 215v178c0 14.7 10 27.5 24.2 31l216.2 54.1c10.2 2.5 20.9 2.5 31 0L551.8 424c14.2-3.6 24.2-16.4 24.2-31V215l-137 39.1c-4.3 1.3-8.8 1.9-13.3 1.9zm212.6-112.2L586.8 41c-3.1-6.2-9.8-9.8-16.7-8.9L320 64l91.7 152.1c3.8 6.3 11.4 9.3 18.5 7.3l197.9-56.5c9.9-2.9 14.7-13.9 10.2-23.1zM53.2 41L1.7 143.8c-4.6 9.2 .3 20.2 10.1 23l197.9 56.5c7.1 2 14.7-1 18.5-7.3L320 64 69.8 32.1c-6.9-.8-13.5 2.7-16.6 8.9z" />
                                                    </svg>
                                                </span>
                                                <input style="width: 70px;" v-model="monto_embalaje" type="text"
                                                    class="form-control" aria-label="PRECIO DESPACHO"
                                                    aria-describedby="basic-addon1">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            ENVÍO:</td>
                                        <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                            <div class="input-group">
                                                <span class="input-group-text btn"
                                                    :class="hayDatosEnvio ? 'btn-success' : 'btn-light'"
                                                    id="basic-addon1" @click.prevent="setDataEnvio">
                                                    <i class="fas fa-truck"></i>
                                                </span>
                                                <input style="width: 70px;" v-model="monto_envio" type="text"
                                                    class="form-control" aria-label="PRECIO ENVÍO"
                                                    aria-describedby="basic-addon1">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            DESCUENTO:</td>
                                        <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                            <span>{{ `S/.
                                                ${Number(monto_descuento).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",")}` }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            MONTO TOTAL:
                                        </td>
                                        <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                            {{ `S/. ${Number(monto_total).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",")}` }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">IGV:
                                        </td>
                                        <td class="igv" colspan="1" style="font-weight: bold; text-align:end;">
                                            {{ `S/. ${Number(monto_igv).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",")}` }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td :colspan="tallas.length + 4" style="font-weight: bold; text-align:end;">
                                            MONTO TOTAL A
                                            PAGAR:</td>
                                        <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                            {{ `S/.
                                            ${Number(monto_total_pagar).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",")}`
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <!-- FIN SEGUNDO PANEL -->


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
            modeloSeleccionado: null,
            categoriaSeleccionada: null,
            marcaSeleccionada: null,
            productoSeleccionado: null,
            precioVentaSeleccionado: null,
            buscarCodigoBarra: null,
            productos: [],
            preciosVenta: [],
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

            //======== SELECT PRODUCTOS ========
            loadingProductos: false,
            paginaActual: 1,
            perPage: 10,
            buscando: false
        }
    },
    watch: {

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

                console.log('WATCH CARRITO');

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
        modeloSeleccionado: {
            handler(value) {
                console.log('modeloSeleccionado cambió:', value);
                this.getProductos();
            },
            deep: true,
        },
        categoriaSeleccionada: {
            handler(value) {
                console.log('categoriaSeleccionada cambió:', value);
                this.getProductos();
            },
            deep: true,
        },
        marcaSeleccionada: {
            handler(value) {
                console.log('marcaSeleccionada cambió:', value);
                this.getProductos();
            },
            deep: true,
        },
        productoSeleccionado: {
            handler(value) {
                const producto_id = value.producto_id;
                console.log('producto seleccionado');
                if (producto_id) {
                    //====== OBTENER COLORES Y TALLAS DEL PRODUCTO ======
                    this.getColoresTallas();
                }

            },
            deep: true,
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
    methods: {
        borrarDataEnvio() {
            this.$emit('borrarDataEnvio');
            this.hayDatosEnvio = false;
        },
        async buscarProducto(query) {
            console.log('OLA');
            if (!this.almacenSeleccionado) {
                toastr.clear();
                toastr.error('DEBES SELECCIONAR UN ALMACÉN!!!');
                //======= FOCUS AL VSELECT ALMACÉN ========
                //this.$parent.$refs.selectAlmacen.$el.querySelector("input").focus();
                return;
            }

            if (query.length > 2) {

                this.loadingProductos = true;
                this.buscando = true;

                try {
                    toastr.clear();
                    const url = route('ventas.documento.getProductosVenta');

                    const response = await axios.get(url, {
                        params: {
                            search: query,
                            page: this.paginaActual,
                            perPage: this.perPage,
                            almacen_id: this.almacenSeleccionado,
                        }
                    });

                    console.log(response);

                    if (response.data.success) {
                        this.productos = response.data.data.data;
                        this.loadingProductos = false;
                        this.buscando = false;
                    } else {
                        console.log(response.data.message)
                        toastr.error(response.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    console.error("Error al buscar productos:", error);
                    this.loadingProductos = false;
                    this.buscando = false;
                }
            }

        },
        openModal(producto) {
            this.$parent.openModal(producto);
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
            producto_color_editar.monto_descuento = porcentaje_descuento === 0 ? 0 : producto_color_editar.subtotal * (porcentaje_descuento / 100);
            producto_color_editar.precio_venta_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar.precio_venta * (1 - porcentaje_descuento / 100)).toFixed(2);
            producto_color_editar.subtotal_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar.subtotal * (1 - porcentaje_descuento / 100)).toFixed(2);

            this.carrito[indiceProductoColor] = producto_color_editar;

            //==== RECALCULANDO MONTOS ====
            this.calcularMontos();

            console.log(this.carrito);

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
                    await this.getColoresTallas();
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
        printTallaDetalle(talla_id, item) {

            const itemTalla = item.tallas.find((t) => talla_id == t.talla_id);
            // console.log(itemTalla);
            const cantidad = itemTalla ? itemTalla.cantidad : '';

            return cantidad;
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
                this.getColoresTallas();

                this.reordenarCarrito();
                this.calcularSubTotal();
                this.calcularMontos();

                //===== RECALCULANDO DESCUENTOS =====
                this.carrito.forEach((c) => {
                    this.calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
                })

                console.log('CARRITO', this.carrito);


            } catch (error) {
                toastr.error(error, 'ERROR AL AGREGAR PRODUCTO!!!');
            }

            this.$parent.ocultarAnimacionVenta();

            console.log(this.asegurarCierre);
            console.log(this.carrito);
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
                console.log('existe carrito', indiceExiste);
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
                        precio_venta_nuevo: 0,
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

                    //===== PREGUNTANDO SI EXISTE LA TALLA EN EL CARRITO ======
                    const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                    //========== TALLA NUEVA ======
                    if (indexTalla === -1) {

                        const objTallaProduct = {
                            talla_id: producto.talla_id,
                            talla_nombre: producto.talla_nombre,
                            cantidad: producto.cantidad
                        };

                        console.log(`${productoModificar.producto_nombre} - ${productoModificar.color_nombre}`);
                        this.carrito[indiceExiste].tallas.push(objTallaProduct);
                        console.log(this.carrito);

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
            const precio_venta = this.precioVentaSeleccionado;
            const cantidad = ic.value ? ic.value : 0;

            const monto_descuento = 0.0;
            const porcentaje_descuento = 0.0;
            const precio_venta_nuevo = 0.0;
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
        async getProductos() {
            toastr.clear();

            const modelo_id = this.modeloSeleccionado;
            const marca_id = this.marcaSeleccionada;
            const categoria_id = this.categoriaSeleccionada;

            if (modelo_id || marca_id || categoria_id) {
                try {
                    const res = await axios.get(route('ventas.documento.getProductos'), {
                        params: {
                            modelo_id: modelo_id,
                            marca_id: marca_id,
                            categoria_id: categoria_id
                        }
                    });

                    if (res.data.success) {
                        this.productos = res.data.productos;
                        this.productoSeleccionado = null;
                        this.precioVentaSeleccionado = null;

                        toastr.info('PRODUCTOS CARGADOS', 'OPERACIÓN COMPLETADA');
                    } else {
                        //ocultarAnimacionCotizacion();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    //ocultarAnimacionCotizacion();
                    toastr.error(error, 'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
                }

            } else {
                //ocultarAnimacionCotizacion();
            }
        },
        //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
        async getColoresTallas() {

            //======= MOSTRAR ANIMACIÓN =======
            this.$parent.mostrarAnimacionVenta();
            const producto_id = this.productoSeleccionado.producto_id;
            const almacen_id = this.almacenSeleccionado;

            if (!producto_id) {
                this.$parent.ocultarAnimacionVenta();
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

            if (producto_id) {
                try {
                    const res = await axios.get(route('ventas.documento.getColoresTallas', { almacen_id, producto_id }));
                    if (res.data.success) {
                        this.productosPorModelo = res.data;
                        this.preciosVenta = res.data.precios_venta;
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
                } finally {
                    //ocultarAnimacionCotizacion();
                }
            }
            this.$parent.ocultarAnimacionVenta();
        },
        printStockLogico(productoId, colorId, tallaId) {
            const stock = this.productosPorModelo.stocks.find(st => st.producto_id === productoId && st.color_id === colorId && st.talla_id === tallaId);
            return stock ? stock.stock_logico : 0;
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
                this.getColoresTallas();
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
                    this.getColoresTallas();

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
            this.productosPorModelo = [];
            this.productos = [];
            this.productoSeleccionado = '';
            this.precioVentaSeleccionado = '';
            this.$parent.ocultarAnimacionVenta();
        }

    }
}
</script>
