@extends('layout') @section('content')
@include('ventas.cotizaciones.modal-cliente') 
    
@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

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
                                            <div class="col-12 col-md-4">
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
                                            {{-- <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label id="igv_requerido">IGV (%):</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-addon">
                                                                <input type="checkbox" id="igv_check" name="igv_check" checked>
                                                            </span>
                                                        </div>
                                                        <input type="text" value="{{ old('igv', '18') }}"
                                                            class="form-control input-required {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                            name="igv" id="igv" maxlength="3" onkeyup="return mayus(this)" required>
                                                        @if ($errors->has('igv'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('igv') }}</strong>
                                                            </span>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div> --}}
                                           

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
                                            <div class="col-12 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                                                            Registrar
                                                        </button>
                                                    </label>
                                                    <select id="cliente" name="cliente"
                                                        class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                        onchange="obtenerTipo(this)" required>
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
                                            <div class="col-12 col-md-6 select-required">
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
                                                <div class="col-lg-3 col-xs-12">
                                                    <label class="required">Modelo</label>
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
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-producto"></span></b></div>
                                                </div>
                                                
                                          

                                                {{-- <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Cantidad</label>
                                                    <input type="numer" id="cantidad" class="form-control"
                                                        maxlength="10" onkeypress="return isNumber(event);"
                                                        disabled>
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-cantidad"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Precio</label>
                                                    <input type="text" id="precio" class="form-control"
                                                        maxlength="15" onkeypress="return filterFloat(event, this);"
                                                        disabled>
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-precio"></span></b></div>
                                                </div>
                                                <div class="col-lg-2 col-xs-12">
                                                    <label class="required">Descuento (%)</label>
                                                    <input type="text" id="pdescuento" class="form-control"
                                                        maxlength="15" onkeypress="return filterFloat(event, this);">
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-precio"></span></b></div>
                                                </div> --}}
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

                                    <div class="row m-t-sm" style="text-transform:uppercase">
                                        <div class="col-lg-12">
                                            {{-- <div class="table-responsive">
                                                <table
                                                    class="table table-hover" id="table-detalle-cotizacion">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th class="text-center">ACCIONES</th>
                                                            <th class="text-center">CANT</th>
                                                            <th class="text-center">PRODUCTO</th>
                                                            <th class="text-center">MODELO</th>
                                                            <th class="text-center">COLOR</th>
                                                            <th class="text-center">TALLA</th>
                                                            <th class="text-center">P.UNITARIO</th>
                                                            <th class="text-center">SUBTOTAL</th>
                                                            <th class="text-center">TOTAL</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="7" style="text-align: right !important;">
                                                                Sub Total:</th>
                                                            <th class="text-center"><span
                                                                    id="subtotal">0.00</span></th>

                                                        </tr>
                                                        <tr>
                                                            <th colspan="7" class="text-right">IGV <span
                                                                    id="igv_int"></span>:</th>
                                                            <th class="text-center"><span
                                                                    id="igv_monto">0.00</span></th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" class="text-right">TOTAL:</th>
                                                            <th class="text-center"><span id="total">0.00</span>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div> --}}
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
                                    <a href="{{ route('almacenes.producto.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>
                                    {{-- <button type="submit" id="btn_grabar" form="form_registrar_cotizacion" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button> --}}
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

<div id="reg_clientes">
    <modal-cliente></modal-cliente>
</div>

@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
@endpush

@push('scripts')

<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>

<script>
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    $(document).ready(function() {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });



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
                    sClass: 'text-center'
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

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';
    });

    //Editar Registro
    $(document).on('click', '.btn-edit', function(event) {
        var data = table.row($(this).parents('tr')).data();
        $('#indice').val(table.row($(this).parents('tr')).index());
        $('#producto_editar').val(data[0]).trigger('change');
        $('#precio_editar').val(data[5]);
        $('#presentacion_producto_editar').val(data[3]);
        $('#codigo_nombre_producto_editar').val(data[4]);
        $('#medida_editar').val(data[7]);
        $('#cantidad_editar').val(data[2]);
        // $('#modal_editar_detalle').modal('show');
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
                var table = $('.dataTables-detalle-cotizacion').DataTable();
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

    function obtenerTipo(tipo) {
        if (tipo.value) {
            //$('#producto').prop('disabled', false)
            $('#modelo').prop('disabled', false)
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

    $("#igv_check").click(function() {
        if ($("#igv_check").is(':checked')) {
            $('#igv').attr('disabled', false)
            $('#igv_requerido').addClass("required")
            $('#igv').prop('required', true)
            $('#igv').val('18')
            var igv = ($('#igv').val()) + ' %'
            $('#igv_int').text(igv);
            sumaTotal();

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
        $('#pdescuento').val('')

    }

    //VALIDACION PARA EL INGRRESO A TABLA DETALLE
    // $("#btn_agregar_detalle").click(function() {
    //     limpiarErrores()
    //     var enviar = false;
    //     if ($('#producto').val() == '') {
    //         toastr.error('Seleccione Producto.', 'Error');
    //         enviar = true;
    //         $('#producto').addClass("is-invalid")
    //         $('#error-producto').text('El campo Producto es obligatorio.')
    //     } else {
    //         var existe = buscarProducto($('#producto').val())
    //         if (existe == true) {
    //             toastr.error('Producto ya se encuentra ingresado.', 'Error');
    //             enviar = true;
    //         }
    //     }

    //     if ($('#precio').val() == '') {
    //         toastr.error('Ingrese el precio del producto.', 'Error');
    //         enviar = true;
    //         $("#precio").addClass("is-invalid");
    //         $('#error-precio').text('El campo Precio es obligatorio.')
    //     } else {
    //         if ($('#precio').val() == 0) {
    //             toastr.error('Ingrese el precio del producto superior a 0.0.', 'Error');
    //             enviar = true;
    //             $("#precio").addClass("is-invalid");
    //             $('#error-precio').text('El campo precio debe ser mayor a 0.')
    //         }
    //     }

    //     if ($('#cantidad').val() == '') {
    //         toastr.error('Ingrese cantidad del producto.', 'Error');
    //         enviar = true;
    //         $("#cantidad").addClass("is-invalid");
    //         $('#error-cantidad').text('El campo Cantidad es obligatorio.')
    //     }

    //     if ($('#cantidad').val() == 0) {
    //         toastr.error('El stock del producto es 0.', 'Error');
    //         enviar = true;
    //         $("#cantidad").addClass("is-invalid");
    //         $('#error-cantidad').text('El campo cantidad debe ser mayor a 0.')
    //     }

    //     if (enviar != true) {
    //         Swal.fire({
    //             title: 'Opción Agregar',
    //             text: "¿Seguro que desea agregar Producto?",
    //             icon: 'question',
    //             showCancelButton: true,
    //             confirmButtonColor: "#1ab394",
    //             confirmButtonText: 'Si, Confirmar',
    //             cancelButtonText: "No, Cancelar",
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 obtenerProducto($('#producto').val())
    //             } else if (
    //                 /* Read more about handling dismissals below */
    //                 result.dismiss === Swal.DismissReason.cancel
    //             ) {
    //                 swalWithBootstrapButtons.fire(
    //                     'Cancelado',
    //                     'La Solicitud se ha cancelado.',
    //                     'error'
    //                 )
    //             }
    //         })

    //     }

    // })

    // function buscarProducto(id) {
    //     var existe = false;
    //     table.rows().data().each(function(el, index) {
    //         if (el[0] == id) {
    //             existe = true
    //         }
    //     });
    //     return existe
    // }

    // function agregarTabla($detalle) {
    //     table.row.add([
    //         $detalle.producto_id,
    //         '',
    //         $detalle.cantidad.toFixed(2),
    //         $detalle.producto,
    //         $detalle.valor_unitario.toFixed(2),
    //         $detalle.precio_unitario.toFixed(2),
    //         $detalle.dinero.toFixed(2),
    //         $detalle.precio_nuevo.toFixed(2),
    //         $detalle.valor_venta.toFixed(2),
    //         $detalle.precio_inicial.toFixed(2),
    //         $detalle.descuento.toFixed(2),

    //     ]).draw(false);
    //     cargarProductos();
    // }

    // function obtenerMedida(id) {
    //     var medida = ""
    //     @foreach (unidad_medida() as $medida)
    //         if ("{{ $medida->id }}" == id) {
    //         medida = "{{ $medida->simbolo . ' - ' . $medida->descripcion }}"
    //         }
    //     @endforeach
    //     return medida
    // }

    // function llegarDatos() {
    //     let pdescuento = $("#pdescuento").val().length > 0 ? convertFloat($("#pdescuento").val()) : 0;
    //     let precio_inicial = convertFloat($('#precio').val());
    //     let igv = convertFloat($('#igv').val());
    //     let igv_calculado = convertFloat(igv / 100);

    //     let valor_unitario = 0.00;
    //     let precio_unitario = 0.00;
    //     let dinero = 0.00;
    //     let precio_nuevo = 0.00;
    //     let valor_venta = 0.00;
    //     let cantidad = convertFloat($('#cantidad').val())
    //     if ($("#igv_check").prop('checked')) {
    //         precio_unitario = precio_inicial;
    //         valor_unitario = precio_unitario / (1 + igv_calculado);
    //         dinero = precio_unitario * (pdescuento / 100);
    //         precio_nuevo = precio_unitario - dinero;
    //         valor_venta = precio_nuevo * cantidad;
    //     } else {
    //         precio_unitario = precio_inicial / 1.18;
    //         valor_unitario = precio_unitario / 1.18;
    //         dinero = precio_unitario * (pdescuento / 100);
    //         precio_nuevo = precio_unitario - dinero;
    //         valor_venta = precio_nuevo * cantidad;
    //     }

    //     var detalle = {
    //         producto_id: $('#producto').val(),
    //         producto: $('#codigo_nombre_producto').val(),
    //         precio_unitario: precio_unitario,
    //         valor_unitario: valor_unitario,
    //         valor_venta: valor_venta,
    //         cantidad: cantidad,
    //         dinero: dinero,
    //         descuento: pdescuento,
    //         precio_nuevo: precio_nuevo,
    //         precio_inicial: precio_inicial
    //     }

    //     agregarTabla(detalle);
    // }

    // function cargarProductos() {
    //     var productos = [];
    //     var data = table.rows().data();
    //     data.each(function(value, index) {
    //         let fila = {
    //             producto_id: value[0],
    //             presentacion: value[3],
    //             precio_unitario: value[5],
    //             valor_unitario:value[4],
    //             dinero:value[6],
    //             precio_inicial:value[9],
    //             precio_nuevo:value[7],
    //             descuento:value[10],
    //             cantidad: value[2],
    //             valor_venta: value[8],
    //         };

    //         productos.push(fila);

    //     });

    //     $('#productos_tabla').val(JSON.stringify(productos));
    // }

    // function sumaTotal() {
    //     let total = 0.00;
    //     let igv = convertFloat($('#igv').val());
    //     let igv_calculado = convertFloat(igv / 100);

    //     let detalles = [];

    //     table.rows().data().each(function(el, index) {
    //         let pdescuento = convertFloat(el[10]);
    //         let precio_inicial = convertFloat(el[9]);
    //         let precio_unitario = 0.00;
    //         let valor_unitario = 0.00;
    //         let dinero = 0.00;
    //         let precio_nuevo = 0.00;
    //         let valor_venta = 0.00;

    //         if ($("#igv_check").prop('checked')) {
    //             precio_unitario = precio_inicial;
    //             valor_unitario = precio_unitario / (1 + igv_calculado);
    //             dinero = precio_unitario * (pdescuento / 100);
    //             precio_nuevo = precio_unitario - dinero;
    //             valor_venta = precio_nuevo * el[2];
    //             let detalle = {
    //                 producto_id: el[0],
    //                 producto: el[3],
    //                 precio_unitario: precio_unitario,
    //                 valor_unitario: valor_unitario,
    //                 valor_venta: valor_venta,
    //                 cantidad: convertFloat(el[2]),
    //                 descuento: pdescuento,
    //                 precio_nuevo: precio_nuevo,
    //                 dinero: dinero,
    //                 precio_inicial: precio_inicial
    //             }
    //             detalles.push(detalle);
    //         } else {
    //             precio_unitario = precio_inicial / 1.18;
    //             valor_unitario = precio_unitario / 1.18;
    //             dinero = precio_unitario * (pdescuento / 100);
    //             precio_nuevo = precio_unitario - dinero;
    //             valor_venta = precio_nuevo * el[2];
    //             let detalle = {
    //                 producto_id: el[0],
    //                 producto: el[3],
    //                 precio_unitario: precio_unitario,
    //                 valor_unitario: valor_unitario,
    //                 valor_venta: valor_venta,
    //                 cantidad: convertFloat(el[2]),
    //                 descuento: pdescuento,
    //                 dinero: dinero,
    //                 precio_nuevo: precio_nuevo,
    //                 precio_inicial: precio_inicial
    //             }
    //             detalles.push(detalle);
    //         }
    //     });

    //     table.clear().draw();

    //     if(detalles.length > 0)
    //     {
    //         for(let i = 0; i < detalles.length; i++)
    //         {
    //             agregarTabla(detalles[i]);
    //         }
    //     }

    //     table.rows().data().each(function(el, index) {
    //         total = Number(el[8]) + total
    //     });

    //     $('#total').text((Math.round(total * 10) / 10).toFixed(2))
    //     //conIgv(total,igv)
    // }

    // function conIgv(subtotal, igv) {
    //     let total = subtotal * (1 + (igv / 100));
    //     let igv_calculado =  total - subtotal;
    //     $('#igv_int').text(igv + '%')
    //     $('#subtotal').text((Math.round(subtotal * 10) / 10).toFixed(2))
    //     $('#igv_monto').text((Math.round(igv_calculado * 10) / 10).toFixed(2))
    //     $('#total').text((Math.round(total * 10) / 10).toFixed(2))
    //     //Math.round(fDescuento * 10) / 10
    // }


    // function registrosProductos() {
    //     var registros = table.rows().data().length;
    //     return registros
    // }

    // function validarFecha() {
    //     var enviar = false
    //     var productos = registrosProductos()
    //     if ($('#fecha_documento').val() == '') {
    //         toastr.error('Ingrese Fecha de Documento.', 'Error');
    //         $("#fecha_documento").focus();
    //         enviar = true;
    //     }

    //     if ($('#fecha_atencion').val() == '') {
    //         toastr.error('Ingrese Fecha de Atención.', 'Error');
    //         $("#fecha_atencion").focus();
    //         enviar = true;
    //     }

    //     if (productos == 0) {
    //         toastr.error('Ingrese al menos 1 Producto.', 'Error');
    //         enviar = true;
    //     }
    //     return enviar
    // }

 
</script>

<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
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
   
    document.addEventListener('DOMContentLoaded',()=>{
        console.log('coti create');
        //setUbicacionDepartamento({value:13});
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
            console.log('hola papu')
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

            // const formData = new FormData(formCotizacion);
            // formData.append("carrito", JSON.stringify(carrito));
            // formData.forEach((valor, clave) => {
            //      console.log(`${clave}: ${valor}`);
            // });
        })
       

        //======= AGREGAR PRODUCTO AL DETALLE ======
        btnAgregarDetalle.addEventListener('click',()=>{
            
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
        const precio_venta          = document.querySelector(`#precio-venta-${producto_id}`).value;
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
        let fila= ``;
        let htmlTallas= ``;
        clearDetalleCotizacion();

        carrito.forEach((c)=>{
            htmlTallas=``;
                fila+= `<tr>   
                            <td>
                                <i class="fas fa-trash-alt btn btn-primary delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>                            
                            </td>
                            <th>${c.producto_nombre} - ${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t)=>{
                    let cantidad = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });
                    cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad=0;
                    htmlTallas += `<td>${cantidad}</td>`; 
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

                fila+=htmlTallas;
                tableDetalleBody.innerHTML=fila;            
        })
    }

    function getProductosByModelo(e){
        modelo_id = e.value;
        btnAgregarDetalle.disabled=true;
        
        if(modelo_id){
            const url = `/get-producto-by-modelo/${modelo_id}`;
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': tokenValue,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.stocks);
                    console.log(data.producto_colores);
                    pintarTableStocks(data.stocks,tallas,data.producto_colores);
                    loadCarrito();
                })
                .catch(error => console.error('Error:', error));
            
        }else{
            tableStocksBody.innerHTML = ``;
        }
    }

    const pintarTableStocks = (stocks,tallas,producto_colores)=>{
        let options =``;

        producto_colores.forEach((pc)=>{
            options+=`  <tr>
                            <th scope="row" data-producto=${pc.producto_id} data-color=${pc.color_id} >
                                ${pc.producto_nombre} - ${pc.color_nombre}
                            </th>
                        `;

            let htmlTallas = ``;

            tallas.forEach((t)=>{
                const stock = stocks.filter(st => st.producto_id == pc.producto_id && st.color_id == pc.color_id && st.talla_id == t.id)[0]?.stock || 0;

                htmlTallas +=   `
                                    <td style="background-color: rgb(210, 242, 242);">${stock}</td>
                                    <td width="8%">
                                        <input type="text" class="form-control inputCantidad"
                                        id="inputCantidad_${pc.producto_id}_${pc.color_id}_${t.id}" 
                                        data-producto-id="${pc.producto_id}"
                                        data-producto-nombre="${pc.producto_nombre}"
                                        data-color-nombre="${pc.color_nombre}"
                                        data-talla-nombre="${t.descripcion}"
                                        data-color-id="${pc.color_id}" data-talla-id="${t.id}"></input>    
                                    </td>
                                `;   
            })

            if(pc.printPreciosVenta){
                htmlTallas+=`
                    <td>
                        <select class="select2_form form-control" id="precio-venta-${pc.producto_id}">
                            <option>${pc.precio_venta_1}</option>    
                            <option>${pc.precio_venta_2}</option>    
                            <option>${pc.precio_venta_3}</option>    
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
    }


    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL CARRITO =========
    function loadCarrito(){
        carrito.forEach((c)=>{
            const inputLoad = document.querySelector(`#inputCantidad_${c.producto_id}_${c.color_id}_${c.talla_id}`);
            if(inputLoad){
                inputLoad.value = c.cantidad;
            }

            //==== ubicando precios venta seleccionados ======
            const selectPrecioVenta =   document.querySelector(`#precio-venta-${c.producto_id}`);
            if(selectPrecioVenta){
                selectPrecioVenta.value =   c.precio_venta.toString();
            }
        }) 
    }

    // //============= ABRIR MODAL CLIENTE =============
    function openModalCliente(){
        $("#modal_cliente").modal("show");
    }


</script>

@endpush







