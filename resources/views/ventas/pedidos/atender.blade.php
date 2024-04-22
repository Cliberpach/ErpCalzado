@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('pedidos-active', 'active')


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
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            <form  method="POST" action="{{route('ventas.pedidos.store')}}"
                                id="form-pedido">
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
                                            <div class="col-12 col-md-4">
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

                                            <input hidden type="text" name="vendedor" value="{{$vendedor_actual}}">

                                        </div>
                                       
                                        <div class="row">
                                            <div class="col-6 col-md-6 select-required">
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
                                            <div class="col-3 col-md-3 select-required">
                                                <div class="form-group">
                                                    <label class="required">Condición</label>
                                                    <select id="condicion_id" name="condicion_id"
                                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($condiciones as $condicion)
                                                            <option value="{{ $condicion->id }}"
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
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label class="required">Tipo de Comprobante: </label>
                                                    <select name="tipo_venta" class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
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
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Detalle del Pedido</b></h4>
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
                                    <a href="{{ route('ventas.pedidos.index') }}" id="btn_cancelar"
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

    let carrito =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        cargarProductosPrevios();
        events();
    })


    function events(){

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
                        htmlTallas += `<td>${talla_data[0].cantidad_solicitada}</td>`; 
                        
                        htmlTallas += ` <td>
    
                                            <input ${talla_data[0].cantidad_atendida == 0?'readonly':''} class="form-control" value="${talla_data[0].cantidad_atendida}"></input>
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
                    subtotal+= parseFloat(p.precio_venta)*parseFloat(t.cantidad_atendida);   
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


    //======== LIMPIAR TABLA PRODUCTOS ========
    function clearTabla(bodyTable){
        while (bodyTable.firstChild) {
            bodyTable.removeChild(bodyTable.firstChild);
        }
    }
</script>
@endpush
