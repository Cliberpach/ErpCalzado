@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')

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
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> <input type="checkbox" class="i-checks" name="envio_sunat" id="envio_sunat" value="1"> <b class="text-danger">Enviar a Sunat</b> </label>
                                        </div>
                                    </div>
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
                            <input type="hidden" name="igv" id="igv" value="{{ $cotizacion->igv }}">
                        @else
                            <input type="hidden" name="igv" id="igv" value="18">
                        @endif

                        <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
                        <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
                        <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}">


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


                                    @include('ventas.documentos.table-detalle',[
                                        'carrito' => 'carrito'
                                    ])

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

                            <a href="{{ route('ventas.documento.index') }}" id="btn_cancelar" class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            @if (empty($errores))
                                <button form="enviar_documento" type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            @else
                                @if (count($errores) == 0)
                                    <button form="enviar_documento" type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('ventas.documentos.modal')
@include('ventas.documentos.modalLote')
@include('ventas.documentos.modalCliente')
@include('ventas.documentos.modalCodigo')
@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/iCheck/custom.css' )}}" rel="stylesheet">
<style>
    .my-swal {
        z-index: 3000 !important;
    }

</style>

@endpush

@push('scripts')
<!-- Data picker -->
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!-- Date range use moment.js same as full calendar plugin -->
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<!-- Date range picker -->
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    let   carrito           =   @json($detalle);
    const tallas            =   @json($tallas);
    const cotizacion        =   @json($cotizacion);
    const erroresJS         =   @json($errores);           
    const tableDetalleBody  =   document.querySelector('#table-detalle tbody');
    const tableDetalleFoot  =   document.querySelector('#table-detalle tfoot');
    const tableSubtotal     =   document.querySelector('.subtotal');
    const tableTotal        =   document.querySelector('.total');
    const tableIgv          =   document.querySelector('.igv');
    const formDocumento         =   document.querySelector('#enviar_documento');
    let clientes_global;
    let carritoFormateado   =   [];

    document.addEventListener('DOMContentLoaded',()=>{
        console.log(carrito);
        $('#asegurarCierre').val(1)
        formataearCarrito();
        getClientes();
        cargarChecks();
        cargarSelect2();
        calcularSubTotal();
        reordenarCarrito(carrito);
        pintarDetalleCotizacion(carrito);
        pintarMontos();
        events();
        console.log(carritoFormateado);
    })


    function events(){
        formDocumento.addEventListener('submit',(e)=>{
            e.preventDefault();
            cargarProductos();
            let correcto = validarCampos();

            document.querySelector('#monto_sub_total').value    =   tableDetalleFoot.querySelector('.subtotal').textContent;   
            document.querySelector('#monto_total_igv').value    =   tableDetalleFoot.querySelector('.igv').textContent;   
            document.querySelector('#monto_total').value        =   tableDetalleFoot.querySelector('.total').textContent;   

            if (correcto) {
                let total = $('#monto_total').val();
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
            }
            console.log(correcto);

            // const formData = new FormData(e.target);
            // const formObject = {};
            // formData.forEach((value, key) => {
            //     formObject[key] = value;
            // });

            // console.log(formObject);
        })
    }

    function formataearCarrito(){
        const producto_color_procesados =   [];
        carrito.forEach((p)=>{
            const llave =   `${p.producto_id}-${p.color_id}`;
            if(!producto_color_procesados.includes(llave)){
                const producto  =   {
                    producto_id:p.producto_id,
                    color_id   :p.color_id,
                    producto_nombre:p.producto_nombre,
                    color_nombre:p.color_nombre
                }
                const tallas_producto   =   carrito.filter((c)=>{
                   return c.producto_id==p.producto_id && c.color_id==p.color_id;
                })
                const tallas = [];
                tallas_producto.forEach((t)=>{
                    const talla={
                        talla_id:t.talla_id,
                        cantidad:t.cantidad
                    }
                    tallas.push(talla);
                })
                producto.tallas=tallas;
                carritoFormateado.push(producto);
                producto_color_procesados.push(llave);
            }
        })
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
    function enviarVenta()
    {
        axios.get("{{ route('Caja.movimiento.verificarestado') }}").then((value) => {
            let data = value.data;
            if (!data.success) {
                toastr.error(data.mensaje);
            } else {
                let envio_ok = true;

                var tipo = validarTipo();

                if (tipo) {
                    cargarProductos();
                    //CARGAR DATOS TOTAL
                    document.querySelector('#monto_sub_total').value    =   tableDetalleFoot.querySelector('.subtotal').textContent;   
                    document.querySelector('#monto_total_igv').value    =   tableDetalleFoot.querySelector('.igv').textContent;   
                    document.querySelector('#monto_total').value        =   tableDetalleFoot.querySelector('.total').textContent;   

                    document.getElementById("moneda").disabled = false;
                    document.getElementById("observacion").disabled = false;
                    document.getElementById("fecha_documento_campo").disabled = false;
                    document.getElementById("fecha_atencion_campo").disabled = false;
                    document.getElementById("empresa_id").disabled = false;
                    document.getElementById("cliente_id").disabled = false;
                    document.getElementById("condicion_id").disabled = false;
                    //HABILITAR EL CARGAR PAGINA
                    $('#asegurarCierre').val(2)
                    //$('#enviar_documento').submit();
                }
                else
                {
                    envio_ok = false;
                }

                if(envio_ok)
                {
                    //let formDocumento = document.getElementById('enviar_documento');
                    let formData = new FormData(formDocumento);

                    var object = {};
                    formData.forEach(function(value, key){
                        object[key] = value;
                    });

                    //var json = JSON.stringify(object);

                    var datos = object;
                    console.log(datos);

                    var init = {
                         // el método de envío de la información será POST
                         method: "POST",
                         headers: { // cabeceras HTTP
                             // vamos a enviar los datos en formato JSON
                             'Content-Type': 'application/json'
                         },
                         // el cuerpo de la petición es una cadena de texto
                         // con los datos en formato JSON
                         body: JSON.stringify(datos) // convertimos el objeto a texto
                    };

                    var url = '{{ route("ventas.documento.store") }}';
                    var textAlert = "¿Seguro que desea guardar cambios?";
                    Swal.fire({
                         title: 'Opción Guardar',
                         text: textAlert,
                         icon: 'question',
                         customClass: {
                             container: 'my-swal'
                         },
                         showCancelButton: true,
                         confirmButtonColor: "#1ab394",
                         confirmButtonText: 'Si, Confirmar',
                         cancelButtonText: "No, Cancelar",
                         showLoaderOnConfirm: true,
                         allowOutsideClick: false,
                         preConfirm: (login) => {
                             return fetch(url,init)
                                 .then(response => {
                                     if (!response.ok) {
                                         throw new Error(response.statusText)
                                     }
                                     return response.json()
                                 })
                                 .catch(error => {
                                     Swal.showValidationMessage(
                                         `Ocurrió un error`
                                     );
                                 })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value !== undefined && result.isConfirmed) {
                            if(result.value.errors)
                            {
                                let mensaje = sHtmlErrores("result.value.data.mensajes");
                                toastr.error(mensaje);
                                 $('#asegurarCierre').val(1);
                                 document.getElementById("moneda").disabled = true;
                                 document.getElementById("observacion").disabled = true;
                                 document.getElementById("fecha_documento_campo").disabled = true;
                                 document.getElementById("fecha_atencion_campo").disabled = true;
                                 document.getElementById("empresa_id").disabled = true;
                                @if (!empty($cotizacion))
                                    document.getElementById("cliente_id").disabled = true;
                                @endif
                            }
                            else if(result.value.success)
                            {
                                toastr.success('¡Documento de venta creado!','Exito')

                                let id = result.value.documento_id;
                                var url_open_pdf = '{{ route("ventas.documento.comprobante", ":id")}}';
                                url_open_pdf = url_open_pdf.replace(':id',id+'-80');
                                window.open(url_open_pdf,'Comprobante SISCOM','location=1, status=1, scrollbars=1,width=900, height=600');

                                $('#asegurarCierre').val(2);

                                location = "{{ route('ventas.documento.index') }}";
                            }
                            //  else
                            //  {
                            //      $('#asegurarCierre').val(1);
                            //      Swal.fire({
                            //          icon: 'error',
                            //          title: 'Error',
                            //          text: '¡'+ result.value.mensaje +'!',
                            //          customClass: {
                            //              container: 'my-swal'
                            //          },
                            //          showConfirmButton: false,
                            //          timer: 2500
                            //      });
                            //      document.getElementById("moneda").disabled = true;
                            //      document.getElementById("observacion").disabled = true;
                            //      document.getElementById("fecha_documento_campo").disabled = true;
                            //      document.getElementById("fecha_atencion_campo").disabled = true;
                            //      document.getElementById("empresa_id").disabled = true;
                            //      @if (!empty($cotizacion))
                            //      document.getElementById("cliente_id").disabled = true;
                            //      @endif
                            // }
                        }
                     });
                }
            }
        })
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

    //==========    CARGAR PRODUCTOS AL FORM   =================
    function cargarProductos() {
        $('#productos_tabla').val(JSON.stringify(carrito));
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
        tableSubtotal.textContent   =   cotizacion.sub_total;
        tableTotal.textContent      =   cotizacion.total;
        tableIgv.textContent        =   cotizacion.total_igv;
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
        const producto_color_procesados=[];

        carrito.forEach((p)=>{
            if(!producto_color_procesados.includes(`${p.producto_id}-${p.color_id}`)){
                tallas.forEach((t)=>{
                  const producto =  carrito.filter((ct)=>{
                       return  ct.producto_id==p.producto_id && ct.color_id==p.color_id && ct.talla_id==t.id
                    })

                    if(producto.length!=0){
                        subtotal+= parseFloat(producto[0].precio_unitario)*parseFloat(producto[0].cantidad);
                    }
                })
                carrito.forEach((c)=>{
                    if(c.producto_id==p.producto_id && c.color_id==p.color_id){
                        c.subtotal=subtotal;
                    }
                })
                subtotal=0;
                producto_color_procesados.push(`${p.producto_id}-${p.color_id}`);
            }
        })  
    }


    function clearDetalleTable(){
        while (tableDetalleBody.firstChild) {
            tableDetalleBody.removeChild(tableDetalleBody.firstChild);
        }
    }

    function pintarDetalleCotizacion(carrito){
            let fila= ``;
            let htmlTallas= ``;
            const producto_color_procesado=[];
            clearDetalleTable();

            carrito.forEach((c)=>{
                htmlTallas=``;
                if (!producto_color_procesado.includes(`${c.producto_id}-${c.color_id}`)) {
                 
                    fila+= `<tr>   
                                <th>${c.producto_nombre} - ${c.color_nombre}</th>`;


                    //tallas
                    tallas.forEach((t)=>{
                        let cantidad = carrito.filter((ct)=>{
                            return ct.producto_id==c.producto_id && ct.color_id==c.color_id && t.id==ct.talla_id;
                        });
                        cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad=0;
                        htmlTallas += `<td>${cantidad}</td>`; 
                    })


                    htmlTallas+=`   <td>${c.precio_unitario}</td>
                                    <td class="td-subtotal">${c.subtotal}</td>
                                </tr>`;

                    fila+=htmlTallas;
                    tableDetalleBody.innerHTML=fila;
                    producto_color_procesado.push(`${c.producto_id}-${c.color_id}`)
                }
            })
    }

    //============= devolver stock logico, ya que hay errores en la cotización ===================
    window.addEventListener('beforeunload', async () => {

        const asegurarCierre    =   document.querySelector('#asegurarCierre');
            if (asegurarCierre.value == 1) {
                localStorage.setItem('devuelto', asegurarCierre.value);

                 await this.DevolverCantidades();
                 asegurarCierre.value = 10;
            } else {
                 console.log("beforeunload", asegurarCierre);
                 localStorage.setItem('no devuelto', asegurarCierre.value);
            }
    });

    //================ devolver cantidades ===============
    async function DevolverCantidades() {
            await this.axios.post(route('ventas.documento.devolver.cantidades'), {
                carrito: JSON.stringify(carritoFormateado),
                vista: 'create-venta-cotizacion'
            });
    }

   

    @if (!empty($errores))
        $('#asegurarCierre').val(1)
        @foreach ($errores as $error)
            toastr.error('La cantidad solicitada '+"{{ $error->cantidad }}"+' excede al stock del producto '+"{{ $error->producto }}", 'Error');
        @endforeach
    @endif

</script>


@endpush
