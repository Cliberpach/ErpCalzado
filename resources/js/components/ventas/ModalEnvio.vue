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
                                <label for="" style="font-weight: bold;">TIPO DE ENVÍO</label>
                                <div class="row">
                                    <div class="col-4">
                                        <v-select v-model="tipo_envio" :options="tipos_envios" :reduce="te=>te"
                                            required :clearable="false" label="descripcion" ></v-select>
                                    </div>
                                    <div class="col-3 d-flex align-items-center" v-if="mostrarColumnaContraentrega">
                                        <input id="check_contraentrega" type="checkbox" v-model="contraentrega" class="form-control">
                                        <label for="check_contraentrega" class="mb-0" style="margin-right: 95px;font-weight: bold;">CONTRAENTREGA</label>
                                    </div>
                                    <div class="col-5 d-flex align-items-center">
                                        <input id="check_envio_gratis" type="checkbox" v-model="envio_gratis" class="form-control">
                                        <label for="check_envio_gratis" class="mb-0" style="margin-right: 350px;font-weight: bold;">ENVIO GRATIS</label>
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
                                        <label for="">DIRECCION DE ENTREGA</label>
                                        <input :readonly="!entrega_domicilio" type="text" class="form-control" v-model="direccion_entrega">
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
                                    <div class="col-6">
                                        <label for="observaciones" style="font-weight: bold;">OBSERVACIÓN</label>
                                        <textarea id="observaciones" v-model="observaciones" class="form-control"></textarea>
                                    </div>
                                </div>
                                <hr>
                                <label for="" style="font-weight: bold;">DATOS DEL DESTINATARIO</label>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="required" for="dni_destinatario">Nro. DNI</label>
                                            <div class="input-group">
                                                <input type="text" id="dni_destinatario"  class="form-control"
                                                    :maxlength="maxlength" v-model="destinatario.dni" required>
                                                <span class="input-group-append">
                                                    <button type="button" style="color:white" class="btn btn-primary"
                                                        @click.prevent="consultarDocumento">
                                                        <i class="fa fa-search"></i>
                                                        <span id="entidad">{{ entidad }}</span>
                                                    </button>
                                                </span>
                                            </div>
                                    </div>
                                    <div class="col-8">
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
            loading: false,
            direccion_entrega:"",
            entrega_domicilio:false,
            contraentrega:false,
            envio_gratis:false,
            mostrarColumnaContraentrega:false,
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
            observaciones:"",
            destinatario:{
                dni:"",
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
            maxlength:8
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
        cliente(value){
            console.log(value)
            if(value.tipo_documento === "DNI"){
                if(value.documento === "99999999"){
                    this.destinatario.dni       =   "";
                    this.destinatario.nombres   =   "";
                    return;
                }
                this.destinatario.dni       =   value.documento;
                this.destinatario.nombres   =   value.nombre;
            }
            if(value.tipo_documento === "RUC"){
                this.destinatario.dni       =   "";
                this.destinatario.nombres   =   "";
            } 
        },
        empresa_envio(value){
            console.log('empresa envio seleccionada');
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
                    this.mostrarColumnaContraentrega    =   false;
                    this.mostrar_entrega_domicilio      =   true;
                }

                //====== DELIVERY ======
                /*  ->NO TIENE SEDES  
                    ->PUEDE HABER CONTRAENTREGA
                    ->HAY ENTREGA A DOMICILIO SIEMPRE
                */
                if(value.descripcion === "DELIVERY"){
                    this.mostrar_combo_sedes            =   false;
                    this.mostrarColumnaContraentrega    =   true;
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
                    this.mostrarColumnaContraentrega    =   false;
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
            if (!this.dataDNI.buscado && this.loadingProvincias) {
                this.provincia = value.length > 0 ? value[0] : null;
            }
            if (!this.dataRUC.buscado && this.loadingProvincias) {
                this.provincia = value.length > 0 ? value[0] : null;
            }
        },
        Distritos(value) {
            if (!this.dataDNI.buscado && this.loadingDistritos) {
                this.distrito = value.length > 0 ? value[0] : null;
            }
            if (!this.dataRUC.buscado && this.loadingDistritos) {
                this.distrito = value.length > 0 ? value[0] : null;
            }
        },
        departamento(value) {
            if(value){
                console.log(`DEPARTAMENTO: `);
                console.log(value);
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
                console.log(`PROVINCIA: `);
                console.log(value);

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

                this.$nextTick(this.getDistritos);
            }
        },
        distrito(value) {
            console.log(`DISTRITO: `);
            console.log(value);
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
        await this.getOrigenesVentas();
        await this.getEmpresasEnvio(this.tipo_envio.descripcion);
        this.loading    =   false;
    },
    methods: {
        borrarEnvio(){
            this.formEnvio      =   {};
            console.log(this.formEnvio);
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
          if(this.destinatario.dni.length < 8){
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
          this.formEnvio.contraentrega          =   this.contraentrega;
          this.formEnvio.direccion_entrega      =   this.direccion_entrega;
          this.formEnvio.entrega_domicilio      =   this.entrega_domicilio;
          this.formEnvio.envio_gratis           =   this.envio_gratis;
          this.formEnvio.origen_venta           =   this.origen_venta;
          this.formEnvio.fecha_envio_propuesta  =   this.fecha_envio;
          this.formEnvio.observaciones          =   this.observaciones;

          toastr.success('DATOS DE ENVÍO GUARDADOS','OPERACIÓN COMPLETADA');
          console.log('FORMULARIO ENVIO');
          console.log(this.formEnvio);
          this.$emit('addDataEnvio',JSON.stringify(this.formEnvio));
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
               
                if (this.destinatario.dni.length === 8) {
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
                let documento   = this.destinatario.dni;
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

            }
        },
        async getEmpresasEnvio(envio) {
            try { 
                this.loading        =   true;
                const { data }      =   await this.axios.get(route("consulta.ajax.getEmpresasEnvio",envio));
                
                if(data.success){
                    this.empresas_envio  = data.empresas_envio;
                    console.log(data);
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR');
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
                    console.log(data);
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR');
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
                    console.log(data);

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
                    }
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR');
            }finally{
                this.loading        =   false;
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