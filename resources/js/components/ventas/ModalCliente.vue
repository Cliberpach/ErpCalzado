<template>
    <div class="modal inmodal fade" tabindex="-1" id="modal_cliente" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <i class="fa fa-user-plus modal-icon"></i>
                    <h4 class="modal-title">NUEVO CLIENTE</h4>
                    <small class="font-bold">Registrar</small>
                </div>
                <div class="modal-body content_cliente" :class="{'sk__loading':loading}">
                    <form id="frmCliente" class="formulario" @submit.prevent="Guardar">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="required" style="font-weight: bold;" for="tipo_documento">Tipo de documento</label>
                                            <v-select v-model="tipo_documento" :options="tipoDocumentos"
                                                :reduce="tp=>tp.simbolo" label="simbolo"></v-select>
                                        </div>
                                        <span style="color:red;" class="error_mdl_client_tipo_documento"></span>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="documento">Nro. Documento</label>

                                            <div class="input-group">
                                                <input type="text" id="documento" name="documento" class="form-control"
                                                    :maxlength="maxlength" v-model="formCliente.documento" required
                                                    :disabled="(tipo_documento !== 'RUC' && tipo_documento !== 'DNI' &&
                                                        tipo_documento !== 'CARNET EXT.' && tipo_documento !== 'PASAPORTE'
                                                        && tipo_documento !== 'P. NAC.')? true: false">
                                                <span class="input-group-append">
                                                    <button type="button" style="color:white" class="btn btn-primary"
                                                        @click.prevent="consultarDocumento"
                                                        :disabled="(tipo_documento == 'RUC' || tipo_documento=='DNI') ? false: true">
                                                        <i class="fa fa-search"></i>
                                                        <span id="entidad">{{ entidad }}</span>
                                                    </button>
                                                </span>
                                            </div>
                                            <span style="color:red;" class="error_mdl_client_documento"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="required" for="tipo_cliente">Tipo Cliente</label>
                                            <v-select v-model="tipo_cliente_id" :options="tipoClientes"
                                                :reduce="tc=>tc.id" label="descripcion"></v-select>
                                        </div>
                                        <span style="color:red;" class="error_mdl_client_tipo_cliente_id"></span>
                                    </div>
                                    <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="" for="activo">Estado</label>
                                            <input type="text" id="activo" name="activo"
                                                class="form-control text-center" v-model="formCliente.activo" readonly>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="required" id="lblNombre" for="nombre">Nombre</label>
                                            <input type="text" id="nombre" name="nombre" class="form-control"
                                                maxlength="191" v-model="formCliente.nombre" required>

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="direccion" class="required">Dirección Fiscal</label>
                                            <input type="text" id="direccion" name="direccion" class="form-control"
                                                maxlength="191" onkeyup="return mayus(this)" required
                                                v-model="formCliente.direccion">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="departamento">Departamento</label>
                                            <v-select v-model="departamento" :options="lst_departamentos_base" :reduce="d=>d"
                                                label="nombre"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="provincia">Provincia</label>
                                            <v-select v-model="provincia" :options="lstProvinciasFiltrado" :reduce="p=>p"
                                                label="nombre"></v-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="distrito">Distrito</label>
                                            <v-select v-model="distrito" :options="lstDistritosFiltrado" :reduce="d=>d"
                                                label="nombre"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="required" for="zona">Zona</label>
                                            <input type="text" id="zona" name="zona" v-model="formCliente.zona"
                                                class=" text-center form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="telefono_movil" class="required">Teléfono móvil</label>
                                            <input type="text" id="telefono_movil" name="telefono_movil"
                                                class="form-control" onkeypress="return isNroPhone(event)" maxlength="9"
                                                required v-model="formCliente.telefono_movil">

                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="telefono_fijo">Teléfono fijo</label>
                                            <input type="text" id="telefono_fijo" name="telefono_fijo"
                                                class="form-control" onkeypress="return isNroPhone(event)" maxlength="9"
                                                v-model="formCliente.telefono_fijo">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="correo_electronico">Correo electr&oacute;nico</label>
                                            <input type="text" id="correo_electronico" name="correo_electronico"
                                                class="form-control" v-model="formCliente.correo_electronico">
                                        </div>
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
                        <button id="btn-guardar-cliente" type="submit" class="btn btn-primary btn-sm" form="frmCliente" style="color:white;">
                            <i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
export default {
    name: "ModalCliente",
    props: {
        lst_departamentos_base:{
            type:Array,
            default:[]
        },
        lst_provincias_base:{
            type:Array,
            default:[]
        },
        lst_distritos_base:{
            type:Array,
            default:[]
        }
    },
    data() {
        return {
            loading: false,
            tipoDocumentos: [],
            tipoClientes: [],
            lstProvinciasFiltrado: [],
            lstDistritosFiltrado: [],
            tipo_documento: "",
            tipo_cliente_id: "",
            departamento: {
                id: 0,
                nombre: "",
                zona: ""
            },
            provincia: {
                id: 0,
                nombre: ""
            },
            distrito: {
                id: 0,
                nombre: ""
            },
            formCliente: {
                tipo_documento: "",
                tipo_cliente_id: "",
                departamento: 0,
                provincia: 0,
                distrito: 0,
                zona: "",
                nombre: "",
                documento: "",
                direccion: "Direccion Trujillo",
                telefono_movil: "999999999",
                correo_electronico: "",
                telefono_fijo: "",
                codigo_verificacion: "",
                activo: "SIN VERIFICAR"
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
            clienteNuevo: {
                id:null,
                tabladetalles_id:null,
                tipo_documento:null,
                documento:null,
                nombre:null
             },
            maxlength:8
        }
    },
    watch: {
        tipoDocumentos(value) {
            this.tipo_documento = value.length > 0 ? value[0].simbolo : "";
        },
        tipoClientes(value) {
            this.tipo_cliente_id = value.length > 0 ? value[0].id : "";
        },
        departamento: {
            handler(value) {
                
                this.provincia                  =   null;
                this.distrito                   =   null;
                this.lstProvinciasFiltrado      =   [];
                this.lstDistritosFiltrado       =   [];
                this.formCliente.zona           =   value ? value.zona : "";
                this.formCliente.departamento   =   value ? value.id : 0;

                let departamento_id = value.id;

                if (departamento_id) {
                    this.setLstProvinciasFiltradas(departamento_id);
                    this.provincia                  =   this.lstProvinciasFiltrado[0];
                    this.formCliente.departamento   =   value.id;
                    this.formCliente.provincia      =   this.provincia.id;
                }
            },
            deep: true 
        },
        provincia(value){
          
            let provincia_id            =   value.id;
            this.distrito               =   null;
            this.lstDistritosFiltrado   =   [];
            
            if(provincia_id){
               this.setLstDistritosFiltrados(provincia_id);
               this.distrito                =   this.lstDistritosFiltrado[0];
               this.formCliente.distrito    =   this.distrito.id;
            }
        },
        tipo_documento(value) {
            //======= LIMPIAR EL NRO DE DOCUMENTO ======
            this.formCliente.documento  =   '';
            this.formCliente.tipo_documento = value;

            //====== EN CASO EL TIPO DOCUMENTO SEA VACÍO ===
            if(!value){
                this.entidad                    =   "Entidad";
                return;
            }
            
            this.formCliente.activo         = "SIN VERIFICAR";
            this.entidad = value == "DNI" ? "Reniec" : (value == "RUC" ? "Sunat" : "Entidad");

            if(value    ==  "DNI"){
                this.maxlength = 8;
            }
            if(value  ==  "RUC"){
                this.maxlength = 11;
            }else{
                this.maxlength = 20; 
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
                
                //========= COLOCANDO DATOS DEL CLIENTE =======
                this.formCliente.codigo_verificacion    = this.dataDNI.codigo_verificacion == "-" || this.dataDNI.codigo_verificacion === null ? "" : this.dataDNI.codigo_verificacion;
                this.formCliente.nombre                 = this.dataDNI.nombres + " " + this.dataDNI.apellido_paterno + " " + this.dataDNI.apellido_materno;
                this.formCliente.direccion              = this.dataDNI.direccion_completa;
                this.formCliente.activo                 = "ACTIVO";

                //======== COLOCANDO UBIGEO =======
                const departamento_id   =   value.ubigeo[0];
                const provincia_id      =   value.ubigeo[1];
                const distrito_id       =   value.ubigeo[2];

                if(departamento_id && provincia_id && distrito_id){

                    this.departamento       =   this.lst_departamentos_base.find((d)=>{
                        return d.id == departamento_id;
                    })

                    this.$nextTick(() => {
                        this.provincia = this.lst_provincias_base.find((pr) => pr.id == provincia_id);

                        this.$nextTick(() => {
                            this.distrito = this.lst_distritos_base.find((d) => d.id == distrito_id);
                        });
                    });
                   
                }
            }
        },
        dataRUC(value){
            if (value.buscado) {

                let iddepart                            = value.ubigeo.length > 0 ? value.ubigeo[0] : 0;
                this.formCliente.codigo_verificacion    = "";
                this.formCliente.nombre                 = this.dataRUC.nombre_o_razon_social;
                this.formCliente.direccion              = this.dataRUC.direccion;
                this.formCliente.activo                 = "ACTIVO";

                //======== COLOCANDO UBIGEO =======
                const departamento_id   =   value.ubigeo[0];
                const provincia_id      =   value.ubigeo[1];
                const distrito_id       =   value.ubigeo[2];

                if(departamento_id && provincia_id && distrito_id){

                    this.departamento       =   this.lst_departamentos_base.find((d)=>{
                        return d.id == departamento_id;
                    })

                    this.$nextTick(() => {
                        this.provincia = this.lst_provincias_base.find((pr) => pr.id == provincia_id);

                        this.$nextTick(() => {
                            this.distrito = this.lst_distritos_base.find((d) => d.id == distrito_id);
                        });
                    });
                   
                }

            }
        },
    },
   
    created() {

        this.setLstProvinciasFiltradas(13);
        this.setLstDistritosFiltrados(1301);

        this.departamento   =   { id: 13, nombre: "LA LIBERTAD", zona: "NORTE" };
        this.provincia      =   { id: 1301, nombre: "TRUJILLO",departamento_id:13 };
        this.distrito       =   { id: 130101, nombre: "TRUJILLO",provincia_id:1301 };


        this.getTipoDocumento();
        this.getTipoCliente();       
    },
    methods: {
       
        setLstProvinciasFiltradas(departamento_id){
            departamento_id             = String(departamento_id).padStart(2, '0');
            this.lstProvinciasFiltrado  = this.lst_provincias_base.filter(provincia => provincia.departamento_id == departamento_id);
            
        },
        setLstDistritosFiltrados(provincia_id){
            provincia_id = String(provincia_id).padStart(4, '0');

            this.lstDistritosFiltrado      =   this.lst_distritos_base.filter((distrito)=>{
                return  distrito.provincia_id == provincia_id;
            })  
        },
        async Guardar() {
            try {
                this.clearErrores();
                document.querySelector('#btn-guardar-cliente').disabled     =   true;
                document.querySelector('#btn-guardar-cliente').innerHTML    =   `<i class="fa fa-save fa-spin"></i> Guardando...`;

                this.loading = true;
                const res = await this.axios.post(route('ventas.cliente.storeFast'), this.formCliente);
                
                if(res.data.success){
                
                    this.clienteNuevo    =   res.data.cliente;

                    this.$emit("newCliente",this.clienteNuevo);

                    this.Cerrar();
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    $("#modal_cliente").modal("hide");
                    return;
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (ex) {
                if('errors' in ex.response.data){
                    //======= PINTAR ERRORES DE VALIDACIÓN =======
                    this.pintarErrores(ex.response.data.errors);
                    return;
                }

                toastr.error(ex,'ERROR EN LA PETICIÓN CREAR CLIENTE');
            }finally{
                this.loading = false;
                document.querySelector('#btn-guardar-cliente').disabled     =   false;
                document.querySelector('#btn-guardar-cliente').innerHTML    =   `<i class="fa fa-save"></i> Guardar`;
            }
        },
        pintarErrores(msgErrors){
            for (let key in msgErrors) {
                if (msgErrors.hasOwnProperty(key)) {
                    const propiedad =   msgErrors[key];
                    const message   =   propiedad[0];
                    document.querySelector(`.error_mdl_client_${key}`).textContent    =   message;
                }
            }
        },
        clearErrores(){
            const elements = document.querySelectorAll('[class^="error_mdl_client_"]');
            elements.forEach((span)=>{
                span.textContent   =   '';
            })
        },
        async getTipoDocumento() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.getTipoDocumentos"));
                this.tipoDocumentos = data;
            } catch (ex) {

            }
        },
        async getTipoCliente() {
            try {
                const { data } = await this.axios.get(route("consulta.ajax.tipoClientes"));
                this.tipoClientes = data;
            } catch (ex) {

            }
        },
        async consultarDocumento() {
            try {
                this.loading = true;
                const { data } = await this.axios.post(route('ventas.cliente.getDocumento'), {
                    tipo_documento: this.tipo_documento,
                    documento: this.formCliente.documento,
                    id: null
                });
                const { existe } = data;
                if (existe) {
                    this.loading = false;
                    toastr.error('El ' + this.tipo_documento + ' ingresado ya se encuentra registrado para un cliente',
                        'Registrado');
                } else {
                    if (this.tipo_documento === "DNI") {
                        if (this.formCliente.documento.length === 8) {
                            this.consultarAPI();
                        } else {
                            this.loading = false;
                            toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                        }
                    } else if (this.tipo_documento === "RUC") {
                        if (this.formCliente.documento.length === 11) {
                            this.consultarAPI();
                        } else {
                            this.loading = false;
                            toastr.error('El RUC debe de contar con 11 dígitos', 'Error');
                        }
                    }
                }
            } catch (ex) {
                alert("Error en consultarDocumento" + ex);
            }
        },
        async consultarAPI() {
            try {
                let tipoDoc     = this.tipo_documento;
                let documento   = this.formCliente.documento;
                let url         = tipoDoc == "DNI" ? route('getApidni', { dni: documento }) : route('getApiruc', { ruc: documento });
                const res       = await this.axios.get(url);
                
                if(res.data.success){
                    const data  =   res.data;
                    if (tipoDoc == "DNI") {
                        this.CamposDNI(data);
                    }

                    if (tipoDoc == "RUC") {
                        this.CamposRUC(data);
                    }
                }else{
                    document.querySelector('#documento').focus();
                    this.loading = false;
                    toastr.error(res.data.message,'ERROR AL CONSULTAR '+tipoDoc);
                }
               
            } catch (ex) {
                this.loading = false;
                alert("Error en consultarAPI" + ex);
            }
        },
        CamposDNI(results) {
            const { success, data } = results;
            if (success) {
                this.dataDNI            =   data;
                this.dataDNI.buscado    =   true;
                this.loading            =   false;
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
       
        Cerrar(){
            
            this.departamento   =   { id: 13, nombre: "LA LIBERTAD", zona: "NORTE" };
            this.provincia      =   { id: 1301, nombre: "TRUJILLO",departamento_id:13 };
            this.distrito       =   { id: 130101, nombre: "TRUJILLO",provincia_id:1301 };

            this.formCliente={
                tipo_documento: "DNI",
                tipo_cliente_id: 121,
                departamento: 13,
                provincia: 1301,
                distrito: 130101,
                zona: "",
                nombre: "",
                documento: "",
                direccion: "Direccion Trujillo",
                telefono_movil: "999999999",
                correo_electronico: "",
                telefono_fijo: "",
                codigo_verificacion: "",
                activo: "SIN VERIFICAR"
            }

            this.tipo_documento     =   "DNI";
            this.tipo_cliente_id    =   121;

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