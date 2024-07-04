@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('pedidos-active', 'active')
@include('ventas.documentos.modal-envio')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Atender Pedido</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    
    <div class="row">
        <div class="col-12">
            @if(Session::has('pedido_facturado_atender'))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {{ Session::get('pedido_facturado_atender') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('components.overlay_esfera_1')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            <form  method="POST" action="{{route('ventas.pedidos.generarDocumentoVenta')}}"
                                id="form-pedido-doc-venta">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h4><b>Datos Generales</b></h4>
                                    </div>
                                    <div class="col-12 d-none">
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="required">Fecha de Documento</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="fecha_documento" name="fecha_documento_campo"
                                                    class="form-control input-required {{ $errors->has('fecha_documento') ? ' is-invalid' : '' }}"
                                                    value="{{ old('fecha_documento', date('Y-m-d')) }}">
                                                        autocomplete="off" required readonly>
                                                    @if ($errors->has('fecha_documento'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('fecha_documento') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="___class_+?31___">Moneda</label>
                                                <select id="moneda" name="moneda"
                                                    class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                    disabled>
                                                    <option selected>SOLES</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="required">Empresa</label>
                                                <select id="empresa" name="empresa"
                                                    class="select2_form form-control {{ $errors->has('empresa') ? ' is-invalid' : '' }}"
                                                    required>
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{ $empresa->id }}"
                                                            {{ old('empresa') == $empresa->id || $empresa->id === 1 ? 'selected' : '' }}>
                                                            {{ $empresa->razon_social }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('empresa'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('empresa') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Atención</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_atencion" name="fecha_atencion_campo"
                                                            class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_atencion', date('Y-m-d')) }}"
                                                            autocomplete="off" required readonly>
                                                        @if ($errors->has('fecha_atencion'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="">Vendedor</label>
                                                    <select id="vendedor" name="vendedor" class="select2_form form-control" disabled>
                                                        <option></option>
                                                        @foreach (vendedores() as $vendedor)
                                                            <option value="{{ $vendedor->id }}" {{ $vendedor->id === $vendedor_actual ? 'selected' : '' }}>
                                                                {{ $vendedor->persona->apellido_paterno . ' ' . $vendedor->persona->apellido_materno . ' ' . $vendedor->persona->nombres }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Tipo de Comprobante: </label>
                                                    <select onchange="validarTipoComprobante(this.value)" name="tipo_venta" id="tipo_venta" class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
                                                        required>
                                                        @foreach ($tipoVentas as $tipo_venta)
                                                            <option value="{{ $tipo_venta['id'] }}"
                                                                {{ old('tipo_venta') == $tipo_venta['id'] ? 'selected' : '' }}
                                                                {{ 129 == $tipo_venta['id'] ? 'selected' : '' }}>
                                                                {{ $tipo_venta['nombre'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('tipo_venta'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('tipo_venta') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6" style="border-left: 2px solid #ccc;">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Condición</label>
                                                    <select id="condicion_id" name="condicion_id" onchange="cambiarCondicion(this.value)"
                                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($condiciones as $condicion)
                                                            <option value="{{ $condicion->id }}"
                                                                {{ old('condicion_id') == $condicion->id ? 'selected' : '' }}
                                                                {{ $pedido->condicion_id == $condicion->id ? 'selected' : '' }}>
                                                                {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('condicion_id'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('condicion_id') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Vencimiento</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento_campo"
                                                            class="form-control {{ $errors->has('fecha_vencimiento') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_vencimiento', date('Y-m-d')) }}"
                                                            autocomplete="off" required readonly>
                                                        @if ($errors->has('fecha_vencimiento'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fecha_vencimiento') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        {{-- <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                                                            Registrar
                                                        </button> --}}
                                                    </label> 
                                                    <select id="cliente" name="cliente"
                                                        class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                         required disabled>
                                                        <option></option>
                                                        @foreach ($clientes as $cliente)
                                                            <option @if ($cliente->id == 1)
                                                                selected
                                                            @endif value="{{ $cliente->id }}"
                                                                {{ old('cliente') == $cliente->id ? 'selected' : '' }}
                                                                {{ $pedido->cliente_id == $cliente->id ? 'selected' : '' }}>
                                                                {{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('cliente'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('cliente') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                 <!-- OBTENER TIPO DE CLIENTE -->
                                 <input hidden type="text" name="cliente_id" id="cliente_id">
                                 <input type="hidden" name="" id="tipo_cliente">
                                 <!-- OBTENER DATOS DEL PRODUCTO -->
                                 <input type="hidden" name="" id="presentacion_producto">
                                 <input type="hidden" name="" id="codigo_nombre_producto">
                                 <!-- LLENAR DATOS EN UN ARRAY -->
                                 <input type="hidden" id="productos_tabla" name="productos_tabla">

                                <input type="text" name="igv" value="18" hidden>
                                <input type="text" name="igv_check" value="on" hidden>
                                <input type="text" name="efectivo" value="0" hidden>
                                <input type="text" name="importe" value="0" hidden>
                                <input type="text" name="empresa_id" hidden value="{{$pedido->empresa_id}}">

                                <input type="hidden" name="monto_sub_total" id="monto_sub_total"    value="{{$pedido->sub_total}}">
                                <input type="hidden" name="monto_embalaje" id="monto_embalaje"      value="{{$pedido->monto_embalaje}}">
                                <input type="hidden" name="monto_envio" id="monto_envio"            value="{{$pedido->monto_envio}}">
                                <input type="hidden" name="monto_total_igv" id="monto_total_igv"                value="{{$pedido->total_igv}}">
                                <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ $pedido->monto_descuento }}">
                                <input type="hidden" name="monto_total" id="monto_total"            value="{{$pedido->total}}">
                                <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{$pedido->total_pagar}}">
                                
                                <input type="hidden" name="data_envio" id="data_envio">

                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-6 col-md-6">
                                            <h4><b>Detalle del Pedido</b></h4>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 mr-3" style="background-color: #ffffff;border-radius:5px;">
                                            <div class="row align-items-center" style="height: 100%;">
                                                <div class="col-6 d-flex justify-content-center p-0">
                                                    <div class="mr-2" style="background-color: rgb(7, 7, 183);padding:5px;"></div>
                                                    <p class="m-0" style="color: black;">CANT SOLICITADA</p>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center p-0">
                                                    <div class="mr-2" style="background-color: black;padding:5px;"></div>
                                                    <p class="m-0" style="color:black;">STOCK</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    @include('ventas.pedidos.table-detalles-atender',[
                                        "carrito" => "carrito"
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" id="btn_regresar_atend"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                </button>
                                   
                                    <button type="submit" id="btn_grabar" form="form-pedido-doc-venta" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<style>
.search-length-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.buttons-container{
    display: flex;
    justify-content:end;
}


.custom-button {
    background-color: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #dcdcdc !important;
    border-radius: 5px;
    padding: 8px 16px;
    font-size: 14px;
    margin: 8px 0px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 3s, color 3s; 
}

.custom-button:hover {
    background-color: #d7e9fb !important;
    color: #000000 !important;
    border-color: #d7e9fb !important;
}


</style>
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.js"></script>
<script>
    const tfootSubtotal         =   document.querySelector('.subtotal');
    const tfootEmbalaje         =   document.querySelector('.embalaje');
    const tfootEnvio            =   document.querySelector('.envio');
    const tfootTotal            =   document.querySelector('.total');
    const tfootIgv              =   document.querySelector('.igv');
    const tfootTotalPagar       =   document.querySelector('.total-pagar');
    const tfootDescuento        =   document.querySelector('.descuento');
    
    const inputSubTotal         =   document.querySelector('#monto_sub_total');
    const inputEmbalaje         =   document.querySelector('#monto_embalaje');
    const inputEnvio            =   document.querySelector('#monto_envio');
    const inputTotal            =   document.querySelector('#monto_total');
    const inputIgv              =   document.querySelector('#monto_total_igv');
    const inputTotalPagar       =   document.querySelector('#monto_total_pagar');
    const inputMontoDescuento   =   document.querySelector('#monto_descuento');

    let carrito         =   [];
    const data_send     =   [];
    let secureClosure   =   1;  //======= 1:DEVUELVE STOCKS_LOGICOS  2: NO DEVUELVE STOCKS_LOGICOS =======

    document.addEventListener('DOMContentLoaded',async ()=>{
        console.log(@json($pedido));
        loadSelect2();
        cargarProductosPrevios();

        setUbicacionDepartamento(13,'first');
        await getTipoEnvios();
        await getTiposPagoEnvio();
        await getOrigenesVentas();
        await getTipoDocumento();
        const tipo_envio    =   $("#tipo_envio").select2('data')[0].text;
        await getEmpresasEnvio(tipo_envio);
        events();
        eventsModalEnvio();
    })


    function events(){

        document.querySelector('#btn_regresar_atend').addEventListener('click',(e)=>{
            event.target.disabled = true;
    
            event.target.innerHTML = '<i class="fa fa-arrow-left"></i> Regresando...';

            window.location.href = "{{ route('ventas.pedidos.index') }}";
        })

        //======== EDITAR INPUT CANTIDAD ATENDIDA =======
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidadAtender')){
                //====== QUITAR EL FOCUS DEL INPUT ======
                e.target.blur();
                //======== ELIMINAR TODOS LOS CARACTERES QUE NO SEAN NÚMEROS ========
                e.target.value = e.target.value.replace(/\D/g, '');
                //======= OBTENER PRODUCTO ID - COLOR ID - TALLA ID ==============
                const producto_id   =   e.target.getAttribute('data-producto-id');
                const color_id      =   e.target.getAttribute('data-color-id');
                const talla_id      =   e.target.getAttribute('data-talla-id');

                //======= VALIDAR CANTIDAD ======
                const cantidad_atender_nueva   =  e.target.value==''?0:parseInt(e.target.value);
                validarCantidadAtendida(producto_id,color_id,talla_id,cantidad_atender_nueva,e.target);
            } 

            if (e.target.classList.contains('embalaje') || e.target.classList.contains('envio')) {
                // Eliminar ceros a la izquierda, excepto si es el único carácter en el campo o si es seguido por un punto decimal y al menos un dígito
                e.target.value = e.target.value.replace(/^0+(?=\d)|(?<=\D)0+(?=\d)|(?<=\d)0+(?=\.)|^0+(?=[1-9])/g, '');

                // Evitar que el primer carácter sea un punto
                e.target.value = e.target.value.replace(/^(\.)/, '');

                // Reemplazar todo excepto los dígitos y el punto decimal
                e.target.value = e.target.value.replace(/[^\d.]/g, '');

                // Reemplazar múltiples puntos decimales con uno solo
                e.target.value = e.target.value.replace(/(\..*)\./g, '$1');

                calcularMontos();
            }
        })

        //========= GENERAR DOC DE VENTA ========
        document.querySelector('#form-pedido-doc-venta').addEventListener('submit',async (e)=>{
            e.preventDefault();

            const message   =   comprobarFacturacion();

            Swal.fire({
                title: message,
                text: "Operación no reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, genera el documento!"
                }).then(async (result) => {
                if (result.isConfirmed) {

                    //===== VALIDACIONES ======
                    const validar = await validarForm();

                    if(!validar){
                        return;
                    }

                    //====== CARGAR PRODUCTOS ========
                    cargarData();

                    //======== ENVIAR FORM =======
                    generarDocumentoVenta(e.target);

                }
            });  

        })

        //============ CIERRE DE LA VENTANA ======
        window.addEventListener('beforeunload', async function(event) {

            if (secureClosure == 1) {
                // var mensaje = '¿Estás seguro de que quieres salir de esta página?';
                // event.returnValue = mensaje;

                try {
                    const response = await axios.post(route('ventas.pedidos.devolverStockLogico'), {
                        carrito: JSON.stringify(carrito)
                    });
                    console.log('Stock devuelto correctamente:', response.data);
                } catch (error) {
                    console.error('Error al devolver el stock:', error);
                }
            }

        });


          //=========== MODAL DESPACHO =========
          document.querySelector('.btn-envio').addEventListener('click',()=>{
            //======= COLCANDO EN MODAL ENVIO EL NOMBRE DEL CLIENTE =======
            const cliente_nombre            =   $("#cliente").find('option:selected').text();
            console.log(cliente_nombre);
            const nroDocumento              =   cliente_nombre.split(':')[1].split('-')[0].trim();
            const cliente_nombre_recortado  =   cliente_nombre.split('-')[1].trim()
            const tipo_documento            =   cliente_nombre.split(':')[0];

            console.log(cliente_nombre);
            console.log(cliente_nombre_recortado);
            console.log(nroDocumento);

            if(tipo_documento === "DNI" || tipo_documento === "CARNET EXT."){
                //====== COLOCAR TEXTO DEL SPAN =====
                document.querySelector('.span-tipo-doc-dest').textContent     =   tipo_documento;
                //====== SELECCIONAR LA OPCIÓN RESPECTIVA EN SELECT TIPO DOC DEST ======
                if(tipo_documento === "DNI"){
                    $('#tipo_doc_destinatario').val(0).trigger('change');
                    if(nroDocumento.trim() != "99999999"){
                        document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                        document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                        document.querySelector('#nombres_destinatario').value   =   cliente_nombre_recortado;
                    }
                }
                if(tipo_documento === "CARNET EXT."){
                    $('#tipo_doc_destinatario').val(1).trigger('change');
                    document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                    document.querySelector('#nombres_destinatario').value   =   cliente_nombre_recortado;
                }                
            }
         
            //========= ABRIR MODAL ENVÍO =======
            $("#modal_envio").modal("show");
        })
    }

    //====== SELECT2 =======
    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //======== VERIFICAR SI EL PEDIDO FUE FACTURADO Y MOSTRAR MENSAJE PERSONALIZADO =======
    function comprobarFacturacion(){
        const pedido        =   @json($pedido);
        const montoTotal    =   parseFloat(inputTotalPagar.value);

        let message =   "";
        if(pedido.facturado === "SI"){
            const saldo_facturado   =   parseFloat(pedido.saldo_facturado);
            //======== EL SALDO CUBRE LA ATENCIÓN AÚN ========
            if(saldo_facturado >= montoTotal){
                message =   `El saldo del pedido facturado de S/.${saldo_facturado} cubre la atención de S/.${montoTotal}.
                Se generará la nota de venta como pagada.¿DESEA CONTINUAR?`;
            }else{
                message =   `El saldo del pedido facturado de S/.${saldo_facturado} NO CUBRE la atención de S/.${montoTotal}.
                Se generará la nota de venta como pagada y un recibo de caja con el excedente.¿DESEA CONTINUAR?`;
            }
        }

        if(!pedido.facturado){
            message =   "¿DESEA GENERAR EL DOCUMENTO DE VENTA?";
        }

        return message;
    }


    function cargarData(){
        //===== CARGANDO PRODUCTOS =====
        const inputProductos    =   document.querySelector('#productos_tabla');
        inputProductos.value    =   JSON.stringify(data_send);

        //======= CARGANDO CLIENTE ======
        const cliente_id        =   document.querySelector('#cliente').value;
        document.querySelector('#cliente_id').value =   cliente_id;

    }

    async function generarDocumentoVenta(form){
        try {
            //=== DESACTIVAR BTN GRABAR ======
            document.querySelector('#btn_grabar').disabled  = true;

            //======== OVERLAY DE CARGA =======
            const overlay = document.getElementById('overlay_esfera_1');
            overlay.style.display = 'flex'; 

            //======== VERIFICAR SI EXISTEN CAJAS ABIERTAS ==========
            const { data } = await this.axios.get(route('Caja.movimiento.verificarestado'));
            //======= MANEJO DE LA RESPUESTA ===========
            const { success } = data;

            if(success){
                const pedido    =   @json($pedido);
                const formData  =   new FormData(form);
                formData.append('pedido_id', pedido.id);
                // for (const pair of formData.entries()) {
                //     console.log(pair[0] + ', ' + pair[1]);
                // }
                const res       =   await axios.post(route('ventas.pedidos.generarDocumentoVenta'),formData)
                const success   =   res.data.success;
                
                if(success){
                    secureClosure = 2;
                    const documento_id  =   res.data.documento_id;

                    toastr.success('¡Documento de venta creado!', 'Exito');
                    window.location.href = '{{ route('ventas.documento.index') }}';
                    
                    var url_open_pdf = route("ventas.documento.comprobante", { id: documento_id +"-80"});
                    window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');
                    //===> asegurar cierre ===
                   
                }else{
                    secureClosure = 1;
                    const mensaje   =   res.data.mensaje;
                    const excepcion =   res.data.excepcion;
                    toastr.error(`${excepcion}`, `${mensaje}`, {
                        timeOut: 0,
                        extendedTimeOut: 0 
                    });
                }

            }else{
                toastr.error('NO HAY CAJAS APERTURADAS','ERROR');
                secureClosure = 1;
            }

            console.log(res);
        } catch (error) {
            
        }finally{
            const overlay = document.getElementById('overlay_esfera_1');
            overlay.style.display = 'none';
            document.querySelector('#btn_grabar').disabled  = false;
        }
    }

    //=========== VALIDAR FORM ========
    async function validarForm(){
        //====== VALIDAR FECHAS ======
        const fechaAtencion     =   document.querySelector('#fecha_atencion');
        const fechaVencimiento  =   document.querySelector('#fecha_vencimiento');

        if(fechaAtencion.value == ''){
            toastr.error('ESTABLEZCA LA FECHA DE ATENCIÓN','ERROR');
            fechaAtencion.focus();
            return false;
        }
        if(fechaVencimiento.value == ''){
            toastr.error('ESTABLEZCA LA FECHA DE VENCIMIENTO','ERROR');
            fechaVencimiento.focus();
            return false;
        }

        //======= VALIDAR TIPO VENTA ======
        const tipo_venta    =   document.querySelector('#tipo_venta');
        if(tipo_venta.value == ''){
            tipo_venta.focus();
            toastr.error('SELECCIONE UN TIPO DE VENTA','ERROR');
            return false;
        }

        //===== VALIDAR CONDICION ======
        const condicion     =   document.querySelector('#condicion_id');
        if(condicion.value == ''){
            condicion.focus();
            toastr.error('SELECCIONE UNA CONDICIÓN PARA LA VENTA','ERROR');
            return false;
        }

        //========= VALIDAR CONTENIDO DEL CARRITO =======
        if(carrito.length == 0){
            toastr.error('EL DETALLE DEL PEDIDO ESTÁ VACÍO','ERROR');
            return false;
        }

        //====== VALIDAR CLIENTE =====
        const cliente   =   document.querySelector('#cliente');
        if(cliente.value == ''){
            toastr.error('DEBE SELECCIONAR UN CLIENTE','ERROR');
            cliente.focus();
        }

        //====== FORMATEAR CARRITO ======
        data_send.length    =   0;
        formatearDetalle(carrito);
        console.log(data_send);

        if(data_send.length == 0){
            toastr.error('LAS CANTIDADES DEL DETALLE SON 0','ERROR');
            return false;
        }

        //====== VALIDAR TIPO COMPROBANTE ACTIVO ======
        const validar = validarTipoComprobante(tipo_venta.value);
        return validar;

    }

    //========= VALIDAR CANTIDAD ATENDIDA ========
    async function validarCantidadAtendida(producto_id,color_id,talla_id,cantidad_atender_nueva,input){

        //======== OBTENER EL PRODUCTO DEL CARRITO =======
        let producto      =   carrito.filter((c)=>{
            return c.producto_id == producto_id && c.color_id == color_id; 
        })[0].tallas.filter((t)=>{
            return t.talla_id == talla_id;
        })

        if(producto.length > 0){
            //======= VALIDAR QUE LA CANTIDAD ATENDIDA SEA MENOR IGUAL A LA SOLICITADA =====
            const cantidad_pendiente            =   parseInt(producto[0].cantidad_pendiente);
            const cantidad_atender_anterior     =   parseInt(producto[0].cantidad_atender);
            
            if(cantidad_atender_nueva > cantidad_pendiente){
                toastr.error('LA CANTIDAD ATENDER DEBE SER MENOR O IGUAL A LA PENDIENTE','ERROR');
                input.value  =   parseInt(cantidad_atender_anterior);
                input.focus();
                return false;
            }

            //======= VALIDAR QUE LA CANTIDAD ATENDER SEA MENOR IGUAL AL STOCK_LOGICO EN VIVO DEL PRODUCTO =======
            try {
                const overlay = document.getElementById('overlay_esfera_1');
                overlay.style.display = 'flex'; 
                const res   =   await axios.post(route('ventas.pedidos.validarCantidadAtender'),{
                    cantidad_atender_anterior,
                    cantidad_atender_nueva,
                    producto_id,
                    color_id,
                    talla_id
                })

                console.log(res);
                const type      =   res.data.type;
                const message   =   res.data.message;
                const data      =   res.data.data;

                if(type == 'success'){
                    //======== ACTUALIZANDO EL CARRITO ========
                    producto[0].cantidad_atender  =   cantidad_atender_nueva; 
                    calcularSubTotal();
                    calcularDescuento(producto_id,color_id);
                    toastr.success(message,'CANTIDAD ACTUALIZADA');
                }

                if(type == 'error'){
                    input.value =   cantidad_atender_anterior;
                    input.focus();   
                    toastr.error(message,'ERROR');
                }
            } catch (error) {
                
            }finally{
                const overlay = document.getElementById('overlay_esfera_1');
                overlay.style.display = 'none'; 
                input.focus();
            }
        }
    }


    //========= PINTAR DETALLE PEDIDO =======
    function pintarDetallePedido(carrito){
        let fila= ``;
        let htmlTallas= ``;
        const bodyDetalleTable  =   document.querySelector('#table-detalle-atender tbody');
        const tallas            =   @json($tallas);
        clearTabla(bodyDetalleTable);

        carrito.forEach((c)=>{
            htmlTallas=``;
                fila+= `<tr>   
                            <th>${c.producto_nombre} - ${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t)=>{
                    let talla_data = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });
                    
                    if(talla_data.length === 0){
                        htmlTallas += `<td></td>`; 
                        htmlTallas += `<td></td>`; 
                    }else{
                        htmlTallas += `<td>
                                        <div class="d-flex flex-column align-items-center">
                                            <p  style="margin:0px;color:rgb(7, 7, 183);font-weight:bold;">${talla_data[0].cantidad_pendiente}</p>
                                            <p  style="margin:0px;color:black;font-weight:bold;">${talla_data[0].stock_logico}</p>
                                        </div>
                                        </td>`; 
                        
                        htmlTallas += ` <td>
                                            <input ${talla_data[0].cantidad_atender == 0?'readonly':''} 
                                            class="form-control inputCantidadAtender" data-producto-id="${c.producto_id}"
                                            data-color-id="${c.color_id}" data-talla-id="${t.id}" 
                                            value="${talla_data[0].cantidad_atender}"></input>
                                        </td>`; 
                    }
                })
               

                htmlTallas+=`   <td style="text-align: right;">
                                    <span class="precio_venta_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.precio_unitario:c.precio_unitario_nuevo}
                                    </span>
                                </td>
                                <td class="td-subtotal" style="text-align: right;">
                                    <span class="subtotal_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.subtotal:c.subtotal_nuevo}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <input readonly data-producto-id="${c.producto_id}" data-color-id="${c.color_id}" 
                                    style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                                    class="form-control detailDescuento"></input>
                                </td>
                            </tr>`;

                fila+=htmlTallas;
                bodyDetalleTable.innerHTML=fila;            
        })
    }

    //=========== CARGAR PRODUCTOS PREVIOS =======
    const cargarProductosPrevios=()=>{
        const productosPrevios  =   @json($atencion_detalle);
        //====== CARGANDO CARRITO ======
        const producto_color_procesados = [];

        productosPrevios.forEach((productoPrevio)=>{
            const id    =   `${productoPrevio.producto_id}-${productoPrevio.color_id}`;

            if(!producto_color_procesados.includes(id)){
                const producto ={
                    producto_id:            productoPrevio.producto_id,
                    producto_nombre:        productoPrevio.producto_nombre,
                    producto_codigo:        productoPrevio.producto_codigo,
                    modelo_nombre:          productoPrevio.modelo_nombre,
                    color_id:               productoPrevio.color_id,
                    color_nombre:           productoPrevio.color_nombre,
                    precio_venta:           productoPrevio.precio_unitario,
                    subtotal:               0,
                    subtotal_nuevo:         0,
                    porcentaje_descuento:   parseFloat(productoPrevio.porcentaje_descuento),
                    monto_descuento:        0,
                    precio_venta_nuevo:     0,
                    tallas:[]
                }

                //==== BUSCANDO SUS TALLAS ====
                const tallas = productosPrevios.filter((t)=>{
                    return t.producto_id==productoPrevio.producto_id && t.color_id==productoPrevio.color_id;
                })

                if(tallas.length > 0){
                    const producto_color_tallas = [];
                    tallas.forEach((t)=>{
                        const talla = {
                            talla_id:                       t.talla_id,
                            talla_nombre:                   t.talla_nombre,
                            cantidad_solicitada:            parseInt(t.cantidad_solicitada),
                            cantidad_atendida:              parseInt(t.cantidad_atendida),
                            cantidad_pendiente:             parseInt(t.cantidad_pendiente),
                            stock_logico:                   parseInt(t.stock_logico),
                            stock_logico_actualizado:       parseInt(t.stock_logico_actualizado),
                            cantidad_atender:               parseInt(t.cantidad),
                            existe:                         t.existe
                        }
                        producto_color_tallas.push(talla);
                    })
                    producto.tallas = producto_color_tallas;
                }
                producto_color_procesados.push(id);
                carrito.push(producto);
            }
        })

      
        //===== CALCULAR SUBTOTAL POR FILA DEL DETALLE ======
        calcularSubTotal();
        //===== CARGANDO EMBALAJE Y ENVÍO PREVIO ========
        cargarEmbalajeEnvioPrevios();
        //===== PINTANDO DETALLE ======
        pintarDetallePedido(carrito);
        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c)=>{
            calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
        })
        
        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();
    }

    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal=()=>{
        let subtotal = 0;

        carrito.forEach((p)=>{
            p.tallas.forEach((t)=>{
                    subtotal+= parseFloat(p.precio_venta)*parseFloat(t.cantidad_atender);   
            })
               
            p.subtotal=subtotal; 
            subtotal=0; 
        })  
    }

     //======= CARGAR EMBALAJE ENVIO PREVIOS =======
     function cargarEmbalajeEnvioPrevios(){
        const precioEmbalaje    =   inputEmbalaje.value;
        const precioEnvio       =   inputEnvio.value;

        tfootEmbalaje.value     =   precioEmbalaje;
        tfootEnvio.value        =   precioEnvio;
    }

    //======== CALCULAR DESCUENTO ========
    const calcularDescuento = (producto_id,color_id)=>{
        const indiceExiste = carrito.findIndex((c)=>{
            return c.producto_id==producto_id && c.color_id==color_id;
        })

        if(indiceExiste !== -1){
            const producto_color_editar =  carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            const porcentaje_descuento                 =    producto_color_editar.porcentaje_descuento;
            producto_color_editar.monto_descuento      =    porcentaje_descuento === 0?0:producto_color_editar.subtotal*(porcentaje_descuento/100);
            producto_color_editar.precio_venta_nuevo   =    porcentaje_descuento === 0?0:(producto_color_editar.precio_venta*(1-porcentaje_descuento/100)).toFixed(2);
            producto_color_editar.subtotal_nuevo       =    porcentaje_descuento === 0?0:(producto_color_editar.subtotal*(1-porcentaje_descuento/100)).toFixed(2);

            carrito[indiceExiste] = producto_color_editar;

            //==== RECALCULANDO MONTOS ====
            calcularMontos();   

            //==== ACTUALIZANDO PRECIO VENTA Y SUBTOTAL EN EL HTML ====
            const detailPrecioVenta =   document.querySelector(`.precio_venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`); 
            const detailSubtotal    =   document.querySelector(`.subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`);    

            if(porcentaje_descuento !== 0){
                detailPrecioVenta.textContent = producto_color_editar.precio_venta_nuevo;
                detailSubtotal.textContent    = producto_color_editar.subtotal_nuevo;
            }else{
                detailPrecioVenta.textContent   =   producto_color_editar.precio_venta;
                detailSubtotal.textContent      =   producto_color_editar.subtotal;
            }

        }
    }

     //=========== CALCULAR MONTOS =======
     const calcularMontos = ()=>{
        let subtotal    =   0;
        let embalaje    =   tfootEmbalaje.value?parseFloat(tfootEmbalaje.value):0;
        let envio       =   tfootEnvio.value?parseFloat(tfootEnvio.value):0;
        let total       =   0;
        let igv         =   0;
        let total_pagar =   0;
        let descuento   =   0;
        
        //====== subtotal es la suma de todos los productos ======
        carrito.forEach((c)=>{
            if(c.porcentaje_descuento === 0){
                subtotal    +=  parseFloat(c.subtotal);
            }else{
                subtotal    +=  parseFloat(c.subtotal_nuevo);
            }
            descuento += parseFloat(c.monto_descuento);
        })

        total_pagar =   subtotal + embalaje + envio;
        total       =   total_pagar/1.18;
        igv         =   total_pagar - total;
       
        tfootTotalPagar.textContent =   'S/. ' + total_pagar.toFixed(2);
        tfootIgv.textContent        =   'S/. ' + igv.toFixed(2);
        tfootTotal.textContent      =   'S/. ' + total.toFixed(2);
        tfootSubtotal.textContent   =   'S/. ' + subtotal.toFixed(2);
        tfootDescuento.textContent  =   'S/. ' + descuento.toFixed(2);
        
        inputTotalPagar.value       =   total_pagar.toFixed(2);
        inputIgv.value              =   igv.toFixed(2);
        inputTotal.value            =   total.toFixed(2);
        inputEmbalaje.value         =   embalaje.toFixed(2);
        inputEnvio.value            =   envio.toFixed(2);
        inputSubTotal.value         =   subtotal.toFixed(2);
        inputMontoDescuento.value   =   descuento.toFixed(2);
    }


    //======== LIMPIAR TABLA PRODUCTOS ========
    function clearTabla(bodyTable){
        while (bodyTable.firstChild) {
            bodyTable.removeChild(bodyTable.firstChild);
        }
    }

    //==== CAMBIAR CONDICION ====
    function cambiarCondicion(condicion_id){
        //===== CONDICION CREDITO ======
        if(condicion_id == "2"){
            const select_fecha_venc     =   document.querySelector('#fecha_vencimiento');
            const select_fecha_aten     =   document.querySelector('#fecha_atencion');

            const fechaAtencion = new Date(select_fecha_aten.value);
            fechaAtencion.setDate(fechaAtencion.getDate() + 10);
            const fechaVencimientoMinima = fechaAtencion.toISOString().split('T')[0];

            select_fecha_venc.readOnly  =   false;
            select_fecha_venc.min       =   fechaVencimientoMinima;
            select_fecha_venc.value     =   fechaVencimientoMinima;
        }
        if(condicion_id == "1"){
            const select_fecha_venc     =   document.querySelector('#fecha_vencimiento');
            const select_fecha_aten     =   document.querySelector('#fecha_atencion');

            const fechaActual = new Date().toISOString().split('T')[0];
            select_fecha_venc.value = fechaActual; 
            select_fecha_venc.readOnly  =   true;
        }
    }

    //========= VALIDAR TIPO COMPROBANTE ==========
    async function validarTipoComprobante(comprobante_id){
        if(comprobante_id != ''){
             //===== VALIDAR TIPO VENTA ACTIVO ======
            try {
                const url = route('ventas.pedidos.validarTipoVenta', { 'comprobante_id': comprobante_id });
                const res = await axios.get(url);
                console.log(res);
                const type      =   res.data.type;
                const message   =   res.data.message;
                if(type == 'success'){
                    const estado    =   res.data.estado;
                    const message   =   res.data.message;
                    if(estado){
                        toastr.success(message,'VALIDACIÓN COMPLETADA');
                        return true;
                    }else{
                        document.querySelector('#tipo_venta').focus();
                        toastr.error(message,'VALIDACIÓN COMPLETADA');
                        return false;
                    }
                }

                if(type == 'error'){
                    const message       =   res.data.message;
                    const exception   =   res.data.exception;
                    document.querySelector('#tipo_venta').focus();
                    toastr.error(exception,message);
                    return false;
                }

            } catch (error) {
                return false;
            }
        }else{
            return false;
        }
    }

    function formatearDetalle(detalles){
        if(detalles.length>0){
            detalles.forEach((d)=>{
                d.tallas.forEach((t)=>{
                    if(t.cantidad_atender != 0){
                        const producto ={};
                        producto.producto_id            =   d.producto_id;
                        producto.color_id               =   d.color_id;
                        producto.talla_id               =   t.talla_id;
                        producto.cantidad               =   t.cantidad_atender;
                        producto.precio_unitario        =   d.precio_venta;  
                        producto.porcentaje_descuento   =   d.porcentaje_descuento;
                        producto.precio_unitario_nuevo  =   d.precio_venta_nuevo;
                        data_send.push(producto);
                    }
                })
            })
        }
    }
</script>
@endpush
