@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('notas-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA NOTA DE DEBITO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Nota de debito</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">

                <div class="ibox-content">

                    <form action="{{route('ventas.notas.store')}}" method="POST" id="enviar_documento">
                        {{csrf_field()}}
                        <input type="hidden" name="comprobante_id" value="{{old('comprobante_id', $comprobante->id)}}">
                        <div class="row">
                            <div class="col-sm-6 b-r">
                                <h4 class=""><b>Nota de Debito</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la nota de debito:</p>
                                        <input type="hidden" name="tipo_nota" value="{{old('tipo_nota', $tipo_nota)}}">
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <div class="col-lg-6 col-xs-12" id="fecha_documento">
                                        <label>Fecha de Emisión</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>

                                            <input type="text" name="fecha_emision"
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

                                    <div class="col-lg-6 col-xs-12">
                                        <label class="">Comprobante Afectado: </label>
                                        <input type="text" id="comprobante_afectado" name="comprobante_afectado" class="form-control {{ $errors->has('comprobante_afectado') ? ' is-invalid' : '' }}" value="{{old('comprobante_afectado',$comprobante->serie.'-'.$comprobante->correlativo )}}" readonly>
                                        
                                        @if ($errors->has('comprobante_afectado'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('comprobante_afectado') }}</strong>
                                        </span>
                                        @endif

                                                                             
                                    </div>


                                </div>

                                <div class="form-group">
                                    <label >Empresa: </label>
                                    <input type="text" id="empresa" name="empresa" class="form-control {{ $errors->has('empresa') ? ' is-invalid' : '' }}" value="{{old('empresa',$comprobante->ruc_empresa.' - '.$comprobante->empresa )}}" readonly>
                                    @if ($errors->has('empresa'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('empresa') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-sm-6">

                                <div class="form-group">

                                    <label >Cliente: </label>
                                    <input type="text" id="cliente" name="cliente" class="form-control {{ $errors->has('empresa') ? ' is-invalid' : '' }}" value="{{old('cliente',$comprobante->documento_cliente.' - '.$comprobante->cliente )}}" readonly>
                                    @if ($errors->has('cliente'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('cliente') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="required">Motivo: </label>
                                    <input type="text" id="motivo" name="motivo" class="form-control {{ $errors->has('motivo') ? ' is-invalid' : '' }}" value="{{old('motivo')}}" required>
                                    
                                    @if ($errors->has('motivo'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('motivo') }}</strong>
                                    </span>
                                    @endif                                  
                                </div>



                                <!-- LLENAR DATOS EN UN ARRAY -->
                                <input type="hidden" id="productos_tabla" name="productos_tabla[]">

                            </div>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle de la Nota de debito</b></h4>
                                    </div>
                                    <div class="panel-body">

                                    
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="col-form-label required">Producto:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="producto_lote" readonly> 
                                                    <span class="input-group-append"> 
                                                        <button type="button" class="btn btn-primary" id="buscarLotes" data-toggle="modal" data-target="#modal_lote"><i class='fa fa-search'></i> Buscar
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                                                </div>
                                            </div>

                                            <input type="hidden" name="producto_id" id="producto_id">
                                            <input type="hidden" name="producto_unidad" id="producto_unidad">

                                            <div class="col-lg-2 col-xs-12">

                                                <label class="col-form-label required">Cantidad:</label>
                                                <input type="number" value='0.00' id="cantidad" class="form-control" disabled>
                                                <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-xs-12">
                                                <div class="form-group">
                                                    <label class="col-form-label required" for="amount">Precio:</label>
                                                    <input type="number" id="precio" class="form-control" disabled >
                                                    <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                                    </div>
                                                </div>
                                            </div>
                                            


                                            <div class="col-lg-2 col-xs-12">

                                                <div class="form-group">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <button type=button class="btn btn-block btn-warning" style='color:white;' id="btn_agregar_detalle" disabled> <i class="fa fa-plus"></i> AGREGAR</button>
                                                </div>

                                            </div>



                                        </div>
                                        <hr>
                                  


                                        <div class="table-responsive">
                                            <table
                                                class="table dataTables-detalle-documento table-striped table-bordered table-hover"
                                                style="text-transform:uppercase">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">ACCIONES</th>
                                                        <th class="text-center">CANTIDAD</th>
                                                        <th class="text-center">UNIDAD DE MEDIDA</th>
                                                        <th class="text-center">DESCRIPCION DEL PRODUCTO</th>
                                                        <th class="text-center">PRECIO</th>
                                                        <th class="text-center">TOTAL</th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="6" style="text-align:right">Sub Total:</th>
                                                        <th><span id="subtotal">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="6" class="text-center">IGV <span
                                                                id="igv_int"></span>:</th>
                                                        <th class="text-center"><span id="igv_monto">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="6" class="text-center">TOTAL:</th>
                                                        <th class="text-center"><span id="total">0.0</span></th>

                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>

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

                                <a href="{{route('ventas.documento.index')}}" id="btn_cancelar"
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
@include('ventas.documentos.modal')
@include('ventas.documentos.modalLote')

@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
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

<script>

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })


    $(document).ready(function() {

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });

        $('.input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            language: 'es',
            format: "dd/mm/yyyy"
        });

        table = $('.dataTables-detalle-documento').DataTable({
            "dom": 'lTfgitp',
            "bPaginate": true,
            "bLengthChange": true,
            "responsive": true,
            "bFilter": true,
            "bInfo": false,
            "columnDefs": [
                {
                    "targets": 0,
                    "visible": false,
                    "searchable": false
                },
                {
                    searchable: false,
                    "targets": [1],
                    data: null,

                    render: function(data, type, row) {
                        return  "<div class='btn-group'>" +
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
                },
                {
                    "targets": [6],
                },
                {
                    "targets": [7],
                    "visible": false,
                },
            ],
            'bAutoWidth': false,
            'aoColumns': [
                { sWidth: '0%' },
                { sWidth: '15%', sClass: 'text-center' },
                { sWidth: '15%', sClass: 'text-center' },
                { sWidth: '15%', sClass: 'text-center' },
                { sWidth: '25%', sClass: 'text-left' },
                { sWidth: '15%', sClass: 'text-center' },
                { sWidth: '15%', sClass: 'text-center' },
            ],
            "language": {
                url: "{{asset('Spanish.json')}}"
            },
            "order": [[ 0, "desc" ]],
        });

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

        //OBTENER LOTES
        obtenerTipocliente("{{$comprobante->cliente_id}}")

    });

    //DATATABLE DOCUMENTO

    function sumaTotal() {
        var subtotal = 0;
        table.rows().data().each(function(el, index) {
            subtotal = Number(el[6]) + subtotal
        });
        conIgv(subtotal)
    }

    function conIgv(subtotal) {
        var calcularIgv = 18/100
        var base = subtotal / (1 + calcularIgv)
        var nuevo_igv = subtotal - base;
        $('#igv_int').text(18+'%')
        $('#subtotal').text(base.toFixed(2))
        $('#igv_monto').text(nuevo_igv.toFixed(2))
        $('#total').text(subtotal.toFixed(2))
    }

    //LLENAR TABLA DE PRODUCTOS SEGUN EL CLIENTE
    function obtenerTipocliente(cliente_id) {
        if (cliente_id) {
            $.ajax({
                dataType : 'json',
                url: '{{ route("ventas.cliente.getcustomer")}}',
                type:'post',
                data : {
                    '_token' : $('input[name=_token]').val(),
                    'cliente_id': cliente_id
                },
                success : function(cliente){
                    $('#buscarLotes').prop("disabled", false)
                    obtenerLotesproductos(cliente.tabladetalles_id)
                },            
            })
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

    //VALIDAR INGRESO DEL PRODUCTO AL DETALLE
    $("#btn_agregar_detalle").click(function() {
        limpiarErrores()
        var enviar = false;
        if ($('#producto_id').val() == '') {
            toastr.error('Seleccione Producto.', 'Error');
            enviar = true;
            $('#producto_id').addClass("is-invalid")
            $('#error-producto').text('El campo Producto es obligatorio.')
        } else {
            var existe = buscarProducto($('#producto_id').val())
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
        }else{
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
                    agregarTabla()
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

    function agregarTabla(detalle) {
        if (detalle) {
            table.row.add([
                detalle.producto_id,
                '',
                Number(detalle.cantidad),
                detalle.presentacion,
                detalle.producto,
                Number(detalle.precio).toFixed(2),
                (detalle.cantidad * detalle.precio).toFixed(2),
                detalle.presentacion
            ]).draw(false);
        }else{
            table.row.add([
                $('#producto_id').val(),
                '',
                Number($('#cantidad').val()),
                $('#producto_unidad').val(),
                $('#producto_lote').val(),
                Number($('#precio').val()).toFixed(2),
                ($('#cantidad').val() * $('#precio').val()).toFixed(2),
                $('#producto_unidad').val(),
            ]).draw(false);
        }
        //INGRESADO EL PRODUCTO SUMA TOTAL DEL DETALLE
        sumaTotal()
        //LIMPIAR LOS CAMPOS DESPUES DE LA BUSQUEDA
        $('#precio').val('')
        $('#cantidad').val('')
        $('#producto_unidad').val('')
        $('#producto_id').val('')
        $('#producto_lote').val('')
    }

    //EDITAR TABLA
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
        obtenerMax(data[0])
    })

    function obtenerMax(id) {
        $.get('/almacenes/productos/obtenerProducto/'+ id, function (data) {
            //AGREGAR LIMITE A LA CANTIDAD
            $("#cantidad_editar").attr({ 
                "max" : data.producto.stock,
                "min" : 1,
            });
        })
    }

    $('#cantidad').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        let max= parseInt(this.max);
        let valor = parseInt(this.value);
        if(valor>max){
            toastr.error('La cantidad ingresada supera al stock del producto Max('+max+').', 'Error');
            this.value = max;
        }
    });

    //BORRAR TABLA
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

    //ENVIA FORMULARIO
    $('#enviar_documento').submit(function(e) {
        e.preventDefault();

        if (table.rows().data().length != 0) {  //VALIDAR SI EXISTE PRODUCTOS
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
        }else{
            toastr.error('Ingrese al menos 1 Producto.', 'Error');
        }

        
    })

    function cargarProductos() {
        var productos = [];
        var data = table.rows().data();
        data.each(function(value, index) {
            let fila = {
                producto_id: value[0],
                presentacion: value[3],
                precio: value[5],
                cantidad: value[2],
                total: value[6],
            };
            productos.push(fila);
        });

        $('#productos_tabla').val(JSON.stringify(productos));
    }


</script>
@endpush