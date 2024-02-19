<style>
    .inputCantidadValido{
        border-color:rgb(59, 63, 255) !important;
    }
    .inputCantidadIncorrecto{
        border-color: red !important;
    }
    .inputCantidadColor{
        border-color: rgb(48, 48, 88);
    }
    .colorStockLogico{
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

    .fulfilling-bouncing-circle-spinner, .fulfilling-bouncing-circle-spinner * {
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

            <div class="panel panel-primary">
                <div id="overlay" class="overlay">
                    <div class="fulfilling-bouncing-circle-spinner">
                        <div class="circle"></div>
                        <div class="orbit"></div>
                    </div>
                </div>
                <div class="panel-heading">
                    <h4 class=""><b>Detalle del Documento de Venta</b></h4>
                </div>
                <div class="panel-body ibox-content">

                    <div class="row" v-if="idcotizacion == 0">

                        <div class="col-12 col-md-12 select-required d-flex justify-content-between align-items-center">
                            <div class="form-group">
                                <label class="required">SELECCIONA UN MODELO: </label>
                                <v-select
                                    v-model="modeloSeleccionado"
                                    :options="modelos"
                                    :reduce="modelo => modelo.id"
                                    label="descripcion"
                                    placeholder="Seleccionar modelo...">
                                </v-select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-danger" @click="eliminarCarrito"> ELIMINAR TODO </button>
                            </div>
                        </div>
                    
                       
                        <!-- <div class="col-lg-6 col-xs-12">
                            <label class="col-form-label required">Producto:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="producto_lote" readonly
                                    v-model="formDetalles.producto_lote">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="ModalLotes"
                                        @click.prevent="ModalLotes" :disabled="btnDisabled">
                                        <i class='fa fa-search'></i>
                                        Buscar
                                    </button>
                                </span>
                            </div>
                            <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                            </div>
                        </div> -->
                        <input type="hidden" name="producto_id" id="producto_id">
                        <input type="hidden" name="producto_unidad" id="producto_unidad">
                        <input type="hidden" name="producto_json" id="producto_json">
                        <!-- <div class="col-lg-2 col-xs-12">
                            <label class="col-form-label required">Cantidad:</label>
                            <input type="text" name="cantidad" id="cantidad" class="form-control"
                                v-model="formDetalles.cantidad">
                            <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                            </div>
                        </div>
                        <div class="col-lg-2 col-xs-12">
                            <div class="form-group">
                                <label class="col-form-label required" for="amount">Precio:</label>
                                <input type="number" id="precio" name="precio" class="form-control"
                                    v-model="formDetalles.precio">
                                <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-xs-12">
                            <div class="form-group">
                                <label class="col-form-label" for="amount">&nbsp;</label>
                                <button type=button class="btn btn-block btn-warning" style='color:white;'
                                    id="btn_agregar_detalle" @click.prevent="Agregar"> <i class="fa fa-plus"></i>
                                    AGREGAR</button>
                            </div>
                        </div> -->
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase" id="table-stocks">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>
                                    <template  v-for="talla in tallas" >
                                        <th class="colorStockLogico">
                                            {{ talla.descripcion }}
                                        </th>
                                        <th>CANT</th>
                                    </template>
                                    <th class="">PRECIO VENTA</th>

                                    <!-- <th v-for="talla in tallas" :key="talla.id">
                                        {{ talla.descripcion }}<th>CANT</th>
                                    </th> -->
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pc in productosPorModelo.producto_colores" :key="`${pc.producto_id}${pc.color_id}`">
                                    <td>{{ pc.producto_nombre }} - {{ pc.color_nombre }}</td>
                                   
                                    <template v-for="t in tallas">
                                        <td class="colorStockLogico">
                                            <span>
                                                {{ printStockLogico(pc.producto_id, pc.color_id, t.id) }}
                                            </span>
                                        </td>
                                        <td style="width: 5%;" v-if="printStockLogico(pc.producto_id, pc.color_id, t.id) !== 0">
                                            <input
                                            type="text"
                                            class="form-control inputCantidad inputCantidadColor"
                                            :data-producto-id="pc.producto_id"
                                            :data-producto-nombre="pc.producto_nombre"
                                            :data-color-nombre="pc.color_nombre"
                                            :data-talla-nombre="t.descripcion"
                                            :data-color-id="pc.color_id"
                                            :data-talla-id="t.id"
                                            @input="validarContenidoInput($event)"
                                            :disabled="printStockLogico(pc.producto_id, pc.color_id, t.id) === 0"
                                            />
                                        </td>
                                        <td v-else>

                                        </td>
                                    </template>
                               
                                    <td v-if="pc.printPreciosVenta">
                                        <select class="form-control" :id="'precio-venta-' + pc.producto_id" >
                                            <option>{{ pc.precio_venta_1 }}</option>    
                                            <option>{{ pc.precio_venta_2 }}</option>    
                                            <option>{{ pc.precio_venta_3 }}</option>    
                                        </select>
                                    </td>
                                    <td v-else>
                                        
                                    </td> 
                                
                                </tr>
                            </tbody>

                           
                            <tfoot>
                                <!-- <tr>
                                    <th class="text-right" colspan="7">Sub Total:</th>
                                    <th class="text-center">
                                        <span id="subtotal">{{ formDetalles.monto_sub_total }}</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="7">IGV <span id="igv_int">{{
                                            formDetalles.igv_int
                                    }}</span>:</th>
                                    <th class="text-center">
                                        <span id="igv_monto">
                                            {{ formDetalles.monto_total_igv }}
                                        </span>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="7">TOTAL:</th>
                                    <th class="text-center">
                                        <span id="total">
                                            {{ formDetalles.monto_total }}
                                        </span>
                                    </th>
                                </tr> -->
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group row mt-1">
                        <div class="col-lg-2 col-xs-12">
                            <button :disabled="deshabilitarBtnAgregar" 
                            type="button" id="btn_agregar_detalle"
                            @click="agregarProducto"
                                class="btn btn-warning btn-block">
                                <i class="fa fa-plus"></i> AGREGAR
                            </button>
                        </div>
                    </div>
                    <hr>

                    
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
                                    <!-- <th class="text-center">DESCUENTO</th> -->
                                    <!-- <th class="text-center">P. NUEVO</th> -->
                                    <th class="text-center">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="carrito.length > 0">
                                    <tr v-for="(item, index) in carrito" :key="index">
                                        <td class="text-center">
                                            <div class='btn-group'>
                                                <button type="button" class='btn btn-sm btn-danger btn-delete'
                                                    style='color:white' @click.prevent="EliminarItem(item, index)">
                                                    <i class='fa fa-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ item.producto_nombre }}-{{ item.color_nombre }}</td>
                                        <td v-for="t in tallas">
                                            {{ printTallaDetalle(t.id,item) }}
                                        </td>
                                        <td>{{ item.precio_venta }}</td>
                                        <td>{{ item.subtotal }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="tallas.length + 4" class="text-center"><strong>No hay detalles</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td :colspan="tallas.length + 3" style="font-weight: bold;text-align:end;">MONTO SUBTOTAL:</td>
                                    <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">
                                        {{`S/. ${monto_subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`}}
                                    </td>
                                </tr>
                                <tr>
                                    <td :colspan="tallas.length + 3" style="font-weight: bold;text-align:end;">IGV:</td>
                                    <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">
                                        {{`S/. ${monto_igv.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`}}
                                    </td>
                                </tr>
                                <tr>
                                    <td :colspan="tallas.length + 3" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
                                    <td  class="total" colspan="1" style="font-weight: bold;text-align:end;">
                                        {{ `S/. ${monto_total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}` }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table> 

                        <!-- <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <i class="fa fa-dashboard"></i>
                                    </th>
                                    <th class="text-center">CANT</th>
                                    <th class="text-center">UM</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">P. UNITARIO</th>
                                    <th class="text-center">DESCUENTO</th>
                                    <th class="text-center">P. NUEVO</th>
                                    <th class="text-center">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="tablaDetalles.length > 0">
                                    <tr v-for="(item, index) in tablaDetalles" :key="index">
                                        <td class="text-center">
                                            <div class='btn-group'>
                                                <button type="button" class='btn btn-sm btn-warning btn-edit'
                                                    style='color:white' @click.prevent="EditarItem(item)">
                                                    <i class='fa fa-pencil'></i>
                                                </button>
                                                <button type="button" class='btn btn-sm btn-danger btn-delete'
                                                    style='color:white' @click.prevent="EliminarItem(item, index)">
                                                    <i class='fa fa-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ item.cantidad }}</td>
                                        <td class="text-center">{{ item.unidad }}</td>
                                        <td class="text-left">{{ item.producto }}</td>
                                        <td class="text-center">{{ item.precio_unitario }}</td>
                                        <td class="text-center">{{ item.dinero }}</td>
                                        <td class="text-center">{{ item.precio_nuevo }}</td>
                                        <td class="text-center">{{ item.valor_venta }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="8" class="text-center"><strong>no hay detalles</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right" colspan="7">Sub Total:</th>
                                    <th class="text-center">
                                        <span id="subtotal">{{ formDetalles.monto_sub_total }}</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="7">IGV <span id="igv_int">{{
                                            formDetalles.igv_int
                                    }}</span>:</th>
                                    <th class="text-center">
                                        <span id="igv_monto">
                                            {{ formDetalles.monto_total_igv }}
                                        </span>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="7">TOTAL:</th>
                                    <th class="text-center">
                                        <span id="total">
                                            {{ formDetalles.monto_total }}
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>  -->
                    </div>
                </div>
            </div>
        </div>
        <!-- <ModalLotesVue :show.sync="modalLote" :fullAccess="fullaccessTable" :tipoCliente="paramsLotes.tipo_cliente"
            :tipoComprobante="paramsLotes.tipocomprobante" @selectedProductos="ProductoSeleccionado" /> -->
        <!-- <ModalLotesVue :dataLotes="Lotes" :fullAccess="fullaccessTable" :searchInput.sync="searchInput"
            @selectedProductos="ProductoSeleccionado" /> -->
        <!-- <ModalCodigoPrecioMenorVue :estadoPrecioMenor="estadoPrecioMenor"
            @addCodigoPrecioMenor="SaveCodigoPrecioMenor" />
        <ModalEditaDetalleVue :item.sync="itemLote" :detalles.sync="tablaDetalles" /> -->
    </div>
</template>
<script>

// import ModalLotesVue from '../ModalLotes.vue';
import { Empresa } from "../../interfaces/Empresa.js";
import ModalCodigoPrecioMenorVue from './ModalCodigoPrecioMenor.vue';
import { RedondearDecimales } from "../../helpers.js";
import ModalEditaDetalleVue from "./ModalEditDetalle.vue";
import TablaProductos from "./TablaProductos.vue";
import axios from "axios";

export default {
    name: "TablaProductos",
    components: {
    // ModalLotesVue,
    ModalCodigoPrecioMenorVue,
    ModalEditaDetalleVue,
    TablaProductos
},
    props: ["fullaccessTable", "btnDisabled", "parametros",
     "productoTabla", "TotalesObj", 'idcotizacion','modelos','tallas'],
    data() {
        return {
            flujo:[],
            monto_subtotal:0,
            monto_igv:0,
            monto_total:0,
            carrito: [],
            deshabilitarBtnAgregar: true,
            productosPorModelo: {},
            modeloSeleccionado:null,
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
            productoJson: {
                cantidad: 0,
                cantidad_inicial: 0,
                cantidad_logica: 0,
                costo_flete_soles: 0,
                costo_flete: 0,
                costo_flete_dolares: 0,
                cantidad_comprada: 0,
                categoria: "",
                cliente: "",
                codigo_barra: "",
                codigo_lote: "",
                compra_documento_id: "",
                confor_almacen: "",
                created_at: "",
                dolar_compra: "",
                dolar_ingreso: "",
                estado: "",
                facturacion: "",
                fecha_entrega: "",
                fecha_venci: "",
                fecha_vencimiento: "",
                id: "",
                igv: "",
                igv_compra: "",
                marca: "",
                moneda: "",
                moneda_compra: "",
                moneda_ingreso: "",
                nombre: "",
                nota_ingreso_id: "",
                observacion: "",
                porcentaje: "",
                porcentaje_distribuidor: "",
                porcentaje_normal: "",
                precioCompra: "",
                precioDistribuidor: "",
                precioNormal: "",
                precio_compra: "",
                precio_ingreso: "",
                precio_ingreso_soles: "",
                precio_mas_igv_soles: "",
                precio_soles: "",
                producto_id: 0,
                unidad_producto: "",
                updated_at: "",
            },
            Lotes: [],
            paramsLotes: {
                tipo_cliente: 0,
                tipocomprobante: 0,
                search: ""
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
            modalLote: false
        }
    },
    watch: {
        carrito:{
            handler(value){
                const montos = {
                    monto_sub_total  :   this.monto_subtotal,
                    monto_total_igv       :   this.monto_igv,
                    monto_total     :   this.monto_total
                };
                this.$emit('addProductoDetalle', {
                    detalles:   this.carrito,
                    totales :   montos
                });
            },
            deep: true,
        },
        modeloSeleccionado: {
            handler(value) {
                console.log('modeloSeleccionado cambió:', value);
                this.getProductosByModelo();
            },
            deep: true,
        },
        tablaDetalles: {
            handler(value) {
                let total = this.tablaDetalles.reduce((sum, { valor_venta }) => Number(sum + valor_venta), 0);
                console.log(total);
                this.conIgv(total);
                this.$emit("addProductoDetalle", {
                    detalles: this.tablaDetalles,
                    totales: {
                        igv: this.Igv,
                        igv_int: "",
                        monto_sub_total: convertFloat(this.formDetalles.monto_sub_total),
                        monto_total_igv: convertFloat(this.formDetalles.monto_total_igv),
                        monto_total: convertFloat(this.formDetalles.monto_total)
                    }
                });
            },
            deep: true
        },
        codigoPrecioMenor: {
            handler(value) {
                this.estadoPrecioMenor = value.estado_precio_menor;
            },
            deep: true,
        },
        productoJson: {
            handler(value) {
                this.formDetalles.precio = this.evaluarPrecioigv(value);
                this.formDetalles.producto_id = value.id;
                this.formDetalles.producto_lote = `${value.nombre} - ${value.codigo_lote}`;
                this.formDetalles.cantidad = "";
                this.formDetalles.producto_unidad = value.unidad_producto;
            },
            deep: true,
        },
        parametros: {
            handler(value) {
                this.paramsLotes.tipo_cliente = value.tipo_cliente;
                this.paramsLotes.tipocomprobante = value.tipocomprobante;
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
        loadingLotes(value) {
            if (value) {
                this.Lotes = [];
                this.loadingLotes = false;
            }
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
        this.ObtenerCodigoPrecioMenor();
        //============= en caso la ventanta se cierre ===============
        window.addEventListener('beforeunload', async () => {
            if (this.asegurarCierre == 1) {
                 await this.DevolverCantidades();
                 this.asegurarCierre = 10;
            } else {
                 console.log("beforeunload", this.asegurarCierre);
            }
            // await this.DevolverCantidades();
            //this.asegurarCierre = 10;
        });
    },
    methods: {
       async eliminarCarrito(){
            if(this.carrito.length !== 0){
                document.getElementById('overlay').style.display = 'flex';
                try {
                    await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                        carrito: JSON.stringify(this.carrito)
                    })
                    this.carrito = [];
                    this.monto_subtotal=0;
                    this.monto_igv=0;
                    this.monto_total=0;
                    await this.getProductosByModelo();       
                    document.getElementById('overlay').style.display = 'none';
                    toastr.success('Detalle eliminado','Completado');
                } catch (error) {
                    toastr.error('Ocurrio un error al eliminar el detalle','Error');
                } finally{
                    document.getElementById('overlay').style.display = 'none';
                }
            }else{
                toastr.warning('El detalle no tiene productos','Advertencia');
            }
           
        },
        calcularMontos(){
            let subtotal,igv,total = 0;
            this.carrito.forEach((producto)=>{
                total += producto.subtotal;
            })
            igv = 0.18*total;
            subtotal = total-igv;
            

            this.monto_subtotal=subtotal;
            this.monto_igv=igv;
            this.monto_total=total;

            
        },
        async getStockLogico(inputCantidad){
            const producto_id           =   inputCantidad.getAttribute('data-producto-id');
            const color_id              =   inputCantidad.getAttribute('data-color-id');
            const talla_id              =   inputCantidad.getAttribute('data-talla-id');
            
            try {  
                const url = `/get-stocklogico/${producto_id}/${color_id}/${talla_id}`;
                const response = await axios.get(url);
                if(response.data.message=='success'){
                    const stock_logico  =   response.data.data[0].stock_logico;
                    return stock_logico;
                }
                 
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock logico:', error);
                return null;
            }
        },
        printTallaDetalle(talla_id,item){
          
            const itemTalla =  item.tallas.find((t)=>  talla_id==t.talla_id);
            // console.log(itemTalla);
            const cantidad  =    itemTalla?itemTalla.cantidad:0;
            
            return cantidad;
        },
        calcularSubTotal(){
            //======= calculando el subtotal de cada producto color =========
           this.carrito.forEach((item,index)=>{
                const precio_venta = item.precio_venta;
                //===== sumando las cantidades de todas las tallas que tiene el producto color ======
                let subtotal=0;
                item.tallas.forEach((talla)=>{
                    subtotal += (precio_venta * talla.cantidad);
                })
                //======= actualizamos el subtotal de ese producto_color
                this.carrito[index].subtotal=subtotal;
           })
        },
        reordenarCarrito(){
            this.carrito.sort(function(a, b) {
                if (a.producto_id === b.producto_id) {
                    return a.color_id - b.color_id;
                } else {
                    return a.producto_id - b.producto_id;
                }
            });
        },
        async validarCantidadCarrito(inputCantidad){
            const stockLogico           =   await  this.getStockLogico(inputCantidad);
            const cantidadSolicitada    =   inputCantidad.value;
            return stockLogico>=cantidadSolicitada;
        },
        async agregarProducto() {
            
            document.getElementById('overlay').style.display = 'flex';

            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            
            for (const ic of inputsCantidad) {
                ic.classList.remove('inputCantidadIncorrecto');
                const cantidad = ic.value ? ic.value : null;

                if (cantidad) {
                    try {
                        const cantidadValida = await this.validarCantidadCarrito(ic);

                        if (cantidadValida) {
                            const producto      = this.formarProducto(ic);
                            const indiceExiste  = this.carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                            if (indiceExiste == -1) {
                                const objProduct = {
                                    producto_id: producto.producto_id,
                                    color_id: producto.color_id,
                                    producto_nombre: producto.producto_nombre,
                                    color_nombre: producto.color_nombre,
                                    precio_venta: producto.precio_venta,
                                    tallas: [{
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    }]
                                };

                                await this.carrito.push(objProduct);
                                this.asegurarCierre = 1;
                                await this.actualizarStockLogico(producto, "nuevo");
                            } else {
                                const productoModificar = this.carrito[indiceExiste];
                                productoModificar.precio_venta = producto.precio_venta;

                                const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                                if (indexTalla !== -1) {
                                    const cantidadAnterior = productoModificar.tallas[indexTalla].cantidad;
                                    productoModificar.tallas[indexTalla].cantidad = producto.cantidad;
                                    this.carrito[indiceExiste] = productoModificar;
                                    this.asegurarCierre = 1;
                                    await this.actualizarStockLogico(producto, "editar", cantidadAnterior);
                                } else {
                                    const objTallaProduct = {
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    };
                                    console.log(`${productoModificar.producto_nombre} - ${productoModificar.color_nombre}`);
                                    this.carrito[indiceExiste].tallas.push(objTallaProduct);
                                    console.log(this.carrito);
                                    // productoModificar.tallas.push(objTallaProduct);
                                    //this.carrito[indiceExiste] = productoModificar;
                                    //this.carrito.splice(indiceExiste, 1, productoModificar);
                                    this.asegurarCierre = 1;
                                    await this.actualizarStockLogico(producto, "nuevo");
                                }
                            }
                            
                        } else {
                            ic.classList.add('inputCantidadIncorrecto');
                        }

                    } catch (error) {
                        console.error("Error:", error);
                    }

                } else {
                    const producto = this.formarProducto(ic);
                    const indiceProductoColor = this.carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                    if (indiceProductoColor !== -1) {
                        const indiceTalla = this.carrito[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto.talla_id);

                        if (indiceTalla !== -1) {
                            const cantidadAnterior = this.carrito[indiceProductoColor].tallas[indiceTalla].cantidad;
                            this.carrito[indiceProductoColor].tallas.splice(indiceTalla, 1);
                            
                            this.asegurarCierre = 1;
                            await this.actualizarStockLogico(producto, "editar", cantidadAnterior);

                            const cantidadTallas = this.carrito[indiceProductoColor].tallas.length;

                            if (cantidadTallas == 0) {
                                this.carrito.splice(indiceProductoColor, 1);
                            }
                        }
                    }
                }
            }

            this.reordenarCarrito();
            this.calcularSubTotal();
            this.calcularMontos();
            this.getProductosByModelo().then(()=>{
                document.getElementById('overlay').style.display = 'none';
            });
            console.log(this.asegurarCierre);
            //console.log(this.carrito);
        },
        async actualizarStockLogico(producto,modo,cantidadAnterior){
            localStorage.setItem('ultimo', JSON.stringify(producto));
            this.flujo.push(JSON.stringify(producto))

            modo=="eliminar"?this.asegurarCierre=0:this.asegurarCierre=1;

            try {
                await this.axios.post(route('ventas.documento.cantidad'), {
                    'producto_id'   :   producto.producto_id,
                    'color_id'      :   producto.color_id,
                    'talla_id'      :   producto.talla_id,
                    'cantidad'      :   producto.cantidad,
                    'condicion'     :   this.asegurarCierre,
                    'modo'          :   modo,
                    'cantidadAnterior'    :   cantidadAnterior,
                    'tallas'        :   producto.tallas,
                });
                
            } catch (ex) {

            }
        },
        formarProducto(ic){
            const producto_id = ic.getAttribute('data-producto-id');
            const producto_nombre = ic.getAttribute('data-producto-nombre');
            const color_id = ic.getAttribute('data-color-id');
            const color_nombre = ic.getAttribute('data-color-nombre');
            const talla_id = ic.getAttribute('data-talla-id');
            const talla_nombre = ic.getAttribute('data-talla-nombre');
            const precio_venta = document.querySelector(`#precio-venta-${producto_id}`).value;
            const cantidad     = ic.value?ic.value:0;
            const producto = {producto_id,producto_nombre,color_id,color_nombre,
                                talla_id,talla_nombre,cantidad,precio_venta};
            return producto;
        },
        validarContenidoInput(e){
            e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            this.validarCantidadInstantanea(e);
        },
        
        async validarCantidadInstantanea(event) {
            const cantidadSolicitada    =   event.target.value;
            try {
                if(cantidadSolicitada !== ''){
                    const stock_logico  =  await this.getStockLogico(event.target);
                    if(stock_logico < cantidadSolicitada){
                            event.target.classList.add('inputCantidadIncorrecto');
                            event.target.classList.remove('inputCantidadValido');
                            event.target.focus();
                            this.deshabilitarBtnAgregar =   true;
                            toastr.error(`Cantidad solicitada: ${cantidadSolicitada}, debe ser menor o igual
                            al stock lógico: ${stock_logico}`,"Error");
                    }else{
                            event.target.classList.add('inputCantidadValido');
                            event.target.classList.remove('inputCantidadIncorrecto');
                            this.deshabilitarBtnAgregar =   false;
                    }                    
                }else{
                    this.deshabilitarBtnAgregar =   false;
                    event.target.classList.remove('inputCantidadIncorrecto');
                    event.target.classList.remove('inputCantidadValido');
                }   
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock logico:', error);
            }
        },
        async  getProductosByModelo() {
            try {
                const url = `/get-producto-by-modelo/${this.modeloSeleccionado}`;
                const response = await axios.get(url);
                //console.log(response.data);
                this.productosPorModelo = response.data;
                //this.pintarTableStocks(response.data.stocks,tallas,response.data.producto_colores);
            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            }
        },
        printStockLogico(productoId, colorId, tallaId) {
            const stock = this.productosPorModelo.stocks.find(st => st.producto_id === productoId && st.color_id === colorId && st.talla_id === tallaId);
            return stock ? stock.stock_logico : 0;
         },
        // async ExecuteDevolverCantidades() {
        //     if (this.asegurarCierre == 1) {
        //         await this.DevolverCantidades();
        //         this.asegurarCierre = 10;
        //     } else {
        //         console.log("ExecuteDevolverCantidades", this.asegurarCierre);
        //     }
        // }
        async DevolverCantidades() {
            localStorage.setItem('flujo', JSON.stringify(this.carrito));

            await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                // cantidades: JSON.stringify(this.tablaDetalles)
                carrito: JSON.stringify(this.carrito)
            });
        },
        async ObtenerCodigoPrecioMenor() {
            
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getCodigoPrecioMenor"));
                this.dataEmpresa = data;
            } catch (ex) {

            }
        },
        // ModalLotes() {
        //     try {
        //         this.modalLote = true;
        //         // $("#modal_lote").modal("show");
        //         // this.ObtenerLotes();
        //     } catch (ex) {
        //         console.log("error en ModalLotes ", ex);
        //     }
        // },
        //async ObtenerLotes() {
            // try {
            //     const { data } = await this.axios.post(route("ventas.getLoteProductos"), this.paramsLotes);
            //     const { lotes } = data;
            //     this.Lotes = lotes.data;
            // } catch (ex) {
            //     console.log("error en ObtenerLotes ", ex);
            // }
        //},
        evaluarPrecioigv(producto) {
            if (producto.precio_compra == null) {
                let cambio = convertFloat(producto.dolar_ingreso);
                let precio = 0;
                var precio_ = producto.precio_ingreso;
                let porcentaje_ = producto.porcentaje;
                let precio_nuevo = 0;
                if (producto.moneda_ingreso == 'DOLARES') {
                    precio = precio_ * cambio;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                else {
                    precio = precio_;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                return convertFloat(precio_nuevo).toFixed(2);
            } else {
                let cambio = convertFloat(producto.dolar_compra);
                let precio = 0;
                let precio_ = producto.precio_compra;
                let porcentaje_ = producto.porcentaje;
                let precio_nuevo = 0;
                let totalCostoFlote = Number(producto.costo_flete) / Number(producto.cantidad_comprada);
                let costo_flete = convertFloat(totalCostoFlote * (1 + porcentaje_ / 100)).toFixed(2);

                if (producto.moneda_compra == 'DOLARES') {
                    if (producto.igv_compra == 1) {
                        precio = precio_ * cambio;
                        precio_nuevo = precio * (1 + porcentaje_ / 100) + Number(costo_flete)
                    }
                    else {
                        precio = (precio_ * cambio * 1.18)
                        precio_nuevo = precio * (1 + porcentaje_ / 100) + Number(costo_flete)
                    }
                }
                else {
                    if (producto.igv_compra == 1) {
                        precio = precio_;
                        precio_nuevo = precio * (1 + porcentaje_ / 100) + Number(costo_flete)
                    }
                    else {
                        precio = (precio_ * 1.18)
                        precio_nuevo = precio * (1 + porcentaje_ / 100) + Number(costo_flete)
                    }
                }
                return convertFloat(precio_nuevo).toFixed(2);
            }
        },
        ProductoSeleccionado(item) {

            let existe = this.buscarProductoAdded(item.id);
            if (existe) {
                toastr.error('Este Producto ya se encuentra en el detalle.', 'Error');
            } else {
                this.modalLote = false;
                let obj = item;
                for (let key in item) {
                    obj[key] = this.TransformarNumber(item[key]);
                }
                this.productoJson = obj;
            }
        },
        Agregar(condicion = "VISTA") {
            try {
                let enviar = true;
                // let cantidad = !isNaN(Number(this.formDetalles.cantidad)) ? Number(this.formDetalles.cantidad) : 0;
                // let precio = !isNaN(Number(this.formDetalles.precio)) ? Number(this.formDetalles.precio) : 0;
                // let precioActual = this.evaluarPrecioigv(this.productoJson);
                // if (this.productoJson.producto_id == "") {
                //     toastr.error("Seleccione Producto.");
                //     enviar = false;
                // } else {
                //     var existe = this.buscarProductoAdded(this.formDetalles.producto_id)
                //     if (existe) {
                //         toastr.error('Producto ya se encuentra ingresado.', 'Error');
                //         enviar = false;
                //     }
                // }

                // if (this.formDetalles.precio == "") {
                //     toastr.error('Ingrese el precio del producto.', 'Error');
                //     enviar = false;
                // } else {
                //     if (precio == 0) {
                //         toastr.error('Ingrese el precio del producto superior a 0.0.', 'Error');
                //         enviar = false;
                //     }

                //     if (precio < precioActual) {

                //         this.estadoPrecioMenor = this.dataEmpresa.estado_precio_menor;

                //         if (this.dataEmpresa.estado_precio_menor == '1') {

                //             if (this.codigo_precio_menor != this.dataEmpresa.codigo_precio_menor) {

                //                 if (condicion == 'MODAL') {
                //                     toastr.error('El codigo para poder vender a un precio menor a lo establecido es incorrecto.', 'Error');
                //                 } else {
                //                     this.codigo_precio_menor = "";
                //                     $('#modal-codigo-precio').modal('show');
                //                 }

                //                 enviar = false;

                //             } else {
                //                 this.codigo_precio_menor = "";
                //                 $('#modal-codigo-precio').modal('hide');
                //             }
                //         } else {
                //             toastr.error('No puedes vender a un precio menor a lo establecido.', 'Error');
                //             enviar = false;
                //         }
                //     }
                // }

                // if (cantidad == '') {
                //     toastr.error('Ingrese cantidad del artículo.', 'Error');
                //     enviar = false;
                // }

                // if (cantidad == 0) {
                //     enviar = false;
                // }

                if (enviar) {
                    this.asegurarCierre = 1;
                    this.CambiarCantidadLogica(detalle);
                    //this.LlenarDatos();
                }

            } catch (ex) {
                alert(ex);
            }
        },
        LlenarDatos() {
            try {
                let pdescuento = 0;
                let precio_inicial = convertFloat(this.formDetalles.precio);
                let igv = convertFloat(this.formDetalles.igv);
                let igv_calculado = convertFloat(igv / 100);

                let valor_unitario = 0.00;
                let precio_unitario = 0.00;
                let dinero = 0.00;
                let precio_nuevo = 0.00;
                let valor_venta = 0.00;
                let cantidad = convertFloat(this.formDetalles.cantidad);

                precio_unitario = precio_inicial;
                valor_unitario = precio_unitario / (1 + igv_calculado);
                dinero = precio_unitario * (pdescuento / 100);
                precio_nuevo = precio_unitario - dinero;
                valor_venta = precio_nuevo * cantidad;

                let detalle = {
                    producto_id: this.formDetalles.producto_id,
                    unidad: this.formDetalles.producto_unidad,
                    producto: this.formDetalles.producto_lote,
                    precio_unitario: precio_unitario,
                    valor_unitario: RedondearDecimales(valor_unitario),
                    valor_venta: RedondearDecimales(valor_venta),
                    cantidad: cantidad,
                    precio_inicial: precio_inicial,
                    dinero: dinero,
                    descuento: pdescuento,
                    precio_nuevo: precio_nuevo,
                    precio_minimo: convertFloat(this.evaluarPrecioigv(this.productoJson)),
                }

                this.AgregarDatos(detalle);
                this.CambiarCantidadLogica(detalle);

            } catch (ex) {
                alert("Error en LlenarDatos " + ex);
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
        conIgv(total) {
            let subtotal = total / (1 + (this.formDetalles.igv / 100));
            let igv_calculado = total - subtotal;
            this.formDetalles.igv_int = this.formDetalles.igv + '%';
            this.formDetalles.monto_sub_total = subtotal.toFixed(2);
            this.formDetalles.monto_total_igv = igv_calculado.toFixed(2);
            this.formDetalles.monto_total = total.toFixed(2);
        },
        SaveCodigoPrecioMenor(value) {
            const { codigoPrecio, condicion } = value;
            this.codigo_precio_menor = codigoPrecio;
            this.Agregar(condicion);
        },
        TransformarNumber(item) {
            if (item && !isNaN(Number(item))) {

                return parseFloat(item);
            } else {
                return item;
            }
        },
        async CambiarCantidadLogica(detalle) {
            
            try {
                await this.axios.post(route('ventas.documento.cantidad'), {
                    'producto_id': detalle.producto_id,
                    'cantidad': detalle.cantidad,
                    'condicion': this.asegurarCierre,
                });
            } catch (ex) {

            }
        },
        EliminarItem(item, index) {
           
            try {
                //==== devolvemos el stock logico separado ========
                this.actualizarStockLogico(item, "eliminar");
                //====== obtenemos los stocks logicos actualizados de la bd ========
                //======== renderizamos la tabla de stocks ==============
                this.getProductosByModelo();
                //========== eliminar el item del carrito ========
                //============ renderizamos la tabla detalle =======
                this.carrito.splice(index, 1);
                //========= recalcular subtotal,igv,total =========
                this.calcularMontos();
                //======= alerta ======================
                toastr.success('Producto eliminado',"Cantidad devuelta")
            } catch (error) {
                console.error("Error en Eliminar item:", error);
                alert("Error en Eliminar item: " + error);
            } 
            // try {
            //     this.asegurarCierre = 0; //aumentar
            //     this.CambiarCantidadLogica(item);
            //     this.tablaDetalles.splice(index, 1);
            // } catch (ex) {
            //     alert("Error en Eliminar item" + ex);
            // }
        },
        async EditarItem(item) {
            try {
                const { data } = await this.axios.post(route('ventas.documento.obtener.lote'), {
                    lote_id: Number(item.producto_id)
                });
                const { success, lote } = data;
                if (success) {
                    let cantidad_logica = lote.cantidad_logica;

                    this.itemLote.cantidadMax = parseFloat(cantidad_logica) + parseFloat(item.cantidad);
                    this.itemLote.precio = item.precio_inicial;
                    this.itemLote.producto = item.producto;
                    this.itemLote.cantidad = item.cantidad;
                    this.itemLote.unidadMedida = item.unidad;
                    this.itemLote.id = item.producto_id;
                    this.itemLote.precioMinimo = item.precio_minimo;
                    this.itemLote.estado_precio_menor = this.dataEmpresa.estado_precio_menor;
                    this.itemLote.codigo_precio_menor = this.dataEmpresa.codigo_precio_menor;
                    this.itemLote.igv = this.Igv;
                    $('#modal_editar_detalle').modal({ backdrop: 'static', keyboard: false });
                } else {
                    toastr.warning('Ocurrió un error porfavor recargar la pagina.')
                }
            } catch (ex) {

            }
        },
        ChangeAsegurarCierre() {
            this.asegurarCierre = 5;
        }
    }
}
</script>