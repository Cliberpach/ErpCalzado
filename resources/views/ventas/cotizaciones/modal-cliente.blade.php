i<div class="modal inmodal" id="modal_cliente" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                {{-- <i class="fa fa-user-plus modal-icon"></i> --}}
                <i class="fas fa-user-astronaut fa-pulse modal-icon" ></i>
                <h4 class="modal-title">NUEVO CLIENTE</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente" :class="{'sk__loading':loading}">
                <form id="frmCliente" class="formulario">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="tipo_documento">Tipo de documento</label>
                                        <select class="select2_form" name="tipo_documento" id="tipo_documento" onchange="controlNroDoc(this)">
                                            @foreach ($tipos_documento as $tipo_documento)
                                                <option value="{{$tipo_documento->id}}">{{$tipo_documento->simbolo}}</option>
                                            @endforeach
                                        </select>
                                        {{-- <v-select v-model="tipo_documento" :options="tipoDocumentos"
                                            :reduce="tp=>tp.simbolo" label="simbolo"></v-select> --}}
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="nro_documento">Nro. Documento</label>
                                        <div class="input-group">
                                            <input type="text" id="nro_documento" name="nro_documento" class="form-control"
                                                 required>
                                                 <button onclick="consultarDocumento()" type="button" style="color:white" class="btn btn-primary">
                                                    <i class="fa fa-search" ></i>
                                                    <span id="entidad"> </span>
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="tipo_cliente">Tipo Cliente</label>
                                        <select class="select2_form" name="tipo_documento" id="tipo_cliente" >
                                            @foreach ($tipo_clientes as $tipo_cliente)
                                                <option value="{{$tipo_cliente->id}}">{{$tipo_cliente->simbolo}}</option>
                                            @endforeach
                                        </select>
                                        {{-- <v-select v-model="tipo_cliente_id" :options="tipoClientes"
                                            :reduce="tc=>tc.id" label="descripcion"></v-select> --}}
                                    </div>
                                </div>
                                <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="" for="activo">Estado</label>
                                        <input type="text" id="activo" name="activo" value="SIN VERIFICAR"
                                            class="form-control text-center" readonly>

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
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="departamento">Departamento</label>
                                        <select class="select2_form" name="departamento" id="departamento" onchange="setUbicacionDepartamento(this)">
                                            @foreach ($departamentos as $departamento)
                                                <option @if ($departamento->id == 13) selected @endif value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                            @endforeach
                                        </select>
                                        {{-- <v-select v-model="departamento" :options="Departamentos" :reduce="d=>d"
                                            label="nombre"></v-select> --}}
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="provincia">Provincia</label>
                                        <select class="select2_form" name="provincia" id="provincia" onchange="setUbicacionProvincia(this)" >
                                           
                                        </select>
                                        {{-- <v-select v-model="provincia" :options="Provincias" :reduce="p=>p"
                                            label="text"></v-select> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="distrito">Distrito</label>
                                        <select class="select2_form" name="distrito" id="distrito" >
                                           
                                        </select>
                                        {{-- <v-select v-model="distrito" :options="Distritos" :reduce="d=>d"
                                            label="text"></v-select> --}}
                                        <!-- <select id="distrito" name="distrito" class="select2_form form-control"
                                            style="width: 100%">
                                            <option></option>
                                        </select> -->
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="zona">Zona</label>
                                        <input type="text" id="zona" name="zona" 
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
                <div class="sk-spinner sk-spinner-wave d-none">
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
                    <button type="submit" class="btn btn-primary btn-sm" form="frmCliente" style="color:white;"><i
                            class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    const spinner           =   document.querySelector('.sk-spinner');
    const selectTipoDoc     =   document.querySelector('#tipo_documento');
    const selectProvincia   =   document.querySelector('#provincia');
    const selectDistrito    =   document.querySelector('#distrito');
    const inputNroDoc       =   document.querySelector('#nro_documento');
    const btnConsultarDoc   =   document.querySelector('#btn_consultar_doc');
    const inputZona         =   document.querySelector('#zona');
    const departamentos     =   @json($departamentos);

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        //====== ESTABLECER UBICACIÓN POR DEFAULT =======
        setUbicacionDepartamento({value:13});

    })

    function events(){
   

    }

    function hola(){
        console.log('hola')
    }

    function controlNroDoc(e){
        const tipoDocSimbolo    =   e.value;

            if(tipoDocSimbolo == 6 || tipoDocSimbolo == 8){
                inputNroDoc.disabled        =   false;
                btnConsultarDoc.disabled    =   false;
            }else{
                inputNroDoc.disabled        =   true;
                btnConsultarDoc.disabled    =   true;
            }
    }

    function setUbicacionDepartamento(e){
        const departamento_id   =   e.value;
       
        setZona(getZona(departamento_id));
        getProvincias(departamento_id);
    }

    function setUbicacionProvincia(e){
        const provincia_id      =   e.value;
        getDistritos(provincia_id);
    }

    function getZona(departamento_id){
        const departamento      =   departamentos.filter((d)=>{
            return d.id==departamento_id;
        })

        return departamento[0].zona;
    }

    function setZona(zona_nombre){
        inputZona.value =   zona_nombre;
    }


    //======= GET PROVINCIAS ==========
    async function getProvincias(departamento_id) {
            try {
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                    departamento_id
                });
                const { error, message, provincias } = data;
                pintarProvincias(provincias);
                // this.Provincias = provincias;
                // this.loadingProvincias = true;
            } catch (ex) {

            }
    }

    //======== pintar provincias =========
    function pintarProvincias(provincias){
        let options =   ``;
        provincias.forEach((provincia)=>{
            options+= `
                <option value="${provincia.id}">${provincia.text}</option>
            `
        })

        selectProvincia.innerHTML   =   options;

        //====== seleccionar primera opción =======
        $(selectProvincia).val($(selectProvincia).find('option').first().val()).trigger('change.select2');
    }

    //====== PINTAR DISTRITOS ========
    async function getDistritos(provincia_id) {
            try {
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.distritos'), {
                    provincia_id
                });
                const { error, message, distritos } = data;
                pintarDistritos(distritos);
                // this.Distritos = distritos;
                // this.loadingDistritos = true;
            } catch (ex) {

            }
    }

    //======== PINTAR DISTRITOS =========
    function pintarDistritos(distritos){
        let options =   ``;
        distritos.forEach((distrito)=>{
            options+= `
                <option value="${distrito.id}">${distrito.text}</option>
            `
        })

        selectDistrito.innerHTML   =   options;

        //====== seleccionar primera opción =======
        $(selectDistrito).val($(selectDistrito).find('option').first().val()).trigger('change.select2');
    }


    //========= CONSULTAR DOCUMENTO ========
    async function consultarDocumento() {
        try {
            const tipoDocumento     =   selectTipoDoc.value;
            const numeroDocumento   =   inputNroDoc.value;

            // spinner.classList.toggle('hide-cliente');
            const { data } = await this.axios.post(route('ventas.cliente.getDocumento'), {
                tipo_documento: selectTipoDoc.value,
                documento: inputNroDoc.value,
                id: null
            });

            const { existe } = data;
            console.log(data);
            if (existe) {
                    this.loading = false;
                    toastr.error('El ' + this.tipo_documento + ' ingresado ya se encuentra registrado para un cliente',
                        'Registrado');
            } else {
                    if (tipoDocumento === "DNI") {
                        if (numeroDocumento.length === 8) {
                             this.consultarAPI(tipoDocumento,numeroDocumento);
                        } else {
                            //  this.loading = false;
                             toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                        }
                    } else if (this.tipo_documento === "RUC") {
                        if (numeroDocumento.length === 11) {
                            this.consultarAPI(tipoDocumento,numeroDocumento);
                        } else {
                            //  this.loading = false;
                            toastr.error('El RUC debe de contar con 11 dígitos', 'Error');   
                        }
                    }
            } 
        }catch (ex) {
                alert("Error en consultarDocumento" + ex);
        }
    }

    //======= CONSULTAR API =======
    async function consultarAPI(tipo_documento,nro_documento) {
            try {
                let tipoDoc     = tipo_documento;
                let documento   = nro_documento;
                let url         = tipoDoc == "DNI" ? route('getApidni', { dni: nro_documento }) : route('getApiruc', { ruc: nro_documento });
                const { data } = await this.axios.get(url);
                console.log(data);
                // if (tipoDoc == "DNI") {
                //     this.CamposDNI(data);
                // }

                // if (tipoDoc == "RUC") {
                //     this.CamposRUC(data);
                // }
            } catch (ex) {
                this.loading = false;
                alert("Error en consultarAPI" + ex);
            }
    }
</script>