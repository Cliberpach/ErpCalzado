@extends('layout') @section('content')

@section('compras-active', 'active')
@section('documento-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>MODIFICAR DOCUMENTO DE COMPRA # {{$documento->id}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('compras.documento.index')}}">Documentos de Compra</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Modificar</strong>
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
                            <form action="{{route('compras.documento.update', $documento->id)}}" method="POST" id="enviar_orden">
                                @csrf @method('PUT')

                                <div class="row">
                                    <div class="col-sm-6 b-r">
                                        <h4 class=""><b>Documento de compra</b></h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Modificar datos del documento de compra:</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">

                                            <div class="col-lg-6 col-xs-12" id="fecha_documento">
                                                <label class="required">Fecha de Emisión</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="text" id="fecha_documento" name="fecha_emision"
                                                        class="form-control {{ $errors->has('fecha_emision') ? ' is-invalid' : '' }}"
                                                        value="{{old('fecha_emision', getFechaFormato($documento->fecha_emision, 'd/m/Y'))}}"
                                                        autocomplete="off" readonly required>
                                                    @if ($errors->has('fecha_emision'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_emision') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-xs-12" id="fecha_entrega">
                                                <label class="required">Fecha de Entrega</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="text" id="fecha_entrega_campo" name="fecha_entrega"
                                                        class="form-control {{ $errors->has('fecha_entrega') ? ' is-invalid' : '' }}"
                                                        value="{{old('fecha_entrega', getFechaFormato($documento->fecha_entrega, 'd/m/Y'))}}"
                                                        autocomplete="off" required readonly>
                                                    @if ($errors->has('fecha_entrega'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_entrega') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label class="required">Empresa: </label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{old('empresa_id')}}"
                                                name="empresa_id" id="empresa_id"  disabled>
                                                <option></option>
                                                @foreach ($empresas as $empresa)
                                                <option value="{{$empresa->id}}"  @if($empresa->id == '1' ) {{'selected'}} @endif >{{$empresa->razon_social}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <p>Modificar Proveedor:</p>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="required">Ruc / Dni: </label>

                                            <select
                                                class="select2_form form-control {{ $errors->has('proveedor_id') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{old('proveedor_id',$documento->proveedor_id)}}"
                                                name="proveedor_id" id="proveedor_id" required @if (!empty($documento->orden_id)) disabled @endif>
                                                <option></option>
                                                @foreach ($proveedores as $proveedor)
                                                @if($proveedor->ruc)
                                                <option value="{{$proveedor->id}}" @if(old('proveedor_id',$documento->proveedor_id)
                                                    == $proveedor->id ) {{'selected'}} @endif
                                                    >{{$proveedor->ruc}}</option>
                                                @else
                                                @if($proveedor->dni)
                                                <option value="{{$proveedor->id}}" @if(old('proveedor_id',$documento->proveedor_id)
                                                    == $proveedor->id ) {{'selected'}} @endif
                                                    >{{$proveedor->dni}}</option>
                                                @endif
                                                @endif
                                                @endforeach
                                            </select>

                                        </div>

                                        <div class="form-group">
                                            <label class="required">Razon Social: </label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('proveedor_razon') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{old('proveedor_razon')}}"
                                                name="proveedor_razon" id="proveedor_razon" required @if (!empty($documento->orden_id)) disabled @endif>
                                                <option></option>
                                                @foreach ($proveedores as $proveedor)
                                                    @if($proveedor->ruc)
                                                    <option value="{{$proveedor->id}}" @if(old('proveedor_id',$documento->proveedor_id)==$proveedor->id )
                                                        {{'selected'}} @endif >{{$proveedor->descripcion}}
                                                    </option>
                                                    @else
                                                    @if($proveedor->dni)
                                                    <option value="{{$proveedor->id}}" @if(old('proveedor_id',$documento->proveedor_id)==$proveedor->id )
                                                        {{'selected'}} @endif >{{$proveedor->descripcion}}
                                                    </option>
                                                    @endif
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>




                                    </div>

                                    <div class="col-sm-6">


                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label class="required">Condición: </label>
                                                <select id="condicion_id" name="condicion_id"
                                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($condiciones as $condicion)
                                                            <option value="{{ $condicion->id }}"
                                                            @if(old('condicion_id',$documento->condicion_id) == $condicion->id ) {{'selected'}} @endif>
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
                                            <div class="col-md-6">
                                                <label class="required">Moneda: </label>
                                                <select
                                                    class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%"
                                                    value="{{old('moneda',$documento->moneda)}}" name="moneda" id="moneda" required @if (!empty($documento->orden_id)) disabled @endif>
                                                    <option></option>
                                                    @foreach (tipos_moneda() as $moneda)
                                                    <option value="{{$moneda->descripcion}}" @if(old('moneda',$documento->
                                                        moneda)==$moneda->
                                                        descripcion ) {{'selected'}} @endif
                                                        >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                                                    @endforeach
                                                    @if ($errors->has('moneda'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('moneda') }}</strong>
                                                    </span>
                                                    @endif
                                                </select>


                                            </div>


                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label class="required">Tipo: </label>
                                                <select
                                                    class="select2_form form-control {{ $errors->has('tipo_compra') ? ' is-invalid' : '' }}"
                                                    style="text-transform: uppercase; width:100%" value="{{old('tipo_compra')}}"
                                                    name="tipo_compra" id="tipo_compra" required onchange="activarNumero()">
                                                    <option></option>
                                                    @foreach (tipo_compra() as $modo)
                                                    <option value="{{$modo->descripcion}}" @if(old('tipo_compra',$documento->tipo_compra)==$modo->
                                                        descripcion ) {{'selected'}} @endif
                                                        >{{$modo->descripcion}}</option>
                                                    @endforeach
                                                    @if ($errors->has('tipo_compra'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('tipo_compra') }}</strong>
                                                    </span>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-12 col-md-4">
                                                        <label class="required" id="serie_comprobante">Serie: </label>
                                                        <input type="text" id="serie_tipo" name="serie_tipo" class="form-control {{ $errors->has('serie_tipo') ? ' is-invalid' : '' }}" value="{{old('serie_tipo',$documento->serie_tipo)}}" required >

                                                        @if ($errors->has('serie_tipo'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('serie_tipo') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-12 col-md-8">
                                                        <label class="required" id="numero_comprobante">Nº: </label>
                                                        <input type="text" id="numero_tipo" name="numero_tipo" class="form-control {{ $errors->has('numero_tipo') ? ' is-invalid' : '' }}" value="{{old('numero_tipo',$documento->numero_tipo)}}" required >

                                                        @if ($errors->has('numero_tipo'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('numero_tipo') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-6">
                                                <label class=""  id="campo_tipo_cambio">Tipo de Cambio (S/.):</label>
                                                <input type="text" id="tipo_cambio" name="tipo_cambio" class="form-control {{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" value="{{old('tipo_cambio',$documento->tipo_cambio)}}" disabled>
                                                @if ($errors->has('tipo_cambio'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('tipo_cambio') }}</strong>
                                                </span>
                                                @endif

                                            </div>

                                            <div class="col-md-6">
                                                <label id="igv_requerido">IGV (%):</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-addon">
                                                            <input type="checkbox" id="igv_check" name="igv_check" @if (!empty($documento->orden_id)) disabled @endif>
                                                        </span>
                                                    </div>
                                                    <input type="text" value="{{old('igv',$documento->igv)}}" maxlength="3"
                                                        class="form-control {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                        name="igv" id="igv"  onkeyup="return mayus(this)" required>
                                                        @if ($errors->has('igv'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('igv') }}</strong>
                                                        </span>
                                                        @endif

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Observación:</label>
                                            <textarea type="text" placeholder=""
                                                class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                                name="observacion" id="observacion"  onkeyup="return mayus(this)"
                                                value="{{old('observacion', $documento->observacion)}}" @if (!empty($documento->orden_id)) disabled @endif>{{old('observacion',$documento->observacion)}}</textarea>
                                            @if ($errors->has('observacion'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('observacion') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <input type="hidden" id="productos_tabla" name="productos_tabla[]">
                                        <input type="hidden" id="productos_detalle" name="productos_detalle[]" value="{{$detalles}}">

                                    </div>

                                    <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total',$documento->sub_total) }}">
                                    <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv',$documento->total_igv) }}">
                                    <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total',$documento->total) }}">
                                    <input type="hidden" name="monto_percepcion" id="monto_percepcion" value="{{ old('monto_percepcion',$documento->percepcion) }}">

                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle de Documento de Compra</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="sk-spinner sk-spinner-wave">
                                        <div class="sk-rect1"></div>
                                        <div class="sk-rect2"></div>
                                        <div class="sk-rect3"></div>
                                        <div class="sk-rect4"></div>
                                        <div class="sk-rect5"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-xs-12 b-r">
                                            <div class="form-group row align-items-end">
                                                <div class="col-10 col-md-10">
                                                    <label class="required">Producto:</label>
                                                    <select class="select2_form form-control"
                                                        style="text-transform: uppercase; width:100%" name="producto_id"
                                                        id="producto_id">
                                                    </select>
                                                    <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                                                    </div>
                                                </div>
                                                <div class="col-2 col-md-2">
                                                    <button type="button" class="btn btn-secondary" onclick="obtenerProducts()"><i class="fa fa-refresh"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="required">Costo Flete:</label>
                                                    <input type="text" id="costo_flete" name="costo_flete" class="form-control" onkeydown="nextFocus(event,'btn_enviar_detalle')">
                                                    <div class="invalid-feedback"><b><span id="error-costo-flete"></span></b></div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-lg-6 col-xs-12">
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label required" for="amount">Importe:</label>
                                                        <input type="text" id="precio" name="precio" class="form-control" onkeydown="nextFocus(event,'cantidad')">
                                                        <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">

                                                    <label class="col-form-label required">Cantidad:</label>
                                                    <input type="text" id="cantidad" class="form-control" onkeydown="nextFocus(event,'costo_flete')">
                                                    <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="form-group row d-none">
                                                <div class="col-md-6" id="fecha_vencimiento_campo">
                                                    <label class="required">Fecha de vencimiento:</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control"  autocomplete="off" readonly>
                                                        <div class="invalid-feedback"><b><span id="error-fecha_vencimiento"></span></b></div>

                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="required">Lote:</label>
                                                    <input type="text" id="lote" name="lote" class="form-control" onkeypress="return mayus(this);">
                                                    <div class="invalid-feedback"><b><span id="error-lote"></span></b></div>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <div class="col-lg-6 col-xs-12">
                                                    <label class="col-form-label" for="amount">&nbsp;</label> <a class="btn btn-block btn-success " onclick="limpiarDetalle()" style='color:white;'> <i class="fa fa-paint-brush"></i> LIMPIAR</a>
                                                </div>

                                                <div class="col-lg-6 col-xs-12">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <button class="btn btn-block btn-warning enviar_producto" style='color:white;' id="btn_enviar_detalle"> <i class="fa fa-plus"></i> AGREGAR</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="table-responsive">
                                        <table
                                            class="table dataTables-orden-detalle table-striped table-bordered table-hover"
                                            style="text-transform:uppercase">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center">ACCIONES</th>
                                                    <th class="text-center">CANTIDAD</th>
                                                    <th class="text-center">PRODUCTO</th>
                                                    <th class="text-center">FECHA. VENC</th>
                                                    <th class="text-center">COSTO FLETE</th>
                                                    <th class="text-center">PRECIO</th>
                                                    <th class="text-center">TOTAL</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot style="text-transform:uppercase">
                                                <tr>
                                                    <th colspan="7" style="text-align:right">Sub Total:</th>
                                                    <th class="text-center"><span id="subtotal">0.0</span></th>

                                                </tr>
                                                <tr>
                                                    <th colspan="7" class="text-center">IGV <span
                                                            id="igv_int"></span>:</th>
                                                    <th class="text-center"><span id="igv_monto"></span></th>

                                                </tr>
                                                <tr>
                                                    <th colspan="7" class="text-center">Percepcion:</th>
                                                    <th class="text-center">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" value="{{old('monto_percepcion',$documento->percepcion)}}" id="percepcion" onkeypress="return filterFloat(event, this, false);">
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="7" class="text-center">TOTAL:</th>
                                                    <th class="text-center"><span id="total"></span></th>

                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
                            <a href="{{route('compras.orden.index')}}" id="btn_cancelar"
                                class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" id="btn_grabar" form="enviar_orden" class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button>
                        </div>

                    </div>

                </div>


            </div>
        </div>

    </div>
</div>


@include('compras.documentos.modalEdit')

@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<style>
.select2-container--open {
    z-index: 9999999
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
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<!-- Chosen -->
<script src="{{asset('Inspinia/js/plugins/chosen/chosen.jquery.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
<script>
//IGV
$(document).ready(function() {
    $('#lote').val('LT-{{ $fecha_actual }}');
    $('#fecha_vencimiento').val('{{$fecha_5}}');
    $('#costo_flete').val('0.00');

    if ($("#igv_check").prop('checked')) {
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
    } else {
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
    }
    //TIPO DE CAMBIO

    if ("{{old('moneda',$documento->moneda)}}" == "SOLES") {
        $('#tipo_cambio').attr('disabled',true)
        $("#tipo_cambio").attr("required", false);
        $("#campo_tipo_cambio").removeClass("required")
    }else{
        $('#tipo_cambio').attr('disabled',false)
        $("#tipo_cambio").attr("required", true);
        $("#campo_tipo_cambio").addClass("required")
    }

    obtenerProducts();

})

$("#igv_check").click(function() {
    if ($("#igv_check").is(':checked')) {
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
        $('#igv').prop('required', true)
        $('#igv').val('18')
        var igv = ($('#igv').val()) + ' %'
        $('#igv_int').text(igv)
        sumaTotal()

    } else {
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
        $('#igv').prop('required', false)
        $('#igv').val('')
        $('#igv_int').text('')
    }
});

$("#igv").on("change", function() {
    if ($("#igv_check").is(':checked')) {
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
        $('#igv').prop('required', true)
        var igv = ($('#igv').val()) + ' %'
        $('#igv_int').text(igv)
        sumaTotal()

    } else {
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
        $('#igv').prop('required', false)
        $('#igv').val('')
        $('#igv_int').text('')
        sumaTotal()
    }
});

if ("{{$documento->igv}}") {
    $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
        $('#igv').prop('required', true)
        $('#igv').val('18')
        var igv = ($('#igv').val()) + ' %'
        $('#igv_int').text(igv)
        // sumaTotal()
}else{
    $('#igv').attr('disabled', true)
    $('#igv_requerido').removeClass("required")
    $('#igv').prop('required', false)
    $('#igv').val('')
    $('#igv_int').text('')
}


//Select2
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    height: '200px',
    width: '100%',
});

$('#fecha_documento .input-group.date , #fecha_vencimiento_campo .input-group.date , #fecha_entrega .input-group.date , #fecha_vencimiento_campo_editar .input-group.date ').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    language: 'es',
    format: "dd/mm/yyyy",
})

const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})

// Solo campos numericos
$('#tipo_cambio , #costo_flete , #precio , #flete_table').keyup(function() {
    var val = $(this).val();1
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});


function activarNumero(){
    $('#numero_tipo').val('')
    $('#numero_comprobante').addClass('required')
    $('#numero_tipo').prop('required', true)
    $('#numero_tipo').prop('disabled', false)
}


$('#numero_tipo').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#cantidad_editar').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#precio_editar').keyup(function() {
    var val = $(this).val();
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});

$('#tipo_cambio').keyup(function() {
    var val = $(this).val();
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});

$('#cantidad').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#igv').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});


function validarFecha() {
    var enviar = true;
    var productos = registrosproductos()

    if ($('#fecha_documento_campo').val() == '') {
        toastr.error('Ingrese Fecha de Documento de la Orden.', 'Error');
        $("#fecha_documento_campo").focus();
        enviar = false;
    }

    if ($('#fecha_entrega_campo').val() == '') {
        toastr.error('Ingrese Fecha de Entrega de la Orden.', 'Error');
        $("#fecha_entrega_campo").focus();
        enviar = false;
    }

    if (productos == 0) {
        toastr.error('Ingrese al menos 1 Producto.', 'Error');
        enviar = false;
    }

    let moneda = $('#moneda').val();
    let serie_tipo = $('#serie_tipo').val();
    let numero_tipo = $('#numero_tipo').val();
    let proveedor_id = $('#proveedor_id').val();
    let tipo_compra = $('#tipo_compra').val();

    $.ajax({
        dataType: 'json',
        type: 'post',
        async: false,
        url: '{{ route('compras.documento.consulta_update') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': '{{ $documento->id }}',
            'moneda': $('#moneda').val(),
            'serie_tipo': $('#serie_tipo').val(),
            'numero_tipo': $('#numero_tipo').val(),
            'proveedor_id': $('#proveedor_id').val(),
            'tipo_compra': $('#tipo_compra').val(),
        }
    }).done(function(result) {
        if(!result.success)
        {
            toastr.error('La serie ' + serie_tipo + ' con el numero ' + numero_tipo + ' ya tiene un documento de compra registrado');
            enviar = false;
        }
        else
        {
            enviar = true;
        }

    });

    return enviar;
}

$('#enviar_orden').submit(function(e) {
    e.preventDefault();
    var correcto = validarFecha()
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
                $('#monto_sub_total').val($('#subtotal').text())
                $('#monto_total_igv').val($('#igv_monto').text())
                $('#monto_total').val($('#total').text())
                $('#monto_percepcion').val($('#percepcion').val())
                @if (!empty($documento->orden_id))
                cargarproductos()
                    document.getElementById("condicion_id").disabled = false;
                    document.getElementById("igv_check").disabled = false;
                    document.getElementById("moneda").disabled = false;
                    document.getElementById("observacion").disabled = false;
                    document.getElementById("proveedor_razon").disabled = false;
                    document.getElementById("proveedor_id").disabled = false;
                    document.getElementById("fecha_documento_campo").disabled = false;
                    document.getElementById("fecha_entrega_campo").disabled = false;
                    this.submit();
                @else
                    cargarproductos()
                    this.submit();
                @endif

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    }



})

$("#igv_check").click(function() {
    if ($("#igv_check").is(':checked')) {
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
        $('#igv').prop('required', true)
        $('#igv').val('18')
        var igv = ($('#igv').val()) + ' %'
        $('#igv_int').text(igv)
        sumaTotal()

    } else {
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
        $('#igv').prop('required', false)
        $('#igv').val('')
        $('#igv_int').text('')
        sumaTotal()
    }
});


$(document).ready(function() {

    // DataTables
    table = $('.dataTables-orden-detalle').DataTable({
        "dom": 'lTfgitp',
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },

        "columnDefs": [{
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            {

                "targets": [1],
                className: "text-center",
                render: function(row, type, data) {
                    if(data[10] == 0)
                    {
                        return "<div class='btn-group'>" +
                        "<a class='btn btn-warning btn-sm modificarDetalle' id='editar_producto' style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                        "<a class='btn btn-danger btn-sm' id='borrar_producto' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                        "</div>";
                    }
                    else
                    {
                        return '-';
                    }
                }
            },
            {
                "targets": [2],
                className: "text-center",
            },
            {
                "targets": [3],

            },
            {
                "targets": [4],
                className: "text-center",
                visible: false
            },

            {
                "targets": [5],
                className: "text-center",
            },
            {
                "targets": [6],
                className: "text-center",
            },

            {
                "targets": [7],
                className: "text-center",
            },

            {
                "targets": [8],
                className: "text-center",
                visible: false
            },
            {
                "targets": [9],
                className: "text-center",
                visible: false
            },
            {
                "targets": [10],
                "visible": false,
                "searchable": false
            },

        ],
    });

    @if(old('igv_check', $documento->igv_check))
        $("#igv_check").attr('checked', true);
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
        $('#igv').prop('required', true)
        var igv = ($('#igv').val()) + ' %'
        $('#igv_int').text(igv)


    @else
        $("#igv_check").attr('checked', false);
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
        $('#igv').prop('required', false)

    @endif

    obtenerTabla()
    sumaTotal()

})

function obtenerTabla() {
    var t = $('.dataTables-orden-detalle').DataTable();
    @foreach($detalles as $detalle)

    t.row.add([
        "{{$detalle->producto_id}}",
        '',
        "{{$detalle->cantidad}}",
        "{{$detalle->producto->nombre}}",
        "{{$detalle->fechaFormateada()}}",
        "{{$detalle->costo_flete}}",
        "{{$detalle->precio}}",
        ("{{$detalle->precio}}" * "{{$detalle->cantidad}}").toFixed(2),
        "{{$detalle->lote}}",
        '1',
        '{{$detalle->id}}'
    ]).draw(false);
    @endforeach
}
//Editar Registro
$(document).on('click', '#editar_producto', function(event) {
    var data = table.row($(this).parents('tr')).data();
    $('#indice').val(table.row($(this).parents('tr')).index());
    $('#producto_id_editar').val(data[0]).trigger('change');
    $('#precio_editar').val(data[6]);
    $('#costo_flete_editar').val(data[5]);
    $('#fecha_vencimiento_editar').val(data[4]);
    $('#lote_editar').val(data[8]);
    $('#editable_lote').val(data[10]);
    $('#cantidad_editar').val(data[2]);

    //MOSTRAR TABLA SI ES INGRESO POR PRIMERA VEZ DEL LOTE
    if (data[9]=='' && data[8] != '') {
        $('#modalLote').hide();
        $('#editarLote').hide();
        $('#editarRegistro').show();
    }else{
        if (data[9]!='1') {
            $('#modalLote').show();
            $('#editarLote').show();
            $('#editarRegistro').hide();
        } else {
            $('#modalLote').hide();
            $('#editarLote').hide();
            $('#editarRegistro').show();
        }
    }
    $('#modal_editar_orden').modal('show');

})

//Borrar registro de productos
$(document).on('click', '#borrar_producto', function(event) {

    Swal.fire({
        title: 'Opción Eliminar',
        text: "¿Seguro que desea eliminar Producto?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            table.row($(this).parents('tr')).remove().draw();
            sumaTotal()
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })



});

//Validacion al ingresar tablas
$(".enviar_producto").click(function() {
    limpiarErrores()
    var enviar = true;
    if ($('#producto_id').val() == '') {
        toastr.error('Seleccione Producto.', 'Error');
        enviar = false;
        $('#producto_id').addClass("is-invalid")
        $('#error-producto').text('El campo Producto es obligatorio.')
    } else {
        var existe = buscarproducto($('#producto_id').val())
        if (existe == true) {
            toastr.error('Producto ya se encuentra ingresado.', 'Error');
            enviar = false;
        }
    }
    if ($('#precio').val() == '') {

        toastr.error('Ingrese el precio del Producto.', 'Error');
        enviar = false;

        $("#precio").addClass("is-invalid");
        $('#error-precio').text('El campo Precio es obligatorio.')
    }

    if ($('#cantidad').val() == '') {
        toastr.error('Ingrese cantidad del Producto.', 'Error');
        enviar = false;

        $("#cantidad").addClass("is-invalid");
        $('#error-cantidad').text('El campo Cantidad es obligatorio.')
    }

    if ($('#costo_flete').val() == '') {
        toastr.error('Ingrese Costo de Flete del Producto.', 'Error');
        enviar = false;

        $("#costo_flete").addClass("is-invalid");
        $('#error-costo_flete').text('El campo Costo de Flete es obligatorio.')
    }

    if ($('#lote').val() == '') {
        toastr.error('Ingrese el Lote del Producto.', 'Error');
        enviar = false;

        $("#lote").addClass("is-invalid");
        $('#error-lote').text('El campo Lote es obligatorio.')
    }

    if ($('#fecha_vencimiento').val() == '') {
        toastr.error('Ingrese la Fecha de Vencimiento del Producto.', 'Error');
        enviar = false;

        $("#fecha_vencimiento").addClass("is-invalid");
        $('#error-fecha_vencimiento').text('El campo Fecha de Vencimiento es obligatorio.')
    }

    let moneda = $('#moneda').val();
    let serie_tipo = $('#serie_tipo').val();
    let numero_tipo = $('#numero_tipo').val();
    let proveedor_id = $('#proveedor_id').val();
    let tipo_compra = $('#tipo_compra').val();

    let precio_aux = convertFloat($('#precio').val()) / convertFloat($('#cantidad').val());
    let precio = (precio_aux).toFixed(4)

    if(moneda == '')
    {
        toastr.error("Seleccionar moneda");
        enviar = false;
    }

    if(serie_tipo == '')
    {
        toastr.error("Completar Serie de documento");
        enviar = false;
    }

    if(numero_tipo == '')
    {
        toastr.error("Completar Numero de documento");
        enviar = false;
    }

    if(proveedor_id == '')
    {
        toastr.error("Seleccionar proveedor");
        enviar = false;
    }

    if(tipo_compra == '')
    {
        toastr.error("Seleccionar tipo de compra");
        enviar = false;
    }

    $.ajax({
        dataType: 'json',
        type: 'post',
        async: false,
        url: '{{ route('compras.documento.consulta_update') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': '{{ $documento->id }}',
            'moneda': $('#moneda').val(),
            'serie_tipo': $('#serie_tipo').val(),
            'numero_tipo': $('#numero_tipo').val(),
            'proveedor_id': $('#proveedor_id').val(),
            'tipo_compra': $('#tipo_compra').val(),
        }
    }).done(function(result) {
        if(!result.success)
        {
            toastr.error('La serie ' + serie_tipo + ' con el numero ' + numero_tipo + ' ya tiene un documento de compra registrado');
        }
        else
        {
            if (enviar) {
                var descripcion_producto = obtenerproducto($('#producto_id').val())
                var detalle = {
                    producto_id: $('#producto_id').val(),
                    descripcion: descripcion_producto.nombre+' - '+$('#lote').val(),
                    costo_flete: $('#costo_flete').val(),
                    precio: precio,
                    cantidad: $('#cantidad').val(),
                    lote: $('#lote').val(),
                    fecha_vencimiento: $('#fecha_vencimiento').val(),
                    detalle_id: 0,
                }
                // limpiarDetalle()
                agregarTabla(detalle);
                sumaTotal()
            }
        }

    });
})

$("#percepcion").on('keyup', function() {
    sumaTotal()
})

function limpiarDetalle() {
    $('#presentacion').val('')
    $('#precio').val('')
    $('#cantidad').val('')
    $('#costo_flete').val('')
    $('#producto_id').val($('#producto_id option:first-child').val()).trigger('change');
    $('#lote').val('')
    $('#fecha_vencimiento').val('')
}

function limpiarErrores() {
    $('#cantidad').removeClass("is-invalid")
    $('#error-cantidad').text('')

    $('#precio').removeClass("is-invalid")
    $('#error-precio').text('')

    $('#producto_id').removeClass("is-invalid")
    $('#error-producto').text('')

    $('#costo_flete').removeClass("is-invalid")
    $('#error-costo_flete').text('')

    $('#fecha_vencimiento').removeClass("is-invalid")
    $('#error-fecha_vencimiento').text('')

    $('#lote').removeClass("is-invalid")
    $('#error-lote').text('')
}

//OBTENER EL producto POR SU ID
function obtenerproducto(id) {
    var producto = "";
    $.ajax({
      url: '{{ route("getProducto", ":id") }}'.replace(':id', id),
      async: false,
      success:function(data) {
        producto = (data) ? data : toastr.error('El Producto no se encuentra en Base de Datos.', 'Error');
      }
   });
   return producto;
}
//AGREGAR EL CAMPO PRESENTACION Y PRECIO DEL PRODUCTO
function cargarPresentacion(producto) {
    if(producto.value != '')
    {
        //$('#precio').val(obtenerproducto(producto.value).precio_venta_minimo)
        $('#precio').val('')
        setTimeout(function() { $('input[name="precio"]').focus() }, 10);
    }
}

$("#moneda").on("change", function() {
    var val = $(this).val();
    if (val == "SOLES") {
        $('#tipo_cambio').attr('disabled',true)
        $('#tipo_cambio').val('')
        $("#tipo_cambio").attr("required", false);
        $("#campo_tipo_cambio").removeClass("required")

    }else{
        $('#tipo_cambio').attr('disabled',false)
        $('#tipo_cambio').val('')
        $("#tipo_cambio").attr("required", true);
        $("#campo_tipo_cambio").addClass("required")
    }
});

function productoPresentacion(producto) {
    var presentacion = ""
    @foreach($productos as $producto)
    if ("{{$producto->id}}" == producto) {
        presentacion = "{{$producto->presentacion}}"
    }
    @endforeach
    return presentacion
}


function agregarTabla($detalle) {

    var t = $('.dataTables-orden-detalle').DataTable();
    t.row.add([
        $detalle.producto_id,
        '',
        $detalle.cantidad,
        $detalle.descripcion,
        $detalle.fecha_vencimiento,
        $detalle.costo_flete,
        $detalle.precio,
        ($detalle.cantidad * $detalle.precio).toFixed(2),
        $detalle.lote,
        editable($detalle.editable),
        $detalle.detalle_id
    ]).draw(false);
    cargarproductos()

}

//EDITABLE SIRVE PARA MANEJAR EL AGREGAR LOTES EN UN PRODUCTO
function editable(editable) {
    if (editable) {
        return editable
    }else{
        return ''
    }
}

function buscarproducto(id) {
    var existe = false;
    table.rows().data().each(function(el, index) {
        (el[0] == id) ? existe = true : ''
    });
    return existe
}


function cargarproductos() {

    var productos = [];
    var table = $('.dataTables-orden-detalle').DataTable();
    var data = table.rows().data();
    data.each(function(value, index) {
        let fila = {
            producto_id: value[0],
            cantidad: value[2],
            nombre: value[3],
            costo_flete: value[5],
            precio: value[6],
            fecha_vencimiento: value[4],
            lote: value[8],
            detalle_id: value[10]
        };

        productos.push(fila);

    });

    $('#productos_tabla').val(JSON.stringify(productos));
}

function obtenerPresentacion($descripcion) {
    var presentacion = ""
    @foreach($presentaciones as $presentacion)
    if ("{{$presentacion->descripcion}}" == $descripcion) {
        presentacion = "{{$presentacion->simbolo}}"
    }
    @endforeach
    return presentacion;
}

function registrosproductos() {
    var registros = table.rows().data().length;
    return registros
}


function sumaTotal() {
    var subtotal = 0;
    table.rows().data().each(function(el, index) {
        subtotal = Number(el[7]) + subtotal
    });

    var igv = $('#igv').val()
    if (!igv) {
        sinIgv(subtotal)
    }else{
        conIgv(subtotal)
    }
}

    function sinIgv(subtotal) {
        // calular igv (calcular la base)
        var percepcion = convertFloat($('#percepcion').val())
        var igv =  subtotal * 0.18
        var total = subtotal + igv + percepcion
        $('#igv_int').text('18%')
        $('#subtotal').text(subtotal.toFixed(2))
        $('#igv_monto').text(igv.toFixed(2))
        $('#total').text(total.toFixed(2))

    }

    function conIgv(subtotal) {
        // CALCULAR IGV (BASE)
        var percepcion = convertFloat($('#percepcion').val())
        var igv = $('#igv').val()
        if (igv) {
            var calcularIgv = igv/100
            var base = subtotal / (1 + calcularIgv)
            var nuevo_igv = subtotal - base;
            $('#igv_int').text(igv+'%')
            $('#subtotal').text(base.toFixed(2))
            $('#igv_monto').text(nuevo_igv.toFixed(2))
            $('#total').text((subtotal + percepcion).toFixed(2))

        }else{
            toastr.error('Ingrese Igv.', 'Error');
        }

    }

    // function sinIgv(subtotal) {
    //     // calular igv (calcular la base)
    //     let igv = 18;
    //     var calcularIgv = igv/100
    //     var base = subtotal / (1 + calcularIgv)
    //     var nuevo_igv = subtotal - base;
    //     $('#igv_int').text(igv+'%')
    //     $('#subtotal').text(base.toFixed(2))
    //     $('#igv_monto').text(nuevo_igv.toFixed(2))
    //     $('#total').text(subtotal.toFixed(2))

    // }

    // function conIgv(subtotal) {
    //     // calular igv (calcular la base)
    //     var igv = $('#igv').val()
    //     ///////////////////////////////

    //     if (igv) {
    //         var calcularIgv = igv/100
    //         var base = subtotal / (1 + calcularIgv)
    //         var nuevo_igv = subtotal - base;
    //         $('#igv_int').text(igv+'%')
    //         $('#subtotal').text(base.toFixed(2))
    //         $('#igv_monto').text(nuevo_igv.toFixed(2))
    //         $('#total').text(subtotal.toFixed(2))

    //     }else{
    //         toastr.error('Ingrese Igv.', 'Error');
    //     }

    // }

    $(document).on("change", "#proveedor_razon", function () {
        id = $(this).val();
        if($("#proveedor_id").val() != id){
            $("#proveedor_id").select2('val',id);
        }
    });

    $(document).on("change", "#proveedor_id", function () {
        id = $(this).val();
        if($("#proveedor_razon").val() != id){
            $("#proveedor_razon").select2('val',id);
        }
    });

    function obtenerProducts()
    {
        $('#panel_detalle').children('.ibox-content').toggleClass('sk-loading');
        $("#producto_id").empty().trigger('change');
        axios.get('{{ route('compras.documento.getProduct') }}').then(response => {
            let data = response.data.data
            console.log(data)
            if (data.length > 0) {
                $('#producto_id').append('<option></option>').trigger('change');
                for(var i = 0;i < data.length; i++)
                {
                    let codigo = data[i].codigo_barra ? (' - ' + data[i].codigo_barra) : '';
                    var newOption = '<option value="'+data[i].id+'" peso="'+data[i].peso_producto+'" unidad="'+data[i].medida_desc+'" descripcion="'+data[i].nombre+'">'+data[i].nombre + codigo + '</option>';
                    $('#producto_id').append(newOption).trigger('change');
                    //departamentos += '<option value="'+result.departamentos[i].id+'">'+result.departamentos[i].nombre+'</option>';
                }

                $('#panel_detalle').children('.ibox-content').toggleClass('sk-loading');

            } else {
                $('#panel_detalle').children('.ibox-content').toggleClass('sk-loading');
                toastr.error('Productos no encontrados.', 'Error');
            }
        })
    }

    function nextFocus(event, inputS) {
        if (event.keyCode == 13) {

            setTimeout(function() { $('#'+inputS).focus() }, 10);
            document.getElementById(inputS).focus();
        }
    }
</script>
@endpush
