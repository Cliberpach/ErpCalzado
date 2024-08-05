<style>
    .talla-no-creada{
        color:rgb(201, 47, 9);
        font-weight: bold;
    }

div.content-window {
    position: relative;
}

div.content-window.sk__loading::after {
    content: '';
    background-color: rgba(255, 255, 255, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3000;
}

.content-window.sk__loading>.sk-spinner.sk-spinner-wave {
    margin: 0 auto;
    width: 50px;
    height: 30px;
    text-align: center;
    font-size: 10px;
}

.content-window.sk__loading>.sk-spinner {
    display: block;
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    z-index: 3500;
}

.content-window .sk-spinner.sk-spinner-wave.hide-window {
    display: none;
}
</style>

<div class="modal inmodal" id="modal_cliente" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-user-astronaut fa-pulse modal-icon"></i>
                <h4 class="modal-title">NUEVO CLIENTE</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente content-window">
                @include('components.overlay_search')
                @include('components.overlay_save')

             
                <div class="sk-spinner sk-spinner-wave hide-window" >
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
                  
             

                <form id="frmCliente" class="formulario">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="tipo_documento">Tipo de documento</label>
                                        <select  required class="select2_form" name="tipo_documento" id="tipo_documento" onchange="controlNroDoc(this)">
                                            @foreach ($tipos_documento as $tipo_documento)
                                                <option value="{{$tipo_documento->id}}">{{$tipo_documento->simbolo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_tipo_documento"></span>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="documento">Nro. Documento</label>
                                        <div class="input-group">
                                            <input type="text" id="documento" name="documento" class="form-control"
                                                 required maxlength="8" oninput="validarDocumento(this)">
                                            <button id="btn_consultar_doc" onclick="consultarDocumento()" type="button" style="color:white" class="btn btn-primary">
                                                <i class="fa fa-search" ></i>
                                                <span id="entidad"> </span>
                                            </button>
                                        </div>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_documento"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="tipo_cliente">Tipo Cliente</label>
                                        <select class="select2_form" name="tipo_cliente_id" id="tipo_cliente_id" >
                                            @foreach ($tipo_clientes as $tipo_cliente)
                                                <option value="{{$tipo_cliente->id}}">{{$tipo_cliente->simbolo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_tipo_cliente_id"></span>
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
                                <div class="col-12 mb-2">
                                    <div class="form-group m-0">
                                        <label for="direccion" class="required">Dirección Fiscal</label>
                                        <input type="text" id="direccion" name="direccion" class="form-control"
                                            maxlength="191" onkeyup="return mayus(this)" required>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_direccion"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="departamento">Departamento</label>
                                        <select required class="select2_form" name="departamento" id="departamento" onchange="setUbicacionDepartamento(this.value,'first')">
                                            @foreach ($departamentos as $departamento)
                                                <option @if ($departamento->id == 13) selected @endif value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_departamento"></span>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="provincia">Provincia</label>
                                        <select required class="select2_form" name="provincia" id="provincia" onchange="setUbicacionProvincia(this.value,'first')" >
                                           
                                        </select>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_provincia"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="distrito">Distrito</label>
                                        <select required class="select2_form" name="distrito" id="distrito">
                                           
                                        </select>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_distrito"></span>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label class="required" for="zona">Zona</label>
                                        <input type="text" id="zona" name="zona" 
                                            class=" text-center form-control" readonly>
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_zona"></span>
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
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="form-group m-0">
                                        <label for="telefono_fijo">Teléfono fijo</label>
                                        <input type="text" id="telefono_fijo" name="telefono_fijo"
                                            class="form-control" onkeypress="return isNroPhone(event)" maxlength="9">
                                    </div>
                                    <span style="color:red;" class="error_mdl_client_telefono_movil"></span>
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
                    <button disabled id="btnGuardarCliente" type="submit" class="btn btn-primary btn-sm" form="frmCliente" style="color:white;"><i
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
    const inputNroDoc       =   document.querySelector('#documento');
    const btnConsultarDoc   =   document.querySelector('#btn_consultar_doc');
    const inputZona         =   document.querySelector('#zona');
    const departamentos     =   @json($departamentos);
    const formCliente       =   document.querySelector('#frmCliente');


    function controlNroDoc(e){
        const tipoDocSimbolo    =   e.value;
       
        //======= LIMPIAR EL NRO DOC ======
        inputNroDoc.value   =   '';
        document.querySelector('#btnGuardarCliente').disabled    =   true;

        //======= SI NO SE ELIGE UN TIPO DE DOCUMENTO, SE DESHABILITA EL INPUT NRO Y EL BTN CONSULTAR =====
        if(tipoDocSimbolo.length === 0){
           inputNroDoc.disabled     =   true;
           btnConsultarDoc.disabled =   true;
           return;
        }

        if(tipoDocSimbolo == 6 || tipoDocSimbolo == 8){
            inputNroDoc.disabled        =   false;
            btnConsultarDoc.disabled    =   false;
        }else{
            btnConsultarDoc.disabled    =   true;
        }

        //======= CONTROLANDO LONGITUDES ======
        //======= DNI ======
        if(tipoDocSimbolo == 6){
            inputNroDoc.maxLength   =   8;
        }

        //======= RUC =======
        if(tipoDocSimbolo == 8){
            inputNroDoc.maxLength   =   11;
        }

        //======= CARNET EXTRANJERÍA =======
        if(tipoDocSimbolo != 6 &&  tipoDocSimbolo != 8){
            inputNroDoc.maxLength   =   20;
        }

    }

    async function setUbicacionDepartamento(dep_id,provincia_id){
        
        const departamento_id   =   dep_id;
        console.log(`provincia: ${provincia_id}`);
       
        setZona(getZona(departamento_id));
        
       
        const provincias    =   await getProvincias(departamento_id,provincia_id);
        pintarProvincias(provincias,provincia_id);
       
    }

    async function setUbicacionProvincia(prov_id,distrito_id){
        const provincia_id      =   prov_id;
        
        const distritos         =   await getDistritos(provincia_id);
        pintarDistritos(distritos,distrito_id);
        ocultarAnimacion();
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

    function mostrarAnimacion(){
        document.querySelector('.content-window').classList.add('sk__loading');
        document.querySelector('.sk-spinner').classList.remove('hide-window');
    }

    function ocultarAnimacion(){
        document.querySelector('.content-window').classList.remove('sk__loading');
        document.querySelector('.sk-spinner').classList.add('hide-window');
    }


    //======= GET PROVINCIAS ==========
    async function getProvincias(departamento_id) {
            try {
                mostrarAnimacion();
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                    departamento_id
                });
                const { error, message, provincias } = data;
                
                return provincias;
            } catch (ex) {

            }
    }

    //======== pintar provincias =========
    function pintarProvincias(provincias,provincia_id){
        let options =   ``;
        provincias.forEach((provincia)=>{
            options+= `
                <option ${provincia.id == provincia_id? 'selected':''} value="${provincia.id}">${provincia.text}</option>
            `
        })

        selectProvincia.innerHTML   =   options;

        //====== seleccionar primera opción =======
        if(provincia_id == 'first'){
            $(selectProvincia).val($(selectProvincia).find('option').first().val()).trigger('change.select2');
        }else{
            $("#provincia").val(provincia_id).trigger("change.select2");
        }
    }

    //====== PINTAR DISTRITOS ========
    async function getDistritos(provincia_id,distrito_id) {
            try {
                mostrarAnimacion();
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.distritos'), {
                    provincia_id
                });
                const { error, message, distritos } = data;
                // this.Distritos = distritos;
                // this.loadingDistritos = true;
                return distritos;
            } catch (ex) {

            }
    }

    //======== PINTAR DISTRITOS =========
    function pintarDistritos(distritos,distrito_id){
        let options =   ``;
        distritos.forEach((distrito)=>{
            options+= `
                <option value="${distrito.id}">${distrito.text}</option>
            `
        })

        selectDistrito.innerHTML   =   options;
        if(distrito_id == 'first'){
            //====== seleccionar primera opción =======
            $(selectDistrito).val($(selectDistrito).find('option').first().val()).trigger('change.select2');
        }else{
            $("#distrito").val(distrito_id).trigger("change.select2");
        }
    }


    //========= CONSULTAR DOCUMENTO ========
    async function consultarDocumento() {
        try {
            //======= MOSTRAR OVERLAY =======
            const overlay = document.getElementById('overlay');
            overlay.style.display = 'flex'; 

            const tipoDocumento     =   selectTipoDoc.options[selectTipoDoc.selectedIndex].textContent;
            const numeroDocumento   =   inputNroDoc.value;
            console.log(tipoDocumento)
            console.log(numeroDocumento);
            console.log(numeroDocumento.trim().length)

           
            //========  VALIDACIÓN DEL NRO DOCUMENTO ==========
            if(tipoDocumento === 'DNI'){
                if(numeroDocumento.length !== 8){
                    toastr.error('EL DNI DEBE CONTAR CON 8 DÍGITOS','NRO DE DNI INCORRECTO');
                    return;
                }
            }
            if(tipoDocumento === 'RUC'){
                if(numeroDocumento.length !== 11){
                    toastr.error('EL RUC DEBE CONTAR CON 11 DÍGITOS','NRO DE RUC INCORRECTO');
                    return;
                }
            }

            //========== CONSULTANDO SI EL CLIENTE YA EXISTE  EN LA BD =======
            const url           = `/ventas/clientes/getCliente/${tipoDocumento}/${numeroDocumento}`;
            const res           = await axios.get(url);
            let existeCliente   =   false;

            if(res.data.success){
                if(res.data.cliente.length === 1){
                    existeCliente   =   true;
                    toastr.error(res.data.message,'CONSULTA COMPLETADA');
                }
                if(res.data.cliente.length === 0){
                    existeCliente   =   false;
                    toastr.info(res.data.message,'CONSULTA COMPLETADA');
                }
            }else{
                toastr.error(res.data.message,'ERROR AL CONSULTAR CLIENTE EN LA BASE DE DATOS');
            }
            
        
            if(!existeCliente){
                //======= DNI = "6" =========
                if (tipoDocumento === "DNI") {
                    if (numeroDocumento.trim().length === 8) {
                         await consultarAPI(tipoDocumento,numeroDocumento);
                    } else {
                         console.log('el dni no tiene 8 digitos')
                         toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                    }
                
                //======= RUC = "8" =========
                } else if (tipoDocumento === "RUC") {
                     if (numeroDocumento.trim().length === 11) {
                         await consultarAPI(tipoDocumento,numeroDocumento);
                     } else {
                         toastr.error('El RUC debe de contar con 11 dígitos', 'Error');   
                     }
                }
            }
            
        }catch (ex) {
                alert("Error en consultarDocumento" + ex);
        }finally{
            const overlay = document.getElementById('overlay');
            overlay.style.display = 'none'; 
        }
    }

    //======= CONSULTAR API =======
    async function consultarAPI(tipo_documento,nro_documento) {
            try {
                let tipoDoc     =   tipo_documento;
                let documento   =   nro_documento;
                let url         =   null;

                if(tipoDoc === "DNI"){
                    url =   route('getApidni', { dni: nro_documento });
                }
                if(tipoDoc === "RUC"){
                    url =   route('getApiruc', { ruc: nro_documento });
                }

                const { data } = await this.axios.get(url);
               
                console.log(data);
                if(data.success){
                    //===== COLOCANDO NOMBRE EN EL INPUT DEL FORMULARIO ======
                    if(tipoDoc === "DNI"){
                        setCamposDni(data);
                    }
                    if(tipoDoc === "RUC"){
                        setCamposRuc(data);
                    }
                }else{
                    toastr.error(data.message,'Error');
                    if(tipoDoc === "DNI"){
                        clearCamposDni();
                        inputNroDoc.focus();
                    }
                    if(tipoDoc === "RUC"){
                        clearCamposRuc();
                        inputNroDoc.focus();
                    }
                }
              
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

    //======== SET CAMPOS DNI =========
    function setCamposDni(data){
        const data_dni  =   data.data;
        document.querySelector('#nombre').value =   `${data_dni.nombres} ${data_dni.apellido_paterno} ${data_dni.apellido_materno}`;
        document.querySelector('#activo').value =   'ACTIVO';
    }

    //====== SET CAMPOS RUC =====
    async function setCamposRuc(data){
        const data_ruc  =   data.data;
        document.querySelector('#nombre').value     =   data_ruc.nombre_o_razon_social;
        document.querySelector('#direccion').value  =   data_ruc.direccion;
        document.querySelector('#activo').value     =   data_ruc.estado;
        
        if(data_ruc.ubigeo[0] && data_ruc.ubigeo[1] && data_ruc.ubigeo[2]){
            document.querySelector('#departamento').onchange    =   null;
            document.querySelector('#provincia').onchange    =   null;

            $("#departamento").val(data_ruc.ubigeo[0]).trigger("change.select2");
            setZona(getZona(data_ruc.ubigeo[0]));
            const provincias = await getProvincias(data_ruc.ubigeo[0]);
            pintarProvincias(provincias,data_ruc.ubigeo[1]);
            const distritos  = await getDistritos(data_ruc.ubigeo[1]);
            pintarDistritos(distritos,data_ruc.ubigeo[2]);

            document.querySelector('#departamento').onchange = function() {
                setUbicacionDepartamento(this.value, 'first');
            };
            document.querySelector('#provincia').onchange = function() {
                setUbicacionProvincia(this.value, 'first');
            };
            ocultarAnimacion();
            return;
        }
        
        toastr.warning('NO SE ENCONTRÓ UBIGEO DEL DOCUMENTO','UBIGEO NO ENCONTRADO');
    }

    //====== CLEAR CAMPOS DNI =====
    function clearCamposDni(){
        document.querySelector('#nombre').value =  '';
        document.querySelector('#activo').value =  'SIN VERIFICAR';
    }

    function clearCamposRuc(){
        document.querySelector('#nombre').value =  '';
        document.querySelector('#activo').value =  'SIN VERIFICAR';
        document.querySelector('#direccion').value =  '';
    }

    function eventsCliente(){
        formCliente.addEventListener('submit',(e)=>{
            e.preventDefault();     
                 
            guardarCliente();
        })
    }

    //====== GUARDAR CLIENTE ======
    async function guardarCliente() {
            try {
                //======= MOSTRAR OVERLAY =======
                const overlay = document.getElementById('overlay_save');
                overlay.style.display = 'flex'; 

                //======== OBTENEMOS EL SIMBOLO DEL TIPO DOCUMENTO =======
                const formData  =   new FormData(formCliente);
                formData.set('tipo_documento', selectTipoDoc.options[selectTipoDoc.selectedIndex].textContent);

                const res = await axios.post(route('ventas.cliente.storeFast'), formData);

                
                if(res.data.success){
                   
                    updateSelectClientes(res.data.cliente);
                    toastr.success(res.data.message,'OPERACION COMPLETADA');
                    formCliente.reset();
                    $("#modal_cliente").modal("hide");
                
                }else{
                  
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (ex) {
                if('errors' in ex.response.data){
                    //======= PINTAR ERRORES DE VALIDACIÓN =======
                    pintarErrores(ex.response.data.errors);
                     return;
                }
                toastr.error(ex,'ERROR EN LA PETICIÓN REGISTRAR CLIENTE');
            }finally{
                const overlay = document.getElementById('overlay_save');
                overlay.style.display = 'none'; 
            }
    }

    //======== PINTAR ERRORES DE VALIDACIÓN ======
    function pintarErrores(msgErrors){
        for (let key in msgErrors) {
            if (msgErrors.hasOwnProperty(key)) {
                const propiedad =   msgErrors[key];
                const message   =   propiedad[0];
                document.querySelector(`.error_mdl_client_${key}`).textContent    =   message;
            }
        }
    }

    const updateSelectClientes = (clienteNuevo) => {
        var newOption = new Option(`${clienteNuevo.tipo_documento}: ${clienteNuevo.documento} - ${clienteNuevo.nombre}`, clienteNuevo.id, false, false);
        $('#cliente').append(newOption).trigger('change');
        $('#cliente').val(clienteNuevo.id).trigger('change');

    };

    //=========== CONTROLAR EL NRO DE DOCUMENTO ======
    function validarDocumento(input){
        const regex = /[^0-9]/g;
        input.value = input.value.replace(regex, '');

        const tipoDocumento     =   selectTipoDoc.options[selectTipoDoc.selectedIndex].textContent;
        document.querySelector('#btnGuardarCliente').disabled   =   false;

        if(tipoDocumento === 'DNI'){
            if(input.value.trim().length !== 8){
                document.querySelector('#btnGuardarCliente').disabled   =   true;
            }else{
                document.querySelector('#btnGuardarCliente').disabled   =   false;
            }
        }
        if(tipoDocumento === 'RUC'){
            if(input.value.trim().length !== 11){
                document.querySelector('#btnGuardarCliente').disabled   =   true;
            }else{
                document.querySelector('#btnGuardarCliente').disabled   =   false;
            }
        }

       
    }

    

</script>

