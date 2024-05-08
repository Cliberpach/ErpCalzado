

<template>
    <div class="">
        <div class="wrapper wrapper-content animated fadeInRight content-create" :class="{'sk__loading':loading}">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            <form @submit.prevent="Grabar" class="formulario" id="EnviarVenta">
                                <div class="row">
                                    <div class="col-12 col-md-6 b-r">
                                        <div class="row">
                                            <div class="col-12 col-md-6" id="fecha_documento">
                                                <div class="form-group">
                                                    <label class="">Fecha de Documento</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>

                                                        <input type="date" id="fecha_documento_campo"
                                                            name="fecha_documento_campo"
                                                            class="form-control input-required" autocomplete="off"
                                                            required v-model="formCreate.fecha_documento_campo">
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong></strong>
                                                        </span>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 d-none" id="fecha_entrega">
                                                <div class="form-group">
                                                    <label>Placa</label>

                                                    <input type="text" placeholder="" class="form-control"
                                                        name="observacion" id="observacion" onkeyup="return mayus(this)"
                                                        maxlength="7" v-model="formCreate.observacion">

                                                    <span class="invalid-feedback" role="alert">
                                                        <strong></strong>
                                                    </span>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label class="required">Tipo de Comprobante: </label>
                                                    <v-select v-model="tipo_venta" :options="initData.tipoVentas"
                                                        :reduce="tipo => tipo.id" label="nombre"
                                                        placeholder="Seleccionar comprobante...">
                                                    </v-select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label>Moneda:</label>
                                                    <select id="moneda" name="moneda" class="select2_form form-control"
                                                        disabled>
                                                        <option selected>SOLES</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="row">
                                            <div class="col-12 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label class="required">Condición</label>
                                                    <v-select v-model="condicion_id" :options="initData.condiciones"
                                                        :reduce="cn => `${cn.id}-${cn.descripcion}` "
                                                        label="descripcion" placeholder="Seleccionar condición...">
                                                        <template v-slot:option="option">
                                                            {{ option.descripcion }}
                                                            {{ option.dias > 0 ? option.dias + 'dias' : '' }}
                                                        </template>
                                                    </v-select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6" id="fecha_vencimiento">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Vencimiento</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_vencimiento_campo"
                                                            name="fecha_vencimiento_campo"
                                                            class="form-control input-required" autocomplete="off"
                                                            v-model="formCreate.fecha_vencimiento_campo" required
                                                            :disabled="estadoFechaVenc">
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong></strong>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-8 select-required">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        <button type="button" class="btn btn-outline btn-primary"
                                                            @click.prevent="NuevoCliente">
                                                            Registrar
                                                        </button>
                                                    </label>
                                                    <v-select v-model="cliente_id" :options="initData.clientes"
                                                        :reduce="cl => cl" label="cliente"
                                                        placeholder="Buscar clientes...">
                                                        <template v-slot:option="option">
                                                            {{ option.cliente }}
                                                        </template>
                                                    </v-select>

                                                </div>
                                            </div>
                                            <!-- <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" class="i-checks" name="envio_sunat"
                                                            id="envio_sunat" v-model="formCreate.envio_sunat">
                                                        <b class="text-danger">Enviar a Sunat</b>
                                                    </label>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <TablaProductos @addProductoDetalle="AddProductoDetalles" @addDataEnvio="addDataEnvio"
                                :fullaccessTable="FullaccessTable" :idcotizacion="idcotizacion" :btnDisabled="disabledBtnProducto"
                                :parametros="paramsLotes"
                                :modelos="initData.modelos"
                                :tallas="initData.tallas" :precio_envio="formCreate.precio_envio"
                                :precio_despacho="formCreate.precio_despacho"
                                :cliente="cliente_id"
                            ref="tablaDetalles" />

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                                    <small>Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>

                                <div class="col-md-6 text-right">

                                    <a href="javascript:void(0)" @click.prevent="VolverAIndex" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>

                                    <button type="submit" form="EnviarVenta" id="btn_grabar"
                                        class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i>
                                        Grabar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sk-spinner sk-spinner-wave" :class="{'hide':!loading}">
                <div class="sk-rect1"></div>
                <div class="sk-rect2"></div>
                <div class="sk-rect3"></div>
                <div class="sk-rect4"></div>
                <div class="sk-rect5"></div>
            </div>
        </div>

        <ModalClienteVue @newCliente="formAddCliente" />

    </div>
</template>

<script>
import ModalClienteVue from '../../../components/ventas/ModalCliente.vue';
import TablaProductos from '../../../components/ventas/TablaProductos.vue';

export default {
    name: "VentaCreate",
    components: {
        ModalClienteVue,
        TablaProductos
    },
    props: {
        ruta: {
            type: String,
            required: true
        },
        idcotizacion:{
            type:Number,
            default:0
        }
    },
    data() {
        return {
            checkDespacho: false,
            checkEnvio: false,
            initData: {
                clientes: [],
                condiciones: [],
                dolar: 0,
                empresas: [],
                fecha_hoy: "",
                fullaccess: false,
                vista: "",
                tipoVentas: [],
                modelos: [],
                tallas:[],
            },
            formCreate: {
                fecha_documento_campo: "",
                fecha_atencion_campo: "",
                fecha_vencimiento_campo: "",
                tipo_venta: "",
                condicion_id: "",
                cliente_id: "",
                tipo_pago_id: null,
                efectivo: 0,
                importe: 0,
                empresa_id: 0,
                observacion: "",
                igv: 18,
                igv_check: true,
                cotizacion_id: null,
                productos_tabla: "",
                envio_sunat: false,
                igv: 18,
                monto_sub_total: 0,
                monto_total_igv: 0,
                monto_total: 0,
                tipo_cliente_documento: null,
                moneda: "SOLES",
                data_envio: JSON.stringify({})
            },
            tipo_venta: "",
            condicion_id: "",
            cliente_id: {
                cliente: "",
                documento: "",
                id: 0,
                nombre: "",
                tabladetalles_id: 0,
                tipo_documento: ""
            },
            loading: true,
            loadingLotes: false,
            loadingClienteNew: false,
            paramsLotes: {
                tipo_cliente: '',
                tipocomprobante: '',
            },
            FullaccessTable: false,
            productos_tabla: [],
            estadoFechaVenc: true,
            disabledBtnProducto:true
        }
    },
    filters: {
        truncate: function (data, num) {
            const reqdString =
                data.split("").slice(0, num).join("");
            return reqdString;
        }
    },
    watch: {
        productos_tabla:{
           handler(value){
                this.formCreate.productos_tabla = JSON.stringify(value);
           },
           deep:true
        },
        initData: {
            handler(value) {
                const { tipoVentas, condiciones, clientes, empresas } = value;
                this.FullaccessTable = value.fullaccess;
                clientes.forEach(item => {
                    item.cliente = `${item.tipo_documento}:${item.documento}-${item.nombre}`;
                });

                if (!this.loadingClienteNew) {
                    this.cliente_id = clientes[0];
                    this.formCreate.empresa_id = empresas.length > 0 ? empresas[0].id : 0;
                    tipoVentas.forEach(item => {
                        if (item.id == 129) {
                            this.tipo_venta = item.id;
                        }
                    });

                    condiciones.forEach(item => {
                        if (item.descripcion == "CONTADO") {
                            this.condicion_id = `${item.id}-${item.descripcion}`;
                        }
                    });

                    this.loadingClienteNew = false;
                }
            },
            deep: true
        },
        tipo_venta(value) {
            this.formCreate.tipo_venta = value;
            this.paramsLotes.tipocomprobante = value;
            if (value != 129) {
                this.formCreate.fecha_documento_campo = this.$fechaActual;
            }

            if (value) {
                this.$nextTick(this.getTipoComprobante);
            }

        },
        condicion_id(value) {
            this.formCreate.condicion_id = value;
            let cadena = value.split("-");
            let id = cadena[0];
            let descripcion = cadena[1];
            let dias = 0;
            if (descripcion == "CONTADO") {
                this.estadoFechaVenc = true;
                this.formCreate.fecha_vencimiento_campo = this.$fechaActual;
            } else {
                this.estadoFechaVenc = false;
                this.initData.condiciones.forEach(item => {
                    if (Number(item.id) == Number(id)) {
                        dias = Number(item.dias) + 1;
                    }
                });
                let fecha = new Date(this.$fechaActual);
                fecha.setDate(fecha.getDate() + dias);
                let month = (fecha.getMonth() + 1).toString().length > 1 ? (fecha.getMonth() + 1) : '0' + (fecha.getMonth() + 1)
                let day = (fecha.getDate()).toString().length > 1 ? (fecha.getDate()) : '0' + (fecha.getDate())
                let resultado = fecha.getFullYear() + '-' + month + '-' + day;
                this.formCreate.fecha_vencimiento_campo = resultado;
            }

        },
        cliente_id(value) {
            if (value) {
                this.formCreate.cliente_id = value.id;
                this.paramsLotes.tipo_cliente = value.tabladetalles_id;
                this.disabledBtnProducto=false;
            } else {
                this.formCreate.cliente_id = null;
                this.disabledBtnProducto=true;
            }
        }
    },
    created() {
        this.formCreate.fecha_documento_campo = this.$fechaActual;
        this.formCreate.fecha_atencion_campo = this.$fechaActual;
        this.formCreate.fecha_vencimiento_campo = this.$fechaActual;
        this.ObtenerData();
    },
    beforeDestroy() {
        console.log('destruyendo',this.$refs.tablaDetalles.asegurarCierre);
        if(this.$refs.tablaDetalles.asegurarCierre == 1){
            this.$refs.tablaDetalles.DevolverCantidades();
        }
    },
    methods: {
        addDataEnvio(value){
            // const { departamento,provincia,distrito,tipo_envio,empresa_envio,sede_envio,destinatario } = value;
            this.formCreate.data_envio             = value;
            console.log(this.formCreate);
        },
        formatearDetalle(detalles){
            if(detalles.length>0){
                let carritoFormateado   =   [];
                detalles.forEach((d)=>{
                    d.tallas.forEach((t)=>{
                        const producto ={};
                        producto.producto_id            =   d.producto_id;
                        producto.color_id               =   d.color_id;
                        producto.talla_id               =   t.talla_id;
                        producto.cantidad               =   t.cantidad;
                        producto.precio_unitario        =   d.precio_venta;  
                        producto.porcentaje_descuento   =   d.porcentaje_descuento;
                        producto.precio_unitario_nuevo  =   d.precio_venta_nuevo;
                        carritoFormateado.push(producto);
                    })
                })
                return carritoFormateado;
            }
            return [];
        },
        async ObtenerData() {
            try {
                this.loading = true;
                const { data } = await this.axios.post(route("ventas.documento.getCreate"));
                const { success, initData } = data;
                this.initData = initData;
                this.loading = false;
            } catch (ex) {

            }
        },
        Volver() {
            this.$emit("update:ruta", "index");
        },
        selectedCliente(value) {
            console.log(value);
        },

        NuevoCliente() {
            $("#modal_cliente").modal("show");
        },
        formAddCliente(value) {
            this.loadingClienteNew = true;
            console.log(value);
            const { cliente, dataCliente } = value;
            this.initData.clientes = dataCliente;
            this.cliente_id = cliente;
        },
        //======= obteniendo carrito del componente hijo TablaProductos.vue ==========
        AddProductoDetalles(value) {
            const { detalles, totales } = value;
            this.productos_tabla        =   this.formatearDetalle(detalles);
            //this.productos_tabla    = detalles;
            this.formCreate             = Object.assign(this.formCreate, totales);
            console.log(this.formCreate);
        },
        Grabar() {
            try {
                let correcto = this.validarCampos();
                if (correcto) {
                    this.EnviarVenta();
                }
            } catch (ex) {

            }
        },
        async EnviarVenta() {
            try {

                //=== desactivar botón grabar ======
                document.querySelector('#btn_grabar').disabled=true;

                //======= spiner de carga =========
                this.loading = true;

                //======== verificar si existen cajas abiertas ==========
                const { data } = await this.axios.get(route('Caja.movimiento.verificarestado'));
                //======= desestructuración de objeto ===========
                const { success } = data;

                //======== si existen cajas abiertas ===========
                if (success) {

                     let envio_ok = true;

                     var tipo = this.validarTipo();

                     if (!tipo) {
                         envio_ok = false;
                     }

                     if (envio_ok) {
                        console.log(this.formCreate)
                        const { data } = await this.axios.post(route("ventas.documento.store"), this.formCreate);
                        const { success, documento_id } = data;


                        if (success) {
                            

                            toastr.success('¡Documento de venta creado!', 'Exito');

                            var url_open_pdf = route("ventas.documento.comprobante", { id: documento_id +"-80"});

                            window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');

                             this.$refs.tablaDetalles.ChangeAsegurarCierre();


                            this.$emit("update:ruta", "index");

                        } else {
                            document.querySelector('#btn_grabar').disabled=false;
                            this.loading = false;
                            toastr.error('Error al crear documento de venta', 'Error');
                        }
                     }
                }

                //======== en caso no existan cajas abiertas ===========
                if(!success){
                    document.querySelector('#btn_grabar').disabled=false;
                    toastr.error('Debe aperturar una caja previamente',"Error");
                    this.loading = false;
                }

            } catch (ex) {
                document.querySelector('#btn_grabar').disabled=false;
                this.loading = false;
                alert("Error en EnviarVentas " + ex);
            }
        },
        validarTipo() {

            var enviar = true

            if (this.formCreate.tipo_cliente_documento == '0' && this.formCreate.tipo_venta == 127) {
                toastr.error('El tipo de documento del cliente es diferente a RUC.', 'Error');
                enviar = false;
            }
            return enviar
        },
        validarCampos() {
            try {
                //====== variable para manejar la validación =========
                let correcto = true;
                //===== moneda por defecto soles ===========
                let moneda = this.formCreate.moneda;
                let observacion = this.formCreate.observacion;
                //==== contado-credito ========
                let condicion_id = this.formCreate.condicion_id;
                let fecha_documento_campo = this.formCreate.fecha_documento_campo;
                let fecha_atencion_campo = this.formCreate.fecha_atencion_campo;
                let fecha_vencimiento_campo = this.formCreate.fecha_vencimiento_campo;
                let empresa_id = this.formCreate.empresa_id;
                let cliente_id = this.formCreate.cliente_id;
                //===== 127:factura | 128:boleta | 129:nota_venta =========
                let tipo_venta = this.formCreate.tipo_venta;

                if (this.productos_tabla.length == 0) {
                    toastr.error("El documento de venta debe tener almenos un producto vendido.");
                    correcto = false;
                }
                if (moneda == null || moneda == '') {
                    correcto = false;
                    toastr.error('El campo moneda es requerido.');
                }
                if (condicion_id == null || condicion_id == '') {
                    correcto = false;
                    toastr.error('El campo condicion de pago es requerido.');
                }
                if (fecha_documento_campo == null || fecha_documento_campo == '') {
                    correcto = false;
                    toastr.error('El campo fecha de documento es requerido.');
                }
                if (fecha_atencion_campo == null || fecha_atencion_campo == '') {
                    correcto = false;
                    toastr.error('El campo fecha de atención es requerido.');
                }
                if (fecha_vencimiento_campo == null || fecha_vencimiento_campo == '') {
                    correcto = false;
                    toastr.error('El campo fecha de vencimiento es requerido.');
                }

                if (this.initData.clientes.length > 0) {
                    let index = this.initData.clientes.findIndex(cliente => Number(cliente.id) == Number(cliente_id));
                    //======= si el cliente existe ==============
                    if (index != undefined) {
                        //========= obtenemos al cliente ===============
                        let cliente = this.initData.clientes[index];
                        //======== si el cliente existe =============
                        //======== validación de tipo de comprobantes de venta ===========
                        if (cliente != undefined) {
                            if (convertFloat(tipo_venta) === 127 && cliente.tipo_documento != 'RUC') {
                                correcto = false;
                                toastr.error('El tipo de comprobante seleccionado requiere que el cliente tenga RUC.');
                            }

                            if (convertFloat(tipo_venta) === 128 && cliente.tipo_documento != 'DNI') {
                                correcto = false;
                                toastr.error('El tipo de comprobante seleccionado requiere que el cliente tenga DNI.');
                            }
                        }
                        else {
                            correcto = false;
                            toastr.error('Ocurrió un error porfavor seleccionar nuevamente un cliente.');
                        }
                    }
                    else {
                        correcto = false;
                        toastr.error('Ocurrió un error porfavor seleccionar nuevamente un cliente.');
                    }
                }
                return correcto;
            } catch (ex) {
                alert("Validar campo" + ex);
                return false;
            }
        },
        async getTipoComprobante() {
            try {
                const { data } = await this.axios.post(route('ventas.vouchersAvaible'), {
                    'empresa_id': this.formCreate.empresa_id,
                    'tipo_id': this.formCreate.tipo_venta
                });
                const { existe, empresa, comprobante } = data;

                if (!existe) {
                    toastr.error('La empresa ' + empresa +
                        ' no tiene registrado el comprobante ' + comprobante, 'Error');
                } else {
                    toastr.success('La empresa ' + empresa +
                        ' tiene registrado el comprobante ' + comprobante,
                        'Accion Correcta');
                }

            } catch (ex) {

            }
        },
        VolverAIndex() {
            this.$emit("update:ruta", "index");
        }
    },
}
</script>
<style lang="scss">
div.content-create {
    position: relative;
}

div.content-create.sk__loading::after {
    content: '';
    background-color: rgba(255, 255, 255, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
}

.content-create.sk__loading>.sk-spinner.sk-spinner-wave {
    margin: 0 auto;
    width: 50px;
    height: 30px;
    text-align: center;
    font-size: 10px;
}

.content-create.sk__loading>.sk-spinner {
    display: block;
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    z-index: 2000;
}

.content-create .sk-spinner.sk-spinner-wave.hide {
    display: none;
}

@media (min-width: 992px) {
    .modal-lg {
        max-width: 1200px;
    }
}
</style>