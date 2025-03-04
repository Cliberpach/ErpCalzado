
<div class="overlay_modal_cliente">
    <span class="loader_modal_cliente"></span>
</div>

  <!-- Modal -->
  <div class="modal fade" id="modal_cliente"  aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 60%;">
      <div class="modal-content">
        <div class="modal-header d-flex flex-column align-items-center justify-content-center position-relative">
            <i class="fas fa-user-tag mb-2" style="font-size: 40px;"></i>
            <h5 class="modal-title" id="exampleModalLabel">REGISTRAR CLIENTE</h5>
            <button type="button" class="close position-absolute" style="right: 15px; top: 15px; font-size: 35px;" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="frmCliente" class="formulario">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="tipo_documento">TIPO DE DOCUMENTO</label>
                                    <select  required class="select2_modal_cliente" name="tipo_documento" id="tipo_documento" onchange="controlNroDoc(this)">
                                        @foreach ($tipos_documento as $tipo_documento)
                                            <option value="{{$tipo_documento->id}}">{{$tipo_documento->simbolo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_tipo_documento"></span>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="documento">NRO. DOCUMENTO</label>
                                    <div class="input-group">
                                        <input type="text" id="documento" name="documento" class="form-control"
                                             required maxlength="8" oninput="validarDocumento(this)">
                                        <button id="btn_consultar_doc" onclick="consultarDocumento()" type="button" style="color:white" class="btn btn-primary">
                                            <i class="fa fa-search" ></i>
                                            <span id="entidad"> </span>
                                        </button>
                                    </div>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_documento"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente"  for="tipo_cliente">TIPO CLIENTE</label>
                                    <select class="select2_modal_cliente" name="tipo_cliente_id" id="tipo_cliente_id" >
                                        @foreach ($tipo_clientes as $tipo_cliente)
                                            <option value="{{$tipo_cliente->id}}">{{$tipo_cliente->simbolo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_tipo_cliente_id"></span>
                            </div>
                            <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="lbl_mdl_cliente" for="activo">Estado</label>
                                    <input type="text" id="activo" name="activo" value="SIN VERIFICAR"
                                        class="form-control text-center" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="required lbl_mdl_cliente" id="lblNombre" for="nombre">NOMBRE</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control"
                                        maxlength="191" v-model="formCliente.nombre" required>

                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-group m-0">
                                    <label for="direccion" class="required lbl_mdl_cliente">DIRECCIÓN</label>
                                    <input type="text" id="direccion" name="direccion" class="form-control"
                                        maxlength="191" onkeyup="return mayus(this)" required>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_direccion"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="departamento">DEPARTAMENTO</label>
                                    <select required class="select2_modal_cliente" name="departamento" id="departamento" onchange="setUbicacionDepartamento(this.value,'first')">
                                        @foreach ($departamentos as $departamento)
                                            <option @if ($departamento->id == 13) selected @endif value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_departamento"></span>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="provincia">PROVINCIA</label>
                                    <select required class="select2_modal_cliente" name="provincia" id="provincia" onchange="setUbicacionProvincia(this.value,'first')" >
                                       
                                    </select>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_provincia"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="distrito">DISTRITO</label>
                                    <select required class="select2_modal_cliente" name="distrito" id="distrito">
                                       
                                    </select>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_distrito"></span>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label class="required lbl_mdl_cliente" for="zona">Zona</label>
                                    <input type="text" id="zona" name="zona" 
                                        class=" text-center form-control" readonly>
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_zona"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="telefono_movil" class="required lbl_mdl_cliente">TELÉFONO MÓVIL</label>
                                    <input type="text" id="telefono_movil" name="telefono_movil"
                                        class="form-control" onkeypress="return isNroPhone(event)" maxlength="9"
                                        required v-model="formCliente.telefono_movil">

                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-group m-0">
                                    <label for="telefono_fijo" class="lbl_mdl_cliente">TELÉFONO FIJO</label>
                                    <input type="text" id="telefono_fijo" name="telefono_fijo"
                                        class="form-control" onkeypress="return isNroPhone(event)" maxlength="9">
                                </div>
                                <span style="color:rgb(251, 135, 135);" class="error_mdl_client_telefono_movil"></span>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="correo_electronico" class="lbl_mdl_cliente">CORREO</label>
                                    <input type="text" id="correo_electronico" name="correo_electronico"
                                        class="form-control" v-model="formCliente.correo_electronico">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="col-md-6 text-left">
                <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                    campos
                    marcados con asterisco (*) son obligatorios.</small>
            </div>
            <div class="col-md-6 text-right">
                <button id="btnGuardarCliente" type="submit" class="btn btn-primary btn-sm" form="frmCliente" style="color:white;"><i
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

    function openModalCliente(){
        $("#modal_cliente").modal("show");
    }

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
        
        mostrarAnimacionModalCliente();
        const provincias    =   await getProvincias(departamento_id,provincia_id);
        pintarProvincias(provincias,provincia_id);
        
       
    }

    async function setUbicacionProvincia(prov_id,distrito_id){
        const provincia_id      =   prov_id;
        
        const distritos         =   await getDistritos(provincia_id);
        pintarDistritos(distritos,distrito_id);
        ocultarAnimacionModalCliente();
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
                console.log(data);
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
                mostrarAnimacionModalCliente();
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
            mostrarAnimacionModalCliente();

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
            ocultarAnimacionModalCliente();
        }
    }

    //======= CONSULTAR API =======
    async function consultarAPI(tipo_documento,nro_documento) {
            try {
                mostrarAnimacionModalCliente();
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
            }finally{
                ocultarAnimacionModalCliente();
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
            

            ocultarAnimacionModalCliente();
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
                mostrarAnimacionModalCliente();

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
                console.log(ex);
                if('errors' in ex.response.data){
                    //======= PINTAR ERRORES DE VALIDACIÓN =======
                    pintarErrores(ex.response.data.errors);
                     return;
                }
                toastr.error(ex,'ERROR EN LA PETICIÓN REGISTRAR CLIENTE');
            }finally{
                ocultarAnimacionModalCliente();
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

    function mostrarAnimacionModalCliente(){
      
        document.querySelector('.overlay_modal_cliente').style.visibility   =   'visible';
    }

    function ocultarAnimacionModalCliente(){
        
        document.querySelector('.overlay_modal_cliente').style.visibility   =   'hidden';
    }

    

</script>

