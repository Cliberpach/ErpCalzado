<div class="modal inmodal" id="modal_proveedor" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-truck-moving modal-icon"></i>                
                <h4 class="modal-title">NUEVO PROVEEDOR</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')
                <form id="frmProveedor" class="formulario">
                    <div class="row">
                        <div class="col-12 b-r">
                            <h4 class=""><b>Proveedor</b></h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Registrar datos del nuevo Proveedor:</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="required">Documento: </label>
                                    <div class="row mt-1" align="center">
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input checked class="form-check-input" type="radio" name="tipo_documento"
                                                    id="tipo_documento_ruc" value="RUC"
                                                    @if(old('tipo_documento')=="RUC" ) {{'checked'}}@endif>
                                                <label class="form-check-label" for="tipo_documento_ruc">RUC</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tipo_documento"
                                                    id="tipo_documento_dni" value="DNI"
                                                    @if(old('tipo_documento')=="DNI" ) {{'checked'}}@endif>
                                                <label class="form-check-label" for="tipo_documento_dni">DNI</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-select-tipo-persona">
                                        <label class="required">Tipo: </label>
                                        <select
                                                class="select2_form form-control {{ $errors->has('tipo_persona') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%"
                                                value="{{old('tipo_persona')}}" name="tipo_persona" id="tipo_persona"
                                                required>
                                                <option></option>
                                                @foreach ($tipos as $tipo)
                                                <option value="{{$tipo->descripcion}}" @if(old('tipo_persona')==$tipo->
                                                    descripcion ) {{'selected'}} @endif >{{$tipo->descripcion}}</option>
                                                @endforeach
                                        </select>
                                        <div class="invalid-feedback"><b><span id="error-tipo"></span></b></div>  
                                        <input type="text" readonly id="tipo_persona_dni" class="form-control d-none"
                                        name="tipo_persona_dni" value="PERSONA CON DNI">
                                        @if ($errors->has('tipo_persona'))
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('tipo_persona') }}</strong>
                                            </span>
                                        @endif      
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6" id="ruc_requerido" style="position:relative;">
                                    <label class="required">Ruc: </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control {{ $errors->has('ruc') ? ' is-invalid' : '' }}"  name="ruc" id="ruc" maxlength="11" value="{{old('ruc')}}"> 
                                        <span class="input-group-append"><a style="color:white" onclick="consultarRuc()" class="btn btn-primary"><i class="fa fa-search"></i> Sunat</a></span>
                                        <strong id="error_ruc" style="color:rgb(234, 44, 44);position:absolute;top:35px;"></strong>
                                        @if ($errors->has('ruc'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('ruc') }}</strong>
                                        </span>
                                        @endif
                                        <div class="invalid-feedback"><b><span id="error-ruc"></span></b></div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="dni_requerido" style="display:none;position:relative;">
                                    <label class="required">Dni: </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control {{ $errors->has('dni') ? ' is-invalid' : '' }}"  name="dni" id="dni" maxlength="8" value="{{old('dni')}}"> 
                                        <span class="input-group-append"><a style="color:white" onclick="consultarDni()" class="btn btn-primary"><i class="fa fa-search"></i> Reniec</a></span>
                                        <strong id="error_dni" style="color:rgb(234, 44, 44);position:absolute;top:35px;"></strong>
                                        @if ($errors->has('dni'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('dni') }}</strong>
                                        </span>
                                        @endif

                                        <div class="invalid-feedback"><b><span id="error-dni"></span></b></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="">Estado: </label>
                                    <input type="text" id="estado"
                                        class="form-control text-center {{ $errors->has('estado') ? ' is-invalid' : '' }}"
                                        name="estado" value="{{old('estado',"SIN VERIFICAR")}}"
                                        onkeyup="return mayus(this)" readonly>
                                    @if ($errors->has('estado'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mt-4">
                                <div class="col-md-12">
                                    <label class="required">Descripción: </label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
                                        name="descripcion" value="{{ old('descripcion')}}" id="descripcion"
                                        onkeyup="return mayus(this)" required>

                                    @if ($errors->has('descripcion'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                    @endif
                                    <div class="invalid-feedback"><b><span id="error-descripcion"></span></b></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label class="required">Dirección:</label>
                                    <textarea type="text" id="direccion" name="direccion"
                                        class="form-control {{ $errors->has('direccion') ? ' is-invalid' : '' }}"
                                        value="{{old('direccion')}}"  onkeyup="return mayus(this)"
                                        required>{{old('direccion')}}</textarea>
                                    @if ($errors->has('direccion'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                    @endif
                                    <div class="invalid-feedback"><b><span id="error-direccion"></span></b></div>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="required">Zona: </label>
                                    <select
                                        class="select2_form form-control {{ $errors->has('zona') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('zona')}}"
                                        name="zona" id="zona" required>
                                        <option></option>
                                        @foreach ($zonas as $zona)
                                        <option value="{{$zona->descripcion}}" @if(old('zona')==$zona->descripcion )
                                            {{'selected'}} @endif >{{$zona->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('zona'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('zona') }}</strong>
                                    </span>
                                    @endif
                                    <div class="invalid-feedback"><b><span id="error-zona"></span></b></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required">Correo:</label>
                                    <input type="email" placeholder=""
                                        class="form-control {{ $errors->has('correo') ? ' is-invalid' : '' }}"
                                        name="correo" id="correo"  onkeyup="return mayus(this)"
                                        value="{{old('correo')}}" required>
                                    @if ($errors->has('correo'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('correo') }}</strong>
                                    </span>
                                    @endif
                                    <div class="invalid-feedback"><b><span id="error-correo"></span></b></div>

                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label class="required">Teléfono:</label>
                                    <input type="text" placeholder=""
                                        class="form-control {{ $errors->has('telefono') ? ' is-invalid' : '' }}" required
                                        name="telefono" id="telefono"  onkeyup="return mayus(this)"
                                        value="{{old('telefono')}}">
                                    @if ($errors->has('telefono'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('telefono') }}</strong>
                                    </span>
                                    @endif
                                    <div class="invalid-feedback"><b><span id="error-telefono"></span></b></div>
                                </div>
                                <div class="col-md-6">
                                    <label>Celular:</label>
                                    <input type="text" placeholder=""
                                        class="form-control {{ $errors->has('celular') ? ' is-invalid' : '' }}"
                                        name="celular" id="celular"  onkeyup="return mayus(this)"
                                        value="{{old('celular')}}">
                                    @if ($errors->has('celular'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('celular') }}</strong>
                                    </span>
                                    @endif
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
                    <button type="submit" class="btn btn-primary btn-sm" form="frmProveedor" style="color:white;"><i
                            class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const checkTipoDocRuc   =   document.querySelector('#tipo_documento_ruc');
    const checkTipoDocDni   =   document.querySelector('#tipo_documento_dni');


    function eventsProveedor(){
        checkTipoDocRuc.addEventListener('change',(e)=>{
            modoTipoDocRuc(e);
        })
        checkTipoDocDni.addEventListener('change',(e)=>{
            modoTipoDocDni(e);
        })

        //====== VALIDAR INPUT DNI =======
        document.querySelector('#dni').addEventListener('input',(e)=>{
            const contenido =   e.target.value;
            e.target.value  =   contenido.replace(/[^0-9]/g, "");
        })

        //====== VALIDAR INPUT RUC =======
        document.querySelector('#ruc').addEventListener('input',(e)=>{
            const contenido =   e.target.value;
            e.target.value  =   contenido.replace(/[^0-9]/g, "");
        })

        //======== ENVIAR FORM PROVEEDOR ======
        document.querySelector('#frmProveedor').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarProveedor();
        })
    }

    function modoTipoDocRuc(e){
        if(e.target.checked){
            //===== MOSTRAR SELECT DE TIPO DE PERSONA ======
            $("#tipo_persona").select2().next().show();
            $("#tipo_persona").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
            //===== OCULTAR INPUT PERSONA CON DNI =====
            document.querySelector('#tipo_persona_dni').classList.add('d-none');
            //==== OCULTAR BUSQUEDA RENIEC =====
            document.querySelector('#dni_requerido').style.display  =   "none";
            //===== MOSTRAR BÚSQUEDA SUNAT =====
            document.querySelector('#ruc_requerido').style.display  =   "block";
            //====== SELECT TIPO PERSONA OBLIGATORIO ======
            document.querySelector('#tipo_persona').required    =   true;
            //========= LIMPIAR INPUT DNI =========
            document.querySelector('#dni').value  =   "";
        }
    }

    function modoTipoDocDni(e){
        if(e.target.checked){
            //===== QUITAR SELECT DE TIPO DE PERSONA ======
            $("#tipo_persona").select2().next().hide();            
            //===== MOSTRAR INPUT PERSONA CON DNI =====
            document.querySelector('#tipo_persona_dni').classList.remove('d-none');
            //==== MOSTRAR BUSQUEDA RENIEC =====
            document.querySelector('#dni_requerido').style.display  =   "block";
            //===== OCULTAR BÚSQUEDA SUNAT =====
            document.querySelector('#ruc_requerido').style.display  =   "none";
            //====== SELECT TIPO PERSONA NO OBLIGATORIO ======
            document.querySelector('#tipo_persona').required    =   false;
            //====== LIMPIAR INPUT RUC ========
            document.querySelector('#ruc').value  =   "";
        }
    }

    //======= CONSULTAR DNI =========
    function consultarDni() {
        const dni   =   document.querySelector('#dni').value.toString().trim();
        //===== LIMPIAR ERRORES ANTERIORES ======
        document.querySelector('#error_dni').textContent  =   "";

        const validacion    =   validarNroDni(dni);
        if(!validacion){
            return;
        }

        //===== CONSULTAR EL NRO DE DNI ===
        apiDni(dni);
    }

    //========= VALIDAR NRO DNI =======
    function validarNroDni(dni){
        if(dni.trim().length == 0){
            document.querySelector('#dni').focus();
            toastr.error('INGRESE UN NÚMERO DE DNI','ERROR');
            return false;
        }
        if(dni.trim().length < 8){
            document.querySelector('#dni').focus();
            toastr.error('EL DNI DEBE TENER 8 DÍGITOS','ERROR');
            return false;
        }
        return true;
    }

    //========== CONSULTA API DNI ==========
    async function apiDni(dni){
        //====== OVERLAY DE BÚSQUEDA ======
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'flex';
        //====== PREPARAR RUTA ======
        const url   = "{{ route('getApidni', ['dni' => ':dni']) }}".replace(':dni', dni);
        //======== MANEJO DE EXCEPCIONES =======
        try {
            const res   = await axios.get(url);
            console.log(res);
            if(res.data.success){
                const nombre_completo   =   res.data.data.nombre_completo;
                setDataDni(nombre_completo);
            }
        } catch (error) {
            console.log(error);
        }finally{
            overlay.style.display = 'none';        
        }
    }

    //======== COLOCAR LA DATA DEL DNI =======
    function setDataDni(nombre_completo){
        //====== COLOCANDO EL NOMBRE COMPLETO ========
        document.querySelector('#descripcion').value    =   nombre_completo;
        document.querySelector('#estado').value         =   "ACTIVO";
    }

    //===== CONSULTAR RUC ======
    function consultarRuc(){
        const ruc   =   document.querySelector('#ruc').value.toString().trim();
        document.querySelector('#error_ruc').textContent  =   "";
        
        //======== VALIDACIÓN ========
        const validacion    =   validarNroRuc(ruc);
        if(!validacion){
            return;
        }
        console.log(ruc);
        apiRuc(ruc);
    }

    //========= VALIDAR NRO RUC =======
    function validarNroRuc(ruc){
        if(ruc.trim().length == 0){
            document.querySelector('#ruc').focus();
            toastr.error('INGRESE UN NÚMERO DE RUC','ERROR');
            return false;
        }
        if(ruc.trim().length < 11){
            document.querySelector('#ruc').focus();
            toastr.error('EL RUC DEBE TENER 11 DÍGITOS','ERROR');
            return false;
        }
        return true;
    }

     //========== CONSULTA API RUC ==========
     async function apiRuc(ruc){
        //====== OVERLAY DE BÚSQUEDA ======
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'flex';
        //====== PREPARAR RUTA ======
        var url = '{{ route("getApiruc", ":ruc")}}';
        url = url.replace(':ruc', ruc);        
        //======== MANEJO DE EXCEPCIONES =======
        try {
            const res   = await axios.get(url);
            console.log(res);
            if(res.data.success){
                setDataRuc(res.data.data);
            }
        } catch (error) {
            console.log(error);
        }finally{
            overlay.style.display = 'none';        
        }
    }

    //======== COLOCAR LA DATA DEL RUC =======
    function setDataRuc(data){
        //====== COLOCANDO EL NOMBRE COMPLETO ========
        document.querySelector('#descripcion').value    =   data.nombre_o_razon_social;
        document.querySelector('#estado').value         =   data.estado;
        document.querySelector('#direccion').value      =   data.direccion;


        const departamento_id                           =   data.ubigeo[0];
        const zona                                      =   getZona(departamento_id);

        //======= POSICIONAR ZONA ======
        if(zona){
            $('#zona').val(zona).trigger('change.select2');
        }
    }

    //======= OBTENER ZONA =====
    function getZona(departamento_id){
        const departamentos     =   @json($departamentos);
        const departamento      =   departamentos.filter((d)=>{
            return d.id==departamento_id;
        })

        return departamento[0].zona;
    }

    //========= REGISTRAR PROVEEDOR ======
    async function registrarProveedor() {
        //===== POSICIONANDO OVERLAY ====
        const overlay = document.getElementById('overlay_save');
        overlay.style.display = 'flex';

        //===== PREPARANDO FORMULARIO ========
        const formProveedor     = document.getElementById('frmProveedor');
        const formData          = new FormData(formProveedor);
        formData.append('estado_transporte', 'SIN VERIFICAR');
        formData.append('contacto',document.querySelector('#descripcion').value.toString().trim());
        formData.append('correo_contacto',document.querySelector('#correo').value.toString().trim());
        formData.append('telefono_contacto',document.querySelector('#telefono').value.toString().trim());
        formData.append('type_store','FAST');


        //======= PREPARANDO RUTA =======
        const url = "{{ route('compras.proveedor.store') }}";

        //====== PETICION POST =====
        try {
            const res   =  await axios.post(url,formData);
            console.log(res);
            if(res.data.success){
                //==== EMITIR ALERTA =====
                toastr.success(res.data.message,"PROVEEDOR REGISTRADO");
                //==== PINTAR NUEVO PROVEEDOR =====
                addProveedorSelect(res.data.data);
                //==== LIMPIAR FORMULARIO ====
                document.querySelector('#frmProveedor').reset();
                //===== CERRAR MODAL =====
                $("#modal_proveedor").modal("hide");    
                //====== LIMPIAR SELECT2 ======
                $("#zona").val(null).trigger("change");
                $("#tipo_persona").val(null).trigger("change");
            }
        } catch (error) {
            console.log(error)
            //=== PINTAR ERRORES =====
            if(error.response.hasOwnProperty('data')){
                if(error.response.data.hasOwnProperty('errors')){
                    const errores   =   error.response.data.errors;
                    pintarErrores(errores);   
                }
            }
        }finally{
            overlay.style.display = 'none';
        }

        // for (var entry of formData.entries()) {
        //     console.log(entry[0] + ': ' + entry[1]);
        // }
    }

    //===== PINTAR ERRORES ======
    function pintarErrores(errores){
        // Iterar sobre las claves del objeto errores
        for (var key in errores) {
            // Obtener el mensaje de error para esta clave
            var mensajeError = errores[key][0];
            // Buscar el elemento HTML correspondiente por su id
            var elemento = document.getElementById(key);
            // Buscar el elemento donde mostrar el error por su id
            var elementoError = document.getElementById('error_' + key); // Convertir la primera letra en mayúscula
            // Mostrar el mensaje de error debajo del elemento correspondiente
            if (elemento && elementoError) {
                elementoError.textContent = mensajeError;
            }
        }
    }

    function addProveedorSelect(proveedor_nuevo){
        var nuevaOpcion = new Option(`${proveedor_nuevo.nroDoc} - ${proveedor_nuevo.descripcion}`, proveedor_nuevo.id);

        $("#proveedor_id").append(nuevaOpcion);

        $("#proveedor_id").trigger("change");
    }


</script>