<style>

    div.content-envio {
        position: relative;
    }
    
    div.content-envio.sk__loading::after {
        content: '';
        background-color: rgba(255, 255, 255, 0.7);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 3000;
    }
    
    .content-envio.sk__loading>.sk-spinner.sk-spinner-wave {
        margin: 0 auto;
        width: 50px;
        height: 30px;
        text-align: center;
        font-size: 10px;
    }
    
    .content-envio.sk__loading>.sk-spinner {
        display: block;
        position: absolute;
        top: 40%;
        left: 0;
        right: 0;
        z-index: 3500;
    }
    
    .content-envio .sk-spinner.sk-spinner-wave.hide-envio {
        display: none;
    }
    
    </style>
    
    <div class="modal inmodal" id="modal_envio" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 94%;">
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
                <div class="modal-body content-envio">
                    
                    @include('ventas.documentos.cotizacion_a_docventa.forms.form_envio')
                    <div class="sk-spinner sk-spinner-wave hide-envio">
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
    
    
    
    <script>
    
        function eventsModalEnvio(){
            document.querySelector('#frmEnvio').addEventListener('submit',(e)=>{
                e.preventDefault();
                guardarEnvio();
            });
    
        }
    
        //========== SELECCIONAR DEPARTAMENTO Y CARGAR PROVINCIAS ===========
        async function setUbicacionDepartamento(dep_id,provincia_id){
    
            //====== LIMPIAR SEDES =======
            $('#sede_envio').empty();
            $('#sede_envio').val(null).trigger('change');
    
            //====== DESELECCIONAR EMPRESAS ENVIO ======
            $('#empresa_envio').val(null).trigger('change');
    
            if(dep_id){
                console.log('buscando provincias');
                const departamento_id   =   dep_id;
    
                setZona(getZona(departamento_id));
                
                const provincias    =   await getProvincias(departamento_id,provincia_id);
                pintarProvincias(provincias,provincia_id);
            }else{
                //======= SI DEPARTAMENTO ES NULL ========
                //======= LIMPIAR PROVINCIAS ======
                $('#provincia').empty();
                $('#provincia').val(null).trigger('change');
    
                //======== LIMPIAR DISTRITOS ======
                $('#distrito').empty();
                $('#distrito').val(null).trigger('change');
    
            }
    
        }
    
        async function setUbicacionProvincia(prov_id,distrito_id){
            //====== LIMPIAR SEDES =======
            $('#sede_envio').empty();
            $('#sede_envio').val(null).trigger('change');
    
            //====== DESELECCIONAR EMPRESAS ENVIO ======
            $('#empresa_envio').val(null).trigger('change');
    
            if(prov_id){
                const provincia_id      =   prov_id;
                const distritos         =   await getDistritos(provincia_id);
                pintarDistritos(distritos,distrito_id);
            }else{
                //======= SI PROVINCIA ES NULL ========
                //======== LIMPIAR DISTRITOS ======
                $('#distrito').empty();
                $('#distrito').val(null).trigger('change');
            }
        }
    
        function setDistrito(){
            //====== LIMPIAR SEDES =======
            $('#sede_envio').empty();
            $('#sede_envio').val(null).trigger('change');
    
            //====== DESELECCIONAR EMPRESAS ENVIO ======
            $('#empresa_envio').val(null).trigger('change');
        }
    
        function getZona(departamento_id){
            const departamentos     =   @json($departamentos);
            const departamento      =   departamentos.filter((d)=>{
                return d.id==departamento_id;
            })
    
            return departamento[0].zona;
        }
    
        function setZona(zona_nombre){
            document.querySelector('#zona').value   =   zona_nombre;
        }
    
        //====== CONTROL DE ANIMACIÓN =======
        function mostrarAnimacion(){
            document.querySelector('.content-envio').classList.add('sk__loading');
            document.querySelector('.sk-spinner').classList.remove('hide-envio');
        }
        function ocultarAnimacion(){
            document.querySelector('.content-envio').classList.remove('sk__loading');
            document.querySelector('.sk-spinner').classList.add('hide-envio');
        }
    
    
        //======= GET PROVINCIAS ==========
        async function getProvincias(departamento_id) {
            //======= MOSTRAR ANIMACIÓN ======
            mostrarAnimacion();
    
            try {
                const { data } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                    departamento_id
                });
                const { error, message, provincias } = data;
                    // this.Provincias = provincias;
                    // this.loadingProvincias = true;
                return provincias;
            } catch (ex) {
    
            }finally{
                ocultarAnimacion();
            }
        }
    
        //======== pintar provincias =========
        function pintarProvincias(provincias,provincia_id){
            let options =   ``;
            const selectProvincia   =   document.querySelector('#provincia');
    
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
            const selectDistrito    =   document.querySelector('#distrito');
    
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
    
    
        //======= CARGAR TIPOS DE ENVIO =======
        async function getTipoEnvios() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.getTipoEnvios"));
                console.log(data);
                pintarTiposEnvio(data);
                    //console.log(data);
            } catch (ex) {
    
            }
        }
    
        //====== PINTAR TIPOS ENVIO =======
        function pintarTiposEnvio(tipos_envio){
           
            const selectTiposEnvio  =   document.querySelector('#tipo_envio');
    
            data    =   [];
            tipos_envio.forEach((te) => {
                data.push({id:te.id,text:te.descripcion});  
            });
    
            console.log(data);
    
            $("#tipo_envio").select2({
                data: data,
                placeholder: "SELECCIONAR",
                allowClear: true,
                width: '100%',
            })
          
        }
    
    
        //============ CARGAR TIPOS DE PAGO ENVÍO ========
        async function getTiposPagoEnvio() {
            try { 
                const { data }      = await this.axios.get(route("consulta.ajax.getTiposPagoEnvio"));
                    
                if(data.success){
                    
                    pintarTiposPagoEnvio(data.tipos_pago_envio); 
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER TIPOS PAGO DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR');
            }finally{
            }
        }
    
        //========= PINTAR TIPOS PAGO ENVÍO ===========
         function pintarTiposPagoEnvio(tipos_pago_envio){
           
           data    =   [];
           tipos_pago_envio.forEach((tpe,index) => {
               data.push({id:index,text:tpe.descripcion});  
           });
    
           console.log(data);
    
           $("#tipo_pago_envio").select2({
               data: data,
               placeholder: "SELECCIONAR",
               allowClear: true,
               width: '100%',
           })
         
       }
    
       //========== CARGAR EMPRESAS ENVÍO ========
       async function getEmpresasEnvio() {
            mostrarAnimacion();
            //======= SI SE SELECCIONÓ ALGUN TIPO DE ENVÍO =====
            if($("#tipo_envio").select2('data').length > 0){
    
                 //==== OBTENIENDO EL TIPO DE ENVÍO SELECCIONADO ====
                const tipo_envio    =   $("#tipo_envio").select2('data')[0].text;
    
                try { 
                    const { data }      =   await this.axios.get(route("consulta.ajax.getEmpresasEnvio",tipo_envio));
                        
                    if(data.success){
                        // this.empresas_envio  = data.empresas_envio;
                        pintarEmpresasEnvio(data.empresas_envio);
                        console.log(data);
                    }else
                    {
                        toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                    }
                } catch (error) {
                    toastr.error(error,'ERROR EN EL SERVIDOR');
                }finally{
                    ocultarAnimacion();
                }
    
            }else{
                //======= SI TIPO ENVÍO ES NULL ========
                //====== LIMPIAR EMPRESAS ENVÍO =======
                $('#empresa_envio').empty();
                $('#empresa_envio').val(null).trigger('change');
    
                //======= LIMPIAR SEDES ENVÍO ======
                $('#sede_envio').empty();
                $('#sede_envio').val(null).trigger('change');
                ocultarAnimacion();
            }
           
        }
       
        //========== PINTAR EMPRESAS ENVÍO =========
        function pintarEmpresasEnvio(empresas_envio){
    
           //========== REMOVER ITEMS SELECT2 ======
            $('#empresa_envio').empty();
            
           
           data    =   [];
           empresas_envio.forEach((ee) => {
               data.push({id:ee.id,text:ee.empresa});  
           });
    
           console.log(data);
    
           $("#empresa_envio").select2({
               data: data,
               placeholder: "SELECCIONAR",
               allowClear: true,
               width: '100%',
           })
    
           //======= ESTABLECER SELECCION EN NULL =========
           $('#empresa_envio').val(null).trigger('change');
    
        }
    
    
       //=========== OBTENER SEDES ENVÍO =========
       async function getSedesEnvio() {
            mostrarAnimacion();
            if($("#empresa_envio").select2('data').length > 0){
                try { 
                    const empresa_envio_id  =    $("#empresa_envio").select2('data')[0].id;
                    let ubigeo            =    [];
    
                    const departamento      =   {id:$("#departamento").val(),nombre:$("#departamento").find('option:selected').text()};
                    const provincia         =   {id:$("#provincia").val(),text:$("#provincia").find('option:selected').text()};
                    const distrito          =   {id:$("#distrito").val(),text:$("#distrito").find('option:selected').text()};
    
                    ubigeo.push(departamento);
                    ubigeo.push(provincia);
                    ubigeo.push(distrito);
                   
                    ubigeo                  =   JSON.stringify(ubigeo);
    
                    const { data }      =   await axios.get(route("consulta.ajax.getSedesEnvio",{empresa_envio_id, ubigeo }));
                        
                    if(data.success){
                        pintarSedesEnvio(data.sedes_envio);
                        console.log(data);
                    }else
                    {
                        toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER SEDES DE ENVÍO');
                    }
                } catch (error) {
                    toastr.error(error,'ERROR EN EL SERVIDOR');
                }finally{
                    ocultarAnimacion();
                }
            }else{
                //======= SI EMPRESA ENVÍO ES NULL ========
                //======= LIMPIAR SEDES ENVÍO ======
                $('#sede_envio').empty();
                $('#sede_envio').val(null).trigger('change');
                ocultarAnimacion();
            }
        }
    
    
        //========== PINTAR SEDES ENVÍO =========
        function pintarSedesEnvio(sedes_envio){
    
            //========== REMOVER ITEMS SELECT2 ======
            $('#sede_envio').empty();
           
           data    =   [];
           sedes_envio.forEach((se) => {
               data.push({id:se.id,text:se.direccion});  
           });
    
           console.log(data);
    
           $("#sede_envio").select2({
               data: data,
               placeholder: "SELECCIONAR",
               allowClear: true,
               width: '100%',
           })
    
            //======= ESTABLECER SELECCION EN NULL =========
            $('#sede_envio').val(null).trigger('change');
         
        }
    
    
        //========= CHECK ENTREGA DOMICILIO =====
        function entregaDomicilio(value_check){
            console.log(value_check);
            document.querySelector('#direccion_entrega').readOnly   =   !value_check;
            document.querySelector('#direccion_entrega').required   =   value_check;
    
            value_check?document.querySelector('#lbl_direccion_entrega').classList.add('required'):document.querySelector('#lbl_direccion_entrega').classList.remove('required');
    
        }
    
    
        //======== CARGAR ORIGENES VENTA ========
        async function getOrigenesVentas() {
            try { 
                const { data }      = await this.axios.get(route("consulta.ajax.getOrigenesVentas"));
                    
                if(data.success){
                    // this.origenes_ventas    =   data.origenes_ventas;
                    console.log(data);
                    pintarOrigenesVenta(data.origenes_ventas);
                       
                }else
                {
                    toastr.error(`${data.message} - ${data.exception}`,'ERROR AL OBTENER ORÍGENES DE VENTA');
                }
            } catch (error) {
                    toastr.error(error,'ERROR EN EL SERVIDOR');
            }finally{
            }
        }
    
        //========== PINTAR ORIGENES VENTA =======
        function pintarOrigenesVenta(origenes_ventas){
            data    =   [];
            origenes_ventas.forEach((ov,index) => {
                data.push({id:index,text:ov.descripcion});  
            });
    
            console.log(data);
    
            $("#origen_venta").select2({
                data: data,
                placeholder: "SELECCIONAR",
                allowClear: true,
                width: '100%',
            })
        }
    
    
        //============ CONSULTAR DOCUMENTO ========
        async function consultarDocumento() {
                try {
                    mostrarAnimacion();
    
                    const dni_destinatario  =   document.querySelector('#nro_doc_destinatario').value;
                    if (dni_destinatario.length === 8) {
                        await consultarAPI(dni_destinatario);
                        ocultarAnimacion();
                    } else {
                            this.loading = false;
                        toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                    }
                    
                } catch (ex) {
                    alert("Error en consultarDocumento" + ex);
                }
        }
    
        async function consultarAPI(dni_destinatario) {
                try {
                    let documento   = dni_destinatario;
                    let url =  route('getApidni', { dni: documento });
    
                    const { data } = await this.axios.get(url);
                    
                    CamposDNI(data);
                    
    
                } catch (ex) {
                    this.loading = false;
                    alert("Error en consultarAPI" + ex);
                }
        }
    
        function CamposDNI(results) {
                const { success, data } = results;
                if (success) {
                    document.querySelector('#nombres_destinatario').value   =   data.nombres +' '+data.apellido_paterno + ' '+data.apellido_materno;   
                } else {
    
                }
        }
    
        async function getTipoDocumento() {
            try {
                const { data }      = await this.axios.get(route("consulta.ajax.getTipoDocumentos"));
    
                //======== SELECCIONAMOS DNI Y CARNET EXTRANJERÍA ======
                    
                const tipoDocumentosFilter  =   data.filter((td)=>{
                    return td.id == 6 || td.id == 7;
                })
    
                pintarTiposDocumento(tipoDocumentosFilter);
    
            } catch (ex) {
    
            }
        }
    
        function pintarTiposDocumento(tipos_documento){
            data    =   [];
            tipos_documento.forEach((td,index) => {
                data.push({id:index,text:td.simbolo});  
            });
    
            $("#tipo_doc_destinatario").select2({
                data: data,
                placeholder: "SELECCIONAR",
                allowClear: true,
                width: '100%',
            })
    
            //=========== CONFIGURACIÓN VISTA ========
            //const destinatario_tipo_doc  =   $("#tipo_doc_destinatario").find('option:selected').text();
            // if(destinatario_tipo_doc === "CARNET EXT."){
            //     document.querySelector('#btn-consultar-dni').style.display  =   'none';
            // }
            // document.querySelector('.span-tipo-doc-dest').textContent     =   destinatario_tipo_doc;
        }
    
        function cambiarTipoDocDest(tipo_doc_dest){
            document.querySelector('#nro_doc_destinatario').value   =   '';
            
            //======== 0:DNI  1:CARNET EXT. ======
            if(tipo_doc_dest.length == 0 || tipo_doc_dest == 1){
                document.querySelector('#btn-consultar-dni').style.display  =   'none';
                document.querySelector('#nro_doc_destinatario').maxLength   =   20;
            }
            if(tipo_doc_dest == 0 && tipo_doc_dest.length === 1){
                document.querySelector('#btn-consultar-dni').style.display  =   'block';
                document.querySelector('#nro_doc_destinatario').maxLength   =   8;
            }
    
        
    
            const destinatario_tipo_doc  =   $("#tipo_doc_destinatario").find('option:selected').text();
            document.querySelector('.span-tipo-doc-dest').textContent     =   destinatario_tipo_doc;
    
        }
    
        function guardarEnvio(){
            toastr.clear();
            if($("#departamento").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UN DEPARTAMENTO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#provincia").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UNA PROVINCIA',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#distrito").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UN DISTRITO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#tipo_envio").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UN TIPO DE ENVÍO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#tipo_pago_envio").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UN TIPO DE PAGO PARA EL ENVÍO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#empresa_envio").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UNA EMPRESA DE ENVÍO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#sede_envio").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UNA SEDE DE ENVÍO',"CAMPO OBLIGATORIO");
                return;
            }
    
            if($("#sede_envio").find('option:selected').text() === ""){
                toastr.error('SELECCIONE UNA SEDE DE ENVÍO',"CAMPO OBLIGATORIO");
                return;
            }
    
            const tipo_doc_destinatario =   $("#tipo_doc_destinatario").find('option:selected').text();
            if(tipo_doc_destinatario === "DNI"){
                if(document.querySelector('#nro_doc_destinatario').value.length !== 8){
                    toastr.error('INGRESE UN NRO DOCUMENTO VÁLIDO PARA EL DESTINATARIO',"ERROR");
                    return;
                }
            }
            if(tipo_doc_destinatario.length === 0){
                toastr.error('SELECCIONE UN TIPO DE DOCUMENTO');
                return;
            }
           
    
            if(document.querySelector('#nombres_destinatario').value == 0){
                toastr.error('DEBE INGRESAR EL NOMBRE DEL DESTINATARIO',"ERROR");
                return;
            }
             
    
            if($("#origen_venta").find('option:selected').text() === ""){
                toastr.warning('ORIGEN VENTA POR DEFECTO WATHSAPP',"ORIGEN VENTA");
                return;
            }
    
            //====== GUARDANDO DATA DE ENVIO ======
            const departamento  =   {nombre:$("#departamento").find('option:selected').text()};
            const provincia     =   {text:$("#provincia").find('option:selected').text()};
            const distrito      =   {text:$("#distrito").find('option:selected').text()};
            const empresa_envio =   {id:$("#empresa_envio").val(),empresa:$("#empresa_envio").find('option:selected').text()};
            const sede_envio    =   {id:$("#sede_envio").val(),direccion:$("#sede_envio").find('option:selected').text()};
            const tipo_envio    =   {descripcion:$("#tipo_envio").find('option:selected').text()};
            const destinatario  =   {nro_documento:document.querySelector('#nro_doc_destinatario').value,
                                    nombres:document.querySelector('#nombres_destinatario').value,
                                    tipo_documento:$("#tipo_doc_destinatario").find('option:selected').text()};
            const tipo_pago_envio   =   {descripcion:$("#tipo_pago_envio").find('option:selected').text()};
            const entrega_domicilio =   document.querySelector('#check_entrega_domicilio').checked;
            const direccion_entrega =   document.querySelector('#direccion_entrega').value;
            const fecha_envio_propuesta =   document.querySelector('#fecha_envio').value; 
            const origen_venta          =   {descripcion:$("#origen_venta").find('option:selected').text()==''?'WATHSAPP':$("#origen_venta").find('option:selected').text()};
            const obs_rotulo         =   document.querySelector('#obs-rotulo').value;
            const obs_despacho       =   document.querySelector('#obs-despacho').value;
    
            const form_envio    =   {departamento,provincia,distrito,empresa_envio,sede_envio,tipo_envio,
                                    destinatario,tipo_pago_envio,entrega_domicilio,direccion_entrega,fecha_envio_propuesta,
                                    origen_venta,obs_rotulo,obs_despacho};
            
    
            console.log(form_envio);
            document.querySelector('#data_envio').value =   JSON.stringify(form_envio);
            toastr.success('DATOS DE ENVÍO GUARDADOS','OPERACIÓN COMPLETADA');
            $("#modal_envio").modal("hide");
        }
    
        function borrarEnvio(){
            document.querySelector('#data_envio').value =   JSON.stringify({});
            toastr.error('DATOS DE ENVÍO BORRADOS','ENVÍO ELIMINADO');
            $("#modal_envio").modal("hide");
        }
    
    
    </script>
    