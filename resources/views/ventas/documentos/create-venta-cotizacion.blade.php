@extends('layout')
@section('content')
@include('ventas.documentos.modal-envio')

@section('ventas-active', 'active')
@section('documento-active', 'active')
<style>
    .toastr-morado {
        background-color: #652e9b !important; /* Color morado */
    }
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVO DOCUMENTO DE VENTA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.documento.index') }}">Documentos de Venta</a>
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
                    <input type="hidden" id='asegurarCierre'>
                    @isset($dolar)
                    <input type="hidden" id='dolar' value="{{$dolar}}">
                    @endisset
                    <form action="" method="POST" id="enviar_documento">
                        {{ csrf_field() }}

                        @if (!empty($cotizacion))
                            <input type="hidden" name="cotizacion_id" value="{{ $cotizacion->id }}">
                            <input type="hidden" name="data_envio" id="data_envio">
                        @endif
                        <div class="row">
                            <div class="col-12 col-md-6 b-r">
                                <div class="row">
                                    <div class="col-12 col-md-6" id="fecha_documento">
                                        <div class="form-group">
                                            <label class="">Fecha de Documento</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                @if (!empty($cotizacion))
                                                    <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                                        class="form-control {{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_documento_campo', $fecha_hoy) }}"
                                                        autocomplete="off" required readonly>
                                                @else
                                                    <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                                        class="form-control input-required{{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_documento_campo', $fecha_hoy) }}" autocomplete="off"
                                                        required>
                                                @endif

                                                @if ($errors->has('fecha_documento_campo'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_documento_campo') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            <label class="required">Tipo de Comprobante: </label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{ old('tipo_venta') }}"
                                                name="tipo_venta" id="tipo_venta" required @if (!empty($cotizacion)) '' @else onchange="consultarSeguntipo()" @endif>
                                                <option></option>

                                                @foreach (tipos_venta() as $tipo)
                                                    @if (ifComprobanteSeleccionado($tipo->id) && ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS'))
                                                        <option value="{{ $tipo->id }}" @if (old('tipo_venta') == $tipo->id || $tipo->id == 129) {{ 'selected' }} @endif>
                                                            {{ $tipo->nombre }}
                                                        </option>
                                                    @endif
                                                @endforeach

                                                @if ($errors->has('tipo_venta'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('tipo_venta') }}</strong>
                                                    </span>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6" id="fecha_entrega">
                                        <div class="form-group d-none">
                                            <label class="">Fecha de Atención</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>

                                                @if (!empty($cotizacion))
                                                    <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                                        class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_atencion', $cotizacion->fecha_atencion) }}"
                                                        autocomplete="off" readonly disabled>
                                                @else

                                                    <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                                        class="form-control input-required {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_atencion', $fecha_hoy) }}" autocomplete="off" required
                                                        readonly disabled>

                                                @endif

                                                @if ($errors->has('fecha_atencion'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                    </span>
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="form-group d-none">
                                            <label>Placa</label>
                                            <input type="text" type="text" placeholder=""
                                            class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                            name="observacion" id="observacion" onkeyup="return mayus(this)"
                                            value="{{ old('observacion') }}" maxlength="7">
                                            @if ($errors->has('observacion'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('observacion') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-12 col-md-6 select-required d-none">
                                        <div class="form-group">
                                            <label>Moneda:</label>
                                            <select id="moneda" name="moneda"
                                                class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                disabled>
                                                <option selected>SOLES</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">

                                <div class="row  d-none">
                                    <div class="col-12">
                                        <div class="form-group select-required">
                                            <label class="required">Empresa: </label>

                                            @if (!empty($cotizacion))
                                                <select
                                                    class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%"
                                                    value="{{ old('empresa_id', $cotizacion->empresa_id) }}" name="empresa_id" id="empresa_id"
                                                    disabled>
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{ $empresa->id }}" @if (old('empresa_id', $cotizacion->empresa_id) == $empresa->id){{ 'selected' }}@endif {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%" value="{{ old('empresa_id') }}" name="empresa_id"
                                                    id="empresa_id" required onchange="obtenerTiposComprobantes(this)" disabled>
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{ $empresa->id }}" @if (old('empresa_id') == $empresa->id)
                                                            {{ 'selected' }}
                                                    @endif
                                                    {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            @if (!empty($cotizacion))
                                            <label class="required">Condición</label>
                                            <select id="condicion_id" name="condicion_id"
                                                class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                required onchange="changeFormaPago()" disabled>
                                                <option></option>
                                                @foreach ($condiciones as $condicion)
                                                    <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                                        {{ old('condicion_id') == $condicion->id.'-'.$condicion->descripcion || $condicion->id == $cotizacion->condicion_id ? 'selected' : '' }}
                                                        data-dias="{{$condicion->dias}}">
                                                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <label class="required">Condición</label>
                                            <select id="condicion_id" name="condicion_id"
                                                class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                required onchange="changeFormaPago()">
                                                <option></option>
                                                @foreach ($condiciones as $condicion)
                                                    <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                                        {{ old('condicion_id') == $condicion->id.'-'.$condicion->descripcion || $condicion->descripcion == 'CONTADO' ? 'selected' : '' }}
                                                        data-dias="{{$condicion->dias}}">
                                                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6" id="fecha_vencimiento">
                                        <div class="form-group">
                                            <label class="required">Fecha de Vencimiento</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="date" id="fecha_vencimiento_campo" name="fecha_vencimiento_campo"
                                                    class="form-control input-required" autocomplete="off"
                                                    {{ $errors->has('fecha_vencimiento_campo') ? ' is-invalid' : '' }}
                                                    value="{{ old('fecha_vencimiento_campo', $fecha_hoy) }}" required>
                                                @if ($errors->has('fecha_vencimiento_campo'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_vencimiento_campo') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            <label class="required">Cliente: @if (empty($cotizacion))<button type="button" class="btn btn-outline btn-primary" onclick="modalCliente()">Registrar</button>@endif</label>
                                            <input type="hidden" name="tipo_cliente_documento" id="tipo_cliente_documento">
                                            <input type="hidden" name="tipo_cliente_2" id="tipo_cliente_2" value='1'>
                                            @if (!empty($cotizacion))
                                                <select
                                                    class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%"
                                                    value="{{ old('cliente_id', $cotizacion->cliente_id) }}" name="cliente_id" id="cliente_id"
                                                    disabled>
                                                    <option></option>
                                                    @foreach ($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}" @if (old('cliente_id', $cotizacion->cliente_id) == $cliente->id){{ 'selected' }}@endif tabladetalle="{{$cliente->tabladetalles_id}}">{{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%" value="{{ old('cliente_id') }}" name="cliente_id"
                                                    id="cliente_id" required onchange="obtenerTipocliente(this.value)"> <!-- disabled -->
                                                    <option></option>
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> <input type="checkbox" class="i-checks" name="envio_sunat" id="envio_sunat" value="1"> <b class="text-danger">Enviar a Sunat</b> </label>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="row d-none">
                                    <div class="col-12">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                </div>


                                <input type="checkbox" id="igv_check" name="igv_check" class="d-none" checked>
                                <!-- OBTENER TIPO DE CLIENTE -->
                                <input type="hidden" class="form-control" name="" id="tipo_cliente">
                                <!-- OBTENER DATOS DEL PRODUCTO -->
                                <input type="hidden" class="form-control" name="" id="presentacion_producto">
                                <input type="hidden" class="form-control" name="" id="codigo_nombre_producto">
                                <!-- LLENAR DATOS EN UN ARRAY -->
                                <input type="hidden" class="form-control" id="productos_tabla" name="productos_tabla">
                                <!-- TIPO PAGO -->
                                <input type="hidden" class="form-control" name="tipo_pago_id" id="tipo_pago_id">
                                <!-- EFECTIVO -->
                                <input type="hidden" class="form-control" name="efectivo" id="efectivo_form">
                                <!-- IMPORTE -->
                                <input type="hidden" class="form-control" name="importe" id="importe_form">

                            </div>

                        </div>

                        @if(!empty($cotizacion))
                            {{-- <input type="hidden" name="igv" id="igv" value="{{ $cotizacion->igv }}"> --}}
                            <input type="hidden" name="igv" id="igv" value="18">
                        @else
                            <input type="hidden" name="igv" id="igv" value="18">
                        @endif


                        <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ $cotizacion->sub_total }}">
                        <input type="hidden" name="monto_embalaje" id="monto_embalaje" value="{{ $cotizacion->monto_embalaje }}">
                        <input type="hidden" name="monto_envio" id="monto_envio" value="{{ $cotizacion->monto_envio }}">
                        <input type="hidden" name="monto_total" id="monto_total" value="{{ $cotizacion->total}}">
                        <input type="hidden" name="monto_descuento" id="monto_descuento" value="{{ $cotizacion->monto_descuento }}">
                        <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ $cotizacion->total_igv}}">
                        <input type="hidden" name="monto_total_pagar" id="monto_total_pagar" value="{{ $cotizacion->total_pagar }}">


                        <input type="hidden" name="cot_doc" id="cot_doc" value="SI">


                    </form>
                    <hr>
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle del Documento de Venta</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="sk-spinner sk-spinner-wave">
                                        <div class="sk-rect1"></div>
                                        <div class="sk-rect2"></div>
                                        <div class="sk-rect3"></div>
                                        <div class="sk-rect4"></div>
                                        <div class="sk-rect5"></div>
                                    </div>
                                    @if (empty($cotizacion))
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="col-form-label required">Producto:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="producto_lote" readonly>
                                                    <span class="input-group-append">
                                                        <button type="button" class="btn btn-primary" disabled id="buscarLotes"
                                                            data-toggle="modal" data-target="#modal_lote"><i
                                                                class='fa fa-search'></i> Buscar
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                                                </div>
                                            </div>

                                            <input type="hidden" name="producto_id" id="producto_id">
                                            <input type="hidden" name="producto_unidad" id="producto_unidad">
                                            <input type="hidden" name="producto_json" id="producto_json">

                                            <div class="col-lg-2 col-xs-12">

                                                <label class="col-form-label required">Cantidad:</label>
                                                <input type="text" name="cantidad"  id="cantidad" class="form-control" onkeypress="return filterFloat(event, this, false);" onkeydown="nextFocus(event,'precio')" disabled>
                                                <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-xs-12">
                                                <div class="form-group">
                                                    <label class="col-form-label required" for="amount">Precio:</label>
                                                    <input type="number" id="precio" name="precio" class="form-control" onkeydown="nextFocus(event,'btn_agregar_detalle')" disabled>
                                                    <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-lg-2 col-xs-12">

                                                <div class="form-group">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <button type=button class="btn btn-block btn-warning" style='color:white;'
                                                        id="btn_agregar_detalle" disabled> <i class="fa fa-plus"></i>
                                                        AGREGAR</button>
                                                </div>

                                            </div>



                                        </div>
                                        <hr>
                                    @endif


                                    @include('ventas.documentos.table-detalle-cvc')

                                     {{-- <div class="table-responsive">
                                        <table
                                            class="table dataTables-detalle-documento table-striped table-bordered table-hover"
                                            style="text-transform:uppercase">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center"><i class="fa fa-dashboard"></i></th>
                                                    <th class="text-center">CANT</th>
                                                    <th class="text-center">PRODUCTO</th>
                                                    <th class="text-center">P. UNITARIO</th>
                                                    <th class="text-center">IMPORTE</th>

                                                     <th></th>
                                                    <th class="text-center"><i class="fa fa-dashboard"></i></th>
                                                    <th class="text-center">CANT</th>
                                                    <th class="text-center">UM</th>
                                                    <th class="text-center">PRODUCTO</th>
                                                    <th class="text-center">V. UNITARIO</th>
                                                    <th class="text-center">P. UNITARIO</th>
                                                    <th class="text-center">DESCUENTO</th>
                                                    <th class="text-center">P. NUEVO</th>
                                                    <th class="text-center">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                             <tfoot>
                                                <tr>
                                                    <th class="text-right" colspan="10"></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="9">Sub Total:</th>
                                                    <th class="text-center"><span
                                                            id="subtotal">@if (!empty($cotizacion)) {{ $cotizacion->sub_total }} @else 0.0 @endif</span></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="9">IGV <span id="igv_int"></span>:</th>
                                                    <th class="text-center"><span
                                                            id="igv_monto">@if (!empty($cotizacion)) {{ $cotizacion->total_igv }} @else 0.0 @endif</span></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="9">TOTAL:</th>
                                                    <th class="text-center"><span id="total">@if (!empty($cotizacion)) {{ $cotizacion->total }} @else 0.0 @endif</span>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>  --}}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group row">

                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                (<label class="required"></label>) son obligatorios.</small>
                        </div>

                        <div class="col-md-6 text-right">

                            <a href="javascript:void(0)" onclick="regresarClick(event)"  id="btn_cancelar" class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            @if($cantidadErrores==0)
                                <button form="enviar_documento" type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            @endif
                            {{-- @if (empty($errores))
                                <button form="enviar_documento" type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            @else
                                @if (count($errores) == 0)
                                    <button form="enviar_documento" type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                @endif
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@stop
@push('styles')

<style>
    .my-swal {
        z-index: 3000 !important;
    }

</style>

@endpush

@push('scripts')

<script>
    const productosPrevios           =   @json($detalle);
    const tallas            =   @json($tallas);
    const cotizacion        =   @json($cotizacion);
    const cantidadErrores   =   @json($cantidadErrores);

    const tableDetalleBody  =   document.querySelector('#table-detalle tbody');
    const tableDetalleFoot  =   document.querySelector('#table-detalle tfoot');

    const tableSubtotal     =   document.querySelector('.subtotal');
    const tableTotal        =   document.querySelector('.total');
    const tableIgv          =   document.querySelector('.igv');

    const inputSubTotal         =   document.querySelector('#monto_sub_total');
    const inputEmbalaje         =   document.querySelector('#monto_embalaje');
    const inputEnvio            =   document.querySelector('#monto_envio');
    const inputTotal            =   document.querySelector('#monto_total');
    const inputIgv              =   document.querySelector('#monto_total_igv');
    const inputTotalPagar       =   document.querySelector('#monto_total_pagar');

    const tfootSubtotal         =   document.querySelector('.subtotal');
    const tfootEmbalaje         =   document.querySelector('.embalaje');
    const tfootEnvio            =   document.querySelector('.envio');
    const tfootTotal            =   document.querySelector('.total');
    const tfootIgv              =   document.querySelector('.igv');
    const tfootTotalPagar       =   document.querySelector('.total-pagar');

    const formDocumento     =   document.querySelector('#enviar_documento');
    const btnRegresar       =   document.querySelector('#btn_cancelar');
    const btnGrabar         =   document.querySelector('#btn_grabar');

    let clientes_global;
    let carrito             =   [];
    let carritoFormateado   =   [];
    let asegurarCierre      =   2;

    document.addEventListener('DOMContentLoaded',async()=>{
        cargarProductosPrevios();
        setAsegurarCierre();
        getClientes();
        cargarChecks();
        cargarSelect2();
        showAlertas();
        formatearDetalle();

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

        //======== evitando doble click en regresar =========
        btnRegresar.addEventListener('click',()=>{
            btnRegresar.disabled = true;
        })

        formDocumento.addEventListener('submit',(e)=>{
            e.preventDefault();
            btnGrabar.disabled = true;
            cargarProductos();
            let correcto = validarCampos();

            // inputSubTotal.value     =   tfootSubtotal.textContent;
            // inputEmbalaje.value     =   tfootEmbalaje.value;
            // inputEnvio.value        =   tfootEnvio.value;
            // inputTotal.value        =   tfootTotal.textContent;
            // inputIgv.value          =   tfootIgv.textContent;
            // inputTotalPagar.value   =   tfootTotalPagar.textContent;

            if (correcto) {
                let total = $('#monto_total_pagar').val();
                $('#monto_venta').val(total);
                $('#importe_venta').val(total);
                let condicion_id = $('#condicion_id').val();
                let cadena = condicion_id.split('-');
                if(cadena[1] != 'CONTADO')
                {
                    $('#importe_form').val(0.00);
                    $('#efectivo_form').val(0.00);
                    $('#tipo_pago_id').val('');
                    enviarVenta();
                }
                else
                {
                    $('#importe_form').val(0.00);
                    $('#efectivo_form').val(0.00);
                    $('#tipo_pago_id').val('');
                    enviarVenta();
                }
            }else{
                btnGrabar.disabled = false;
            }
            console.log(correcto);

            // const formData = new FormData(e.target);
            // const formObject = {};
            // formData.forEach((value, key) => {
            //     formObject[key] = value;
            // });

            // console.log(formObject);
        })


        //=========== MODAL DESPACHO =========
        document.querySelector('#btn-envio').addEventListener('click',()=>{
            //======= COLCANDO EN MODAL ENVIO EL NOMBRE DEL CLIENTE =======
            const cliente_nombre            =   $("#cliente_id").find('option:selected').text();

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

    function setAsegurarCierre(){
        //======= SI NO HAY ERRORES DEVUELVE  STOCKS LOGICOS =======
        if(cantidadErrores == 0){
            asegurarCierre  =   1;
        }else{
            asegurarCierre  =   2;
        }
    }

    //===== SHOW ALERTAS ============
    function showAlertas(){
        productosPrevios.forEach((c)=>{
            if(c.tipo == "NO EXISTE EL PRODUCTO COLOR TALLA"){
                toastr.error(`${c.producto_nombre} - ${c.color_nombre} - ${c.talla_nombre}`, 'No existe el producto', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    toastClass: 'toastr-morado'
                });
            }
            if(c.tipo == "STOCK LOGICO INSUFICIENTE"){
                toastr.error(`${c.producto_nombre} - ${c.color_nombre} - ${c.talla_nombre}`, 'Stock lógico insuficiente', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                });
            }
        })
    }

    //======= EVITAR DOBLE CLICK EN REGRESAR =========
    function regresarClick(event){
            event.preventDefault();
            var btnCancelar = document.getElementById("btn_cancelar");
            if (!btnCancelar.classList.contains("disabled")) {
                btnCancelar.classList.add("disabled");
                window.location.href = '{{ route('ventas.cotizacion.index') }}';
            }
    }

    //========== VALIDAR TIPO ===============
    function validarTipo() {
        var enviar = true

        if ($('#tipo_cliente_documento').val() == '0' && $('#tipo_venta').val() == 'FACTURA') {
            toastr.error('El tipo de documento del cliente es diferente a RUC.', 'Error');
            enviar = false;
        }
        return enviar
    }

    //============ ENVIAR VENTA ===================
    async function enviarVenta() {
        try {

            let response    = await axios.get("{{ route('Caja.movimiento.verificarestado') }}");
            let data        = response.data;

            if (!data.success) {
                toastr.error(data.mensaje);
                btnGrabar.disabled = false;
                return;
            }

            let envio_ok = true;
            let tipo = validarTipo();

            if (tipo) {
                cargarProductos();

                // Habilitar campos del formulario
                document.getElementById("moneda").disabled = false;
                document.getElementById("observacion").disabled = false;
                document.getElementById("fecha_documento_campo").disabled = false;
                document.getElementById("fecha_atencion_campo").disabled = false;
                document.getElementById("empresa_id").disabled = false;
                document.getElementById("cliente_id").disabled = false;
                document.getElementById("condicion_id").disabled = false;

                // Evitar devolución de stocks
                asegurarCierre = 2;
                console.log(asegurarCierre);
            } else {
                envio_ok = false;
            }

            if (!envio_ok) return;

            let formData = new FormData(formDocumento);
            let datos = object;

            let url = '{{ route("ventas.documento.store") }}';

            let textAlert = "¿Seguro que desea guardar cambios?";

            let result = await Swal.fire({
                title: textAlert,
                text: "Se generará el documento de venta!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, genéralo!"
            });

            if (!result.isConfirmed) {
                asegurarCierre = 1;
                btnGrabar.disabled = false;
                return;
            }

            console.log('Redireccionando a store document');

            // Enviar datos con Fetch
            let responseFetch = await fetch(url, {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            if (!responseFetch.ok) throw new Error(responseFetch.statusText);

            let resultData = await responseFetch.json();
            console.log(resultData);

            if (resultData.errors) {
                let mensaje = sHtmlErrores("result.value.data.mensajes");
                toastr.error(mensaje);
                asegurarCierre = 1;
                document.getElementById("moneda").disabled = true;
                document.getElementById("observacion").disabled = true;
                document.getElementById("fecha_documento_campo").disabled = true;
                document.getElementById("fecha_atencion_campo").disabled = true;
                document.getElementById("empresa_id").disabled = true;
                document.getElementById("cliente_id").disabled = true;
                return;
            }

            if (resultData.success) {
                toastr.success('¡Documento de venta creado!', 'Éxito');

                let id = resultData.documento_id;
                let url_open_pdf = '{{ route("ventas.documento.comprobante", [":id1", ":size"]) }}'
                    .replace(':id1', id)
                    .replace(':size', 80);

                asegurarCierre = 2;

                window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');
                location = "{{ route('ventas.documento.index') }}";
            } else {
                toastr.error(resultData.mensaje, 'ERROR AL GENERAR EL DOC DE VENTA');
                asegurarCierre = 1;
            }
        } catch (error) {
            console.error("Error:", error);
            alert('Ocurrió un error inesperado.');
            asegurarCierre = 1;
        }
    }


    //========== LIBRERIA SELECT 2 =============
    const cargarSelect2 =   ()=>{
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
        });
    }

    //============== CARGAR CHECKS ============
    const cargarChecks = ()=>{
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    }

    //============= GET CLIENTES ==========
    function getClientes(){
        @if(empty($cotizacion))
            obtenerClientes();
        @else
            $.ajax({
                dataType: 'json',
                url: '{{ route('ventas.customers_all') }}',
                type: 'post',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'tipo_id': $('#tipo_venta').val()
                },
                success: function(data) {
                    clientes_global = data.clientes;
                },
            })
        @endif
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
                producto.precio_unitario        =   d.precio_venta;
                producto.porcentaje_descuento   =   d.porcentaje_descuento;
                producto.precio_unitario_nuevo  =   d.precio_venta_nuevo;
                carritoFormateado.push(producto);
            })
        })
    }

    //==========    CARGAR PRODUCTOS AL FORM   =================
    function cargarProductos() {
        carrito.forEach((c)=>{
            c.cantidad = c.cantidad_solicitada;
        })
        $('#productos_tabla').val(JSON.stringify(carritoFormateado));

    }

    //========== VALIDAR CAMPOS ======================
    function validarCampos() {
        let correcto = true;
        const moneda  =   document.querySelector('#moneda').value;
        const observacion   =   document.querySelector('#observacion').value;
        const condicion_id  =   document.querySelector('#condicion_id').value;
        const fecha_documento_campo     =   document.querySelector('#fecha_documento_campo').value;
        const fecha_atencion_campo      =   document.querySelector('#fecha_atencion_campo').value;
        const fecha_vencimiento_campo   =   document.querySelector('#fecha_vencimiento_campo').value;

        const empresa_id            =   document.querySelector('#empresa_id').value;
        const cliente_id            =   document.querySelector('#cliente_id').value;
        const tipo_venta            =   document.querySelector('#tipo_venta').value;
        const detalles              =   document.querySelector('#productos_tabla').value;

        const detalles_convertido   =   JSON.parse(detalles);
        if(detalles_convertido.length<1){
            correcto    =   false;
            toastr.error('El documento de venta debe tener almenos un producto vendido.');
        }
        if (moneda == null || moneda == '') {
            correcto = false;
            toastr.error('El campo moneda es requerido.');
        }
        if (condicion_id == null || condicion_id == '') {
             correcto = false;
             toastr.error('El campo condicion de pago es requerido.');
         }
        if (fecha_documento_campo == null || fecha_documento_campo == '') {
             correcto = false;
             toastr.error('El campo fecha de documento es requerido.');
         }
        if (fecha_atencion_campo == null || fecha_atencion_campo == '') {
             correcto = false;
             toastr.error('El campo fecha de atención es requerido.');
         }
        if (fecha_vencimiento_campo == null || fecha_vencimiento_campo == '') {
             correcto = false;
             toastr.error('El campo fecha de vencimiento es requerido.');
        }

        const campos ={moneda,observacion,condicion_id,fecha_documento_campo,fecha_atencion_campo,
        fecha_vencimiento_campo,empresa_id,cliente_id,tipo_venta};
        console.log(campos);

        if(clientes_global.length > 0)
        {
             let index = clientes_global.findIndex(cliente => cliente.id == cliente_id);
             if(index != undefined)
             {
                 let cliente = clientes_global[index];
                 if(cliente != undefined)
                 {
                     if(convertFloat(tipo_venta) === 127 && cliente.tipo_documento != 'RUC')
                     {
                         correcto = false;
                         toastr.error('El tipo de comprobante seleccionado requiere que el cliente tenga RUC.');
                     }

                     if(convertFloat(tipo_venta) === 128 && cliente.tipo_documento != 'DNI')
                     {
                         correcto = false;
                         toastr.error('El tipo de comprobante seleccionado requiere que el cliente tenga DNI.');
                     }
                 }
                 else{
                     correcto = false;
                     toastr.error('Ocurrió un error porfavor seleccionar nuevamente un cliente.');
                 }
             }
             else{
                 correcto = false;
                 toastr.error('Ocurrió un error porfavor seleccionar nuevamente un cliente.');
             }
        }
        else{
             correcto = false;
             toastr.error('Ocurrió un error porfavor recargar la pagina.');
        }

        //validación de fechas...
        const fechaDocumento    =   new Date(fecha_documento_campo);
        const fechaVencimiento  =   new Date(fecha_vencimiento_campo);


        if (fecha_documento_campo > fecha_vencimiento_campo) {
              correcto = false;
              toastr.error('El campo fecha de vencimiento debe ser mayor a la fecha de atención.');
        }

        if (empresa_id == null || empresa_id == '') {
             correcto = false;
             toastr.error('El campo empresa es requerido.');
        }
        if (cliente_id == null || cliente_id == '') {
             correcto = false;
             toastr.error('El campo cliente es requerido.');
        }
        if (tipo_venta == null || tipo_venta == '') {
             correcto = false;
             toastr.error('El campo tipo de venta es requerido.');
        }

        return correcto;
    }


    //=================== PINTAR MONTOS ==============
    const pintarMontos = ()=>{
        tfootSubtotal.textContent   =   cotizacion.sub_total;
        tfootEmbalaje.value         =   cotizacion.monto_embalaje;
        tfootEnvio.value            =   cotizacion.monto_envio;
        tfootTotal.textContent      =   cotizacion.total;
        tfootIgv.textContent        =   cotizacion.total_igv;
        tfootTotalPagar.textContent =   cotizacion.total_pagar;
    }

    //================== REORDENAR CARRITO ==================
    const reordenarCarrito= ()=>{
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    //=============== CALCULAR SUBTOTAL POR PRODUCTO-COLOR ======================
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


    function clearDetalleTable(){
        while (tableDetalleBody.firstChild) {
            tableDetalleBody.removeChild(tableDetalleBody.firstChild);
        }
    }

    //======  CARGAR PRODUCTOS AL CARRITO EN FORMATO ANIDADO =======
    const cargarProductosPrevios=()=>{
        //====== CARGANDO CARRITO ======
        const producto_color_procesados = [];

        productosPrevios.forEach((productoPrevio)=>{
            const id    =   `${productoPrevio.producto_id}-${productoPrevio.color_id}`;

            if(!producto_color_procesados.includes(id)){
                const producto ={
                    producto_id: productoPrevio.producto_id,
                    producto_nombre:productoPrevio.producto_nombre,
                    color_id:productoPrevio.color_id,
                    color_nombre:productoPrevio.color_nombre,
                    precio_venta:productoPrevio.precio_unitario,
                    subtotal:0,
                    subtotal_nuevo:0,
                    porcentaje_descuento: parseFloat(productoPrevio.porcentaje_descuento),
                    monto_descuento:0,
                    precio_venta_nuevo:0,
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
                            talla_id:t.talla_id,
                            talla_nombre:t.talla_nombre,
                            cantidad: parseInt(t.cantidad_solicitada),
                            tipo:t.tipo,
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
        reordenarCarrito();
        //===== PINTANDO DETALLE ======
        pintarDetalleCotizacion(carrito);
        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c)=>{
            calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
        })


    }


        //======= CALCULAR DESCUENTO ========
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

     //====== PINTAR DETALLE COTIZACIÓN ======
     function pintarDetalleCotizacion(carrito){
        let fila= ``;
        let htmlTallas= ``;

        carrito.forEach((c)=>{
            htmlTallas=``;
                fila+= `<tr>

                            <th>${c.producto_nombre} - ${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t)=>{
                    let talla_item = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });

                    const cantidad = talla_item.length>0?talla_item[0].cantidad:0;
                    const validacion = talla_item.length>0?talla_item[0].tipo:'';

                    htmlTallas +=   `<td>
                                        <span style="${validacion === 'STOCK LOGICO INSUFICIENTE' ? 'color: #ff6666; font-weight: bold;' : (validacion === 'NO EXISTE EL PRODUCTO COLOR TALLA' ? 'color: #9966cc; font-weight: bold;' : (validacion === 'STOCK LOGICO VÁLIDO' ? 'color: black; font-weight: bold;' : ''))}">
                                            ${cantidad}
                                        </span>
                                    </td>
                                    `;
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
                                    <input readonly data-producto-id="${c.producto_id}" data-color-id="${c.color_id}"
                                    style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                                    class="form-control detailDescuento"></input>
                                </td>
                            </tr>`;

                fila+=htmlTallas;
                tableDetalleBody.innerHTML=fila;
        })
    }

    //============= devolver stock logico, ya que hay errores en la cotización ===================
    window.addEventListener('beforeunload', async () => {
        if (asegurarCierre == 1) {
            await this.DevolverCantidades();
            asegurarCierre = 2;
        } else {
            console.log("beforeunload", asegurarCierre);
        }
    });

    //================ devolver cantidades ===============
    async function DevolverCantidades() {
            await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                 carrito: JSON.stringify(carrito)
            });
    }



    @if (!empty($errores))
        // $('#asegurarCierre').val(1);
        asegurarCierre = 1;
        @foreach ($errores as $error)
            @if ($error->tipo == 'stocklogico')
                toastr.error('La cantidad solicitada {{ $error->cantidad }} excede al stock del producto {{ $error->producto }}', 'Error', {
                    timeOut: 0,
                    extendedTimeOut: 0
                });
            @elseif($error->tipo == 'producto_no_existe')
                toastr.error('No existe stock para el producto: {{ $error->producto }}', 'Error',{
                    timeOut: 0,
                    extendedTimeOut: 0
                });
            @endif
        @endforeach
    @endif


</script>


@endpush
