<template>

    <div class="">

        <EditarItemVue :visible="modalVisible" :title="modalTitle" :tallas="initData.tallas"
            :tallasProducto="tallasProductoEdit" :productoEditar="productoEditar" :detalleVenta="productos_tabla"
            @update-producto="actualizarProducto" @close="closeModal" @show-spinner="mostrarAnimacionVenta"
            @hide-spinner="ocultarAnimacionVenta">
        </EditarItemVue>

        <SpinnerOverlay :visible="spinnerVisible" />

        <div class="wrapper wrapper-content animated fadeInRight content-create" :class="{ 'sk__loading': loading }">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            <form @submit.prevent="Grabar" class="formulario" id="EnviarVenta">
                                <div class="row">

                                    <div class="col-12 col-md-6 b-r">

                                        <div class="row">

                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3"
                                                id="fecha_documento">
                                                <label style="font-weight: bold;">FECHA DOCUMENTO</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>

                                                    <input readonly type="date" id="fecha_documento_campo"
                                                        name="fecha_documento_campo" class="form-control input-required"
                                                        autocomplete="off" required
                                                        v-model="formCreate.fecha_documento_campo">

                                                    <span class="invalid-feedback" role="alert">
                                                        <strong></strong>
                                                    </span>

                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3" id="almacen">

                                                <label style="font-weight: bold;">ALMACÉN</label>

                                                <v-select v-model="almacenSeleccionado" :options="lst_almacenes"
                                                    :reduce="a => a.id" label="descripcion" placeholder="Seleccionar"
                                                    ref="selectAlmacen">
                                                </v-select>

                                            </div>


                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select-required mb-3">
                                                <div class="form-group">
                                                    <label style="font-weight: bold;" class="required">COMPROBANTE
                                                    </label>
                                                    <v-select v-model="tipo_venta" :options="initData.tipoVentas"
                                                        :reduce="tipo => tipo.id" label="nombre"
                                                        placeholder="Seleccionar comprobante...">
                                                    </v-select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select-required mb-3">
                                                <label style="font-weight: bold;">TELÉFONO</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-phone"></i>
                                                        </span>
                                                    </div>
                                                    <input v-model="formCreate.telefono" type="text" maxlength="9"
                                                        class="form-control" placeholder="Ingrese número de teléfono">
                                                </div>
                                                <span class="telefono_error msgError"></span>
                                            </div>

                                            <div
                                                class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select-required mb-3 d-none">
                                                <div class="form-group">
                                                    <label style="font-weight: bold;">MONEDA</label>
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
                                                    <label class="required" style="font-weight:bold;">CONDICIÓN</label>
                                                    <v-select v-model="condicion_id" :options="lst_condiciones"
                                                        :reduce="cn => cn.id" label="descripcion"
                                                        placeholder="Seleccionar condición...">
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
                                                    <label class="required" style="font-weight: bold;">CLIENTE:
                                                        <button type="button" class="btn btn-outline btn-primary"
                                                            @click.prevent="NuevoCliente">
                                                            Registrar
                                                        </button>
                                                    </label>

                                                    <v-select v-model="cliente_id" label="descripcion"
                                                        :options="clientes" :reduce="cl => cl" :filterable="false"
                                                        @search="onSearchCliente" @search:blur="resetClientes"
                                                        placeholder="Buscar cliente...">
                                                        <template v-slot:option="option">
                                                            {{ option.descripcion }}
                                                        </template>
                                                        <template v-slot:selected-option="option">
                                                            {{ option.descripcion }}
                                                        </template>
                                                    </v-select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>

                            <TablaProductos @addProductoDetalle="addProductoDetalle" @addDataEnvio="addDataEnvio"
                                @borrarDataEnvio="borrarDataEnvio" :fullaccessTable="FullaccessTable"
                                :idcotizacion="idcotizacion" :btnDisabled="disabledBtnProducto"
                                :parametros="paramsLotes" :modelos="initData.modelos" :categorias="initData.categorias"
                                :marcas="initData.marcas" :tallas="initData.tallas" :cliente="cliente_id"
                                :almacenSeleccionado="almacenSeleccionado" ref="tablaProductos" />

                            <DetalleDocumentoVue :carrito="carrito" :tallas="initData.tallas"
                                :monto_subtotal="monto_subtotal" :monto_embalaje="monto_embalaje"
                                :monto_envio="monto_envio" :monto_descuento="monto_descuento" :monto_igv="monto_igv"
                                :monto_total="monto_total" :monto_total_pagar="monto_total_pagar"
                                :hayDatosEnvio="hayDatosEnvio" @editarItem="openMdlEditItem"
                                @eliminarItem="onEliminarItem" @descuento="onDescuento" @setDataEnvio="onSetDataEnvio"
                                @update:monto_embalaje="onUpdateEmbalaje" @update:monto_envio="onUpdateEnvio" />

                            <div class="hr-line-dashed"></div>

                            <PagosComponent ref="datosPago" :lstMetodosPago="lst_metodos_pago"
                                :montoTotal="monto_total_pagar" @show-spinner="mostrarAnimacionVenta"
                                @hide-spinner="ocultarAnimacionVenta" @update-pagos="formCreate.lstPagos = $event"
                                @update-isPay="formCreate.isPay = $event" />

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

        </div>

        <ModalClienteVue @newCliente="formAddCliente" :lst_departamentos_base="this.lst_departamentos_base"
            :lst_provincias_base="this.lst_provincias_base" :lst_distritos_base="this.lst_distritos_base"
            :v_sede="this.v_sede" />

        <ModalConsultarStock />

    </div>
</template>

<script>
import ModalClienteVue from '../../../components/ventas/ModalCliente.vue';
import TablaProductos from '../../../components/ventas/TablaProductos.vue';
import EditarItemVue from '../../../components/ventas/EditarItem.vue';
import ModalConsultarStock from '../../../components/ventas/ModalConsultarStock.vue';
import DetalleDocumentoVue from '../../../components/ventas/DetalleDocumento.vue';
import SpinnerOverlay from '../../../components/shared/SpinnerOverlay.vue';
import PagosComponent from '../../../components/ventas/PagosComponent.vue';

export default {
    name: "VentaCreate",
    components: {
        ModalClienteVue,
        TablaProductos,
        EditarItemVue,
        ModalConsultarStock,
        DetalleDocumentoVue,
        SpinnerOverlay,
        PagosComponent,
    },
    props: {
        ruta: {
            type: String,
            required: true
        },
        idcotizacion: {
            type: Number,
            default: 0
        },
        v_sede: {
            default: null
        },
        lst_departamentos_base: {
            type: Array,
            default: []
        },
        lst_provincias_base: {
            type: Array,
            default: []
        },
        lst_distritos_base: {
            type: Array,
            default: []
        },
        lst_almacenes: {
            type: Array,
            default: []
        },
        lst_condiciones: {
            type: Array,
            default: []
        },
        registrador: {
            type: Object,
            default: []
        },
        lst_metodos_pago: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {

            //======= MODAL EDITAR ITEM ======
            productoEditar: { producto_id: null, color_id: null },
            tallasProductoEdit: [],
            modalTitle: '',
            modalVisible: false,

            //====== ALMACÉN =====
            almacenSeleccionado: null,
            initData: {
                condiciones: [],
                empresas: [],
                fullaccess: false,
                tipoVentas: [],
                modelos: [],
                categorias: [],
                marcas: [],
                tallas: [],
                sede_id: null
            },
            formCreate: {
                fecha_documento_campo: "",
                fecha_atencion_campo: "",
                fecha_vencimiento_campo: "",
                tipo_venta: "",
                condicion_id: 1,
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
                monto_sub_total: 0,
                monto_total_igv: 0,
                monto_total: 0,
                tipo_cliente_documento: null,
                moneda: "SOLES",
                data_envio: null,
                telefono: null,

                lstPagos: [],
                isPay: true
            },
            tipo_venta: "",
            condicion_id: 1,

            loading: true,
            paramsLotes: {
                tipo_cliente: '',
                tipocomprobante: '',
            },
            FullaccessTable: false,
            productos_tabla: [],
            estadoFechaVenc: true,
            disabledBtnProducto: true,

            //======= SELECT CLIENTE SERVERSIDE =======
            cliente_id: {
                cliente: null,
                documento: null,
                id: null,
                nombre: null,
                tabladetalles_id: null,
                tipo_documento: null,
                descripcion: null
            },

            clientes: [{
                cliente: 'VARIOS',
                documento: 999999999,
                id: 1,
                nombre: 'CLIENTES VARIOS',
                tabladetalles_id: 0,
                tipo_documento: 'DNI',
                descripcion: "DNI:99999999-CLIENTES VARIOS"
            }],
            search: '',
            page: 1,
            searchTimeout: null,

            //====== ESTADO COMPARTIDO PARA DetalleDocumento ======
            carrito: [],
            monto_subtotal: 0,
            monto_embalaje: 0,
            monto_envio: 0,
            monto_descuento: 0,
            monto_igv: 0,
            monto_total: 0,
            monto_total_pagar: 0,
            hayDatosEnvio: false,

            spinnerVisible: false,
        }
    },
    watch: {
        almacenSeleccionado: {
            handler(value) {
                //===== LIMPIAR FORMULARIO DETALLE ======
                this.$refs.tablaProductos.limpiarFormularioDetalle();
            }
        },
        productos_tabla: {
            handler(value) {
                this.formCreate.productos_tabla = JSON.stringify(value);
            },
            deep: true
        },
        initData: {
            handler(value) {
                const { tipoVentas, condiciones, empresas } = value;
                this.FullaccessTable = value.fullaccess;

                this.formCreate.empresa_id = empresas.length > 0 ? empresas[0].id : 0;
                tipoVentas.forEach(item => {
                    if (item.id == 129) {
                        this.tipo_venta = item.id;
                    }
                });

            },
            deep: true
        },
        tipo_venta(value) {
            this.formCreate.tipo_venta = value;
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
                this.disabledBtnProducto = false;
                this.formCreate.telefono = value.telefono_movil ?? null;
            } else {
                this.formCreate.cliente_id = null;
                this.disabledBtnProducto = true;
            }
        }
    },
    created() {

        this.cliente_id = this.clientes[0];

        //======= SELECCIONAR ALMACÉN PRINCIPAL DE LA SEDE DEL USUARIO ======
        this.lst_almacenes.forEach((a) => {
            if (a.sede_id == this.registrador.sede_id) {
                this.almacenSeleccionado = a.id;
            }
        })

        window.addEventListener("beforeunload", this.handleBeforeUnload);

        this.formCreate.fecha_documento_campo = this.$fechaActual;
        this.formCreate.fecha_atencion_campo = this.$fechaActual;
        this.formCreate.fecha_vencimiento_campo = this.$fechaActual;
        this.ObtenerData();
    },
    mounted() {
    },
    beforeDestroy() {

        this.$refs.tablaProductos.devolverCantidades();

    },
    methods: {
        actualizarProducto(productoEditado) {
            this.$refs.tablaProductos.actualizarItemCarrito(productoEditado);
        },
        borrarDataEnvio() {
            this.formCreate.data_envio = null;
            this.hayDatosEnvio = false;
        },
        resetClientes() {
            this.search = '';
            this.page = 1;
            this.clientes = [];
            this.more = false;
        },

        onSearchCliente(search, loading) {
            this.search = search;

            if (search.length < 3) {
                this.resetClientes();
                return;
            }

            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.page = 1;
                loading(true);

                axios.get(route('utilidades.getClientes'), { params: { search, page: this.page } })
                    .then(res => {
                        this.clientes = res.data.clientes;
                        this.more = res.data.more;
                    })
                    .finally(() => {
                        loading(false);
                    });
            }, 1000);
        },

        handleBeforeUnload(event) {
            this.$refs.tablaProductos?.devolverCantidades();
        },
        openMdlEditItem(producto) {
            this.modalVisible = true;
            this.tallasProductoEdit = producto.tallas;
            this.productoEditar = producto;
        },
        closeModal() {
            this.modalVisible = false;
        },
        // --- delegados desde DetalleDocumento ---
        onEliminarItem(item, index) {
            this.$refs.tablaProductos.EliminarItem(item, index);
        },
        onDescuento(producto_id, color_id, event) {
            this.$refs.tablaProductos.validarDescuento(producto_id, color_id, event);
        },
        onSetDataEnvio() {
            this.$refs.tablaProductos.setDataEnvio();
        },
        onUpdateEmbalaje(val) {
            this.$refs.tablaProductos.monto_embalaje = val;
        },
        onUpdateEnvio(val) {
            this.$refs.tablaProductos.monto_envio = val;
        },
        addDataEnvio(value) {
            this.formCreate.data_envio = JSON.stringify(value);
            this.hayDatosEnvio = true;
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
        NuevoCliente() {
            $("#modal_cliente").modal("show");
        },
        formAddCliente(clienteNuevo) {
            this.clientes.push(clienteNuevo);
            this.cliente_id = clienteNuevo;
        },
        //======= OBTENIENDO CARRITO DEL COMPONENTE HIJO TablaProductos.vue ==========
        addProductoDetalle(value) {
            const { detalles, totales } = value;
            this.productos_tabla = detalles;
            this.formCreate = Object.assign(this.formCreate, totales);
            // feed DetalleDocumento
            this.carrito = detalles;
            this.monto_subtotal = totales.monto_sub_total || 0;
            this.monto_embalaje = totales.monto_embalaje || 0;
            this.monto_envio = totales.monto_envio || 0;
            this.monto_descuento = totales.monto_descuento || 0;
            this.monto_igv = totales.monto_total_igv || 0;
            this.monto_total = totales.monto_total || 0;
            this.monto_total_pagar = totales.monto_total_pagar || 0;
        },
        Grabar() {
            try {

                toastr.clear();
                let correcto = this.validarCampos();

                if (!correcto) return;

                const datosEnvio = this.formCreate.data_envio;
                const descripcion = datosEnvio
                    ? `<span class="text-success">📦 TIENE DATOS DE ENVÍO</span>`
                    : `<span class="text-danger">❌ SIN DATOS DE ENVÍO</span>`;

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: "Desea generar el documento de venta?",
                    html: `${descripcion}`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "SÍ!",
                    cancelButtonText: "NO, CANCELAR!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        this.EnviarVenta();

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire({
                            title: "OPERACIÓN CANCELADA",
                            text: "NO SE REALIZARON ACCIONES",
                            icon: "error"
                        });
                    }
                });

            } catch (ex) {
                toastr.error(ex, 'ERROR EN LA PETICIÓN GENERAR DOCUMENTO DE VENTA');
            }
        },
        async EnviarVenta() {

            Swal.fire({
                title: 'Registrando venta...',
                text: 'Por favor, espera.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {

                limpiarErroresValidacion('msgError');

                this.formCreate.almacenSeleccionado = this.almacenSeleccionado;
                this.formCreate.sede_id = this.initData.sede_id;

                const formData = new FormData();
                // adjuntar imágenes de cada pago
                this.formCreate.lstPagos.forEach((pago, index) => {
                    if (pago.imgPago) {
                        formData.append(`lstImgsPagos[${index}]`, pago.imgPago);
                    }
                });
                // serializar el resto de campos
                for (const key in this.formCreate) {
                    if (key === 'lstPagos') {
                        formData.append('lstPagos', JSON.stringify(this.formCreate.lstPagos));
                    } else if (key !== 'lstImgsPagos') {
                        const val = this.formCreate[key];
                        if (val !== null && val !== undefined) {
                            formData.append(key, val);
                        }
                    }
                }

                const res = await this.axios.post(route('ventas.documento.store'), formData);

                if (res.data.success) {
                    this.$refs.tablaProductos.ChangeAsegurarCierre();
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    const url_open_pdf = route("ventas.documento.comprobante", { id: res.data.documento_id, size: 80 });
                    window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');
                    window.location.href = route("ventas.documento.index");

                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    Swal.close();
                }
            } catch (error) {
                const status = error.response?.status;
                const errors = error.response?.data?.errors;
                Swal.close();

                if (status === 422 && errors) {
                    pintarErroresValidacion(errors, 'error');
                } else if (status) {
                    toastr.error(`Error ${status}: ${error.response?.data?.message || 'Ocurrió un problema'}`);
                } else {
                    toastr.error(error.message || 'Error de conexión con el servidor', 'ERROR');
                }
            }

        },
        validarCampos() {

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
            let cliente_id = this.formCreate.cliente_id.id;
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

            //========= obtenemos al cliente ===============
            let cliente = this.cliente_id;
            //======== si el cliente existe =============
            //======== validación de tipo de comprobantes de venta ===========
            if (cliente.id) {
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


            correcto = this.$refs.datosPago.validar(this.monto_total_pagar);


            return correcto;

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
        },
        mostrarAnimacionVenta() {
            this.spinnerVisible = true;
        },
        ocultarAnimacionVenta() {
            this.spinnerVisible = false;
        }
    },
}
</script>
<style lang="scss">
@media (min-width: 992px) {
    .modal-lg {
        max-width: 1200px;
    }
}

.v-select .vs__dropdown-menu {
    max-height: 200px;
    overflow-y: auto;
}
</style>
