@extends('layout') @section('content')
    @include('ventas.cotizaciones.edit-detalle')
@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>MODIFICAR COTIZACIÓN # {{ $cotizacion->id }}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a>
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
                            <form action="{{ route('ventas.cotizacion.update', $cotizacion->id) }}" method="POST"
                                id="form_modificar_cotizacion">
                                @csrf @method('PUT')
                                <div class="row">
                                    <div class="col-12">
                                        <h4><b>Datos Generales</b></h4>
                                    </div>
                                    <div class="col-12 d-none">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label class="required">Fecha de Documento</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="fecha_documento" name="fecha_documento"
                                                        class="form-control input-required {{ $errors->has('fecha_documento') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_documento', $cotizacion->fecha_documento) }}"
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
                                                <label class="required">Moneda</label>
                                                <select id="moneda" name="moneda" disabled
                                                    class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}">
                                                    <option selected>SOLES</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="required">Empresa</label>
                                                <select id="empresa" name="empresa"
                                                    class="select2_form form-control {{ $errors->has('empresa') ? ' is-invalid' : '' }}">
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{ $empresa->id }}"
                                                            {{ old('empresa', $cotizacion->empresa_id) == $empresa->id ? 'selected' : '' }}>
                                                            {{ 'RUC: ' . $empresa->ruc . ' - ' . $empresa->razon_social }}</option>
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
                                                            class="form-control input-required {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_atencion', $cotizacion->fecha_atencion) }}"
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
                                                    <label id="igv_requerido">IGV (%):</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-addon">
                                                                <input type="checkbox" id="igv_check" name="igv_check">
                                                            </span>
                                                        </div>
                                                        <input type="text" value="{{ old('igv', $cotizacion->igv) }}" maxlength="3"
                                                            class="form-control input-required {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                            name="igv" id="igv" onkeyup="return mayus(this)" required>
                                                        @if ($errors->has('igv'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('igv') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label class="___class_+?40___">Vendedor</label>
                                                    <select id="vendedor" name="vendedor" class="select2_form form-control">
                                                        <option value=""></option>
                                                        @foreach (vendedores() as $vendedor)
                                                            <option value="{{ $vendedor->id }}" {{$cotizacion->vendedor_id==null? '' :($cotizacion->vendedor_id==$vendedor->id ? 'selected' : '')}}>
                                                                {{ $vendedor->persona->apellido_paterno . ' ' . $vendedor->persona->apellido_materno . ' ' . $vendedor->persona->nombres }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="required">Cliente</label>
                                                <select id="cliente" name="cliente"
                                                    class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                    onchange="obtenerTipo(this)" required>
                                                    <option></option>
                                                    @foreach ($clientes as $cliente)
                                                        <option value="{{ $cliente->id }}"
                                                            {{ old('cliente', $cotizacion->cliente_id) == $cliente->id ? 'selected' : '' }}>
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
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="required">Condición</label>
                                                <select id="condicion_id" name="condicion_id"
                                                    class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                    required>
                                                    <option></option>
                                                    @foreach ($condiciones as $condicion)
                                                        <option value="{{ $condicion->id }}"
                                                            {{ old('condicion_id') == $condicion->id || $cotizacion->condicion_id == $condicion->id ? 'selected' : '' }}>
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
                                        <!-- OBTENER TIPO DE CLIENTE -->
                                        <input type="hidden" name="" id="tipo_cliente">
                                        <!-- OBTENER DATOS DEL PRODUCTO -->
                                        <input type="hidden" name="" id="presentacion_producto">
                                        <input type="hidden" name="" id="codigo_nombre_producto">
                                        <!-- LLENAR DATOS EN UN ARRAY -->
                                        <input type="hidden" id="productos_tabla" name="productos_tabla[]">
                                    </div>
                                </div>
                                <input type="hidden" name="monto_sub_total" id="monto_sub_total"
                                    value="{{ old('monto_sub_total',$cotizacion->sub_total) }}">
                                <input type="hidden" name="monto_total_igv" id="monto_total_igv"
                                    value="{{ old('monto_total_igv',$cotizacion->total_igv) }}">
                                <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total',$cotizacion->total) }}">
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Detalle de la Cotización</b></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row">
                                                <div class="col-lg-4 col-xs-12">
                                                    <label class="required">Producto</label>
                                                    <select id="producto"
                                                        class="select2_form form-control {{ $errors->has('producto') ? ' is-invalid' : '' }}"
                                                        onchange="obtenerMonto(this)">
                                                        <option></option>
                                                        @foreach ($lotes as $lote)
                                                            <option value="{{ $lote->id }}"
                                                                {{ old('producto') == $lote->id ? 'selected' : '' }}>
                                                                {{ $lote->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-producto"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Cantidad</label>
                                                    <input type="text" id="cantidad" class="form-control"
                                                        maxlength="10" onkeypress="return isNumber(event);">
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-cantidad"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Precio</label>
                                                    <input type="text" id="precio" class="form-control"
                                                        maxlength="15"
                                                        onkeypress="return filterFloat(event, this);">
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-precio"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Descuento (%)</label>
                                                    <input type="text" id="pdescuento" class="form-control"
                                                        maxlength="15">
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-precio"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <button type="button" id="btn_agregar_detalle"
                                                        class="btn btn-warning btn-block m-t-lg"><i
                                                            class="fa fa-plus"></i> AGREGAR</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-sm" style="text-transform:uppercase">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table dataTables-detalle-cotizacion table-striped table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th class="text-center">ACCIONES</th>
                                                            <th class="text-center">CANT</th>
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
                                                        {{-- <tr>
                                                            <th colspan="8" style="text-align: right !important;">
                                                                Sub Total:</th>
                                                            <th class="text-center"><span
                                                                    id="subtotal">0.00</span></th>

                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" class="text-right">IGV <span
                                                                    id="igv_int"></span>:</th>
                                                            <th class="text-center"><span
                                                                    id="igv_monto">0.00</span></th>
                                                        </tr> --}}
                                                        <tr>
                                                            <th colspan="8" class="text-right">TOTAL:</th>
                                                            <th class="text-center"><span id="total">0.00</span>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
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
                                    <button type="submit" id="btn_grabar" form="form_modificar_cotizacion" class="btn btn-w-m btn-primary">
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
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })
    //IGV
    $(document).ready(function() {
        if ($("#igv_check").prop('checked')) {
            $('#igv').attr('disabled', false)
            $('#igv_requerido').addClass("required")
        } else {
            $('#igv').attr('disabled', true)
            $('#igv_requerido').removeClass("required")
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

    $('#precio_editar').keyup(function() {
        var val = $(this).val();
        if (isNaN(val)) {
            val = val.replace(/[^0-9\.]/g, '');
            if (val.split('.').length > 2)
                val = val.replace(/\.+$/, "");
        }
        $(this).val(val);
    });

    $('#cantidad_editar').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#cantidad').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });


    //Select2
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });


    function sumaTotal() {
        let total = 0.00;
        let igv = convertFloat($('#igv').val());
        let igv_calculado = convertFloat(igv / 100);

        let detalles = [];

        table.rows().data().each(function(el, index) {
            let pdescuento = convertFloat(el[10]);
            let precio_inicial = convertFloat(el[9]);
            let precio_unitario = 0.00;
            let valor_unitario = 0.00;
            let dinero = 0.00;
            let precio_nuevo = 0.00;
            let valor_venta = 0.00;

            if ($("#igv_check").prop('checked')) {
                precio_unitario = precio_inicial;
                valor_unitario = precio_unitario / (1 + igv_calculado);
                dinero = precio_unitario * (pdescuento / 100);
                precio_nuevo = precio_unitario - dinero;
                valor_venta = precio_nuevo * el[2];
                let detalle = {
                    producto_id: el[0],
                    producto: el[3],
                    precio_unitario: precio_unitario,
                    valor_unitario: valor_unitario,
                    valor_venta: valor_venta,
                    cantidad: convertFloat(el[2]),
                    descuento: pdescuento,
                    precio_nuevo: precio_nuevo,
                    dinero: dinero,
                    precio_inicial: precio_inicial
                }
                detalles.push(detalle);
            } else {
                precio_unitario = precio_inicial / 1.18;
                valor_unitario = precio_unitario / 1.18;
                dinero = precio_unitario * (pdescuento / 100);
                precio_nuevo = precio_unitario - dinero;
                valor_venta = precio_nuevo * el[2];
                let detalle = {
                    producto_id: el[0],
                    producto: el[3],
                    precio_unitario: precio_unitario,
                    valor_unitario: valor_unitario,
                    valor_venta: valor_venta,
                    cantidad: convertFloat(el[2]),
                    descuento: pdescuento,
                    dinero: dinero,
                    precio_nuevo: precio_nuevo,
                    precio_inicial: precio_inicial
                }
                detalles.push(detalle);
            }
        });

        table.clear().draw();

        if(detalles.length > 0)
        {
            for(let i = 0; i < detalles.length; i++)
            {
                agregarTabla(detalles[i]);
            }
        }

        table.rows().data().each(function(el, index) {
            total = Number(el[8]) + total
        });

        $('#total').text((Math.round(total * 10) / 10).toFixed(2));
        //conIgv(total, igv)
    }


    function conIgv(subtotal, igv) {
        let total = subtotal * (1 + (igv / 100));
        let igv_calculado =  total - subtotal;
        $('#igv_int').text(igv + '%')
        $('#subtotal').text((Math.round(subtotal * 10) / 10).toFixed(2))
        $('#igv_monto').text((Math.round(igv_calculado * 10) / 10).toFixed(2))
        $('#total').text((Math.round(total * 10) / 10).toFixed(2))
        //Math.round(fDescuento * 10) / 10
    }

    $(document).ready(function() {
        if ($("#igv_check").prop('checked')) {
            $('#igv').attr('disabled', false)
            $('#igv_requerido').addClass("required")
        } else {
            $('#igv').attr('disabled', true)
            $('#igv_requerido').removeClass("required")
        }

        //DATATABLE - COTIZACION
        table = $('.dataTables-detalle-cotizacion').DataTable({
            "dom": 'lTfgitp',
            "bPaginate": true,
            "bLengthChange": true,
            "responsive": true,
            "bFilter": true,
            "bInfo": false,
            "columnDefs": [{
                    "targets": 0,
                    "visible": false,
                    "searchable": false
                },
                {
                    searchable: false,
                    "targets": [1],
                    data: null,
                    defaultContent: "<div class='btn-group'>" +
                        // "<a class='btn btn-sm btn-warning btn-edit' style='color:white'>" +
                        // "<i class='fa fa-pencil'></i>" + "</a>" +
                        "<a class='btn btn-sm btn-danger btn-delete' style='color:white'>" +
                        "<i class='fa fa-trash'></i>" + "</a>" +
                        "</div>"
                },
                {
                    "targets": [2],
                },
                {
                    "targets": [3],
                },
                {
                    "targets": [4],
                },
                {
                    "targets": [5],
                },
                {
                    "targets": [6],
                },
                {
                    "targets": [7],
                },
                {
                    "targets": [8],
                },
                {
                    "targets": [9],
                    'visible': false,
                },
                {
                    "targets": [10],
                    'visible': false,
                }
            ],
            'bAutoWidth': false,
            'aoColumns': [{
                    sWidth: '0%'
                },
                {
                    sWidth: '10%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '10%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '40%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '15%',
                    sClass: 'text-left'
                },
                {
                    sWidth: '15%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '15%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '0%',
                    sClass: 'text-center'
                },
                {
                    sWidth: '0%',
                    sClass: 'text-center'
                }
            ],
            "language": {
                url: "{{ asset('Spanish.json') }}"
            },
            "order": [
                [0, "desc"]
            ],
        });

        @if (old('igv_check', $cotizacion->igv_check))
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
        let detalle = {
            value : '{{ $cotizacion->cliente_id }}'
        }
        obtenerTipo(detalle)

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';
    });


    function obtenerTipo(tipo) {

        if (tipo.value) {
            $('#producto').prop('disabled', false)
            $('#precio').prop('disabled', false)
            $('#cantidad').prop('disabled', false)
            //CONSULTA A CLIENTE PARA OBTENER EL MONTO
            $.ajax({
                dataType: 'json',
                url: '{{ route('ventas.cliente.getcustomer') }}',
                type: 'post',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'cliente_id': tipo.value
                },
                success: function(cliente) {
                    $('#tipo_cliente').val(cliente.tabladetalles_id)
                },
            })
        } else {
            $('#producto').prop('disabled', true)
            $('#precio').prop('disabled', true)
            $('#cantidad').prop('disabled', true)
        }
    }

    function obtenerMonto(producto) {
        if (producto.value.length > 0) {
            var tipo = $('#tipo_cliente').val()
            $.get('/almacenes/productos/obtenerProducto/' + producto.value, function(data) {
                for (var i = 0; i < data.cliente_producto.length; i++)
                {
                    //SOLO SOLES LOS MONTOS
                    if (data.cliente_producto[i].cliente == tipo && data.cliente_producto[i].moneda == '1') {
                        if (data.cliente_producto[i].igv == '0') {
                            var monto = Number(data.cliente_producto[i].monto * 0.18) + Number(data
                                .cliente_producto[i].monto)
                            $('#precio').val(Number(monto).toFixed(2))

                        } else {
                            var monto = data.cliente_producto[i].monto
                            $('#precio').val(Number(monto).toFixed(2))

                        }
                    }
                }
            });
        }


    }

    function obtenerProducto(id) {
        // Consultamos nuestra BBDD
        var url = '{{ route('almacenes.producto.productoDescripcion', ':id') }}';
        url = url.replace(':id', id);
        $.ajax({
            dataType: 'json',
            type: 'get',
            url: url,
        }).done(function(result) {
            $('#presentacion_producto').val(result.medida)
            $('#codigo_nombre_producto').val(result.codigo + ' - ' + result.nombre)
            llegarDatos()
            sumaTotal()
            limpiarDetalle()
        });
    }


    function obtenerTabla() {
        @foreach ($detalles as $detalle)
            table.row.add([
            "{{ $detalle->producto_id }}",
            '',
            "{{ $detalle->cantidad }}",
            "{{ $detalle->producto->codigo . ' - ' . $detalle->producto->nombre }}",
            "{{ $detalle->valor_unitario }}",
            "{{ $detalle->precio_unitario }}",
            "{{ $detalle->dinero }}",
            "{{ $detalle->precio_nuevo }}",
            ("{{ $detalle->valor_venta }}"),
            "{{ $detalle->precio_inicial }}",
            "{{ $detalle->descuento}}",
            ]).draw(false);
        @endforeach
        sumaTotal();
    }


    function limpiarErrores() {
        $('#cantidad').removeClass("is-invalid")
        $('#error-cantidad').text('')

        $('#precio').removeClass("is-invalid")
        $('#error-precio').text('')

        $('#producto').removeClass("is-invalid")
        $('#error-producto').text('')
    }


    function limpiarDetalle() {
        $('#precio').val('')
        $('#cantidad').val('')
        $('#presentacion_producto').val('')
        $('#codigo_nombre_producto').val('')
        $('#producto').val($('#producto option:first-child').val()).trigger('change');

    }


    //Editar Registro
    $(document).on('click', '.btn-edit', function(event) {
        var data = table.row($(this).parents('tr')).data();
        $('#indice').val(table.row($(this).parents('tr')).index());
        $('#producto_editar').val(data[0]).trigger('change');
        $('#precio_editar').val(data[5]);
        $('#presentacion_producto_editar').val(data[3]);
        $('#codigo_nombre_producto_editar').val(data[4]);
        $('#cantidad_editar').val(data[2]);
        $('#medida_editar').val(data[7]);
        $('#modal_editar_detalle').modal('show');
    })


    //Borrar registro de Producto
    $(document).on('click', '.btn-delete', function(event) {
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


    function agregarTabla($detalle) {
        table.row.add([
            $detalle.producto_id,
            '',
            $detalle.cantidad.toFixed(2),
            $detalle.producto,
            $detalle.valor_unitario.toFixed(2),
            $detalle.precio_unitario.toFixed(2),
            $detalle.dinero.toFixed(2),
            $detalle.precio_nuevo.toFixed(2),
            $detalle.valor_venta.toFixed(2),
            $detalle.precio_inicial.toFixed(2),
            $detalle.descuento.toFixed(2),

        ]).draw(false);
        cargarProductos();
    }

    function obtenerMedida(id) {
        var medida = ""
        @foreach (unidad_medida() as $medida)
            if ("{{ $medida->id }}" == id) {
            medida = "{{ $medida->simbolo . ' - ' . $medida->descripcion }}"
            }
        @endforeach
        return medida
    }

    function llegarDatos() {
        let pdescuento = $("#pdescuento").val().length > 0 ? convertFloat($("#pdescuento").val()) : 0;
        let precio_inicial = convertFloat($('#precio').val());
        let igv = convertFloat($('#igv').val());
        let igv_calculado = convertFloat(igv / 100);

        let valor_unitario = 0.00;
        let precio_unitario = 0.00;
        let dinero = 0.00;
        let precio_nuevo = 0.00;
        let valor_venta = 0.00;
        let cantidad = convertFloat($('#cantidad').val())
        if ($("#igv_check").prop('checked')) {
            precio_unitario = precio_inicial;
            valor_unitario = precio_unitario / (1 + igv_calculado);
            dinero = precio_unitario * (pdescuento / 100);
            precio_nuevo = precio_unitario - dinero;
            valor_venta = precio_nuevo * cantidad;
        } else {
            precio_unitario = precio_inicial / 1.18;
            valor_unitario = precio_unitario / 1.18;
            dinero = precio_unitario * (pdescuento / 100);
            precio_nuevo = precio_unitario - dinero;
            valor_venta = precio_nuevo * cantidad;
        }

        var detalle = {
            producto_id: $('#producto').val(),
            producto: $('#codigo_nombre_producto').val(),
            precio_unitario: precio_unitario,
            valor_unitario: valor_unitario,
            valor_venta: valor_venta,
            cantidad: cantidad,
            dinero: dinero,
            descuento: pdescuento,
            precio_nuevo: precio_nuevo,
            precio_inicial: precio_inicial
        }
        console.log(detalle);
        agregarTabla(detalle);
    }

    function cargarProductos() {
        var productos = [];
        var data = table.rows().data();
        data.each(function(value, index) {
            let fila = {
                producto_id: value[0],
                presentacion: value[3],
                precio_unitario: value[5],
                valor_unitario:value[4],
                dinero:value[6],
                precio_inicial:value[9],
                precio_nuevo:value[7],
                descuento:value[10],
                cantidad: value[2],
                valor_venta: value[8],
            };

            productos.push(fila);

        });

        $('#productos_tabla').val(JSON.stringify(productos));
    }

    function registrosProductos() {
        var registros = table.rows().data().length;
        return registros
    }

    function validarFecha() {
        var enviar = false
        var productos = registrosProductos()
        if ($('#fecha_documento').val() == '') {
            toastr.error('Ingrese Fecha de Documento.', 'Error');
            $("#fecha_documento").focus();
            enviar = true;
        }

        if ($('#fecha_atencion').val() == '') {
            toastr.error('Ingrese Fecha de Atención.', 'Error');
            $("#fecha_atencion").focus();
            enviar = true;
        }

        if (productos == 0) {
            toastr.error('Ingrese al menos 1 Producto.', 'Error');
            enviar = true;
        }
        return enviar
    }


    $('#form_modificar_cotizacion').submit(function(e) {
        e.preventDefault();
        var correcto = validarFecha()

        if (correcto == false) {
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
                    cargarProductos()
                    //CARGAR DATOS TOTAL
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

    })


    //Validacion al ingresar tablas
    $("#btn_agregar_detalle").click(function() {
        limpiarErrores()
        var enviar = false;
        if ($('#producto').val() == '') {
            toastr.error('Seleccione Producto.', 'Error');
            enviar = true;
            $('#producto').addClass("is-invalid")
            $('#error-producto').text('El campo Producto es obligatorio.')
        } else {
            var existe = buscarProducto($('#producto').val())
            if (existe == true) {
                toastr.error('Producto ya se encuentra ingresado.', 'Error');
                enviar = true;
            }
        }

        if ($('#precio').val() == '') {

            toastr.error('Ingrese el precio del producto.', 'Error');
            enviar = true;

            $("#precio").addClass("is-invalid");
            $('#error-precio').text('El campo Precio es obligatorio.')
        }

        if ($('#cantidad').val() == '') {
            toastr.error('Ingrese cantidad del Producto.', 'Error');
            enviar = true;

            $("#cantidad").addClass("is-invalid");
            $('#error-cantidad').text('El campo Cantidad es obligatorio.')
        }
        if (enviar != true) {

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
                    obtenerProducto($('#producto').val())

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


    function buscarProducto(id) {
        var existe = false;
        table.rows().data().each(function(el, index) {
            if (el[0] == id) {
                existe = true
            }
        });
        return existe
    }
</script>
@endpush
