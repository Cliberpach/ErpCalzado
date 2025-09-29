<template>
    <div class="modal inmodal" id="modal_envio" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <i class="fa fa-truck modal-icon"></i>
                    <h4 class="modal-title">DATOS DE ENVÍO</h4>
                    <small class="font-bold">Registrar</small>
                </div>
                <div class="modal-body content_cliente" :class="{ 'sk__loading': loading }">
                    <form id="frmEnvio" class="formulario" @submit.prevent="Guardar">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="row justify-content-between">
                                    <div class="col-6">
                                        <label for="" style="font-weight: bold;">UBIGEO</label>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end">
                                        <button @click="borrarDataEnvio" type="button" class="btn btn-danger">BORRAR
                                            ENVÍO</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="departamento"
                                                style="font-weight: bold;">DEPARTAMENTO</label>
                                            <v-select placeholder="SELECCIONAR" v-model="departamento"
                                                :options="Departamentos" :reduce="d => d.id" required label="nombre"
                                                :clearable="false"></v-select>
                                        </div>
                                        <span class="departamento_error msgError"></span>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="provincia"
                                                style="font-weight: bold;">PROVINCIA</label>
                                            <v-select placeholder="SELECCIONAR" v-model="provincia"
                                                :options="Provincias" :reduce="p => p.id" required label="text"
                                                :clearable="false"></v-select>
                                        </div>
                                        <span class="provincia_error msgError"></span>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="distrito"
                                                style="font-weight: bold;">DISTRITO</label>
                                            <v-select placeholder="SELECCIONAR" v-model="distrito" :options="Distritos"
                                                :reduce="d => d.id" required label="text" :clearable="false"></v-select>
                                        </div>
                                        <span class="distrito_error msgError"></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="required" for="" style="font-weight: bold;">TIPO DE ENVÍO</label>
                                        <v-select placeholder="SELECCIONAR" v-model="tipo_envio" :options="tipos_envios"
                                            :reduce="te => te.id" required :clearable="false"
                                            label="descripcion"></v-select>
                                        <span class="tipo_envio_error msgError"></span>
                                    </div>
                                    <div class="col-4">
                                        <label class="required" for="" style="font-weight: bold;">TIPO PAGO</label>
                                        <v-select v-model="tipo_pago_envio" :options="tipos_pago_envio"
                                            :reduce="tp => tp.id" required :clearable="false"
                                            label="descripcion"></v-select>
                                        <span class="tipo_pago_envio_error msgError"></span>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <label class="required" for="vselectEmpresa"
                                            style="font-weight: bold;">EMPRESAS</label>
                                        <v-select placeholder="SELECCIONAR" v-model="empresa_envio"
                                            :options="empresas_envio" :reduce="ee => ee.id" label="empresa"
                                            id="vselectEmpresa" ref="vselectEmpresa"></v-select>
                                        <span class="empresa_envio_error msgError"></span>
                                    </div>
                                    <div class="col-6" v-if="mostrar_combo_sedes">
                                        <label class="required" for="" style="font-weight: bold;">SEDES</label>
                                        <v-select placeholder="SELECCIONAR" :required="mostrar_combo_sedes"
                                            v-model="sede_envio" :options="sedes_envio" :reduce="se => se.id"
                                            label="direccion"></v-select>
                                        <span class="sede_envio_error msgError"></span>
                                    </div>
                                </div>
                                <div class="row mt-3" v-if="mostrar_entrega_domicilio">
                                    <div class="col-4 d-flex align-items-center">
                                        <div class="row" style="width: 100%;">
                                            <div class="col-2 pr-0 d-flex align-items-center">
                                                <input style="width: 50px;" id="check_entrega_domicilio" type="checkbox"
                                                    v-model="entrega_domicilio" class="form-control">
                                            </div>
                                            <div class="col-9 pl-0">
                                                <label for="check_entrega_domicilio" class="mb-0"
                                                    style="font-weight: bold;">ENTREGA EN DOMICILIO</label>
                                            </div>
                                        </div>
                                        <span class="entrega_domicilio_error msgError"></span>
                                    </div>
                                    <div class="col-7">
                                        <label :class="{ 'required': entrega_domicilio }" for=""
                                            style="font-weight: bold;">DIRECCION DE ENTREGA</label>
                                        <input maxlength="150" :readonly="!entrega_domicilio"
                                            :required="entrega_domicilio" type="text" class="form-control"
                                            v-model="direccion_entrega">
                                        <span class="direccion_entrega_error msgError"></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        <label for="origen_venta" style="font-weight: bold;">ORIGEN VENTA</label>
                                        <v-select placeholder="SELECCIONAR" v-model="origen_venta"
                                            :options="origenes_ventas" :reduce="ov => ov.id" label="descripcion"
                                            :clearable="false"></v-select>
                                        <span class="origen_venta_error msgError"></span>
                                    </div>
                                    <div class="col-3">
                                        <label for="fecha_envio" style="font-weight: bold;">FECHA ENVÍO</label>
                                        <input id="fecha_envio" v-model="fecha_envio" type="date" class="form-control">
                                        <span class="fecha_envio_error msgError"></span>
                                    </div>
                                    <div class="col-3">
                                        <label for="obs_rotulo" style="font-weight: bold;">OBS RÓTULO</label>
                                        <textarea maxlength="35" id="obs_rotulo" v-model="obs_rotulo"
                                            class="form-control"></textarea>
                                        <span class="obs_rotulo_error msgError"></span>
                                    </div>
                                    <div class="col-3">
                                        <label for="obs_despacho" style="font-weight: bold;">OBS DESPACHO</label>
                                        <textarea id="obs_despacho" v-model="obs_despacho"
                                            class="form-control"></textarea>
                                        <span class="obs_despacho_error msgError"></span>
                                    </div>
                                </div>
                                <hr>
                                <label for="" style="font-weight: bold;">DATOS DEL DESTINATARIO</label>
                                <div class="row">
                                    <div class="col-3">
                                        <label class="required" for="tipo_doc" style="font-weight: bold;">TIPO
                                            DOC</label>
                                        <v-select v-model="destinatario.tipo_documento" :options="tipoDocumentos"
                                            :reduce="td => td" label="" :clearable="false"></v-select>
                                    </div>
                                    <div class="col-4">
                                        <label class="required" for="dni_destinatario" style="font-weight: bold;">NRO.
                                            {{
                                                destinatario.tipo_documento }}</label>
                                        <div class="input-group">
                                            <input type="text" id="dni_destinatario" class="form-control"
                                                :maxlength="maxLengthDocumento" v-model="destinatario.nro_documento"
                                                required>
                                            <span class="input-group-append">
                                                <button type="button" style="color:white" class="btn btn-success"
                                                    v-if="destinatario.tipo_documento == 'DNI'"
                                                    @click.prevent="consultarDocumento">
                                                    <i class="fa fa-search"></i>
                                                    <span id="entidad"> CONSULTAR</span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <label class="required" for="nombres_destinatario"
                                            style="font-weight: bold;">NOMBRES</label>
                                        <input required type="text" id="nombres_destinatario"
                                            v-model=destinatario.nombres class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="sk-spinner sk-spinner-wave" :class="{ 'hide-cliente': !loading }">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                            campos
                            marcados con asterisco (*) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-success btn-sm" form="frmEnvio" style="color:white;"><i
                                class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "ModalEnvio",
    props: ['cliente'],
    data() {
        return {
            hayDatosEnvio: false,
            tipoDocumentos: [],
            despacho: null,
            mode: 'create',
            loading: false,
            direccion_entrega: "",
            entrega_domicilio: false,
            tipos_pago_envio: [],
            tipo_pago_envio: null,
            mostrar_combo_sedes: true,
            mostrar_entrega_domicilio: true,
            origenes_ventas: [],
            empresas_envio: [],
            sedes_envio: [],
            tipos_envios: [],
            Departamentos: [],
            Provincias: [],
            Distritos: [],
            origen_venta: null,
            fecha_envio: "",
            obs_rotulo: "",
            obs_despacho: "",
            destinatario: {
                tipo_documento: "SELECCIONAR",
                nro_documento: "",
                nombres: ""
            },
            departamento: null,
            provincia: null,
            distrito: null,
            tipo_envio: null,
            empresa_envio: null,
            sede_envio: null,
            formEnvio: {
                departamento: null,
                provincia: null,
                distrito: null,
                tipo_envio: null,
                empresa_envio: null,
                sede_envio: null,
                destinatario: null
            },
            entidad: "Entidad",
            dataDNI: {
                apellido_materno: "",
                apellido_paterno: "",
                codigo_verificacion: 0,
                departamento: "",
                direccion: "0",
                direccion_completa: "",
                distrito: "",
                nombre_completo: "",
                nombres: "",
                numero: "",
                provincia: "",
                ubigeo: [],
                ubigeo_reniec: "",
                ubigeo_sunat: "",
                buscado: false
            },
            dataRUC: {
                anexos: [],
                condicion: "",
                departamento: "",
                direccion: "",
                direccion_completa: "",
                distrito: "",
                es_agente_de_retencion: null,
                estado: "",
                nombre_o_razon_social: "",
                provincia: "",
                ruc: "",
                ubigeo: [],
                ubigeo_sunat: "",
                buscado: false
            },
            loadingProvincias: false,
            loadingDistritos: false,
            maxlength: 8
        }
    },
    computed: {
        maxLengthDocumento() {

            if (this.destinatario.tipo_documento === "DNI" && this.destinatario.nro_documento.length > 8) {
                this.destinatario.nro_documento = '';
            }
            return this.destinatario.tipo_documento === 'DNI' ? 8 : 20;
        }
    },
    watch: {
        fecha_envio(value) {
            if (value.length == 0) {
                this.setFechaEnvioDefault();
            }
        },
        entrega_domicilio(value) {
            if (!value) {
                //====== LIMPIAR LA DIRECCION DE ENTREGA =========
                this.direccion_entrega = "";
            }
        },
        async cliente(value) {

            if (this.hayDatosEnvio) {
                return;
            }
            console.log('WATCH CLIENTE', value);

            this.destinatario.nro_documento = "";
            this.destinatario.nombres = "";

            if (value.tipo_documento === "DNI" || value.tipo_documento === "CARNET EXT.") {
                this.destinatario.tipo_documento = value.tipo_documento;
                if (value.documento !== "99999999") {
                    this.destinatario.nro_documento = value.documento;
                    this.destinatario.nombres = value.nombre;
                }
            }

            //======== COLOCAR DATA EN EL MODAL ========
            this.departamento = null;
            this.$nextTick(() => {
                console.log('SET DEPARTAMENTO ID');
                this.departamento = parseInt(value.departamento_id);
            });

            //=========== ESPERAR A QUE EL UBIGEO SE COLOQUE COMPLETAMENTE ==========
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            console.log('SET PROVINCIA ID');
            //====== SETTEAR PROVINCIA Y DISTRITO ======
            this.provincia = null
            this.$nextTick(() => {
                this.provincia = parseInt(value.provincia_id)
            })

            //=========== ESPERAR A QUE EL UBIGEO SE COLOQUE COMPLETAMENTE ==========
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            console.log('SET DISTRITO ID');
            this.distrito = null
            this.$nextTick(() => {
                this.distrito = parseInt(value.distrito_id)
            })
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            this.tipo_envio = 188;

        },
        empresa_envio(value) {
            console.log('WATCH EMPRESA ENVÍO', value);
            //====== LIMPIAR LAS SEDES ======
            this.sede_envio = null;
            this.sedes_envio = [];

            if (value) {
                if (this.tipo_envio == 189) { //====== DELIVERY ======
                    this.sede_envio = null;
                }

                const ubigeo = JSON.stringify([this.departamento, this.provincia, this.distrito]);
                this.getSedesEnvio(value, ubigeo);
            }
        },
        tipo_envio(value) {

            console.log('WATCH TIPO ENVÍO');

            //=======  LIMPIAR EMPRESA ENVIO ===
            this.empresa_envio = null;

            //==== LIMPIAR SEDE ENVIO =====
            this.sede_envio = null;

            console.log('TIPO ENVIO', value);

            if (value) {

                //====== AGENCIA ======
                /*  ->TIENE SEDES
                    ->NO HAY CONTRAENTREGA
                    ->PUEDE HABER ENTREGA A DOMICILIO
                */
                if (value === 188) {
                    this.mostrar_combo_sedes = true;
                    this.mostrar_entrega_domicilio = true;
                }

                //====== DELIVERY ======
                /*  ->NO TIENE SEDES
                    ->PUEDE HABER CONTRAENTREGA
                    ->HAY ENTREGA A DOMICILIO SIEMPRE
                */

                if (value === 189) {
                    this.mostrar_combo_sedes = false;
                    this.mostrar_entrega_domicilio = true;
                    this.entrega_domicilio = true;
                }


                //====== RECOJO EN TIENDA ======
                /*
                    ->TIENE SEDES
                    ->NO HAY CONTRAENTREGA
                    ->NO HAY ENTREGA A DOMICILIO
                */
                if (value === 190) {
                    this.mostrar_combo_sedes = true;
                    this.mostrar_entrega_domicilio = false;
                    this.entrega_domicilio = false;
                }

                //====== OBTENIENDO EMPRESAS DE ENVIO =====
                this.getEmpresasEnvio(value);
            }
        },
        tipoClientes(value) {
            this.tipo_cliente_id = value.length > 0 ? value[0].id : "";
        },
        departamento(value) {
            if (value) {
                console.log('WATCH DEPARTAMENTO');
                //=======  LIMPIAR EMPRESA ENVIO ===
                this.empresa_envio = null;
                //======= LIMPIANDO SEDES =====
                this.sedes_envio = [];
                this.sede_envio = null;
                this.$nextTick(this.getProvincias);
            }
        },
        provincia(value) {
            if (value) {
                console.log('WATCH PROVINCIA');
                //=======  LIMPIAR EMPRESA ENVIO ===
                this.empresa_envio = null;

                //======= LIMPIANDO SEDES =====
                this.sede_envio = null;
                this.$nextTick(this.getDistritos);
            }
        },
        distrito(value) {
            //=======  LIMPIAR EMPRESA ENVIO ===
            this.empresa_envio = null;

            //======= LIMPIANDO SEDES =====
            this.sedes_envio = [];
            this.sede_envio = null;
            this.$emit('ubigeoCompletado');
        },
        tipo_documento(value) {
            if (value) {
                this.formCliente.tipo_documento = value;
                this.formCliente.activo = "SIN VERIFICAR";
                this.entidad = value == "DNI" ? "Reniec" : (value == "RUC" ? "Sunat" : "Entidad");

                if (value == "DNI") {
                    this.maxlength = 8;
                } else if (value == "RUC") {
                    this.maxlength = 11;
                } else {
                    this.maxlength = 20;
                }
            }
        },
        tipo_cliente_id(value) {
            this.formCliente.tipo_cliente_id = value;
        },
        loadingProvincias(value) {
            if (this.dataDNI.buscado && value) {
                let iddprov = this.dataDNI.ubigeo.length > 0 ? this.dataDNI.ubigeo[1] : 0;
                this.Provincias.forEach(item => {
                    if (Number(item.id) == Number(iddprov)) {
                        this.provincia = item;
                    }
                });
            }

            if (this.dataRUC.buscado && value) {
                let iddprov = this.dataRUC.ubigeo.length > 0 ? this.dataRUC.ubigeo[1] : 0;
                this.Provincias.forEach(item => {
                    if (Number(item.id) == Number(iddprov)) {
                        this.provincia = item;
                    }
                });
            }
        },
        loadingDistritos(value) {
            if (this.dataDNI.buscado && value) {
                let iddistrito = this.dataDNI.ubigeo.length > 0 ? this.dataDNI.ubigeo[2] : 0;
                this.Distritos.forEach(item => {
                    if (Number(item.id) == Number(iddistrito)) {
                        this.distrito = item;
                    }
                });
            }

            if (this.dataRUC.buscado && value) {
                let iddistrito = this.dataRUC.ubigeo.length > 0 ? this.dataRUC.ubigeo[2] : 0;
                this.Distritos.forEach(item => {
                    if (Number(item.id) == Number(iddistrito)) {
                        this.distrito = item;
                    }
                });
            }

            this.loadingProvincias = false;
            this.loadingDistritos = false;
            this.dataDNI.buscado = false;
            this.dataRUC.buscado = false;
        },
        dataDNI(value) {
            if (value.buscado) {
                let iddepart = value.ubigeo.length > 0 ? value.ubigeo[0] : 0;
                this.Departamentos.forEach(item => {
                    if (Number(item.id) == Number(iddepart)) {
                        this.departamento = item;
                    }
                });
                this.formCliente.codigo_verificacion = this.dataDNI.codigo_verificacion == "-" || this.dataDNI.codigo_verificacion === null ? "" : this.dataDNI.codigo_verificacion;
                this.formCliente.nombre = this.dataDNI.nombres + " " + this.dataDNI.apellido_paterno + " " + this.dataDNI.apellido_materno;
                this.formCliente.direccion = this.dataDNI.direccion_completa;
                this.formCliente.activo = "ACTIVO";
            }
        },
        dataRUC(value) {
            if (value.buscado) {
                let iddepart = value.ubigeo.length > 0 ? value.ubigeo[0] : 0;
                this.Departamentos.forEach(item => {
                    if (Number(item.id) == Number(iddepart)) {
                        this.departamento = item;
                    }
                });
                this.formCliente.codigo_verificacion = "";
                this.formCliente.nombre = this.dataRUC.nombre_o_razon_social;
                this.formCliente.direccion = this.dataRUC.direccion;
                this.formCliente.activo = "ACTIVO";
            }
        },
    },
    async created() {
        this.loading = true;
        await this.setFechaEnvioDefault();
        await this.getTipoEnvios();
        await this.getTipoDocumento();
        await this.getTiposPagoEnvio();
        await this.getOrigenesVentas();
        await this.getDepartamentos();

        if (!this.hayDatosEnvio) {
            this.setDatosDefault();
        }

        this.loading = false;
    },
    methods: {
        openMdlEnvio() {
            // if (!this.hayDatosEnvio) {
            //     this.departamento = 15;
            // }
            $("#modal_envio").modal("show");
        },
        async setDatosDefault() {
            this.loading = true;
        },
        async setDatosDespacho(despacho) {

            console.log('============>SET DATOS DESPACHO', despacho);

            //======== COLOCAR DATA EN EL MODAL ========
            this.departamento = null;
            this.$nextTick(() => {
                console.log('SET DEPARTAMENTO ID');
                this.departamento = parseInt(despacho.departamento_id);
            });

            //=========== ESPERAR A QUE EL UBIGEO SE COLOQUE COMPLETAMENTE ==========
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            console.log('SET PROVINCIA ID');
            //====== SETTEAR PROVINCIA Y DISTRITO ======
            this.provincia = null
            this.$nextTick(() => {
                this.provincia = parseInt(despacho.provincia_id)
            })

            //=========== ESPERAR A QUE EL UBIGEO SE COLOQUE COMPLETAMENTE ==========
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            console.log('SET DISTRITO ID');
            this.distrito = null
            this.$nextTick(() => {
                this.distrito = parseInt(despacho.distrito_id)
            })
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('ubigeoCompletado', resolve);
                })
            ]);

            console.log('SET TIPO PAGO ENVIO');
            //======= COLOCAR TIPO PAGO ENVIO =======
            this.tipo_pago_envio = null
            this.$nextTick(() => {
                this.tipo_pago_envio = despacho.tipo_pago_envio_id
            })

            console.log('SET TIPO ENVIO');
            //======== COLOCAR TIPO DE ENVIO =======
            this.tipo_envio = despacho.tipo_envio_id;
            await Promise.all([
                new Promise((resolve) => {
                    this.$once('tipoEnvioColocadoEmpresasEnvioCargadas', resolve);
                })
            ]);

            console.log('SET EMPRESA ENVÍO', this.empresa_envio);
            //============ COLOCANDO EMPRESA ENVÍO ========
            this.empresa_envio = null
            this.$nextTick(() => {
                this.empresa_envio = despacho.empresa_envio_id
            })

            await Promise.all([
                new Promise((resolve) => {
                    this.$once('empresasColocadasSedesCargadas', resolve);
                })
            ]);

            //========= COLOCANDO SEDE ENVÍO ========
            this.sede_envio = null
            this.$nextTick(() => {
                this.sede_envio = despacho.sede_envio_id
            })

            //========== COLOCANDO ENTREGA DOMICILIO ========
            if (this.despacho.entrega_domicilio === "SI") {
                this.entrega_domicilio = true;
                this.direccion_entrega = this.despacho.direccion_entrega;
            }

            if (this.despacho.entrega_domicilio === "NO") {
                this.entrega_domicilio = false;
                this.direccion_entrega = '';
            }

            //========= COLOCANDO ORIGEN VENTA =========
            this.origen_venta = this.despacho.origen_venta_id;

            //========= COLOCANDO FECHA ENVÍO PROPUESTA =======
            this.fecha_envio = '';
            this.fecha_envio = this.despacho.fecha_envio_propuesta;

            //====== COLOCANDO OBSERVACIONES =======
            this.obs_rotulo = '';
            this.obs_rotulo = this.despacho.obs_rotulo;
            this.obs_despacho = '';
            this.obs_despacho = this.despacho.obs_despacho;

            //======= COLOCANDO TIPO DOCUMENTO DESTINATARIO =====
            const tipo_doc = this.tipoDocumentos.find((td) => {
                return td === this.despacho.destinatario_tipo_doc;
            })

            this.destinatario.tipo_documento = tipo_doc;
            this.destinatario.nro_documento = this.despacho.destinatario_nro_doc;
            this.destinatario.nombres = this.despacho.destinatario_nombre;

        },
        async metodoHijo(despacho, documento_id) {
            this.loading = true;
            this.formEnvio.documento_id = documento_id;
            this.despacho = despacho;
            this.limpiarDatosEnvio();

            if (!despacho) {
                this.departamento = 15;
                this.tipo_pago_envio = 196;
                this.tipo_envio = 188;
                this.mode = 'store';
                toastr.warning('PODRÁ CREAR DATOS DE DESPACHO', 'EL DOCUMENTO NO TIENE DATOS DE DESPACHO');
                this.loading = false;
            } else {
                this.mode = 'edit';
                await this.setDatosDespacho(this.despacho);
                this.loading = false;
            }
        },
        borrarDataEnvio() {

            if (this.mode == 'create') {
                this.hayDatosEnvio = false;
                this.formEnvio = {};
                this.destinatario = {
                    dni: "",
                    nombres: ""
                };
                this.empresa_envio = null;
                this.sede_envio = null;

                this.observaciones = "";
                this.sedes = [];
                this.$emit('borrarDataEnvio');
                $("#modal_envio").modal("hide");
                toastr.success("ENVÍO BORRADO", "OPERACIÓN COMPLETADA");
            }

        },
        setFechaEnvioDefault() {
            const today = new Date();

            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');

            this.fecha_envio = `${year}-${month}-${day}`;
        },
        async Guardar() {
            if (!this.empresa_envio) {
                toastr.error('SELECCIONE UNA EMPRESA DE ENVÍO', "ERROR");
                return;
            }
            if (!this.sede_envio && this.tipo_envio != 189) {
                toastr.error('SELECCIONE UNA SEDE DE ENVÍO', "ERROR");
                return;
            }
            if (this.destinatario.nro_documento.length < 8) {
                toastr.error('INGRESE UN DNI VÁLIDO PARA EL DESTINATARIO', "ERROR");
                return;
            }
            if (this.destinatario.nombres.length == 0) {
                toastr.error('DEBE INGRESAR EL NOMBRE DEL DESTINATARIO', "ERROR");
                return;
            }

            //====== GUARDANDO DATA DE ENVIO ======
            this.formEnvio.departamento = this.departamento;
            this.formEnvio.provincia = this.provincia;
            this.formEnvio.distrito = this.distrito;
            this.formEnvio.tipo_envio = this.tipo_envio;
            this.formEnvio.empresa_envio = this.empresa_envio;
            this.formEnvio.sede_envio = this.sede_envio;
            this.formEnvio.destinatario = this.destinatario;
            this.formEnvio.direccion_entrega = this.direccion_entrega;
            this.formEnvio.entrega_domicilio = this.entrega_domicilio;
            this.formEnvio.origen_venta = this.origen_venta;
            this.formEnvio.fecha_envio_propuesta = this.fecha_envio;
            this.formEnvio.obs_rotulo = this.obs_rotulo;
            this.formEnvio.obs_despacho = this.obs_despacho;
            this.formEnvio.tipo_pago_envio = this.tipo_pago_envio;

            if (this.mode == "create") {
                this.hayDatosEnvio = true;
                this.$emit('addDataEnvio', this.formEnvio);
                toastr.success('DATOS DE ENVÍO GUARDADOS', 'OPERACIÓN COMPLETADA');
                $("#modal_envio").modal("hide");
            }

            if (this.mode == "edit") {
                this.$emit('updateDataEnvio', this.formEnvio);
            }

            if (this.mode == 'store') {
                this.$emit('storeDataEnvio', this.formEnvio);
            }

        },
        cerrarMdlEnvio() {
            $("#modal_envio").modal("hide");
        },
        async getTipoCliente() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.tipoClientes"));
                this.tipoClientes = data;
            } catch (ex) {

            }
        },
        async getDepartamentos() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getDepartamentos"));
                this.Departamentos = data;

            } catch (ex) {
                toastr.error(ex, 'ERROR EN LA PETICIÓN OBTENER DEPARTAMENTOS');
            }
        },
        async getProvincias() {
            try {
                this.provincia = null;
                console.log('GET PROVINCIAS');
                this.loading = true;
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                    departamento_id: this.departamento
                });
                const { error, message, provincias } = data;
                this.Provincias = provincias;
                this.loading = false;
                this.provincia = parseInt(provincias[0].id);

            } catch (ex) {
                toastr.error(ex, 'ERROR EN LA PETICIÓN OBTENER PROVINCIAS');
            }
        },
        async getDistritos() {
            try {
                this.distrito = null
                console.log('GET DISTRITOS');
                this.loading = true;
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.distritos'), {
                    provincia_id: this.provincia
                });
                const { error, message, distritos } = data;
                this.Distritos = distritos;

                this.loading = false;

                this.distrito = parseInt(distritos[0].id);

            } catch (ex) {
                toastr.error(ex, 'ERROR EN LA PETICIÓN OBTENER DISTRITOS');
            }
        },
        async consultarDocumento() {
            try {
                this.loading = true;

                if (this.destinatario.nro_documento.length === 8) {
                    this.consultarAPI();
                } else {
                    this.loading = false;
                    toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                }

            } catch (ex) {
                alert("Error en consultarDocumento" + ex);
            }
        },
        async consultarAPI() {
            try {
                let documento = this.destinatario.nro_documento;
                let url = route('utilidades.consultarDocumento', { tipo_doc: 6, nro_doc: documento });

                const res = await this.axios.get(url);

                if (res.data.success) {
                    toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
                    this.CamposDNI(res.data.data);
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (ex) {
                this.loading = false;
                alert("Error en consultarAPI" + ex);
            }
        },
        CamposDNI(data) {

            this.dataDNI = data.numero;

            //this.dataDNI.buscado = true;
            this.destinatario.nombres = data.nombre_completo;
            //this.destinatario.nombres = data.nombres + ' ' + data.apellido_paterno + ' ' + data.apellido_materno;
            this.loading = false;

        },
        CamposRUC(results) {
            const { success, data } = results;
            if (success) {
                this.dataRUC = data;
                this.dataRUC.buscado = true;
                this.loading = false;
            }

        },
        limpiarDatosEnvio() {
            this.departamento = null;
            this.provincia = null;
            this.distrito = null;
            this.origen_venta = null;
            this.tipo_pago_envio = null;
            this.tipo_envio = null;
            this.empresa_envio = null;
            this.sede_envio = null;
            this.Provincias = [];
            this.Distritos = [];
        },
        async getTipoEnvios() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getTipoEnvios"));
                this.tipos_envios = data;
            } catch (ex) {
                toastr.error(ex.message, 'ERROR EN LA SOLICITUD AL OBTENER TIPOS DE ENVÍO');
            }
        },
        async getEmpresasEnvio(envio) {
            try {
                this.loading = true;
                const { data } = await this.axios.get(route("consulta.ajax.getEmpresasEnvio", envio));

                if (data.success) {
                    this.empresas_envio = data.empresas_envio;

                    const defaultEmpresa = this.empresas_envio.findIndex(e => e.empresa == 'SHALOM');
                    if (defaultEmpresa !== -1) {
                        this.empresa_envio = defaultEmpresa.id;
                    }

                    this.$emit('tipoEnvioColocadoEmpresasEnvioCargadas');

                } else {
                    toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA SOLICITUD OBTENER EMPRESAS ENVÍO');
            } finally {
                this.loading = false;
            }
        },
        async getSedesEnvio(empresa_envio_id, ubigeo) {
            try {
                this.loading = true;
                const { data } = await this.axios.get(route("consulta.ajax.getSedesEnvio", { empresa_envio_id, ubigeo }));

                if (data.success) {
                    this.sedes_envio = data.sedes_envio;
                    this.$emit('empresasColocadasSedesCargadas');
                } else {
                    toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER SEDES DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA SOLICITUD OBTENER SEDES ENVÍO');
            } finally {
                this.loading = false;
            }
        },
        async getOrigenesVentas() {
            try {
                this.loading = true;
                const { data } = await this.axios.get(route("consulta.ajax.getOrigenesVentas"));

                if (data.success) {
                    this.origenes_ventas = data.origenes_ventas;

                    //======= COLOCANDO POR DEFECTO WATHSAPP ====
                    if (data.origenes_ventas.length > 0) {

                        const index_ov = data.origenes_ventas.findIndex((ov) => {
                            return ov.descripcion == "WATHSAPP";
                        })

                        if (index_ov !== -1) {
                            this.origen_venta = data.origenes_ventas[index_ov].id;
                        }

                    } else {
                        this.origen_venta = null;
                        toastr.error("REGISTRE ORÍGENES DE VENTA EN TABLAS GENERALES", 'ERROR AL OBTENER ORÍGENES DE VENTA');
                    }
                } else {
                    toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER ORÍGENES DE VENTA');
                }
            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA SOLICITUD OBTENER ORÍGENES DE VENTAS');
            } finally {
                this.loading = false;
            }
        },
        async getTiposPagoEnvio() {
            try {
                this.loading = true;
                const { data } = await this.axios.get(route("consulta.ajax.getTiposPagoEnvio"));

                if (data.success) {
                    this.tipos_pago_envio = data.tipos_pago_envio;

                    //========= COLOCANDO PRIMERA OPCIÓN POR DEFECTO ======
                    if (data.tipos_pago_envio.length > 0) {
                        this.tipo_pago_envio = this.tipos_pago_envio[2].id;
                    }
                } else {
                    toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER TIPOS PAGO DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message, 'ERROR EN LA SOLICITUD OBTENER TIPO DE PAGOS ENVÍO');
            } finally {
                this.loading = false;
            }
        },
        async getTipoDocumento() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getTipoDocumentos"));

                //======== SELECCIONAMOS DNI Y CARNET EXTRANJERÍA ======
                const tipoDocumentosFilter = [];

                data.forEach((td) => {

                    if (td.id == 6 || td.id == 7) {
                        tipoDocumentosFilter.push(td.simbolo);
                    }
                })

                if (tipoDocumentosFilter.length > 0) {
                    this.destinatario.tipo_documento = tipoDocumentosFilter[0];
                    this.tipoDocumentos = tipoDocumentosFilter;
                }

            } catch (ex) {
                toastr.error(ex.message, 'ERROR EN LA SOLICITUD AL OBTENER TIPOS DE DOCUMENTO');
            }
        },
        pintarErroresValidacionMdlEnvio(errors) {
            pintarErroresValidacion(errors, 'error');
        }
    }
}
</script>
<style lang="scss">
div.content_cliente {
    position: relative;
}

div.content_cliente.sk__loading::after {
    content: '';
    background-color: rgba(255, 255, 255, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3000;
}

.content_cliente.sk__loading>.sk-spinner.sk-spinner-wave {
    margin: 0 auto;
    width: 50px;
    height: 30px;
    text-align: center;
    font-size: 10px;
}

.content_cliente.sk__loading>.sk-spinner {
    display: block;
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    z-index: 3500;
}

.content_cliente .sk-spinner.sk-spinner-wave.hide-cliente {
    display: none;
}
</style>
