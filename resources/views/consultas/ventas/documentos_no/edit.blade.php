@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('consulta-ventas-active', 'active')
@section('consulta-ventas-documento-no-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>EDITAR DOCUMENTO DE VENTA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('consultas.ventas.documento.no.index') }}">Documentos de venta no enviados</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Editar</strong>
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
                    <form action="" method="POST" id="enviar_documento">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-12">
                                <h4 class=""><b>Documento de venta</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Edtar datos del documento de venta:</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 b-r">
                                <div class="row">
                                    <div class="col-12 col-md-6" id="fecha_documento">
                                        <div class="form-group">
                                            <label class="">Fecha de Documento</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="date" id="fecha_documento_campo" name="fecha_documento_campo"
                                                    class="form-control input-required{{ $errors->has('fecha_documento_campo') ? ' is-invalid' : '' }}"
                                                    value="{{ old('fecha_documento_campo', $documento->fecha_documento) }}" autocomplete="off"
                                                    required readonly>

                                                @if ($errors->has('fecha_documento_campo'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_documento_campo') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6" id="fecha_entrega">
                                        <div class="form-group">
                                            <label class="">Fecha de Atención</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>

                                                <input type="date" id="fecha_atencion_campo" name="fecha_atencion_campo"
                                                        class="form-control input-required {{ $errors->has('fecha_atencion_campo') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_atencion_campo', $documento->fecha_atencion) }}" autocomplete="off" required
                                                        readonly disabled>

                                                @if ($errors->has('fecha_atencion_campo'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_atencion_campo') }}</strong>
                                                    </span>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            <label class="required">Tipo de Comprobante: </label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('tipo_venta') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{ old('tipo_venta', $documento->tipo_venta) }}"
                                                name="tipo_venta" id="tipo_venta" required onchange="consultarSeguntipo()" disabled required>
                                                <option></option>

                                                @foreach (tipos_venta() as $tipo)
                                                    @if ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')
                                                        <option value="{{ $tipo->id }}" @if (old('tipo_venta') == $tipo->id) {{ 'selected' }} @endif  {{ $tipo->id == $documento->tipo_venta ? 'selected' : '' }}>
                                                            {{ $tipo->nombre }}</option>
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
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            <label>Moneda:</label>
                                            <select id="moneda" name="moneda"
                                                class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                required disabled>
                                                <option selected>SOLES</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group select-required d-none">
                                            <label class="required">Empresa: </label>

                                            <select class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{ old('empresa_id') }}" name="empresa_id"
                                                id="empresa_id" required onchange="obtenerTiposComprobantes(this)" disabled>
                                                <option></option>
                                                @foreach ($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}" @if (old('empresa_id') == $empresa->id) {{ 'selected' }} @endif {{ $empresa->id === 1 ? 'selected' : '' }}>{{ $empresa->razon_social }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 select-required">
                                        <div class="form-group">
                                            <label class="required">Condición</label>
                                            <select id="condicion_id" name="condicion_id"
                                                class="select2_form form-control {{ $errors->has('condicion_id') ? ' is-invalid' : '' }}"
                                                required disabled onchange="changeFormaPago()">
                                                <option></option>
                                                @foreach ($condiciones as $condicion)
                                                    <option value="{{ $condicion->id }}-{{ $condicion->descripcion }}"
                                                        {{ old('condicion_id') == $condicion->id || $documento->condicion_id == $condicion->id ? 'selected' : '' }} data-dias="{{$condicion->dias}}">
                                                        {{ $condicion->descripcion }} {{ $condicion->dias > 0 ? $condicion->dias.' dias' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6" id="fecha_vencimiento">
                                        <div class="form-group">
                                            <label class="required">Fecha de vencimiento</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="date" id="fecha_vencimiento_campo" name="fecha_vencimiento_campo"
                                                    class="form-control input-required" autocomplete="off"
                                                    {{ $errors->has('fecha_vencimiento_campo') ? ' is-invalid' : '' }}
                                                    value="{{ old('fecha_vencimiento_campo', $documento->fecha_vencimiento) }}" required>
                                                @if ($errors->has('fecha_vencimiento_campo'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha_vencimiento_campo') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group select-required">
                                            <label class="required">Cliente:</label>
                                            <input type="hidden" name="tipo_cliente_documento" id="tipo_cliente_documento">
                                            <input type="hidden" name="tipo_cliente_2" id="tipo_cliente_2" value='1'>
                                            <select class="select2_form form-control input-required {{ $errors->has('cliente_id') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{ old('cliente_id', $documento->cliente_id) }}" name="cliente_id"
                                                id="cliente_id" required onchange="obtenerTipocliente(this.value)">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group d-none">
                                            <label>Observación:</label>

                                            <textarea type="text" placeholder=""
                                                class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                                name="observacion" id="observacion" onkeyup="return mayus(this)"
                                                value="{{ old('observacion') }}">{{ old('observacion', $documento->observacion) }}</textarea>


                                            @if ($errors->has('observacion'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('observacion') }}</strong>
                                                </span>
                                            @endif

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
                                <input type="hidden" class="form-control" id="productos_detalle" name="productos_detalle" value="{{$detalles}}">
                                <!-- TIPO PAGO -->
                                <input type="hidden" class="form-control" name="tipo_pago_id" id="tipo_pago_id" value="{{ $documento->tipo_pago_id}}">
                                <!-- EFECTIVO -->
                                <input type="hidden" class="form-control" name="efectivo" id="efectivo_form">
                                <!-- IMPORTE -->
                                <input type="hidden" class="form-control" name="importe" id="importe_form">

                            </div>

                        </div>

                        <input type="hidden" name="igv" id="igv" value="{{ $documento->igv ? $documento->igv : 18}}">

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

                                        <input type="hidden" name="lote_id" id="lote_id">
                                        <input type="hidden" name="producto_unidad" id="producto_unidad">

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
                                    <div class="row">
                                        <div class="col-12">
                                            <button  type="button" class="btn btn-info" id="buscarLotesRecientes"
                                            data-toggle="modal" data-target="#modal_lote_recientes">Buscar lotes recientes</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table dataTables-detalle-documento table-striped table-bordered table-hover"
                                                    style="text-transform:uppercase">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th class="text-center">ACCIONES</th>
                                                            <th class="text-center">CANTIDAD</th>
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
                                                            <th class="text-center" colspan="9">Sub Total:</th>
                                                            <th class="text-center"><span
                                                                    id="subtotal">0.0</span></th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" colspan="9">IGV <span id="igv_int"></span>:</th>
                                                            <th class="text-center"><span
                                                                    id="igv_monto">0.0</span></th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center" colspan="9">TOTAL:</th>
                                                            <th class="text-center"><span id="total">0.0</span>
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
                    <div class="form-group row">

                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                (<label class="required"></label>) son obligatorios.</small>
                        </div>

                        <div class="col-md-6 text-right">

                            <a href="{{ route('consultas.ventas.documento.no.index') }}" id="btn_cancelar" class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            <button type="button" id="btn_grabar" class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('consultas.ventas.documentos_no.modal')
@include('consultas.ventas.documentos_no.modalLote')
@include('consultas.ventas.documentos_no.modalPago')
@include('consultas.ventas.documentos_no.modalLoteRecientes')
@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">

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


<script>
    //PRUEBA
    var clientes_global = [];
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
            container: 'my-swal',
        },
        buttonsStyling: false
    })

    $('#cantidad').on('input', function() {
        let max = convertFloat(this.max);
        let valor = convertFloat(this.value);
        if (valor > max) {
            toastr.error('La cantidad ingresada supera al stock del producto Max(' + max + ').', 'Error');
            this.value = max;
        }
    });

    //Editar Registro
    $(document).on('click', '.btn-edit', function(event) {
        var table = $('.dataTables-detalle-documento').DataTable();
        var data = table.row($(this).parents('tr')).data();
        let indice = table.row($(this).parents('tr')).index();
        $.ajax({
            type: 'POST',
            url: '{{ route('consultas.ventas.documento.no.obtener.lote') }}',
            data: {
                '_token': $('input[name=_token]').val(),
                'lote_id': data[0],
            }
        }).done(function(response) {
            if (response.success) {
                $('#indice').val(indice);
                $('#producto_lote_editar').val(data[4]);
                $('#producto_editar').val(data[0]);
                $('#precio_editar').val(data[10]);
                $('#codigo_nombre_producto_editar').val(data[4]);
                $('#cantidad_editar').val(data[2]);
                $('#id_editar').val(data[12]);
                $('#cantidad_editar_actual').val(data[2]);
                $('#medida_editar').val(data[3]);
                $('#modal_editar_detalle').modal('show');

                let detalles = JSON.parse($("#productos_detalle").val());
                let cont = 0;
                let existe = false;
                while(cont < detalles.length)
                {
                    if(detalles[cont].lote_id == data[0])
                    {
                        existe = true;
                        cont = detalles.length;
                    }
                    cont =  cont + 1;
                }

                let suma_cant = 0;
                if(existe)
                {
                    let iIndice = detalles.findIndex(detalle => detalle.lote_id == data[0]);
                    let lot = detalles[iIndice];
                    suma_cant = parseFloat(response.lote.cantidad_logica) + (parseFloat(data[2]));// - lot.cantidad
                }
                else
                {
                    suma_cant = parseFloat(response.lote.cantidad_logica) + parseFloat(data[2]);
                }


                //AGREGAR LIMITE A LA CANTIDAD SEGUN EL LOTE SELECCIONADO
                $("#cantidad_editar").attr({
                    "max": suma_cant,
                    "min": 1,
                });
            } else {
                toastr.warning('Ocurrió un error porfavor recargar la pagina.')
            }
        });

    })


    function obtenerMax(id) {
        $.get('/almacenes/productos/obtenerProducto/' + id, function(data) {
            //AGREGAR LIMITE A LA CANTIDAD
            $("#cantidad_editar").attr({
                "max": data.producto.stock,
                "min": 1,
            });
        })
    }


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
                var table = $('.dataTables-detalle-documento').DataTable();
                var data = table.row($(this).parents('tr')).data();
                var detalle = {
                    lote_id: data[0],
                    cantidad: data[2],
                }
                //DEVOLVER LA CANTIDAD LOGICA

                let detalles = JSON.parse($("#productos_detalle").val());
                let cont = 0;
                let existe = false;
                while(cont < detalles.length)
                {
                    if(detalles[cont].lote_id == detalle.lote_id)
                    {
                        existe = true;
                        cont = detalles.length;
                    }
                    cont =  cont + 1;
                }

                if(existe)
                {
                    let iIndice = detalles.findIndex(item => item.lote_id == detalle.lote_id);
                    let lot = detalles[iIndice];

                    if(detalle.cantidad - lot.cantidad > 0)
                    {
                        let detalle_aux = {
                            lote_id: lot.lote_id,
                            cantidad: detalle.cantidad - lot.cantidad
                        }
                        cambiarCantidad(detalle_aux, '0');
                    }
                }
                else
                {
                    cambiarCantidad(detalle, '0')
                }

                $('#asegurarCierre').val(1);
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

    $(document).ready(function() {
        changeFormaPago();
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
        });
        obtenerClientes();
    });

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

    $(document).ready(function() {
        table = $('.dataTables-detalle-documento').DataTable({
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

                    render: function(data, type, row) {
                        return "<div class='btn-group'>" +
                            "<a class='btn btn-sm btn-warning btn-edit' style='color:white'>"+ "<i class='fa fa-pencil'></i>"+"</a>" +
                            "<a class='btn btn-sm btn-danger btn-delete' style='color:white'>"+"<i class='fa fa-trash'></i>"+"</a>"+
                            "</div>";
                    }

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
                    'visible': false
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
                },
                {
                    "targets": [10],
                    'visible': false
                },
                {
                    "targets": [11],
                    'visible': false
                }
            ],
            'bAutoWidth': false,
            'aoColumns': [{
                    sWidth: '0%'
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
                    sWidth: '25%',
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
            ],
            "language": {
                url: "{{ asset('Spanish.json') }}"
            },
            "order": [
                [0, "desc"]
            ],
        });

        @if ($documento->igv_check == '1')
            $('#igv').prop('disabled', false)
            $("#igv_check").prop('checked',true)

            $('#igv_requerido').addClass("required")
            $('#igv').prop('required', true)
            var igv = ($('#igv').val()) + ' %'
            $('#igv_int').text(igv)
        @else
            if ($("#igv_check").prop('checked')) {
                $('#igv').attr('disabled', false)
                $('#igv_requerido').addClass("required")
            } else {
                $('#igv').attr('disabled', true)
                $('#igv_requerido').removeClass("required")
            }
        @endif

        @if ($detalles)
            obtenerTabla()
        @endif


        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';
    });

    function changeFormaPago()
    {
        let condicion_id = $('#condicion_id').val();
        if(condicion_id)
        {
            let cadena = condicion_id.split('-');
            let dias = convertFloat($('#condicion_id option:selected').data('dias')) + 1
            let fecha = new Date('{{ $fecha_hoy }}')

            fecha.setDate(fecha.getDate() + dias)

            let month = (fecha.getMonth() + 1).toString().length > 1 ? (fecha.getMonth() + 1) : '0' + (fecha.getMonth() + 1)
            let day = (fecha.getDate()).toString().length > 1 ? (fecha.getDate()) : '0' + (fecha.getDate())
            let resultado = fecha.getFullYear() + '-' + month + '-' + day
            $("#fecha_vencimiento_campo").val(resultado);
            if(cadena[1] == 'CONTADO')
            {
                $('#fecha_vencimiento').addClass('d-none');
            }
            else
            {
                $('#fecha_vencimiento').removeClass('d-none');
            }
        }
        else
        {
            $('#fecha_vencimiento').addClass('d-none');
            $("#fecha_vencimiento_campo").val('{{ $fecha_hoy }}');
        }
    }

    function limpiarErrores() {
        $('#cantidad').removeClass("is-invalid")
        $('#error-cantidad').text('')

        $('#precio').removeClass("is-invalid")
        $('#error-precio').text('')

        $('#producto').removeClass("is-invalid")
        $('#error-producto').text('')
    }

    //Validacion al ingresar tablas
    $("#btn_agregar_detalle").click(function() {
        limpiarErrores()
        var enviar = false;
        if ($('#lote_id').val() == '') {
            toastr.error('Seleccione Producto.', 'Error');
            enviar = true;
            $('#lote_id').addClass("is-invalid")
            $('#error-producto').text('El campo Producto es obligatorio.')
        } else {
            var existe = buscarProducto($('#lote_id').val())
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
        } else {
            if ($('#precio').val() == 0) {
                toastr.error('Ingrese el precio del producto superior a 0.0.', 'Error');
                enviar = true;
                $("#precio").addClass("is-invalid");
                $('#error-precio').text('El campo precio debe ser mayor a 0.')
            }
        }

        if ($('#cantidad').val() == '') {
            toastr.error('Ingrese cantidad del artículo.', 'Error');
            enviar = true;
            $("#cantidad").addClass("is-invalid");
            $('#error-cantidad').text('El campo Cantidad es obligatorio.')
        }

        if ($('#cantidad').val() == 0) {
            toastr.error('El stock del producto es 0.', 'Error');
            enviar = true;
            $("#cantidad").addClass("is-invalid");
            $('#error-cantidad').text('El campo cantidad debe ser mayor a 0.')
        }

        if (enviar != true) {
            llegarDatos();
            sumaTotal();
            $('#asegurarCierre').val(1);
        }
    })

    function buscarProducto(id) {
        var existe = false;
        var t = $('.dataTables-detalle-documento').DataTable();
        t.rows().data().each(function(el, index) {
            if (el[0] == id) {
                existe = true
            }
        });
        return existe
    }

    function llegarDatos() {
        let pdescuento = 0;
        let precio_inicial = convertFloat($('#precio').val());
        let igv = convertFloat($('#igv').val());
        let igv_calculado = convertFloat(igv / 100);

        let valor_unitario = 0.00;
        let precio_unitario = 0.00;
        let dinero = 0.00;
        let precio_nuevo = 0.00;
        let valor_venta = 0.00;
        let cantidad = convertFloat($('#cantidad').val());

        precio_unitario = precio_inicial;
        valor_unitario = precio_unitario / (1 + igv_calculado);
        dinero = precio_unitario * (pdescuento / 100);
        precio_nuevo = precio_unitario - dinero;
        valor_venta = precio_nuevo * cantidad;

        let lote_id = $('#lote_id').val();

        let detalles = JSON.parse($("#productos_detalle").val());
        let detalle_id = 0;
        let cont = 0;
        let existe = false;
        while(cont < detalles.length)
        {
            if(detalles[cont].lote_id == lote_id)
            {
                detalle_id = detalles[cont].id;
                existe = true;
                cont = detalles.length;
            }
            cont =  cont + 1;
        }

        let detalle = {
            lote_id: $('#lote_id').val(),
            unidad: $('#producto_unidad').val(),
            producto: $('#producto_lote').val(),
            precio_unitario: precio_unitario,
            valor_unitario: valor_unitario,
            valor_venta: valor_venta,
            cantidad: cantidad,
            precio_inicial: precio_inicial,
            dinero: dinero,
            descuento: pdescuento,
            precio_nuevo: precio_nuevo,
            detalle_id: detalle_id,
        }
        if(existe)
        {
            toastr.warning('Este producto es parte del detalle actual, si desea ingresarlo nuevamente buscarlo en lotes recientes.');
            limpiarDetalleLote();
        }
        else
        {
            agregarTabla(detalle);
            cambiarCantidad(detalle, '1');
        }
        $('#precio').prop('disabled' , true)
        $('#cantidad').prop('disabled' , true)
    }

    //AGREGAR EL DETALLE A LA TABLA
    function agregarTabla($detalle) {
        var t = $('.dataTables-detalle-documento').DataTable();
        t.row.add([
            $detalle.lote_id,
            '',
            Number($detalle.cantidad),
            $detalle.unidad,
            $detalle.producto,
            Number($detalle.valor_unitario).toFixed(2),
            Number($detalle.precio_unitario).toFixed(2),
            Number($detalle.dinero).toFixed(2),
            Number($detalle.precio_nuevo).toFixed(2),
            Number($detalle.valor_venta).toFixed(2),
            $detalle.precio_inicial,
            $detalle.descuento,
            $detalle.detalle_id,
        ]).draw(false);
        limpiarDetalleLote();
    }

    function limpiarDetalleLote()
    {
        $('#precio').val('')
        $('#cantidad').val('')
        $('#producto_unidad').val('')
        $('#lote_id').val('')
        $('#producto_lote').val('')
    }
    //CARGAR EL DETALLE A UNA VARIABLE
    function cargarProductos() {
        var productos = [];
        var table = $('.dataTables-detalle-documento').DataTable();
        var data = table.rows().data();
        data.each(function(value, index) {
            let fila = {
                lote_id: value[0],
                unidad: value[3],
                valor_unitario: value[5],
                precio_unitario: value[6],
                dinero: value[7],
                precio_nuevo: value[8],
                precio_inicial: value[10],
                descuento: value[11],
                cantidad: value[2],
                valor_venta: value[9],
                detalle_id: value[12],
            };
            productos.push(fila);
        });

        $('#productos_tabla').val(JSON.stringify(productos));
    }
    //CAMBIAR LA CANTIDAD LOGICA DEL PRODUCTO
    function cambiarCantidad(detalle, condicion) {
        $.ajax({
            dataType: 'json',
            type: 'post',
            url: '{{ route('consultas.ventas.documento.no.cantidad') }}',
            data: {
                '_token': $('input[name=_token]').val(),
                'lote_id': detalle.lote_id,
                'cantidad': detalle.cantidad,
                'condicion': condicion,
            }
        }).done(function(result) {
            alert('REVISAR')
        });
    }
    //DEVOLVER CANTIDADES A LOS LOTES
    function devolverCantidades() {
        //CARGAR PRODUCTOS PARA DEVOLVER LOTE
        cargarProductos()
        return $.ajax({
            dataType: 'json',
            type: 'post',
            url: '{{ route('consultas.ventas.documento.no.devolver.cantidades') }}',
            data: {
                '_token': $('input[name=_token]').val(),
                'cantidades': $('#productos_tabla').val(),
                'detalles' : $("#productos_detalle").val()
            },
            async: true
        }).responseText();
    }

    function sumaTotal() {
        var t = $('.dataTables-detalle-documento').DataTable();
        let total = 0.00;
        let detalles = [];

        t.rows().data().each(function(el, index) {
            let igv = convertFloat(18);
            let igv_calculado = convertFloat(igv / 100);
            let pdescuento = convertFloat(el[11]);
            let precio_inicial = convertFloat(el[10]);
            let precio_unitario = precio_inicial;
            let valor_unitario = precio_unitario / (1 + igv_calculado);
            let dinero = precio_unitario * (pdescuento / 100);
            let precio_nuevo = precio_unitario - dinero;
            let valor_venta = precio_nuevo * el[2];
            let detalle_id = el[12];

            let detalle = {
                lote_id: el[0],
                unidad: el[3],
                producto: el[4],
                precio_unitario: precio_unitario,
                valor_unitario: valor_unitario,
                valor_venta: valor_venta,
                cantidad: convertFloat(el[2]),
                precio_inicial: precio_inicial,
                dinero: dinero,
                descuento: pdescuento,
                precio_nuevo: precio_nuevo,
                detalle_id: detalle_id,
            }
            detalles.push(detalle);
        });

        t.clear().draw();

        if(detalles.length > 0)
        {
            for(let i = 0; i < detalles.length; i++) {
                agregarTabla(detalles[i]);
            }
        }
        t.rows().data().each(function(el, index) {
            total=Number(el[9]) + total
        });
        conIgv(convertFloat(total),convertFloat(18))
    }

    function conIgv(total, igv) {
        let subtotal = total / (1 + (igv / 100));
        let igv_calculado = total - subtotal;
        $('#igv_int').text(igv + '%')
        $('#subtotal').text((subtotal).toFixed(2))
        $('#igv_monto').text((igv_calculado).toFixed(2))
        $('#total').text((total).toFixed(2))
        //Math.round(fDescuento * 10) / 10
    }

    function registrosProductos() {
        var table = $('.dataTables-detalle-documento').DataTable();
        var registros = table.rows().data().length;
        return registros
    }

    function validarFecha() {
        var enviar = false
        var productos = registrosProductos()
        if ($('#fecha_documento_campo').val() == '') {
            toastr.error('Ingrese Fecha de Documento.', 'Error');
            $("#fecha_documento_campo").focus();
            enviar = true;
        }

        if ($('#fecha_atencion_campo').val() == '') {
            toastr.error('Ingrese Fecha de Atención.', 'Error');
            $("#fecha_atencion_campo").focus();
            enviar = true;
        }

        if (productos == 0) {
            toastr.error('Ingrese al menos 1 Producto.', 'Error');
            enviar = true;
        }
        return enviar
    }

    function validarTipo() {

        var enviar = false

        if ($('#tipo_cliente_documento').val() == '0' && $('#tipo_venta').val() == 'FACTURA') {
            toastr.error('El tipo de documento del cliente es diferente a RUC.', 'Error');
            enviar = true;
        }
        return enviar

    }

    $('#btn_grabar').click(function(e) {
        e.preventDefault();
        cargarProductos();
        let correcto = validarCampos();

        $('#monto_sub_total').val($('#subtotal').text())
        $('#monto_total_igv').val($('#igv_monto').text())
        $('#monto_total').val($('#total').text())

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
    });

    $('#btn_grabar_pago').click(function(e) {
        e.preventDefault();
        let monto = convertFloat($('#monto_venta').val());
        let importe = convertFloat($('#importe_venta').val());
        let efectivo = convertFloat($('#efectivo_venta').val());
        let suma = importe + efectivo;

        $('#importe_form').val(importe);
        $('#efectivo_form').val(efectivo);

        let correcto = validarCampos();

        if ($('#monto_venta').val() == null || $('#monto_venta').val() == '') {
            correcto = false;
            toastr.error('El campo monto es requerido.');
        }

        if ($('#importe_venta').val() == null || $('#importe_venta').val() == '') {
            correcto = false;
            toastr.error('El campo monto es requerido.');
        }

        if ($('#efectivo_venta').val() == null || $('#efectivo_venta').val() == '') {
            correcto = false;
            toastr.error('El campo efectivo es requerido.');
        }

        if (monto.toFixed(2) != suma.toFixed(2)) {
            correcto = false;
            toastr.error('La suma del importe y el efectivo debe ser igual al monto de la venta.');
        }
        if (correcto) {
            enviarVenta();
        }
    });

    function validarCampos() {
        let correcto = true;
        let moneda = $('#moneda').val();
        let observacion = $('#observacion').val();
        let condicion_id = $('#condicion_id').val();
        let fecha_documento_campo = $('#fecha_documento_campo').val();
        let fecha_atencion_campo = $('#fecha_atencion_campo').val();
        let fecha_vencimiento_campo = $('#fecha_vencimiento_campo').val();
        let empresa_id = $('#empresa_id').val();
        let cliente_id = $('#cliente_id').val();
        let tipo_venta = $('#tipo_venta').val();


        let detalles = $('#productos_tabla').val();
        let detalles_convertido = JSON.parse(detalles);
        if (detalles_convertido.length < 1) {
            correcto = false;
            toastr.error('El documento de venta debe tener almenos un producto vendido.');
        }
        if (moneda == null || moneda == '') {
            correcto = false;
            toastr.error('El campo moneda es requerido.');
        }
        if (condicion_id == null || condicion_id == '') {
            correcto = false;
            toastr.error('El campo condición es requerido.');
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

    function obtenerTabla() {
        var t = $('.dataTables-detalle-documento').DataTable();
        var detalles = JSON.parse($("#productos_detalle").val());

        for (var i = 0; i < detalles.length; i++) {
            t.row.add([
                detalles[i].lote_id,
                '',
                detalles[i].cantidad,
                detalles[i].unidad,
                detalles[i].lote.producto.nombre+'-'+detalles[i].lote.codigo_lote,
                detalles[i].valor_unitario,
                detalles[i].precio_unitario,
                detalles[i].dinero,
                detalles[i].precio_nuevo,
                detalles[i].valor_venta,
                detalles[i].precio_inicial,
                detalles[i].descuento,
                detalles[i].id,
            ]).draw(false);
        }

        $('#asegurarCierre').val(1);
        //SUMATORIA TOTAL
        sumaTotal()
    }

    //OBTENER TIPOS DE COMPROBANTES
    function obtenerTiposComprobantes() {

        if ($('#empresa_id').val() != '' && $('#tipo_venta').val() != '') {
            $.ajax({
                dataType: 'json',
                url: '{{ route('ventas.vouchersAvaible') }}',
                type: 'post',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'empresa_id': $('#empresa_id').val(),
                    'tipo_id': $('#tipo_venta').val()
                },
                success: function(response) {
                    if (response.existe == false) {
                        toastr.error('La empresa ' + response.empresa +
                            ' no tiene registrado el comprobante ' + response.comprobante, 'Error');
                    } else {
                        toastr.success('La empresa ' + response.empresa +
                            ' tiene registrado el comprobante ' + response.comprobante,
                            'Accion Correcta');
                    }

                },
            })
        }

    }

    function consultarSeguntipo() {
        $('#empresa_id').prop("disabled", false);
        obtenerTiposComprobantes()
        //obtenerClientes()
    }

    function obtenerClientes() {
        clientes_global = [];
        $("#cliente_id").empty().trigger('change');
        $("#cliente_id").removeAttr('onchange', 'obtenerTipocliente(this.value)');
        $('#panel_detalle').children('.ibox-content').toggleClass('sk-loading');
        axios.post('{{ route('ventas.customers_all') }}',{'_token': $('input[name=_token]').val(), 'tipo_id': $('#tipo_venta').val()}).then(response => {

            let data = response.data;
            clientes_global = data.clientes;
            if (data.clientes.length > 0) {
                $('#cliente_id').append('<option></option>').trigger('change');
                for(var i = 0;i < data.clientes.length; i++)
                {
                    var newOption = '';
                    if(data.clientes[i].id == '{{$documento->cliente_id}}')
                    {
                        newOption = '<option value="'+data.clientes[i].id+'" selected tabladetalle="'+data.clientes[i].tabladetalles_id+'">'+data.clientes[i].tipo_documento + ': ' + data.clientes[i].documento + ' - ' + data.clientes[i].nombre+'</option>'
                    }
                    else
                    {
                        newOption = '<option value="'+data.clientes[i].id+'" tabladetalle="'+data.clientes[i].tabladetalles_id+'">'+data.clientes[i].tipo_documento + ': ' + data.clientes[i].documento + ' - ' + data.clientes[i].nombre+'</option>'
                    }
                    $('#cliente_id').append(newOption).trigger('change');
                }

            } else {
                toastr.error('Clientes no encontrados.', 'Error');
            }
            $('#tipo_cliente_documento').val(data.tipo);
            $("#cliente_id").attr('onchange', 'obtenerTipocliente(this.value)');
            obtenerTipocliente(1)
            $('#panel_detalle').children('.ibox-content').toggleClass('sk-loading');
        })
    }

    function obtenerTipocliente(cliente_id) {
        if (cliente_id != '') {
            $('#buscarLotes').prop("disabled", false)
        }
        else{
            $('#buscarLotes').prop("disabled", true)
        }
    }

    function enviarVenta()
    {
        axios.get("{{ route('Caja.movimiento.verificarestado') }}").then((value) => {
            let data = value.data;
            if (!data.success) {
                toastr.error(data.mensaje);
            } else {
                let envio_ok = true;

                var tipo = validarTipo();

                if (tipo == false) {
                    cargarProductos();
                    //CARGAR DATOS TOTAL
                    $('#monto_sub_total').val($('#subtotal').text())
                    $('#monto_total_igv').val($('#igv_monto').text())
                    $('#monto_total').val($('#total').text())

                    document.getElementById("moneda").disabled = false;
                    document.getElementById("observacion").disabled = false;
                    document.getElementById("fecha_documento_campo").disabled = false;
                    document.getElementById("fecha_atencion_campo").disabled = false;
                    document.getElementById("empresa_id").disabled = false;
                    document.getElementById("cliente_id").disabled = false;
                    document.getElementById("condicion_id").disabled = false;
                    //HABILITAR EL CARGAR PAGINA
                }
                else
                {
                    envio_ok = false;
                }

                if(envio_ok)
                {
                    let formDocumento = document.getElementById('enviar_documento');
                    let formData = new FormData(formDocumento);

                    var object = {};
                    formData.forEach(function(value, key){
                        object[key] = value;
                    });

                    //var json = JSON.stringify(object);

                    var datos = object;
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

                    var url = '{{ route("consultas.ventas.documento.no.update",":id") }}';
                    url = url.replace(":id","{{ $documento->id }}")
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
                                let mensaje = sHtmlErrores(result.value.data.mensajes);
                                toastr.error(mensaje);

                                $('#asegurarCierre').val(1);
                                document.getElementById("moneda").disabled = true;
                                document.getElementById("observacion").disabled = true;
                                document.getElementById("fecha_documento_campo").disabled = true;
                                document.getElementById("fecha_atencion_campo").disabled = true;
                                document.getElementById("empresa_id").disabled = true;
                                document.getElementById("cliente_id").disabled = true;
                                document.getElementById("condicion_id").disabled = true;
                            }
                            else if(result.value.success)
                            {
                                toastr.success('¡Documento de venta modificado!','Exito')

                                $('#asegurarCierre').val(2);

                                location = "{{ route('consultas.ventas.documento.no.index') }}";
                            }
                            else
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: '¡'+ result.value.mensaje +'!',
                                    customClass: {
                                        container: 'my-swal'
                                    },
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                                $('#asegurarCierre').val(1);
                                document.getElementById("moneda").disabled = true;
                                document.getElementById("observacion").disabled = true;
                                document.getElementById("fecha_documento_campo").disabled = true;
                                document.getElementById("fecha_atencion_campo").disabled = true;
                                document.getElementById("empresa_id").disabled = true;
                                document.getElementById("cliente_id").disabled = true;
                                document.getElementById("condicion_id").disabled = true;
                            }
                        }
                    });
                }
            }
        })
    }

    function modalCliente() {
        document.getElementById('frmCliente').reset();
        $('#departamento').val("13").trigger("change");
        $('#tipo_cliente_id').val("121").trigger("change");
        $('#tipo_documento').val("").trigger("change");
        $('#direccion').val('Direccion Trujillo');
        $('#telefono_movil').val('999999999');
        $('#modal_cliente').modal('show');
    }

    function nextFocus(event, inputS) {
        if (event.keyCode == 13) {

            setTimeout(function() { $('#'+inputS).focus() }, 10);
            document.getElementById(inputS).focus();
        }
    }

    //background-color: #00f;
</script>

<script>
    window.onbeforeunload = () => {
        if ($('#asegurarCierre').val() == 1) {
            while (true) {
                devolverCantidades()
            }
        }
    }
</script>
@endpush
