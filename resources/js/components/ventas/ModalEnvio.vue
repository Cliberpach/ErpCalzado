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
                <div class="modal-body content_cliente" :class="{'sk__loading':loading}">
                    <form id="frmEnvio" class="formulario" @submit.prevent="Guardar">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="row justify-content-between">
                                    <div class="col-6">
                                        <label for="" style="font-weight: bold;">UBIGEO</label>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end">
                                        <button @click="borrarEnvio" type="button" class="btn btn-danger">BORRAR ENVÍO</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="departamento">Departamento</label>
                                            <v-select v-model="departamento" :options="Departamentos" :reduce="d=>d"
                                             required   label="nombre" :clearable="false"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="provincia">Provincia</label>
                                            <v-select v-model="provincia" :options="Provincias" :reduce="p=>p"
                                                required   label="text" :clearable="false"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="distrito">Distrito</label>
                                            <v-select v-model="distrito" :options="Distritos" :reduce="d=>d"
                                                required    label="text" :clearable="false"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-3">
                                        <div class="form-group">
                                            <label class="required" for="zona">Zona</label>
                                            <input type="text" id="zona" name="zona" v-model="departamento.zona"
                                            required   class=" text-center form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="required" for="" style="font-weight: bold;">TIPO DE ENVÍO</label>
                                        <v-select v-model="tipo_envio" :options="tipos_envios" :reduce="te=>te"
                                            required :clearable="false" label="descripcion" ></v-select>
                                    </div>
                                    <div class="col-4">
                                        <label class="required" for="" style="font-weight: bold;">TIPO PAGO</label>
                                        <v-select v-model="tipo_pago_envio" :options="tipos_pago_envio" :reduce="tp=>tp"
                                            required :clearable="false" label="descripcion" ></v-select>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <label class="required" for="vselectEmpresa" style="font-weight: bold;">EMPRESAS</label>
                                        <v-select  v-model="empresa_envio" :options="empresas_envio" :reduce="ee=>ee"
                                            label="empresa" id="vselectEmpresa"   ref="vselectEmpresa"></v-select>
                                    </div>
                                    <div class="col-6">
                                        <label class="required" for="" style="font-weight: bold;">SEDES</label>
                                        <v-select :required="mostrar_combo_sedes" v-model="sede_envio"  :options="sedes_envio" :reduce="se=>se"
                                        v-if="mostrar_combo_sedes"        label="direccion"></v-select>
                                        <input :required="!mostrar_combo_sedes" readonly class="form-control" v-if="!mostrar_combo_sedes" type="text" v-model="sede_envio.direccion">

                                    </div>
                                </div>
                                <div class="row mt-3" v-if="mostrar_entrega_domicilio">
                                    <div class="col-4 d-flex align-items-center">
                                        <div class="row" style="width: 100%;">
                                            <div class="col-2 pr-0 d-flex align-items-center">
                                                <input style="width: 50px;" id="check_entrega_domicilio" type="checkbox" v-model="entrega_domicilio" class="form-control">
                                            </div>
                                            <div class="col-9 pl-0">
                                                <label for="check_entrega_domicilio" class="mb-0" style="font-weight: bold;">ENTREGA EN DOMICILIO</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <label :class="{ 'required': entrega_domicilio }" for="" style="font-weight: bold;">DIRECCION DE ENTREGA</label>
                                        <input :readonly="!entrega_domicilio" :required="entrega_domicilio" type="text" class="form-control" 
                                        v-model="direccion_entrega">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        <label for="origen_venta" style="font-weight: bold;">ORIGEN VENTA</label>
                                        <v-select  v-model="origen_venta"  :options="origenes_ventas" :reduce="ov=>ov"
                                        label="descripcion" :clearable="false"></v-select>
                                    </div>
                                    <div class="col-3">
                                        <label for="fecha_envio" style="font-weight: bold;">FECHA ENVÍO</label>
                                        <input id="fecha_envio" v-model="fecha_envio" type="date" class="form-control">
                                    </div>
                                    <div class="col-3">
                                        <label for="obs_rotulo" style="font-weight: bold;">OBS RÓTULO</label>
                                        <textarea maxlength="35"  id="obs_rotulo" v-model="obs_rotulo" class="form-control"></textarea>
                                    </div>
                                    <div class="col-3">
                                        <label for="obs_despacho" style="font-weight: bold;">OBS DESPACHO</label>
                                        <textarea id="obs_despacho" v-model="obs_despacho" class="form-control"></textarea>
                                    </div>
                                </div>
                                <hr>
                                <label for="" style="font-weight: bold;">DATOS DEL DESTINATARIO</label>
                                <div class="row">
                                    <div class="col-3">
                                        <label class="required" for="origen_venta" style="font-weight: bold;">TIPO DOC</label>
                                        <v-select  v-model="destinatario.tipo_documento"  :options="tipoDocumentos" :reduce="td=>td"
                                        label="" :clearable="false"></v-select>
                                    </div>
                                    <div class="col-4">
                                        <label class="required" for="dni_destinatario">Nro. {{ destinatario.tipo_documento }}</label>
                                        <div class="input-group">
                                            <input type="text" id="dni_destinatario"  class="form-control"
                                            :maxlength="maxLengthDocumento" v-model="destinatario.nro_documento" required>
                                            <span class="input-group-append">
                                                <button type="button" style="color:white" class="btn btn-primary"
                                                v-if="destinatario.tipo_documento == 'DNI'"
                                                @click.prevent="consultarDocumento">
                                                    <i class="fa fa-search"></i>
                                                    <span id="entidad"> CONSULTAR</span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <label class="required" for="nombres_destinatario">Nombres</label>
                                        <input required type="text" id="nombres_destinatario" v-model=destinatario.nombres 
                                        class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="sk-spinner sk-spinner-wave" :class="{'hide-cliente':!loading}">
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
                        <button type="submit" class="btn btn-primary btn-sm" form="frmEnvio" style="color:white;"><i
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
            tipoDocumentos:[],
            despacho:null,
            mode:'create',
            loading: false,
            direccion_entrega:"",
            entrega_domicilio:false,
            tipos_pago_envio:[],
            tipo_pago_envio:{
                id:0,
                descripcion:"SELECCIONAR"
            },
            mostrar_combo_sedes:true,
            mostrar_entrega_domicilio:true,
            origenes_ventas:[],
            empresas_envio:[],
            sedes_envio:[],
            tipos_envios:[],
            Departamentos: [],
            Provincias: [],
            Distritos: [],
            origen_venta:{descripcion:"SELECCIONAR"},
            fecha_envio:"",
            obs_rotulo:"",
            obs_despacho:"",
            destinatario:{
                tipo_documento:"SELECCIONAR",
                nro_documento:"",
                nombres:""
            },
            departamento: {
                id: 0,
                nombre: "SELECCIONAR",
                zona: ""
            },
            provincia: {
                id: 0,
                text: "SELECCIONAR"
            },
            distrito: {
                id: 0,
                text: "SELECCIONAR"
            },
            tipo_envio:{
                id:187,
                descripcion:"AGENCIA"
            },
            empresa_envio:{
                id:0,
                empresa:"SELECCIONAR"
            },
            sede_envio:{
                id:0,
                empresa_envio_id:0,
                direccion:"SELECCIONAR"
            }, 
            formEnvio: {
                departamento: {},
                provincia: {},
                distrito: {},
                tipo_envio: {},
                empresa_envio: {},
                sede_envio: {},
                destinatario: {}
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
    computed:{
        maxLengthDocumento() {
            
            if(this.destinatario.tipo_documento === "DNI" && this.destinatario.nro_documento.length >8){
                this.destinatario.nro_documento =   '';
            }
            return this.destinatario.tipo_documento === 'DNI' ? 8 : 20;
        }
    },
    watch: {
        fecha_envio(value){
            if(value.length == 0){
                this.setFechaEnvioDefault();
            }
        },
        entrega_domicilio(value){
            if(!value){
                //====== LIMPIAR LA DIRECCION DE ENTREGA =========
                this.direccion_entrega  =   "";
            }
        },
        async cliente(value){
            console.log(value);

            this.destinatario.nro_documento         =   "";
            this.destinatario.nombres               =   "";

            if(value.tipo_documento === "DNI" || value.tipo_documento === "CARNET EXT."){
                this.destinatario.tipo_documento    =   value.tipo_documento;
                if(value.documento !== "99999999"){
                    this.destinatario.nro_documento     =   value.documento;
                    this.destinatario.nombres           =   value.nombre;
                }
            }  
           
        },
        empresa_envio(value){
            //====== LIMPIAR LAS SEDES ======
            this.sede_envio =   {
                id:0,
                empresa_envio_id:0,
                direccion:"SELECCIONAR"
            }

            //======= EN CASO ELIMINE MI ELECCIÓN ======
            if(!value){
                //==== COLOCAR EN VALOR SELECCIONAR =====
                this.empresa_envio  =   {
                    id:0,
                    empresa:"SELECCIONAR"
                }  
                this.sedes_envio    =   [];
            }
            
            if(value && value.id !== 0){
                if(this.tipo_envio.descripcion  === "DELIVERY"){
                    this.sede_envio =   {
                        id:0,
                        empresa_envio_id:0,
                        direccion:value.empresa
                    }
                }

                const ubigeo    =  JSON.stringify([this.departamento,this.provincia,this.distrito]);
                this.getSedesEnvio(value.id,ubigeo);
            } 
        },
        tipo_envio(value){
            //=======  LIMPIAR EMPRESA ENVIO ===
            this.empresa_envio  =   {
                     id:0,
                     empresa:"SELECCIONAR"
            };

            //==== LIMPIAR SEDE ENVIO =====
            this.sede_envio =   {
                id:0,
                empresa_envio_id:0,
                direccion:"SELECCIONAR"
            }
            this.sedes_envio    =   [];
           
            if(value){
                //====== AGENCIA ======
                /*  ->TIENE SEDES  
                    ->NO HAY CONTRAENTREGA
                    ->PUEDE HABER ENTREGA A DOMICILIO
                */
                if(value.descripcion === "AGENCIA"){
                    this.mostrar_combo_sedes            =   true;
                    this.mostrar_entrega_domicilio      =   true;
                }

                //====== DELIVERY ======
                /*  ->NO TIENE SEDES  
                    ->PUEDE HABER CONTRAENTREGA
                    ->HAY ENTREGA A DOMICILIO SIEMPRE
                */
                if(value.descripcion === "DELIVERY"){
                    this.mostrar_combo_sedes            =   false;
                    this.mostrar_entrega_domicilio      =   true;
                    this.entrega_domicilio              =   true;
                }
                
                //====== RECOJO EN TIENDA ======
                /*  ->TIENE SEDES  
                    ->NO HAY CONTRAENTREGA
                    ->NO HAY ENTREGA A DOMICILIO
                */
                if(value.descripcion === "RECOJO EN TIENDA"){
                    this.mostrar_combo_sedes            =   true;
                    this.mostrar_entrega_domicilio      =   false;
                    this.entrega_domicilio              =   false;
                }

                //====== OBTENIENDO EMPRESAS DE ENVIO =====
                this.getEmpresasEnvio(value.descripcion);
            }
  
        },
        tipoClientes(value) {
            this.tipo_cliente_id = value.length > 0 ? value[0].id : "";
        },
        Departamentos() {
            this.departamento = {
                id: 13,
                nombre: "LA LIBERTAD",
                zona: "NORTE"
            };
        },
        Provincias(value) {
            if(this.despacho){
               
               const provincia_filter   =   value.filter((d)=>{
                        return d.text === this.despacho.provincia;
               })

               this.provincia =   provincia_filter[0];
 
           }else{
               this.provincia = value.length > 0 ? value[0] : null;
           }
       
        },
        Distritos(value) {
            //====== EN MODO EDICIÓN, COLOCAR EL DISTRITO DEL DESPACHO ======
            if(this.despacho){
               
                const distrito_filter   =   value.filter((d)=>{
                         return d.text === this.despacho.distrito;
                })

                this.distrito   =   distrito_filter[0];
                //======= UBIGEO COMPLETADO =====
                console.log('UBIGEO COMPLETADO');
                this.$emit('ubigeoCompletado'); 
  
            }else{
                this.distrito = value.length > 0 ? value[0] : null;
            }

        },
        departamento(value) {
            if(value){
                //=======  LIMPIAR EMPRESA ENVIO ===
                this.empresa_envio  =   {
                    id:0,
                    empresa:"SELECCIONAR"
                };
                //======= LIMPIANDO SEDES =====
                this.sedes_envio    =   [];
                this.sede_envio     =   {
                                            id:0,
                                            empresa_envio_id:0,
                                            direccion:"SELECCIONAR"
                                        };
                this.$nextTick(this.getProvincias);
            }
        },
        provincia(value) {
            if(value){
              
                //=======  LIMPIAR EMPRESA ENVIO ===
                this.empresa_envio  =   {
                    id:0,
                    empresa:"SELECCIONAR"
                };

                //======= LIMPIANDO SEDES =====
                //this.sedes_envio    =   [];
                this.sede_envio     =   {
                                            id:0,
                                            empresa_envio_id:0,
                                            direccion:"SELECCIONAR"
                                        };
                this.$nextTick(this.getDistritos);
            }
        },
        distrito(value) {
            
            //=======  LIMPIAR EMPRESA ENVIO ===
            this.empresa_envio  =   {
                id:0,
                empresa:"SELECCIONAR"
            };

            //======= LIMPIANDO SEDES =====
            this.sedes_envio    =   [];
            this.sede_envio     =   {
                                    id:0,
                                    empresa_envio_id:0,
                                    direccion:"SELECCIONAR"
                                    };
        },
        tipo_documento(value) {
            if(value){
                this.formCliente.tipo_documento = value;
                this.formCliente.activo = "SIN VERIFICAR";
                this.entidad = value == "DNI" ? "Reniec" : (value == "RUC" ? "Sunat" : "Entidad");

                if(value=="DNI"){
                    this.maxlength = 8;
                }else if(value=="RUC"){
                    this.maxlength = 11;
                }else{
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
            this.dataRUC.buscado=false;
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
        dataRUC(value){
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
        this.loading    =   true;
        await this.setFechaEnvioDefault();
        await this.getDepartamentos();
        await this.getTipoEnvios();
        await this.getTipoDocumento();
        await this.getTiposPagoEnvio();
        await this.getOrigenesVentas();
        await this.getEmpresasEnvio(this.tipo_envio.descripcion);
        this.loading    =   false;
    },
    methods: {
        async metodoHijo(despacho,documento_id){
            this.loading    =   true;
            this.formEnvio.documento_id     =   documento_id;

            if(despacho.length == 0){
                toastr.warning('PODRÁ CREAR DATOS DE DESPACHO','EL DOCUMENTO NO TIENE DATOS DE DESPACHO');
                return;
            }

            this.mode           =   'edit';
            this.departamento   =   {};
            console.log('----');
            this.despacho       =   despacho[0];
            
            if(despacho.length == 1){                
                //======== COLOCAR DATA EN EL MODAL ========
                const departamento  =   despacho[0].departamento;
                
                const departamento_filter   =   this.Departamentos.filter((d)=>{
                    return d.nombre === departamento;
                });

                if(departamento_filter.length === 1){
                    console.log('COLOCANDO UBIGEO');
                  this.departamento   =   departamento_filter[0];
                }


                //=========== ESPERAR A QUE EL UBIGEO SE COLOQUE COMPLETAMENTE ==========
                await Promise.all([
                    new Promise((resolve) => {
                        this.$once('ubigeoCompletado', resolve);
                    })
                ]);
                
                console.log('COLOCANDO TIPO PAGO ENVÍO');

                //======= COLOCAR TIPO PAGO ENVIO =======
                const tipo_pago_envio_filter =   this.tipos_pago_envio.filter((te)=>{
                    return te.descripcion === despacho[0].tipo_pago_envio;
                })
                if(tipo_pago_envio_filter.length === 1){
                    this.tipo_pago_envio =   tipo_pago_envio_filter[0];
                }

               
                console.log('COLOCANDO TIPO ENVÍO Y CARGANDO EMPRESAS ENVÍO')
                //======== COLOCAR TIPO DE ENVIO =======
                this.tipo_envio         =   {};
                const tipo_envio_filter =   this.tipos_envios.filter((te)=>{
                       return te.descripcion === despacho[0].tipo_envio;
                })
                if(tipo_envio_filter.length === 1){
                    this.tipo_envio =   tipo_envio_filter[0];
                }

                await Promise.all([
                    new Promise((resolve) => {
                        this.$once('tipoEnvioColocadoEmpresasEnvioCargadas', resolve);
                    })
                ]);

                //============ COLOCANDO EMPRESA ENVÍO ========
                console.log('COLOCANDO EMPRESA ENVÍO Y CARGANDO SEDES ENVÍO');
                const empresa_envio_filter =   this.empresas_envio.filter((ee)=>{
                    return ee.empresa === this.despacho.empresa_envio_nombre;
                })

                if(empresa_envio_filter.length === 1){
                    this.empresa_envio      =   empresa_envio_filter[0];
                }

                await Promise.all([
                    new Promise((resolve) => {
                        this.$once('empresasColocadasSedesCargadas', resolve);
                    })
                ]);

                console.log('COLOCANDO SEDE ENVÍO')
                //========= COLOCANDO SEDE ENVÍO ========
                const sede_envio_filter =   this.sedes_envio.filter((se)=>{
                    return se.direccion === this.despacho.sede_envio_nombre;
                })

                if(sede_envio_filter.length === 1){
                    this.sede_envio      =   sede_envio_filter[0];
                }
                console.log('SEDE ENVÍO COLOCADA');

                //========== COLOCANDO ENTREGA DOMICILIO ========
                if(this.despacho.entrega_domicilio === "SI"){
                    this.entrega_domicilio  =   true;
                    this.direccion_entrega  =   this.despacho.direccion_entrega;   
                }

                if(this.despacho.entrega_domicilio === "NO"){
                    this.entrega_domicilio  =   false;
                    this.direccion_entrega  =   '';   
                }

                //========= COLOCANDO ORIGEN VENTA =========
                this.origen_venta   =   {descripcion:this.despacho.origen_venta};

                //========= COLOCANDO FECHA ENVÍO PROPUESTA =======
                this.fecha_envio    =   '';
                this.fecha_envio    =   this.despacho.fecha_envio_propuesta;

                //====== COLOCANDO OBSERVACIONES =======
                this.obs_rotulo  =   '';
                this.obs_rotulo  =   this.despacho.obs_rotulo;
                this.obs_despacho  =   '';
                this.obs_despacho  =   this.despacho.obs_despacho;

                //======= COLOCANDO TIPO DOCUMENTO DESTINATARIO =====
                const tipo_doc      =   this.tipoDocumentos.filter((td)=>{
                    return td === this.despacho.destinatario_tipo_doc;
                })

                this.destinatario.tipo_documento    =   tipo_doc[0];
                this.destinatario.nro_documento     =   this.despacho.destinatario_nro_doc;
                this.destinatario.nombres           =   this.despacho.destinatario_nombre;

                this.despacho   =   null;
                this.loading    =   false;
            }
        },
        borrarEnvio(){
            this.formEnvio      =   {};
            this.destinatario   =   {
                                        dni:"",
                                        nombres:""
                                    };
            this.empresa_envio  =   {
                                        id:0,
                                        empresa:"SELECCIONAR"
                                    };
            this.sede_envio     =   {
                                        id:0,
                                        empresa_envio_id:0,
                                        direccion:"SELECCIONAR"
                                    };

            this.observaciones  =   "";
            this.sedes          =   [];   
            this.$emit('addDataEnvio',JSON.stringify(this.formEnvio));
            $("#modal_envio").modal("hide");
            toastr.success("ENVÍO BORRADO","OPERACIÓN COMPLETADA");
        },
        setFechaEnvioDefault(){
            const today = new Date();

            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0'); 
            const day = String(today.getDate()).padStart(2, '0'); 

            this.fecha_envio = `${year}-${month}-${day}`;
        },
        async Guardar() {
          if(this.empresa_envio.empresa === "SELECCIONAR"){
            toastr.error('SELECCIONE UNA EMPRESA DE ENVÍO',"ERROR");
            return;
          }
          if(this.sede_envio.direccion === "SELECCIONAR"){
            toastr.error('SELECCIONE UNA SEDE DE ENVÍO',"ERROR");
            return;
          }
          if(this.destinatario.nro_documento.length < 8){
            toastr.error('INGRESE UN DNI VÁLIDO PARA EL DESTINATARIO',"ERROR");
            return;
          }
          if(this.destinatario.nombres.length == 0){
            toastr.error('DEBE INGRESAR EL NOMBRE DEL DESTINATARIO',"ERROR");
            return;
          }
         
          //====== GUARDANDO DATA DE ENVIO ======
          this.formEnvio.departamento           =   this.departamento;
          this.formEnvio.provincia              =   this.provincia;
          this.formEnvio.distrito               =   this.distrito;
          this.formEnvio.tipo_envio             =   this.tipo_envio;
          this.formEnvio.empresa_envio          =   this.empresa_envio;
          this.formEnvio.sede_envio             =   this.sede_envio;
          this.formEnvio.destinatario           =   this.destinatario;
          this.formEnvio.direccion_entrega      =   this.direccion_entrega;
          this.formEnvio.entrega_domicilio      =   this.entrega_domicilio;
          this.formEnvio.origen_venta           =   this.origen_venta;
          this.formEnvio.fecha_envio_propuesta  =   this.fecha_envio;
          this.formEnvio.obs_rotulo             =   this.obs_rotulo;
          this.formEnvio.obs_despacho           =   this.obs_despacho;
          this.formEnvio.tipo_pago_envio        =   this.tipo_pago_envio;

          console.log('FORMULARIO ENVIO');
          console.log(this.formEnvio);

          if(this.mode == "create"){
            this.$emit('addDataEnvio',JSON.stringify(this.formEnvio));
            toastr.success('DATOS DE ENVÍO GUARDADOS','OPERACIÓN COMPLETADA');
          }

          if(this.mode == "edit"){  
            this.$emit('updateDataEnvio',JSON.stringify(this.formEnvio));
            toastr.success('DATOS DE ENVÍO ACTUALIZADOS','OPERACIÓN COMPLETADA');
          }
         
          $("#modal_envio").modal("hide");
        },
        async getTipoCliente() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.tipoClientes"));
                this.tipoClientes   = data;
            } catch (ex) {

            }
        },
        async getDepartamentos() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.getDepartamentos"));
                this.Departamentos  = data;
               
            } catch (ex) {

            }
        },
        async getProvincias() {
            try {
                this.loading    =   true;
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                    departamento_id: this.departamento.id
                });
                const { error, message, provincias } = data;
                this.Provincias = provincias;
                this.loadingProvincias = true;
                this.loading    =   false;
                if(this.despacho){
                    this.provincia.text =   this.despacho.provincia;
                }
            } catch (ex) {

            }
        },
        async getDistritos() {
            try {
                this.loading    =   true;
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.distritos'), {
                    provincia_id: this.provincia.id
                });
                const { error, message, distritos } = data;
                this.Distritos = distritos;
                this.loadingDistritos = true;
                this.loading    =   false;
            } catch (ex) {

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
                let documento   = this.destinatario.nro_documento;
                let url =  route('getApidni', { dni: documento });

                const { data } = await this.axios.get(url);
                
                this.CamposDNI(data);
                

            } catch (ex) {
                this.loading = false;
                alert("Error en consultarAPI" + ex);
            }
        },
        CamposDNI(results) {
            const { success, data } = results;
            if (success) {
                this.dataDNI = data;
                this.dataDNI.buscado = true;
                this.destinatario.nombres   =  data.nombres +' '+data.apellido_paterno + ' '+data.apellido_materno;
                this.loading = false;
            } else {

            }
        },
        CamposRUC(results) {
            const { success, data } = results;
            if(success){
                this.dataRUC = data;
                this.dataRUC.buscado = true;
                this.loading = false;
            }

        },
       
        // Cerrar(){

        //     this.departamento = {
        //         id: 13,
        //         nombre: "LA LIBERTAD",
        //         zona: "NORTE"
        //     };
        //     this.formCliente={
        //         tipo_documento: "",
        //         tipo_cliente_id: "",
        //         departamento: 0,
        //         provincia: 0,
        //         distrito: 0,
        //         zona: "",
        //         nombre: "",
        //         documento: "",
        //         direccion: "Direccion Trujillo",
        //         telefono_movil: "999999999",
        //         correo_electronico: "",
        //         telefono_fijo: "",
        //         codigo_verificacion: "",
        //         activo: "SIN VERIFICAR"
        //     }
        //     this.tipo_documento="DNI";
        //     this.tipo_cliente_id=121;
        // },
        async getTipoEnvios() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.getTipoEnvios"));
                this.tipos_envios   = data;
                //console.log(data);
            } catch (ex) {
                toastr.error(ex.message,'ERROR EN LA SOLICITUD AL OBTENER TIPOS DE ENVÍO');
            }
        },
        async getEmpresasEnvio(envio) {
            try { 
                this.loading        =   true;
                const { data }      =   await this.axios.get(route("consulta.ajax.getEmpresasEnvio",envio));
                
                if(data.success){
                    this.empresas_envio  = data.empresas_envio;
                    console.log('TIPO ENVÍO COLOCADO Y EMPRESAS ENVÍO CARGADAS')
                    this.$emit('tipoEnvioColocadoEmpresasEnvioCargadas'); 
                    
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message,'ERROR EN LA SOLICITUD OBTENER EMPRESAS ENVÍO');
            }finally{
                this.loading        =   false;
            }
        },
        async getSedesEnvio(empresa_envio_id,ubigeo) {
            try { 
                this.loading        =   true;
                const { data }      = await this.axios.get(route("consulta.ajax.getSedesEnvio",{empresa_envio_id,ubigeo}));
                
                if(data.success){   
                    this.sedes_envio  = data.sedes_envio;
                    console.log(data.sedes_envio);
                    console.log('EMPRESA ENVIO COLOCADA Y SEDES ENVIO CARGADAS');
                    this.$emit('empresasColocadasSedesCargadas');
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER SEDES DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message,'ERROR EN LA SOLICITUD OBTENER SEDES ENVÍO');
            }finally{
                this.loading        =   false;
            }
        },
        async getOrigenesVentas() {
            try { 
                this.loading        =   true;
                const { data }      = await this.axios.get(route("consulta.ajax.getOrigenesVentas"));
                
                if(data.success){
                    this.origenes_ventas    =   data.origenes_ventas;

                    //======= COLOCANDO POR DEFECTO WATHSAPP ====
                    if(data.origenes_ventas.length > 0){

                        const index_ov  = data.origenes_ventas.findIndex((ov)=>{
                            return ov.descripcion == "WATHSAPP";
                        })

                        if(index_ov !== -1){
                            this.origen_venta   =   {descripcion:data.origenes_ventas[index_ov].descripcion};
                        }else{
                            this.origen_venta   =   data.origenes_ventas[0].descripcion;
                        }

                    }else{
                        this.origen_venta       =   {descripcion:"SIN DATOS"};
                        toastr.error("REGISTRE ORÍGENES DE VENTA EN TABLAS GENERALES",'ERROR AL OBTENER ORÍGENES DE VENTA');
                    }
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER ORÍGENES DE VENTA');
                }
            } catch (error) {
                toastr.error(error.message,'ERROR EN LA SOLICITUD OBTENER ORÍGENES DE VENTAS');
            }finally{
                this.loading        =   false;
            }
        },
        async getTiposPagoEnvio() {
            try { 
                this.loading        =   true;
                const { data }      = await this.axios.get(route("consulta.ajax.getTiposPagoEnvio"));
                
                if(data.success){
                    this.tipos_pago_envio    =   data.tipos_pago_envio;

                    //========= COLOCANDO PRIMERA OPCIÓN POR DEFECTO ======
                    if(data.tipos_pago_envio.length > 0){
                        this.tipo_pago_envio   =   {
                            id:this.tipos_pago_envio[0].id,
                            descripcion:this.tipos_pago_envio[0].descripcion
                        };
                    }else{
                        this.tipo_pago_envio       =   {id:0,descripcion:"SIN DATOS"};
                        toastr.error("REGISTRE TIPOS DE PAGO ENVÍO EN TABLAS GENERALES",'ERROR AL OBTENER TIPOS PAGO ENVÍO');
                    }
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER TIPOS PAGO DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error.message,'ERROR EN LA SOLICITUD OBTENER TIPO DE PAGOS ENVÍO');
            }finally{
                this.loading        =   false;
            }
        },
        async getTipoDocumento() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.getTipoDocumentos"));

                //======== SELECCIONAMOS DNI Y CARNET EXTRANJERÍA ======
                const tipoDocumentosFilter  =  []; 
                
                data.forEach((td)=>{
                   
                    if(td.id == 6 || td.id == 7){
                        tipoDocumentosFilter.push(td.simbolo);
                    } 
                })

                if(tipoDocumentosFilter.length > 0){
                    this.destinatario.tipo_documento        =   tipoDocumentosFilter[0];         
                    this.tipoDocumentos                     =   tipoDocumentosFilter;
                }


                
            } catch (ex) {
                toastr.error(ex.message,'ERROR EN LA SOLICITUD AL OBTENER TIPOS DE DOCUMENTO');
            }
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