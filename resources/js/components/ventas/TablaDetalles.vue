<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class=""><b>Detalle del Documento de Venta</b></h4>
                </div>
                <div class="panel-body ibox-content">
                    <div class="row" v-if="idcotizacion == 0">
                        <div class="col-lg-6 col-xs-12">
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
                        </div>
                        <input type="hidden" name="producto_id" id="producto_id">
                        <input type="hidden" name="producto_unidad" id="producto_unidad">
                        <input type="hidden" name="producto_json" id="producto_json">
                        <div class="col-lg-2 col-xs-12">
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
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <ModalLotesVue :show.sync="modalLote" :fullAccess="fullaccessTable" :tipoCliente="paramsLotes.tipo_cliente"
            :tipoComprobante="paramsLotes.tipocomprobante" @selectedProductos="ProductoSeleccionado" />
        <!-- <ModalLotesVue :dataLotes="Lotes" :fullAccess="fullaccessTable" :searchInput.sync="searchInput"
            @selectedProductos="ProductoSeleccionado" /> -->
        <ModalCodigoPrecioMenorVue :estadoPrecioMenor="estadoPrecioMenor"
            @addCodigoPrecioMenor="SaveCodigoPrecioMenor" />
        <ModalEditaDetalleVue :item.sync="itemLote" :detalles.sync="tablaDetalles" />
    </div>
</template>
<script>

import ModalLotesVue from '../ModalLotes.vue';
import { Empresa } from "../../interfaces/Empresa.js";
import ModalCodigoPrecioMenorVue from './ModalCodigoPrecioMenor.vue';
import { RedondearDecimales } from "../../helpers.js";
import ModalEditaDetalleVue from "./ModalEditDetalle.vue";
export default {
    name: "TablaDetalles",
    components: {
        ModalLotesVue,
        ModalCodigoPrecioMenorVue,
        ModalEditaDetalleVue
    },
    props: ["fullaccessTable", "btnDisabled", "parametros", "productoTabla", "TotalesObj", 'idcotizacion'],
    data() {
        return {
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

        window.addEventListener('beforeunload', async () => {
            if (this.asegurarCierre == 1) {
                await this.DevolverCantidades();
                this.asegurarCierre = 10;
            } else {
                console.log("beforeunload", this.asegurarCierre);
            }
        });
    },

    methods: {
        async ExecuteDevolverCantidades() {
            if (this.asegurarCierre == 1) {
                await this.DevolverCantidades();
                this.asegurarCierre = 10;
            } else {
                console.log("ExecuteDevolverCantidades", this.asegurarCierre);
            }
        },
        async DevolverCantidades() {
            await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                cantidades: JSON.stringify(this.tablaDetalles)
            });
        },
        async ObtenerCodigoPrecioMenor() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getCodigoPrecioMenor"));
                this.dataEmpresa = data;
            } catch (ex) {

            }
        },
        ModalLotes() {
            try {
                this.modalLote = true;
                // $("#modal_lote").modal("show");
                // this.ObtenerLotes();
            } catch (ex) {
                console.log("error en ModalLotes ", ex);
            }
        },
        async ObtenerLotes() {
            // try {
            //     const { data } = await this.axios.post(route("ventas.getLoteProductos"), this.paramsLotes);
            //     const { lotes } = data;
            //     this.Lotes = lotes.data;
            // } catch (ex) {
            //     console.log("error en ObtenerLotes ", ex);
            // }
        },
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
                let cantidad = !isNaN(Number(this.formDetalles.cantidad)) ? Number(this.formDetalles.cantidad) : 0;
                let precio = !isNaN(Number(this.formDetalles.precio)) ? Number(this.formDetalles.precio) : 0;
                let precioActual = this.evaluarPrecioigv(this.productoJson);
                if (this.productoJson.producto_id == "") {
                    toastr.error("Seleccione Producto.");
                    enviar = false;
                } else {
                    var existe = this.buscarProductoAdded(this.formDetalles.producto_id)
                    if (existe) {
                        toastr.error('Producto ya se encuentra ingresado.', 'Error');
                        enviar = false;
                    }
                }

                if (this.formDetalles.precio == "") {
                    toastr.error('Ingrese el precio del producto.', 'Error');
                    enviar = false;
                } else {
                    if (precio == 0) {
                        toastr.error('Ingrese el precio del producto superior a 0.0.', 'Error');
                        enviar = false;
                    }

                    if (precio < precioActual) {

                        this.estadoPrecioMenor = this.dataEmpresa.estado_precio_menor;

                        if (this.dataEmpresa.estado_precio_menor == '1') {

                            if (this.codigo_precio_menor != this.dataEmpresa.codigo_precio_menor) {

                                if (condicion == 'MODAL') {
                                    toastr.error('El codigo para poder vender a un precio menor a lo establecido es incorrecto.', 'Error');
                                } else {
                                    this.codigo_precio_menor = "";
                                    $('#modal-codigo-precio').modal('show');
                                }

                                enviar = false;

                            } else {
                                this.codigo_precio_menor = "";
                                $('#modal-codigo-precio').modal('hide');
                            }
                        } else {
                            toastr.error('No puedes vender a un precio menor a lo establecido.', 'Error');
                            enviar = false;
                        }
                    }
                }

                if (cantidad == '') {
                    toastr.error('Ingrese cantidad del artículo.', 'Error');
                    enviar = false;
                }

                if (cantidad == 0) {
                    enviar = false;
                }

                if (enviar) {
                    this.asegurarCierre = 1;
                    this.LlenarDatos();
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
                this.asegurarCierre = 0;
                this.CambiarCantidadLogica(item);
                this.tablaDetalles.splice(index, 1);
            } catch (ex) {
                alert("Error en Eliminar item" + ex);
            }
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