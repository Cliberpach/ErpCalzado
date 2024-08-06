@extends('layout') @section('content')
@include('ventas.cotizaciones.modal-cliente') 
    
@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

<style>

.overlay_cotizacion_create {
  position: fixed; /* Fija el overlay para que cubra todo el viewport */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7); /* Color oscuro con opacidad */
  z-index: 9999; /* Asegura que el overlay esté sobre todo */
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-size: 24px;
  visibility:hidden;
}

/*========== LOADER SPINNER =======*/
.loader_cotizacion_create {
    position: relative;
    width: 75px;
    height: 100px;
    background-repeat: no-repeat;
    background-image: linear-gradient(#DDD 50px, transparent 0),
                      linear-gradient(#DDD 50px, transparent 0),
                      linear-gradient(#DDD 50px, transparent 0),
                      linear-gradient(#DDD 50px, transparent 0),
                      linear-gradient(#DDD 50px, transparent 0);
    background-size: 8px 100%;
    background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px;
    animation: pillerPushUp 4s linear infinite;
  }
.loader_cotizacion_create:after {
    content: '';
    position: absolute;
    bottom: 10px;
    left: 0;
    width: 10px;
    height: 10px;
    background: #de3500;
    border-radius: 50%;
    animation: ballStepUp 4s linear infinite;
  }

@keyframes pillerPushUp {
  0% , 40% , 100%{background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px}
  50% ,  90% {background-position: 0px 50px, 15px 58px, 30px 66px, 45px 78px, 60px 90px}
}

@keyframes ballStepUp {
  0% {transform: translate(0, 0)}
  5% {transform: translate(8px, -14px)}
  10% {transform: translate(15px, -10px)}
  17% {transform: translate(23px, -24px)}
  20% {transform: translate(30px, -20px)}
  27% {transform: translate(38px, -34px)}
  30% {transform: translate(45px, -30px)}
  37% {transform: translate(53px, -44px)}
  40% {transform: translate(60px, -40px)}
  50% {transform: translate(60px, 0)}
  57% {transform: translate(53px, -14px)}
  60% {transform: translate(45px, -10px)}
  67% {transform: translate(37px, -24px)}
  70% {transform: translate(30px, -20px)}
  77% {transform: translate(22px, -34px)}
  80% {transform: translate(15px, -30px)}
  87% {transform: translate(7px, -44px)}
  90% {transform: translate(0, -40px)}
  100% {transform: translate(0, 0);}
}
    
    
</style>


<div class="overlay_cotizacion_create">
    <span class="loader_cotizacion_create"></span>
</div>

@csrf
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVA COTIZACIÓN</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>
        </ol>

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('ventas.cotizacion.store') }}" method="POST"
                                id="form-cotizacion">
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
                                                    <input type="date" id="fecha_documento" name="fecha_documento"
                                                        class="form-control input-required {{ $errors->has('fecha_documento') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_documento', $fecha_hoy) }}"
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
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Atención</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_atencion" name="fecha_atencion"
                                                            class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_atencion', $fecha_hoy) }}"
                                                            autocomplete="off" required readonly>
                                                        @if ($errors->has('fecha_atencion'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                           
                                           
                                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
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

                                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Condición</label>
                                                    <select id="condicion_id" name="condicion_id"
                                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($condiciones as $condicion)
                                                            <option value="{{ $condicion->id }}"
                                                                {{ $condicion->id == 1 ? 'selected' : '' }}
                                                                {{ old('condicion_id') == $condicion->id ? 'selected' : '' }}>
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

                                            <input hidden type="text" name="vendedor" value="{{$vendedor_actual}}">

                                        </div>
                                       
                                        <div class="row">
                                            <div class="col-12 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                                                            Registrar
                                                        </button>
                                                    </label>
                                                    <select id="cliente" name="cliente"
                                                        class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                         required>
                                                        <option></option>
                                                        @foreach ($clientes as $cliente)
                                                            <option @if ($cliente->id == 1)
                                                                selected
                                                            @endif value="{{ $cliente->id }}"
                                                                {{ old('cliente') == $cliente->id ? 'selected' : '' }}>
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
                                        <!-- OBTENER TIPO DE CLIENTE -->
                                        <input type="hidden" name="" id="tipo_cliente">
                                        <!-- OBTENER DATOS DEL PRODUCTO -->
                                        <input type="hidden" name="" id="presentacion_producto">
                                        <input type="hidden" name="" id="codigo_nombre_producto">
                                        <!-- LLENAR DATOS EN UN ARRAY -->
                                        <input type="hidden" id="productos_tabla" name="productos_tabla[]">

                                    </div>
                                </div>

                                <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
                                <input type="hidden" name="monto_embalaje" id="monto_embalaje" value="{{ old('monto_embalaje') }}">
                                <input type="hidden" name="monto_envio" id="monto_envio" value="{{ old('monto_envio') }}">
                                <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
                                <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ old('monto_descuento') }}">
                                <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}">
                                <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{ old('monto_total_pagar') }}">

                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Seleccionar productos</b></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label class="required" style="font-weight: bold;">MODELO</label>
                                                    <select id="modelo"
                                                        class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                                        onchange="getProductosByModelo(this)" >
                                                        <option></option>
                                                        @foreach ($modelos as $modelo)
                                                            <option value="{{ $modelo->id }}"
                                                                {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                                                {{ $modelo->descripcion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label class="required" style="font-weight: bold;">PRODUCTO</label>
                                                    <select id="producto"
                                                        class="select2_form form-control {{ $errors->has('producto') ? ' is-invalid' : '' }}"
                                                        onchange="getColoresTallas()" >
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label class="required" style="font-weight: bold;">PRECIO VENTA</label>
                                                    <select id="precio_venta" class="select2_form form-control">
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row mt-3">
                                                <div class="col-lg-12">
                                                    @include('ventas.cotizaciones.table-stocks')
                                                    
                                                </div>
                                            </div>

                                            <div class="form-group row mt-1">
                                                <div class="col-lg-2 col-xs-12">
                                                    <button disabled type="button" id="btn_agregar_detalle"
                                                        class="btn btn-warning btn-block"><i
                                                          class="fa fa-plus"></i> AGREGAR</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Detalle de la Cotización</b></h4>
                                </div>
                                <div class="panel-body">
                                    @include('ventas.cotizaciones.table-stocks',[
                                        "carrito" => "carrito"
                                    ])

                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="table-responsive">
                                           @include('ventas.cotizaciones.table_montos')     
                                        </div>
                                    </div>

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
                                    <a href="{{ route('ventas.cotizacion.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>
                                  
                                    <button type="submit" id="btn_grabar" form="form-cotizacion" class="btn btn-w-m btn-primary">
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
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
@endpush

@push('scripts')

<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>

<script>
    const tableStocksBody       =   document.querySelector('#table-stocks tbody');
    const tableDetalleBody      =   document.querySelector('#table-detalle tbody');
    const tokenValue            =   document.querySelector('input[name="_token"]').value;
    const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');
    const formCotizacion        =   document.querySelector('#form-cotizacion');

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

    const selectClientes    =   document.querySelector('#cliente');

    const inputProductos        =   document.querySelector('#productos_tabla');
    const tallas                =   @json($tallas);

    let modelo_id           = null;
    let carrito             =   [];
    let carritoFormateado   =   [];
    let dataTableStocksCotizacion   =   null;
    let dataTableDetallesCotizacion =   null;
   
    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        setUbicacionDepartamento(13,'first');
        events();
        eventsCliente();
    })

    function events(){
        
        //===== VALIDAR CONTENIDO DE INPUTS CANTIDAD ========
        //===== VALIDAR TFOOTS EMBALAJE Y ENVIO ======
        document.addEventListener('input',(e)=>{

            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
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

            if(e.target.classList.contains('detailDescuento')){
                //==== CONTROLANDO DE QUE EL VALOR SEA UN NÚMERO ====
                const valor         =   event.target.value;
                const producto_id   =   e.target.getAttribute('data-producto-id');
                const color_id      =   e.target.getAttribute('data-color-id');

                //==== SI EL INPUT ESTA VACÍO ====
                if(valor.trim().length === 0){
                    calcularDescuento(producto_id,color_id,0);
                    return;
                }

                //===== EXPRESION REGULAR PARA EVITAR CARACTERES NO NUMÉRICOS EN LA CADENA ====
                const regex = /^[0-9]+(\.[0-9]{0,2})?$/;
                //==== BORRAR CARACTER NO NUMÉRICO ====
                if (!regex.test(valor)) {
                    event.target.value = valor.slice(0, -1); 
                    return;
                }

                //==== EN CASO SEA NUMÉRICO ====
                let porcentaje_desc = parseFloat(event.target.value);

                //==== EL MÁXIMO DESCUENTO ES 100% ====
                if(porcentaje_desc>100){
                    event.target.value = 100;
                    porcentaje_desc = event.target.value;
                }

                //==== CALCULAR DESCUENTO ====
                calcularDescuento(producto_id,color_id,porcentaje_desc)
            }

        })



        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');
                eliminarProducto(productoId,colorId);
                pintarDetalleCotizacion(carrito);
                calcularMontos();
            }
        })

        formCotizacion.addEventListener('submit',(e)=>{
            e.preventDefault();
            
            //===== VALIDAR FECHA =====
            const correcto =  validarFecha();

            if (correcto) {
                Swal.fire({
                    title: 'Opción Guardar',
                    text: "¿Seguro que desea guardar cambios?",
                    icon: 'question',
                showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        if(carrito.length>0){
                            document.querySelector('#btn_grabar').disabled = true;
                            formatearDetalle();
                            inputProductos.value=JSON.stringify(carritoFormateado);
                            console.log(carritoFormateado);
                            formCotizacion.submit();
                        }else{
                            toastr.error('Debe tener almenos un producto en el detalle','ERROR');
                        }

                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                        )
                        document.querySelector('#btn_grabar').disabled = false;
                    }
                })
            }

        })
       

        //======= AGREGAR PRODUCTO AL DETALLE ======
        btnAgregarDetalle.addEventListener('click',()=>{

            if(!$('#modelo').val()){
                toastr.error('DEBE SELECCIONAR UN MODELO','OPERACIÓN INCORRECTA');
                return;
            }
            if(!$('#producto').val()){
                toastr.error('DEBE SELECCIONAR UN PRODUCTO','OPERACIÓN INCORRECTA');
                return;
            }
            if(!$('#precio_venta').val()){
                toastr.error('DEBE SELECCIONAR UN PRECIO DE VENTA','OPERACIÓN INCORRECTA');
                return;
            }
          
            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            
            for (const ic of inputsCantidad) {

                const cantidad = ic.value ? ic.value : null;
                if (cantidad) {
                            const producto      = formarProducto(ic);
                            const indiceExiste  = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                            //===== PRODUCTO NUEVO =====
                            if (indiceExiste == -1) {
                                const objProduct = {
                                    producto_id: producto.producto_id,
                                    color_id: producto.color_id,
                                    producto_nombre: producto.producto_nombre,
                                    color_nombre: producto.color_nombre,
                                    precio_venta: producto.precio_venta,
                                    monto_descuento:0,
                                    porcentaje_descuento:0,
                                    precio_venta_nuevo:0,
                                    subtotal_nuevo:0,
                                    tallas: [{
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    }]
                                };

                                carrito.push(objProduct);
                            } else {
                                const productoModificar = carrito[indiceExiste];
                                productoModificar.precio_venta = producto.precio_venta;

                                const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                                if (indexTalla !== -1) {
                                    const cantidadAnterior = productoModificar.tallas[indexTalla].cantidad;
                                    productoModificar.tallas[indexTalla].cantidad = producto.cantidad;
                                    carrito[indiceExiste] = productoModificar;
                                } else {
                                    const objTallaProduct = {
                                        talla_id: producto.talla_id,
                                        talla_nombre: producto.talla_nombre,
                                        cantidad: producto.cantidad
                                    };
                                    carrito[indiceExiste].tallas.push(objTallaProduct);
                                }
                            }
                } else {
                    const producto = formarProducto(ic);
                    const indiceProductoColor = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                    if (indiceProductoColor !== -1) {
                        const indiceTalla = carrito[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto.talla_id);

                        if (indiceTalla !== -1) {
                            const cantidadAnterior = carrito[indiceProductoColor].tallas[indiceTalla].cantidad;
                            carrito[indiceProductoColor].tallas.splice(indiceTalla, 1);
                            
                            const cantidadTallas = carrito[indiceProductoColor].tallas.length;

                            if (cantidadTallas == 0) {
                                carrito.splice(indiceProductoColor, 1);
                            }
                        }
                    }
                }
            }

            reordenarCarrito();
            calcularSubTotal();
            pintarDetalleCotizacion(carrito);
            //===== RECALCULANDO DESCUENTOS Y MONTOS =====
            carrito.forEach((c)=>{
                calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
            })

        })
    }

    function loadDataTableStocksCotizacion(){
        dataTableStocksCotizacion =   new DataTable('#table-stocks',{
            language: {
                "sEmptyTable": "No hay datos disponibles en la tabla",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                },
                "oAria": {
                        "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function loadDataTableDetallesCotizacion(){
        dataTableDetallesCotizacion =   new DataTable('#table-detalle',{
            language: {
                "sEmptyTable": "No hay datos disponibles en la tabla",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                },
                "oAria": {
                        "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    //====== VALIDAR FECHA ======
    function validarFecha() {
        var enviar = true;

        if ($('#fecha_documento').val() == '') {
            toastr.error('Ingrese Fecha de Documento.', 'Error');
            $("#fecha_documento").focus();
            enviar = false;
        }

        if ($('#fecha_atencion').val() == '') {
            toastr.error('Ingrese Fecha de Atención.', 'Error');
            $("#fecha_atencion").focus();
            enviar = false;
        }


        return enviar
    }

    //============ LOAD SELECT2 ========
    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //====== FORMATEAR EL CARRITO A FORMATO DE BD ======
    function formatearDetalle(){
        carrito.forEach((d)=>{
            console.log('producto_color')
            d.tallas.forEach((t)=>{
                 console.log('talla')
                const producto ={};
                producto.producto_id            =   d.producto_id;
                producto.color_id               =   d.color_id;
                producto.talla_id               =   t.talla_id;
                producto.cantidad               =   t.cantidad;
                producto.precio_venta           =   d.precio_venta;  
                producto.porcentaje_descuento   =   d.porcentaje_descuento;
                producto.precio_venta_nuevo     =   d.precio_venta_nuevo;
                carritoFormateado.push(producto);
            })
        })  
    }


    const calcularDescuento = (producto_id,color_id,porcentaje_descuento)=>{
        const indiceExiste = carrito.findIndex((c)=>{
            return c.producto_id==producto_id && c.color_id==color_id;
        })

        if(indiceExiste !== -1){
            const producto_color_editar =  carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            producto_color_editar.porcentaje_descuento =    porcentaje_descuento;
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

    

    const eliminarProducto = (productoId,colorId)=>{
        carrito = carrito.filter((p)=>{
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal=()=>{
        let subtotal = 0;

        carrito.forEach((p)=>{
            p.tallas.forEach((t)=>{
                    subtotal+= parseFloat(p.precio_venta)*parseFloat(t.cantidad);   
            })
               
            p.subtotal=subtotal; 
            subtotal=0; 
        })  
    }

    const reordenarCarrito= ()=>{
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    const formarProducto = (ic)=>{
        const producto_id           = ic.getAttribute('data-producto-id');
        const producto_nombre       = ic.getAttribute('data-producto-nombre');
        const color_id              = ic.getAttribute('data-color-id');
        const color_nombre          = ic.getAttribute('data-color-nombre');
        const talla_id              = ic.getAttribute('data-talla-id');
        const talla_nombre          = ic.getAttribute('data-talla-nombre');
        const precio_venta          = $('#precio_venta').find('option:selected').text();
        const cantidad              = ic.value?ic.value:0;
        const subtotal              = 0;
        const subtotal_nuevo        = 0;
        const porcentaje_descuento  = 0;
        const monto_descuento       = 0;
        const precio_venta_nuevo    = 0;

        const producto = {producto_id,producto_nombre,color_id,color_nombre,
                            talla_id,talla_nombre,cantidad,precio_venta,
                            subtotal,subtotal_nuevo,porcentaje_descuento,monto_descuento,precio_venta_nuevo
                        };
        return producto;
    }

    function clearDetalleCotizacion(){
        while (tableDetalleBody.firstChild) {
            tableDetalleBody.removeChild(tableDetalleBody.firstChild);
        }
    }

    function pintarDetalleCotizacion(carrito){
        let filas       =   ``;
        let htmlTallas  =   ``;
        clearDetalleCotizacion();

        if(dataTableDetallesCotizacion){
            dataTableDetallesCotizacion.destroy();
        }

        carrito.forEach((c)=>{
            htmlTallas=``;
                filas += `<tr>   
                            <td>
                                <i class="fas fa-trash-alt btn btn-primary delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>                            
                            </td>
                            <th><div style="width:200px;">${c.producto_nombre}</div></th>
                            <th>${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t)=>{
                    let cantidad = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });
                    cantidad.length!=0? cantidad = cantidad[0].cantidad : cantidad = '';
                    htmlTallas += `<td><p style="margin:0;font-weight:bold;">${cantidad}</p></td>`; 
                })


                htmlTallas+=`   <td style="text-align: right;">
                                    <span class="precio_venta_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.precio_venta:c.precio_venta_nuevo}
                                    </span>
                                </td>
                                <td class="td-subtotal" style="text-align: right;">
                                    <span class="subtotal_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.subtotal:c.subtotal_nuevo}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <input data-producto-id="${c.producto_id}" data-color-id="${c.color_id}" 
                                    style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                                    class="form-control detailDescuento"></input>
                                </td>
                            </tr>`;

                filas += htmlTallas;
        })

        tableDetalleBody.innerHTML  =   filas;
        loadDataTableDetallesCotizacion();
    }

    function mostrarAnimacionCotizacion(){
      
        document.querySelector('.overlay_cotizacion_create').style.visibility   =   'visible';
    }

    function ocultarAnimacionCotizacion(){
       
        document.querySelector('.overlay_cotizacion_create').style.visibility   =   'hidden';
    }

    //======== OBTENER PRODUCTOS POR MODELO ========
    async function  getProductosByModelo(e){
        mostrarAnimacionCotizacion();
        limpiarTableStocks();
        modelo_id                   =   e.value;
        btnAgregarDetalle.disabled  =   true;
        
        if(modelo_id){
            try {
                const res   =   await axios.get(route('ventas.cotizacion.getProductosByModelo',{modelo_id}));
                if(res.data.success){
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS','OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            }finally{
                ocultarAnimacionCotizacion();
            }
               
        }else{
            ocultarAnimacionCotizacion();
        }
    }

    //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
    async function getColoresTallas(){
        mostrarAnimacionCotizacion();
        const producto_id   =   $('#producto').val();
        if(producto_id){
            try {
                const res   =   await   axios.get(route('ventas.cotizacion.getColoresTallas',{producto_id}));
                if(res.data.success){
                    pintarTableStocks(res.data.producto_color_tallas);
                    pintarPreciosVenta(res.data.producto_color_tallas);
                    loadCarrito();
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }finally{
                ocultarAnimacionCotizacion();
            }
        }else{
            limpiarTableStocks();
            limpiarSelectPreciosVenta();
            ocultarAnimacionCotizacion();
        }
    }

    function limpiarSelectPreciosVenta(){
        $('#precio_venta').empty();
        $('#precio_venta').trigger('change');

    }

    //======= PINTAR PRECIOS VENTA =======
    function pintarPreciosVenta(producto_color_tallas){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#precio_venta').empty();

        //====== LLENAR =======

        if(producto_color_tallas){

            if(producto_color_tallas.precio_venta_1 != null){
                const option_1 = new Option(producto_color_tallas.precio_venta_1, 'precio_venta_1', false, false);
                $('#precio_venta').append(option_1);
            }

            if(producto_color_tallas.precio_venta_2 != null){
                const option_2 = new Option(producto_color_tallas.precio_venta_2, 'precio_venta_2', false, false);
                $('#precio_venta').append(option_2);
            }
           
            if(producto_color_tallas.precio_venta_3 != null){
                const option_3 = new Option(producto_color_tallas.precio_venta_3, 'precio_venta_3', false, false);
                $('#precio_venta').append(option_3);
            }
 
        }
       
        // Refrescar Select2
        $('#precio_venta').trigger('change');
    }

    //======== PINTAR SELECT PRODUCTOS =======
    function pintarSelectProductos(productos){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto').empty();

        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto').append(option);
        });

        // Refrescar Select2
        $('#producto').val(null);
        $('#producto').trigger('change');
    }

    function limpiarTableStocks(){
        while (tableStocksBody.firstChild) {
            tableStocksBody.removeChild(tableStocksBody.firstChild);
        }
    }

    const pintarTableStocks = (producto)=>{
        let filas = ``;

        if(dataTableStocksCotizacion){
            dataTableStocksCotizacion.destroy();
        }

        producto.colores.forEach((color)=>{
            filas   +=  `  <tr>
                            <th scope="row" data-producto=${producto.id} data-color=${color.id} >
                                <div style="width:200px;">${producto.nombre}</div>
                            </th>
                            <th scope="row">${color.nombre}</th>
                        `;

            color.tallas.forEach((talla)=>{
                filas   +=  `<td style="background-color: rgb(210, 242, 242);">
                                        <p style="margin:0;width:20px;text-align:center;${talla.stock != 0?'font-weight:bold':''};">${talla.stock}</p>
                            </td>
                            <td width="8%">
                                <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                id="inputCantidad_${producto.id}_${color.id}_${talla.id}" 
                                data-producto-id="${producto.id}"
                                data-producto-nombre="${producto.nombre}"
                                data-color-nombre="${color.nombre}"
                                data-talla-nombre="${talla.nombre}"
                                data-color-id="${color.id}" data-talla-id="${talla.id}"></input>    
                            </td>`;
            })

            filas   +=  `</tr>`;
           
        })

        tableStocksBody.innerHTML = filas;
        loadDataTableStocksCotizacion();
        btnAgregarDetalle.disabled = false;

    }


    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL CARRITO =========
    function loadCarrito(){

        carrito.forEach((c)=>{

            c.tallas.forEach((talla)=>{
                let llave   =   `#inputCantidad_${c.producto_id}_${c.color_id}_${talla.talla_id}`;   
                const inputLoad = document.querySelector(llave);
            
                if(inputLoad){
                    inputLoad.value = talla.cantidad;
                }
            })
        

            let targetValue;
            //==== UBICANDO PRECIO VENTA SELECCIONADO ======
            $('#precio_venta option').each(function() {
                if ($(this).text() == c.precio_venta.toString()) {
                targetValue = $(this).val(); 
                return false;
                }
            });
            
            if (targetValue) {
                $('#precio_venta').val(targetValue).trigger('change'); 
            } 

        }) 
    }

    //============= ABRIR MODAL CLIENTE =============
    function openModalCliente(){
        $("#modal_cliente").modal("show");
    }

    function cargarDataTables(){
        table = new DataTable('#table-productos',
        {
            language: {
                processing:     "Traitement en cours...",
                search:         "BUSCAR: ",
                lengthMenu:    "MOSTRAR _MENU_ PRODUCTOS",
                info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ PRODUCTOS",
                infoEmpty:      "MOSTRANDO 0 ELEMENTOS",
                infoFiltered:   "(FILTRADO de _MAX_ PRODUCTOS)",
                infoPostFix:    "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "NO HAY PRODUCTOS DISPONIBLES",
                paginate: {
                    first:      "PRIMERO",
                    previous:   "ANTERIOR",
                    next:       "SIGUIENTE",
                    last:       "ÚLTIMO"
                },
                aria: {
                    sortAscending:  ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
        
    }


</script>

@endpush







