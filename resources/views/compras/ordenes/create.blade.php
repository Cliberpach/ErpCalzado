@extends('layout') @section('content')

@section('compras-active', 'active')
@section('orden-compra-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA ORDEN DE COMPRA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('compras.orden.index')}}">Ordenes de Compra</a>
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

                    <form action="{{route('compras.orden.store')}}" method="POST" id="enviar_orden">
                        {{csrf_field()}}

                        <div class="row">
                            <div class="col-sm-6 b-r">
                                <h4 class=""><b>Orden de compra</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la orden de compra:</p>
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <div class="col-lg-6 col-xs-12" id="fecha_documento">
                                        <label class="required">Fecha de Emisión</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input type="text" id="fecha_documento_campo" name="fecha_emision"
                                                class="form-control {{ $errors->has('fecha_emision') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha_emision',getFechaFormato($fecha_hoy, 'd/m/Y'))}}"
                                                autocomplete="off" required readonly>
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
                                                value="{{old('fecha_entrega',getFechaFormato($fecha_hoy, 'd/m/Y'))}}"
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
                                        name="empresa_id" id="empresa_id" disabled>
                                        <option></option>
                                        @foreach ($empresas as $empresa)
                                            <option value="{{$empresa->id}}" @if($empresa->id == '1' )
                                            {{'selected'}} @endif >{{$empresa->razon_social}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar Proveedor:</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="required">Ruc / Dni: </label>
                                    <select
                                        class="select2_form form-control {{ $errors->has('proveedor_id') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('proveedor_id')}}"
                                        name="proveedor_id" id="proveedor_id" required>
                                        <option></option>
                                        @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->ruc}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->dni}}
                                            </option>
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
                                        name="proveedor_razon" id="proveedor_razon" required>
                                        <option></option>
                                        @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @endif
                                        @endif
                                        @endforeach
                                    </select>
                                </div>


                            </div>

                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label class="required">Condición</label>
                                    <select id="condicion_id" name="condicion_id"
                                        class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                        required>
                                        <option></option>
                                        @foreach ($condiciones as $condicion)
                                            <option value="{{ $condicion->id }}"
                                                {{ old('condicion_id') == $condicion->id || $condicion->descripcion == 'CONTADO' ? 'selected' : '' }}>
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
                                <div class="form-group row">
                                    <div class="col-md-5">
                                        <label class="required">Moneda: </label>
                                        <select
                                            class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('moneda')}}"
                                            name="moneda" id="moneda" onchange="cambioMoneda(this)" required>
                                            <option></option>
                                            @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->descripcion}}" @if(old('moneda')==$moneda->
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

                                    <div class="col-md-4">
                                        <label class="" id="campo_tipo_cambio">Tipo de Cambio (S/.) :</label>
                                        <input type="text" id="tipo_cambio" name="tipo_cambio" class="form-control {{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" value="{{old('tipo_cambio')}}" disabled>
                                        @if ($errors->has('tipo_cambio'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('tipo_cambio') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <label id="igv_requerido">IGV (%):</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-addon">
                                                    <input type="checkbox" id="igv_check" name="igv_check">
                                                </span>
                                            </div>
                                            <input type="text" value="{{old('igv')}}"
                                                class="form-control {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                name="igv" id="igv" maxlength="3"  onkeyup="return mayus(this)"
                                                required>
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
                                        value="{{old('observacion')}}">{{old('observacion')}}</textarea>
                                    @if ($errors->has('observacion'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('observacion') }}</strong>
                                    </span>
                                    @endif


                                </div>


                                <input type="hidden" id="productos_tabla" name="productos_tabla[]">

                            </div>

                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary" id="panel_detalle">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Seleccionar productos</b></h4>
                                    </div>
                                    <div class="panel-body ibox-content">
                                       <div class="row">
                                        <div class="col-4">
                                            <label for="modelo">MODELO</label>
                                            <select class="select2_form form-control" name="modelo" id="modelo">
                                                <option value=""></option>
                                                @foreach ($modelos as $modelo)
                                                   <option value="{{$modelo->id}}">{{$modelo->descripcion}}</option> 
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="table-responsive">
                                                @include('compras.ordenes.table-productos')
                                            </div>
                                        </div>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">

                            <div class="col-lg-12">
                                <div class="panel panel-primary" id="panel_detalle">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle de la Orden de
                                                Compra</b></h4>
                                    </div>
                                    <div class="panel-body ibox-content">
                                        <div class="sk-spinner sk-spinner-wave">
                                            <div class="sk-rect1"></div>
                                            <div class="sk-rect2"></div>
                                            <div class="sk-rect3"></div>
                                            <div class="sk-rect4"></div>
                                            <div class="sk-rect5"></div>
                                        </div>

                                        <div class="row align-items-end">

                                            <div class="col-10 col-md-3">
                                                <div class="form-group">
                                                    <label class="required">Producto</label>
                                                    <select class="select2_form form-control"
                                                        style="text-transform: uppercase; width:100%" name="producto_id"
                                                        id="producto_id">
                                                    </select>
                                                    <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2 col-md-2">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-secondary" onclick="obtenerProducts()"><i class="fa fa-refresh"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label class="col-form-label required" for="amount">Importe</label>
                                                    <input type="text" id="precio" class="form-control">
                                                    <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label class="col-form-label required">Cantidad</label>
                                                    <input type="text" id="cantidad" class="form-control">
                                                    <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <a class="btn btn-block btn-warning enviar_producto"
                                                        style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
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
                                                        <th class="text-center">PRECIO</th>
                                                        <th class="text-center">TOTAL</th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" style="text-align:right">Sub Total:</th>
                                                        <th><span id="subtotal">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-center">IGV <span
                                                                id="igv_int"></span>:</th>
                                                        <th class="text-center"><span id="igv_monto">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="text-center">TOTAL:</th>
                                                        <th class="text-center"><span id="total">0.0</span></th>

                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>






                                    </div>
                                </div>
                            </div>

                        </div> --}}

                        <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
                        <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
                        <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}">

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
                                <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>

                        </div>

                    </form>

                </div>


            </div>
        </div>

    </div>

</div>
@include('compras.ordenes.modal')
@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">


<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
{{-- <script>
//Select2
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    width: '100%',
});

$('#fecha_documento .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    language: 'es',
    format: "dd/mm/yyyy",
    startDate: "today"
})

$('#fecha_entrega .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    language: 'es',
    format: "dd/mm/yyyy",
    startDate: "today"
})



$(document).ready(function() {
    if ($("#igv_check").prop('checked')) {
        $('#igv').attr('disabled', false)
        $('#igv_requerido').addClass("required")
    } else {
        $('#igv').attr('disabled', true)
        $('#igv_requerido').removeClass("required")
    }

    obtenerProducts();
});

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

// Solo campos numericos
$('#precio').keyup(function() {
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

function validarFecha() {
    var enviar = false
    var productos = registrosproductos()

    if ($('#fecha_documento_campo').val() == '') {
        toastr.error('Ingrese Fecha de Documento de la Orden.', 'Error');
        $("#fecha_documento_campo").focus();
        enviar = true;
    }

    if ($('#fecha_entrega_campo').val() == '') {
        toastr.error('Ingrese Fecha de Entrega de la Orden.', 'Error');
        $("#fecha_entrega_campo").focus();
        enviar = true;
    }
    if (productos == 0) {
        toastr.error('Ingrese al menos 1 Producto.', 'Error');
        enviar = true;
    }
    return enviar
}

function cambioMoneda(b)
{
    if(b.value == 'DOLARES')
    {
        $.ajax({
            dataType: 'json',
            type: 'get',
            url: '{{route("compras.orden.dolar")}}',
        }).done(function(result) {
            $('#tipo_cambio').val(result.venta);
        });
    }
    else
    {
        $('#tipo_cambio').val('');
    }
}

$('#enviar_orden').submit(function(e) {
    e.preventDefault();
    var correcto = validarFecha()

    if (correcto == false) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

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
                //CARGAR DATOS TOTAL
                cargarproductos()
                $('#monto_sub_total').val($('#subtotal').text())
                $('#monto_total_igv').val($('#igv_monto').text())
                $('#monto_total').val($('#total').text())
                this.submit();
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

});


$(document).ready(function() {

    // DataTables
    $('.dataTables-orden-detalle').DataTable({
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
                render: function(data, type, row) {
                    return "<div class='btn-group'>" +
                        "<a class='btn btn-warning btn-sm modificarDetalle' id='editar_producto' style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                        "<a class='btn btn-danger btn-sm' id='borrar_producto' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                        "</div>";
                }
            },
            {
                "targets": [2],
                className: "text-center",
            },
            {
                "targets": [3],
                className: "text-left",
            },
            {
                "targets": [4],
                className: "text-center",
            },
            {
                "targets": [5],
                className: "text-center",
            },

        ],

    });

});

//Editar Registro
$(document).on('click', '#editar_producto', function(event) {
    var table = $('.dataTables-orden-detalle').DataTable();
    var data = table.row($(this).parents('tr')).data();

    $('#indice').val(table.row($(this).parents('tr')).index());
    $('#producto_id_editar').val(data[0]).trigger('change');
    $('#presentacion_editar').val(productoPresentacion(data[0]));
    $('#precio_editar').val(data[4]);
    $('#cantidad_editar').val(data[2]);
    $('#modal_editar_orden').modal('show');
});

//Borrar registro de productos
$(document).on('click', '#borrar_producto', function(event) {

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: 'Opción Eliminar',
        text: "¿Seguro que desea eliminar Artículo?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var table = $('.dataTables-orden-detalle').DataTable();
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
    var enviar = false;
    if ($('#producto_id').val() == '') {
        toastr.error('Seleccione artículo.', 'Error');
        enviar = true;
        $('#producto_id').addClass("is-invalid")
        $('#error-producto').text('El campo Producto es obligatorio.')
    } else {
        var existe = buscarproducto($('#producto_id').val())
        if (existe == true) {
            toastr.error('Producto ya se encuentra ingresado.', 'Error');
            enviar = true;
        }
    }

    if ($('#precio').val() == '') {

        toastr.error('Ingrese el precio del artículo.', 'Error');
        enviar = true;

        $("#precio").addClass("is-invalid");
        $('#error-precio').text('El campo Precio es obligatorio.')
    }

    if ($('#cantidad').val() == '') {
        toastr.error('Ingrese cantidad del artículo.', 'Error');
        enviar = true;

        $("#cantidad").addClass("is-invalid");
        $('#error-cantidad').text('El campo Cantidad es obligatorio.')
    }
    let precio_aux = convertFloat($('#precio').val()) / convertFloat($('#cantidad').val());
    let precio = (precio_aux).toFixed(4)

    if (enviar != true) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        Swal.fire({
            title: 'Opción Agregar',
            text: "¿Seguro que desea agregar Producto?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                var descripcion_producto = obtenerproducto($('#producto_id').val())
                var detalle = {
                    producto_id: $('#producto_id').val(),
                    descripcion: descripcion_producto,
                    precio: precio,
                    cantidad: $('#cantidad').val(),
                }
                limpiarDetalle()
                agregarTabla(detalle);
                sumaTotal()
                // calcularIgv($('#igv').val())

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
});

function limpiarDetalle() {
    $('#presentacion').val('')
    $('#precio').val('')
    $('#cantidad').val('')
    $('#producto_id').val($('#producto_id option:first-child').val()).trigger('change');

}

function limpiarErrores() {
    $('#cantidad').removeClass("is-invalid")
    $('#error-cantidad').text('')

    $('#precio').removeClass("is-invalid")
    $('#error-precio').text('')

    $('#producto_id').removeClass("is-invalid")
    $('#error-producto').text('')
}

function agregarTabla($detalle) {

    var t = $('.dataTables-orden-detalle').DataTable();
    t.row.add([
        $detalle.producto_id,
        '',
        $detalle.cantidad,
        $detalle.descripcion,
        $detalle.precio,
        ($detalle.cantidad * $detalle.precio).toFixed(2),
    ]).draw(false);

    cargarproductos()

}


function obtenerproducto($id) {
    var producto = ""
    @foreach($productos as $producto)
    if ("{{$producto->id}}" == $id) {
        producto = "{{$producto->nombre}}"
    }
    @endforeach
    return producto;
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
function buscarproducto(id) {
    var existe = false;
    var t = $('.dataTables-orden-detalle').DataTable();
    t.rows().data().each(function(el, index) {
        if (el[0] == id) {
            existe = true
        }
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
            precio: value[4],
            cantidad: value[2],
        };

        productos.push(fila);

    });

    $('#productos_tabla').val(JSON.stringify(productos));
}


function registrosproductos() {
    var table = $('.dataTables-orden-detalle').DataTable();
    var registros = table.rows().data().length;
    return registros
}

function productoPresentacion(producto) {
    var presentacion = ""
    @foreach($productos as $producto)
    if ("{{$producto->id}}" == producto) {
        presentacion = "{{$producto->presentacion}}"
    }
    @endforeach
    return presentacion
}

function sumaTotal() {
    var t = $('.dataTables-orden-detalle').DataTable();
    var subtotal = 0;
    t.rows().data().each(function(el, index) {
        subtotal = Number(el[5]) + subtotal
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
    var igv =  subtotal * 0.18
    var total = subtotal + igv
    $('#igv_int').text('18%')
    $('#subtotal').text(subtotal.toFixed(2))
    $('#igv_monto').text(igv.toFixed(2))
    $('#total').text(total.toFixed(2))

}

function conIgv(subtotal) {
    // CALCULAR IGV (BASE)
    var igv = $('#igv').val()
    if (igv) {
        var calcularIgv = igv/100
        var base = subtotal / (1 + calcularIgv)
        var nuevo_igv = subtotal - base;
        $('#igv_int').text(igv+'%')
        $('#subtotal').text(base.toFixed(2))
        $('#igv_monto').text(nuevo_igv.toFixed(2))
        $('#total').text(subtotal.toFixed(2))

    }else{
        toastr.error('Ingrese Igv.', 'Error');
    }

}

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


</script> --}}
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
<script>
    const bodyTablaProductos    =   document.querySelector('#table-productos tbody');
    const tallas                =   @json($tallas);

    let dataTableProductos      =   null;
    let modelo_id               =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        cargarSelect2();
        events();
    })

    function events(){
        $('#modelo').on('change', function (e) {
            getProductosByModelo(e.target.value);
        });
    
    }

    function cargarSelect2(){
        $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
        })

    }


    //====== OBTENER PRODUCTOS POR MODELO =======
    async function getProductosByModelo(idModelo){
        modelo_id = idModelo;
        
        if(modelo_id){
            const url = "{{ route('compras.orden.getProductosByModelo', ['modelo_id' => ':modelo_id']) }}".replace(':modelo_id', modelo_id);
            const response = await axios.get(url);
            
            console.log(response);
            if(response.data.type   ==  'success'){
                
                if(dataTableProductos){
                    dataTableProductos.destroy();
                }
                //======= PINTAR PRODUCTOS ========
                pintarTablaProductos(response.data.message);

                //====== CARGAR DATATABLE =======
                cargarDataTablesProductos();            
            }
        }else{
            clearTablaProductos();
        }
    }

    //======== ORDENAR POR EL PRODUCTO NOMBRE =====
    function ordenarByProducto(a, b) {
        //Primero, ordenar por producto_nombre
        const nombreA = a.producto_nombre.toUpperCase();
        const nombreB = b.producto_nombre.toUpperCase();

        if (nombreA !== nombreB) {
            return nombreA > nombreB ? 1 : -1;
        } else {
            // Si los nombres de los productos son iguales, ordenar por color_nombre
            const colorA = a.color_nombre.toUpperCase();
            const colorB = b.color_nombre.toUpperCase();

            return colorA > colorB ? 1 : colorA < colorB ? -1 : 0;
        }
    }

    //======= PINTAR TABLE PRODUCTOS ==========
    function pintarTablaProductos(productos){
        let filas       = ``;
        let htmlTallas  = ``;

        clearTablaProductos();

        productos.forEach((producto)=>{
            htmlTallas=``;
                filas+= `<tr>   
                            <th>${producto.producto_nombre}</th>
                            <th>
                                ${producto.color_nombre}
                            </th>
                        `;

                tallas.forEach((t)=>{
                    let producto_color_talla =  producto.tallas.filter((pt)=>{
                        return t.id==pt.talla_id;
                    });
                    const stock =   producto_color_talla.length==0?0:producto_color_talla[0].stock;

                    htmlTallas +=   `   <td style="background-color: rgb(210, 242, 242);">${stock}</td>
                                        <td>
                                            <input class="form-control"></input>   
                                        </td>
                                    `; 
                })

                const input =   producto.print_importe?`<input class="form-control" data-producto="${producto.producto_id}" data-color="${producto.color_id}">`:'';

                htmlTallas +=   `<td>${input}</td>
                                </tr>`;

                filas+=htmlTallas;
        })

        bodyTablaProductos.innerHTML=filas;            
    }

    //======= CLEAR TABLA PRODUCTOS ======
    function clearTablaProductos(){
        while (bodyTablaProductos.firstChild) {
            bodyTablaProductos.removeChild(bodyTablaProductos.firstChild);
        }
    }

    function cargarDataTablesProductos(){
        dataTableProductos = new DataTable('#table-productos',
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
        
        const tableProductos   = document.querySelector('#table-productos');
        if(tableProductos.children[1]){
            tableProductos.children[1].remove();
        }
    }

</script>
@endpush
