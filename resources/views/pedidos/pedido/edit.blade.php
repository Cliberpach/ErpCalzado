@extends('layout') @section('content')
@include('ventas.cotizaciones.modal-cliente') 

@section('pedidos-active', 'active')
@section('pedido-active', 'active')

<style>

    .overlay_pedido_edit {
      position: fixed; /* Fija el overlay para que cubra todo el viewport */
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7); /* Color oscuro con opacidad */
      z-index: 999999999; /* Asegura que el overlay esté sobre todo */
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      font-size: 24px;
      visibility:hidden;
    }
    
    /*========== LOADER SPINNER =======*/
    .loader_pedido_edit{
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
    .loader_pedido_edit:after {
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

<div class="overlay_pedido_edit">
    <span class="loader_pedido_edit"></span>
</div>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Modificar Pedido #{{$pedido->id}}</b></h2>
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
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            <form method="POST" action="{{ route('pedidos.pedido.update', $pedido->id) }}" id="form-pedido">
                                @csrf
                                @method('PUT')
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
                                                    value="{{ old('fecha_registro', $pedido->fecha_registro) }}"
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
                                                            @if ($empresa->id == $pedido->empresa_id)
                                                                selected
                                                            @endif
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
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Atención</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_atencion" name="fecha_atencion"
                                                            class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_registro', $pedido->fecha_registro) }}"
                                                            autocomplete="off" required readonly>
                                                        @if ($errors->has('fecha_atencion'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                           
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="">Vendedor</label>
                                                    <select id="vendedor" name="vendedor" class="select2_form form-control" disabled>
                                                        <option></option>
                                                        @foreach (vendedores() as $vendedor)
                                                            <option value="{{ $vendedor->id }}" {{ $vendedor->id === $vendedor_actual_id ? 'selected' : '' }}>
                                                                {{ $vendedor->persona->apellido_paterno . ' ' . $vendedor->persona->apellido_materno . ' ' . $vendedor->persona->nombres }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <input hidden type="text" name="vendedor" value="{{$vendedor_actual_id}}">

                                        </div>
                                       
                                        <div class="row" style="align-items: flex-end;">
                                            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 select-required">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                                                            Registrar
                                                        </button>
                                                    </label>
                                                    <select id="cliente" name="cliente" 
                                                        @if ($pedido->estado === "ATENDIENDO")
                                                            disabled
                                                        @endif
                                                        class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                         required>
                                                        <option></option>
                                                        @foreach ($clientes as $cliente)
                                                            <option @if ($cliente->id == $pedido->cliente_id)
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
                                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 select-required">
                                                <div class="form-group">
                                                    <label class="required">Condición</label>
                                                    <select id="condicion_id" name="condicion_id"
                                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($condiciones as $condicion)
                                                            <option value="{{ $condicion->id }}"
                                                                @if ($condicion->id == $pedido->condicion_id)
                                                                    selected
                                                                @endif
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
                                            <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label class="required">Fecha Propuesta</label>
                                                    <input type="date" class="form-control" id="fecha_propuesta" name="fecha_propuesta" value="{{$pedido->fecha_propuesta}}">
                                                    @if ($errors->has('fecha_propuesta'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('fecha_propuesta') }}</strong>
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

                                <input type="hidden" name="monto_sub_total" id="monto_sub_total"    value="{{$pedido->sub_total}}">
                                <input type="hidden" name="monto_embalaje" id="monto_embalaje"      value="{{$pedido->monto_embalaje}}">
                                <input type="hidden" name="monto_envio" id="monto_envio"            value="{{$pedido->monto_envio}}">
                                <input type="hidden" name="monto_total_igv" id="monto_total_igv"                value="{{$pedido->total_igv}}">
                                <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ $pedido->monto_descuento }}">
                                <input type="hidden" name="monto_total" id="monto_total"            value="{{$pedido->total}}">
                                <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{$pedido->total_pagar}}">

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
                                                            {{ old('modelo') == $modelo->id ? 'selected' : '' }}>{{$modelo->descripcion}}</option>
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
                                                    <div class="table-responsive">
                                                        @include('pedidos.pedido.tables.table_pedido_stocks')
                                                    </div>
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
                                    <h4>
                                        <b>Detalle del Pedido</b>
                                    </h4>
                                    <h5>
                                        <span>
                                            Pueden agregarse o retirarse productos del detalle, pero las cantidades
                                            atendidas no podrán modificarse. Si desea modificar cantidades atendidas debe realizar
                                            notas de crédito,devolución o cambios de talla sobre el documento venta de atención. 
                                        </span>
                                    </h5>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        @include('pedidos.pedido.tables.table_pedido_detalle')
                                    </div>
                                </div>
                                <div class="panel-footer panel-primary">
                                    <div class="table-responsive">
                                        @include('pedidos.pedido.tables.table_montos_atender')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h4><b>Detalle Atendiendo</b></h4>
                                </div>
                                <div class="panel-body">
                                    @include('pedidos.pedido.tables.table_pedido_atencion_historial')
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
                                    <a href="{{ route('pedidos.pedido.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>
                                   
                                    <button type="submit" id="btn_grabar" form="form-pedido" class="btn btn-w-m btn-primary">
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
    const tfootTotal            =   document.querySelector('.total');
    const tfootIgv              =   document.querySelector('.igv');
    const tfootTotalPagar       =   document.querySelector('.total-pagar');
    const tfootDescuento        =   document.querySelector('.descuento');
    const tfootEmbalaje         =   document.querySelector('.embalaje');
    const tfootEnvio            =   document.querySelector('.envio');
    
    const inputSubTotal         =   document.querySelector('#monto_sub_total');
    const inputTotal            =   document.querySelector('#monto_total');
    const inputIgv              =   document.querySelector('#monto_total_igv');
    const inputTotalPagar       =   document.querySelector('#monto_total_pagar');
    const inputMontoDescuento   =   document.querySelector('#monto_descuento');
    const inputEmbalaje         =   document.querySelector('#monto_embalaje');
    const inputEnvio            =   document.querySelector('#monto_envio');


    let noEditTotal         =   0;

    let pedidos_data_table  = null;
    let carrito             =   [];
    let dataTableStocksPedido   =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        cargarProductosPrevios();
        pintarTablePedidoAtencionHistorial();
        setUbicacionDepartamento(13,'first');
        events();
        eventsCliente();
    })

    function events(){
        //====== ELIMINAR ITEM =========
        document.addEventListener('click',async (e)=>{
            if(e.target.classList.contains('delete-product')){

                const productoId    =   e.target.getAttribute('data-producto');
                const colorId       =   e.target.getAttribute('data-color');

                //======== OBTENIENDO TODOS LOS PRODUCTOS DE ESE PRODUCTOID Y COLORID =======
                const lstProductos  =   [];
                carrito.forEach((producto)=>{
                    producto.tallas.forEach((talla)=>{

                        if(producto.producto_id == productoId && producto.color_id == colorId){
                            const item  =   {   
                                                producto_id:producto.producto_id,
                                                color_id:producto.color_id,
                                                talla_id:talla.talla_id,
                                                producto_nombre:producto.producto_nombre,
                                                color_nombre:producto.color_nombre,
                                                talla_nombre:talla.talla_nombre,
                                                cantidad:0
                                            }
                            lstProductos.push(item);
                        }
                       
                    })
                })


                //======== VALIDAR CANTIDAD CON CANTIDAD ATENDIDA ======
                const  lstProductosValidados   =  await validarProductos(lstProductos);
                mostrarAnimacionPedido();
                const cantProductosEliminados   =   eliminarProducto(lstProductosValidados);

                if(cantProductosEliminados > 0){
                    calcularSubTotal(carrito);
                    calcularMontos();
                    pintarDetallePedido(carrito);
                    clearInputsCantidad();
                    loadCarrito();
                    
                    toastr.info('SE ELIMINARON SOLO LOS PRODUCTOS CUYA CANTIDAD ATENDIDA NO SEA AFECTADA');
                }else{
                    toastr.error('NO SE PUDO ELIMINAR NINGÚN PRODUCTO DE LA FILA');
                }

                ocultarAnimacionPedido();

            }
        })

        //====== SUBMIT FORM PEDIDOS =======
        document.querySelector('#form-pedido').addEventListener('submit',(e)=>{

            e.preventDefault();
            //===== VALIDAR FECHA =====
            const correcto =  validarDatosPedido();

            if (correcto) {
                Swal.fire({
                    title: 'Opción Guardar',
                    text: "¿Seguro que desea guardar cambios?",
                    icon: 'question',
                showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        
                        Swal.fire({
                            title: 'Actualizando Pedido...',
                            text: 'Por favor, espere.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading(); 
                            }
                        });

                        try {
                            document.querySelector('#btn_grabar').disabled = true;
                            const res   =   await axios.put(route('pedidos.pedido.update',{id:@json($pedido->id)}),
                            {   
                                lstProductos:   JSON.stringify(carrito),
                                cliente:        document.querySelector('#cliente').value,
                                condicion_id:   document.querySelector('#condicion_id').value,
                                fecha_propuesta:document.querySelector('#fecha_propuesta').value,
                                empresa:        document.querySelector('#empresa').value
                            });

                            if(res.data.success){
                                const routeIndex        =   "{{ route('pedidos.pedido.index') }}";
                                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                                window.location.href    =   routeIndex;
                            }else{
                                if('lstErroresValidacion' in res.data){
                                    pintarErroresValidacion(res.data.lstErroresValidacion);
                                    return;
                                }
                                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                            }
                        } catch (error) {
                            toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR PEDIDO');
                        }finally{
                            document.querySelector('#btn_grabar').disabled = false;
                            Swal.close();
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


        //===== VALIDAR CONTENIDO DE INPUTS CANTIDAD ========
        //===== VALIDAR TFOOTS EMBALAJE Y ENVIO ======
        document.addEventListener('input',(e)=>{

            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }

            if(e.target.classList.contains('detailDescuento')){
                //==== CONTROLANDO DE QUE EL VALOR SEA UN NÚMERO ====
                const valor = event.target.value;
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

            //====== EMBALAJE Y ENVÍO =======
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

       //======== AGREGAR PRODUCTO AL DETALLE =====
       document.querySelector('#btn_agregar_detalle').addEventListener('click',async ()=>{

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

            despintarErrorInputsCantidad();
            const lstProductos              =   obtenerProductos();
            const lstProductosValidados     =   await validarProductos(lstProductos);
            
            if(lstProductosValidados){
                agregarProducto(lstProductosValidados);
                reordenarCarrito();
                calcularSubTotal(carrito);
                pintarDetallePedido(carrito);
                //===== RECALCULANDO DESCUENTOS Y MONTOS =====
                carrito.forEach((c)=>{
                    calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
                })
                //===== RECALCULANDO MONTOS =====
                calcularMontos();
            }
           

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

    //========= SWAL ======
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    function obtenerProductos(){

        const lstProductos  =   [];
        const inputsCantidad = document.querySelectorAll('.inputCantidad');

        inputsCantidad.forEach((inputCantidad)=>{
            const producto      = formarProducto(inputCantidad);
            lstProductos.push(producto);
        })
        return lstProductos;

    }

    function agregarProducto(lstProductosValidados){

        //======== RECORRER TODOS LOS PRODUCTOS VALIDADOS ========
        lstProductosValidados.forEach((producto_validado)=>{

            //======= PROCESAR EN CASO EL PRODUCTO SEA VÁLIDO =======
            if(producto_validado.validacion){

                //======== OBTENER CANTIDAD DEL PRODUCTO ======
                const cantidad = producto_validado.cantidad ? producto_validado.cantidad : 0;

                //======== EN CASO LA CANTIDAD SEA MAYOR A 0 =======
                if(cantidad > 0){

                    const producto      = producto_validado;
                    const indiceExiste  = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                    //===== PRODUCTO NUEVO =====
                    if (indiceExiste == -1) {

                        const objProduct = {
                            producto_id:            producto.producto_id,
                            color_id:               producto.color_id,
                            modelo_nombre:          producto.modelo_nombre,
                            producto_nombre:        producto.producto_nombre,
                            producto_codigo:        producto.producto_codigo,
                            color_nombre:           producto.color_nombre,
                            precio_venta:           producto.precio_venta,
                            monto_descuento:        0,
                            porcentaje_descuento:   0,
                            precio_venta_nuevo:     0,
                            subtotal_nuevo:         0,
                            tallas: [{
                                        talla_id:           producto.talla_id,
                                        talla_nombre:       producto.talla_nombre,
                                        cantidad:           producto.cantidad
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

                }

                //========= EN CASO LA CANTIDAD SEA  0 =========
                if(cantidad == 0){

                    const producto              =   producto_validado;
                    const indiceProductoColor   =   carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

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
            

        })
            
     
    }

    //======= VALIDAR LA CANTIDAD INGRESADA POR EL USUARIO CON LA CANTIDAD ATENDIDA DEL PRODUCTO ========
    async function validarProductos(lstProductos){

        //========= VALIDACIÓN EN BACKEND ========
        try {
            mostrarAnimacionPedido();
            toastr.clear();
            const res   =   await axios.get(route('pedidos.pedido.validarCantidadAtendida',{
                pedido_id   :   @json($pedido->id),
                lstProductos: JSON.stringify(lstProductos)
            }))

            if(res.data.success){
                pintarErroresValidacion(res.data.lstErroresValidacion);
                toastr.info('VALIDACIÓN COMPLETADA');
                return res.data.lstProductosValidados;
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR AL VALIDAR LOS PRODUCTOS');
                return null;
            }

        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VALIDAR PRODUCTOS');
            return null;
        }finally{
            ocultarAnimacionPedido();
        }


    }

    function despintarErrorInputsCantidad() {
        const inputsCantidad = document.querySelectorAll('.inputCantidad');
        
        inputsCantidad.forEach(input => {
            input.style.border = ''; 
        });
    }


    function pintarErroresValidacion(lstErroresValidacion) {
        lstErroresValidacion.forEach((producto)=>{
           
            toastr.error(producto.mensaje,'',{
                closeButton: true,      
                timeOut: 0,             
                extendedTimeOut: 0,    
                tapToDismiss: false     
            });

            const inputCantidad =   document.querySelector(`#inputCantidad_${producto.producto_id}_${producto.color_id}_${producto.talla_id}`);
            if(inputCantidad){
                inputCantidad.style.border = '1.6px solid #FF9999';
            }   
        })
    }

    function mostrarAnimacionPedido(){
        document.querySelector('.overlay_pedido_edit').style.visibility   =   'visible';
    }

    function ocultarAnimacionPedido(){
        
        document.querySelector('.overlay_pedido_edit').style.visibility   =   'hidden';
    }

    //======== ELIMINAR ITEM ========
    const eliminarProducto = (lstProductosValidados)=>{
        let productosEliminados =   0;

        //========= RECORRER LOS PRODUCTOS VALIDADOS ===========
        lstProductosValidados.forEach((producto_validado)=>{

            //======= SI EL PRODUCTO ES VÁLIDO =======
            if(producto_validado.validacion){

                //========== ELIMINAR =========
                const indiceProducto    =   carrito.findIndex((producto)=>{
                    return producto.producto_id == producto_validado.producto_id && producto.color_id == producto_validado.color_id;
                })

                if(indiceProducto !== -1){
                    //========= OBTENIENDO TALLA ======
                    const indiceTalla   =   carrito[indiceProducto].tallas.findIndex((talla)=>{
                        return talla.talla_id == producto_validado.talla_id;
                    })

                    if(indiceTalla !== -1){
                        //======= ELIMINAR TALLA ========
                        carrito[indiceProducto].tallas.splice(indiceTalla,1);

                        productosEliminados ++;

                        //======= EN CASO EL PRODUCTO SE QUEDE SIN TALLAS, ELIMINAR PRODUCTO =======
                        if(carrito[indiceProducto].tallas.length === 0){
                            carrito.splice(indiceProducto,1);
                        }

                    }
                }
            }

        })
      
        return productosEliminados;
    }

    //========= VALIDAR FECHAS =========
    function validarDatosPedido() {
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

        if(carrito.length === 0){
            toastr.error('El detalle del pedido está vacío.', 'Error');
            enviar = false;
        }

        return enviar
    }

    //======= CARGAR PRODUCTOS ========
    function setProductosForm(){
        document.querySelector('#productos_tabla').value    =   JSON.stringify(carrito);
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

        subtotal    +=  noEditTotal;
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
        inputEmbalaje.value         =   embalaje.toFixed(2);
        inputEnvio.value            =   envio.toFixed(2);
        inputTotal.value            =   total.toFixed(2);
        inputSubTotal.value         =   subtotal.toFixed(2);
        inputMontoDescuento.value   =   descuento.toFixed(2);
    }

    //======== CALCULAR DESCUENTO ========
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


    //========= REORDENAR CARRITO =========
    const reordenarCarrito= ()=>{
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    function destruirDataTableStocks(){
        if(dataTableStocksPedido){
            dataTableStocksPedido.destroy();
            dataTableStocksPedido   =   null;
        }
    }

    function limpiarTableStocks(){
        const   tableStocksBody     =   document.querySelector('#table-stocks-pedidos tbody');

        while (tableStocksBody.firstChild) {
            tableStocksBody.removeChild(tableStocksBody.firstChild);
        }
    }

    //====== CARGAR EL PRECIO DE VENTA ELEGIDO PARA EL PRODUCTO EN EL CARRITO ======
    function loadPrecioVentaProductoCarrito(producto_id){
        const producto_elegido_id   =   producto_id;
        //===== LO BUSCAMOS EN EL CARRITO ======
        const indiceProducto    =   carrito.findIndex((p)=>{
            return p.producto_id    == producto_elegido_id;
        })

        if(indiceProducto !== -1){
            const itemProducto  =   carrito[indiceProducto];
            let targetValue;
            //==== UBICANDO PRECIO VENTA SELECCIONADO ======
            $('#precio_venta option').each(function() {
                if ($(this).text() == itemProducto.precio_venta) {
                    targetValue = $(this).val(); 
                    return false;
                }
            });
            
            if (targetValue) {
                $('#precio_venta').val(targetValue).trigger('change');
                console.log('precio venta fijado'); 
            } 
        }else{
            toastr.info('NO SE PUEDO FIJAR EL PRECIO DE VENTA PREVIO PARA EL PRODUCTO');
        }

    }

    //=========== FORMAR PRODUCTO =========
    const formarProducto = (ic)=>{
        const producto_id           = ic.getAttribute('data-producto-id');
        const modelo_nombre         = $('#modelo').find('option:selected').text().trim();
        const producto_nombre       = ic.getAttribute('data-producto-nombre');
        const producto_codigo       = ic.getAttribute('data-producto-codigo');
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

        const producto = {producto_id,producto_nombre,producto_codigo,modelo_nombre,
                            color_id,color_nombre,
                            talla_id,talla_nombre,
                            cantidad,precio_venta,
                            subtotal,subtotal_nuevo,porcentaje_descuento,monto_descuento,precio_venta_nuevo
                        };
        return producto;
    }


    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal=(productos)=>{
        let subtotal = 0;

        productos.forEach((p)=>{
            p.tallas.forEach((t)=>{
                    subtotal+= parseFloat(p.precio_venta)*parseFloat(t.cantidad);   
            })
               
            p.subtotal=subtotal; 
            subtotal=0; 
        })  
    }

    //========= PINTAR DETALLE PEDIDO =======
    function pintarDetallePedido(carrito){
        let fila= ``;
        let htmlTallas= ``;
        const bodyDetalleTable  =   document.querySelector('#table-detalle-pedido tbody');
        const tallas            =   @json($tallas);
        clearTabla(bodyDetalleTable);

        carrito.forEach((c)=>{
            htmlTallas=``;
                fila+= `<tr>   
                            <td>
                                <i class="fas fa-trash-alt btn btn-danger delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>                            
                            </td>
                            <th>
                                <div style="width:120px;">${c.producto_nombre}</div>    
                            </th>
                            <th>${c.color_nombre}</th>`;


                //tallas
                tallas.forEach((t)=>{
                    let cantidad = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });
                    cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad='';
                    htmlTallas += `<td>${cantidad}</td>`; 
                })


                htmlTallas+=`   <td style="text-align: right;">
                                    <div style="width:100px;">
                                        <span class="precio_venta_${c.producto_id}_${c.color_id}">
                                            ${c.porcentaje_descuento === 0? c.precio_venta:c.precio_venta_nuevo}
                                        </span>       
                                    </div>
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

                fila+=htmlTallas;
                bodyDetalleTable.innerHTML=fila;            
        })
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

    //===== LIMPIAR INPUTS DEL TABLERO PRODUCTOS ======
    function clearInputsCantidad(){
        const inputsCantidad    =   document.querySelectorAll('.inputCantidad');
        inputsCantidad.forEach((inputCantidad)=>{
            inputCantidad.value =   '';
        })  
    }

    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL CARRITO =========
    function loadCarrito(){
        
        carrito.forEach((c)=>{
            c.tallas.forEach((talla)=>{
                let llave   =   `#inputCantidad_${c.producto_id}_${c.color_id}_${talla.talla_id}`;   
                const inputLoad = document.querySelector(llave);
                console.log(inputLoad)
                if(inputLoad){
                    inputLoad.value = talla.cantidad;
                }
            })
        }) 

    }

    //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
    async function getColoresTallas(){
        mostrarAnimacionPedido();
        const producto_id   =   $('#producto').val();
        if(producto_id){
            try {
                const res   =   await   axios.get(route('pedidos.pedido.getColoresTallas',{producto_id}));
                if(res.data.success){
                    destruirDataTableStocks();
                    pintarTableStocks(res.data.producto_color_tallas);
                    loadDataTableStocksPedido();
                    pintarPreciosVenta(res.data.producto_color_tallas);
                    loadCarrito();
                    loadPrecioVentaProductoCarrito(producto_id);
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }finally{
                ocultarAnimacionPedido();
            }
        }else{
            destruirDataTableStocks();
            limpiarTableStocks();
            loadDataTableStocksPedido();
            limpiarSelectPreciosVenta();
            ocultarAnimacionPedido();
        }
    }

    //======== PINTAR SELECT PRODUCTOS =======
    function pintarSelectProductos(productos){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto').empty();
    
        if(productos.length === 0){
            ocultarAnimacionPedido();
        }
        
        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto').append(option);
        });

        // Refrescar Select2
        //$('#producto').val(null);
        $('#producto').trigger('change');
    }

    async function  getProductosByModelo(e){
        toastr.clear();
        mostrarAnimacionPedido();
        const bodyTableStocks   =   document.querySelector('#table-stocks-pedidos tbody');
        const btnAgregarDetalle =   document.querySelector('#btn_agregar_detalle');
        clearTabla(bodyTableStocks);

        const modelo_id                   =   e.value;
        btnAgregarDetalle.disabled  =   true;
        
        if(modelo_id){
            try {
                const res       =   await axios.get(route('pedidos.pedido.getProductosByModelo', modelo_id));
                if(res.data.success){
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS','OPERACIÓN COMPLETADA');
                }else{
                    ocultarAnimacionPedido();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                ocultarAnimacionPedido();
                toastr.error(error,'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            }
               
        }else{
            ocultarAnimacionPedido();
        }
    }

    
    //========== OBTENER PRODUCTOS POR MODELO =========
    /*async function getProductosByModelo(target){
        const   btnAgregarDetalle   =   document.querySelector('#btn_agregar_detalle')
        const modelo_id =   target.value;
        btnAgregarDetalle.disabled = true;

        if(modelo_id){
            try {
                const res       =   await axios.get(route('pedidos.pedido.getProductosByModelo', modelo_id));
                const productos =   res.data.message;
                console.log(productos);
                pintarTableProductos(productos);
                setCantidadesTablero();
            } catch (error) {
                    
            }
        }else{
            const bodyTablaProductos    =   document.querySelector('#table-stocks-pedidos tbody');
            clearTabla(bodyTablaProductos);
        }  
    }*/

    const pintarTableStocks = (producto)=>{
        let filas = ``;
        const   tableStocksBody     =   document.querySelector('#table-stocks-pedidos tbody');
        const   btnAgregarDetalle   =   document.querySelector('#btn_agregar_detalle')


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
        btnAgregarDetalle.disabled = false;

    }

    function loadDataTableStocksPedido(){
        dataTableStocksPedido =   new DataTable('#table-stocks-pedidos',{
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



    //======== PINTAR TABLA PRODUCTOS =========
    /*function pintarTableStocks(productos){
        let     options             =   ``;
        const   tallas              =   @json($tallas);
        const   tableStocksBody     =   document.querySelector('#table-stocks-pedidos tbody');
        const   btnAgregarDetalle   =   document.querySelector('#btn_agregar_detalle')

        productos.forEach((p)=>{
            options+=`  <tr>
                            <th scope="row" data-producto=${p.producto_id} data-color=${p.color_id} >
                                <div style="width:120px;">${p.producto_nombre}</div>
                            </th>
                            <th>${p.color_nombre}</th>
                        `;

            let htmlTallas = ``;

            tallas.forEach((t)=>{
                const stock = p.tallas.filter(pt => pt.talla_id == t.id)[0]?.stock_logico || 0;

                htmlTallas +=   `
                                    <td style="background-color: rgb(210, 242, 242);">
                                        <p style="margin:0;width:20px;text-align:center;">${stock}</p>    
                                    </td>
                                    <td width="8%">
                                        <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                        id="inputCantidad_${p.producto_id}_${p.color_id}_${t.id}" 
                                        data-modelo-nombre="${p.modelo_nombre}"
                                        data-producto-id="${p.producto_id}"
                                        data-producto-nombre="${p.producto_nombre}"
                                        data-producto-codigo="${p.producto_codigo}"
                                        data-color-id="${p.color_id}"
                                        data-color-nombre="${p.color_nombre}"
                                        data-talla-id="${t.id}"
                                        data-talla-nombre="${t.descripcion}"></input>    
                                    </td>
                                `;   
            })

            if(p.print_precios){
                htmlTallas+=`
                    <td>
                        <select style="width:100px;" class="select2_form form-control" id="precio-venta-${p.producto_id}">
                            <option>${p.precio_venta_1}</option>    
                            <option>${p.precio_venta_2}</option>    
                            <option>${p.precio_venta_3}</option>    
                        </select>
                    </td>`;
            }else{
                htmlTallas+=`<td></td>`;
            }

            htmlTallas += `</tr>`;
            options += htmlTallas;
        })

        tableStocksBody.innerHTML = options;
        btnAgregarDetalle.disabled = false;
    }*/

    //======== LIMPIAR TABLA PRODUCTOS ========
    function clearTabla(bodyTable){
        while (bodyTable.firstChild) {
            bodyTable.removeChild(bodyTable.firstChild);
        }
    }


    //=========== CARGAR PRODUCTOS PREVIOS =======
    const cargarProductosPrevios=()=>{
        //====== CARGANDO EMBALAJE Y ENVÍO PREVIO =======
        tfootEmbalaje.value     =   @json($pedido->monto_embalaje);
        tfootEnvio.value        =   @json($pedido->monto_envio);
        inputEmbalaje.value     =   @json($pedido->monto_embalaje);
        inputEnvio.value        =   @json($pedido->monto_envio);

        const productosPrevios  =   @json($pedido_detalles);
       
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
                            talla_id:           t.talla_id,
                            talla_nombre:       t.talla_nombre,
                            cantidad:           parseInt(t.cantidad),
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
        calcularSubTotal(carrito);
      
        //===== PINTANDO DETALLE ======
        pintarDetallePedido(carrito);
        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c)=>{
            calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
        })
        
        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();

    }

    function pintarDetalleNoEditable(productosNoEditables){
        const tbodyNoEditable   =   document.querySelector('#table-detalle-noeditable tbody');
        let fila    =   ``;
        let total   =   0;
        productosNoEditables.forEach((p)=>{
            fila    +=   `<tr>
                                <th scope="row">${p.producto_nombre}</th>
                                <td>${p.color_nombre}</td>
                                <td>${p.talla_nombre}</td>
                                <td>${p.cantidad}</td>
                                <td>${p.cantidad_atendida}</td>
                                <td>${p.cantidad_pendiente}</td>
                                <td>${p.precio_unitario_nuevo}</td>
                                <td>${p.importe_nuevo}</td>
                            </tr>`;
            total   +=  parseFloat(p.importe_nuevo);
        })
        tbodyNoEditable.innerHTML = fila;

        noEditTotal =   parseFloat(total);
        const formattedTotal = 'S/ ' + total.toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.querySelector('.total-pagar-noedit').textContent   =   formattedTotal;
    }

    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL CARRITO =========
    function setCantidadesTablero(){
        carrito.forEach((c)=>{
            c.tallas.forEach((t)=>{
                const inputLoad = document.querySelector(`#inputCantidad_${c.producto_id}_${c.color_id}_${t.talla_id}`);
                if(inputLoad){
                    inputLoad.value = t.cantidad;
                }
            })
            //==== UBICANDO PRECIOS DE VENTA SELECCIONADOS ======
            const selectPrecioVenta =   document.querySelector(`#precio-venta-${c.producto_id}`);
            if(selectPrecioVenta){
                selectPrecioVenta.value =   c.precio_venta;
            }
        }) 
    }

    function limpiarSelectPreciosVenta(){
        $('#precio_venta').empty();
        $('#precio_venta').trigger('change');
    }
   
    //============= ABRIR MODAL CLIENTE =============
    function openModalCliente(){
        $("#modal_cliente").modal("show");
    }

    //======= PINTAR TABLE PEDIDO ATENCIÓN HISTORIAL =======
    function pintarTablePedidoAtencionHistorial(){
        let filas               =   ``;
        const tbody             =   document.querySelector('#table_pedido_atencion_historial tbody');
        const pedido_detalles   =   @json($pedido_detalles);

        pedido_detalles.forEach((producto)=>{
            filas   +=  `<tr>
                            <th>${producto.producto_nombre}</th>
                            <th>${producto.color_nombre}</th>
                            <th>${producto.talla_nombre}</th>
                            <th>${producto.cantidad}</th>
                            <th>${producto.cantidad_atendida}</th>
                            <th>${producto.cantidad_pendiente}</th>
                            <th>${producto.cantidad_devuelta}</th>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

</script>
@endpush
