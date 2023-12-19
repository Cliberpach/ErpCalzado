@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('nota_ingreso-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVAS NOTA DE INGRESO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_ingreso.index')}}">Notas de Ingreso</a>
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
                            <form action="{{route('almacenes.nota_ingreso.store')}}" method="POST" id="enviar_ingresos">
                                {{csrf_field()}}
                                <div class="col-sm-12">
                                    <h4 class=""><b>Nota de Ingreso</b></h4>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Registrar datos de la Nota de Ingreso :</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                            <input type="hidden" id="numero"  name="numero" class="form-control" value="{{$ngenerado}}" >


                                        <div class="col-12 col-md-3"  id="fecha">
                                            <label>Fecha</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="date" id="fecha" name="fecha"
                                                    class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}"
                                                    value="{{old('fecha',$fecha_hoy)}}"
                                                    autocomplete="off" readonly required>
                                                @if ($errors->has('fecha'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('fecha') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">

                                            <label class="required">Moneda</label>
                                            <select
                                                class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%" value="{{old('moneda')}}"
                                                name="moneda" id="moneda" required disabled>
                                                {{-- onchange="cambioMoneda(this)" --}}
                                                    <option></option>
                                                @foreach ($monedas as $moneda)
                                                <option value="{{$moneda->descripcion}}" @if(old('moneda') == $moneda->descripcion || $moneda->descripcion == 'SOLES') {{'selected'}} @endif
                                                    >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                                                @endforeach
                                                @if ($errors->has('moneda'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('moneda') }}</strong>
                                                </span>
                                                @endif

                                            </select>

                                            <input type="hidden" id="moneda" name="moneda" value="SOLES">

                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label class="required">Origen</label>
                                            <select name="origen" id="origen" class="select2_form form-control {{ $errors->has('origen') ? ' is-invalid' : '' }}" required>
                                                <option value="">Seleccionar Origen</option>
                                                @foreach ($origenes as  $tabla)
                                                    <option {{ old('origen') == $tabla->id ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('origen'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('origen') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label>Destino</label>
                                            <select name="destino" id="destino" class="select2_form form-control {{ $errors->has('destino') ? ' is-invalid' : '' }}">
                                                <option value="">Seleccionar Destino</option>
                                                @foreach ($destinos as $tabla)
                                                    <option {{ old('destino') == $tabla->id ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('destino'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('destino') }}</strong>
                                            </span>
                                            @endif
                                        </div>


                                    </div>
                                </div>
                                <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
                                <input type="hidden" id="monto_total" name="monto_total">
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle de la Nota de Ingreso</b></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Producto</label>
                                            <select name="producto" id="producto" class="form-control select2_form">
                                                <option value=""></option>
                                                @foreach ($productos as $producto)
                                                    <option  value="{{$producto->id}}" id="{{$producto->id}}">{{$producto->nombre}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="col-form-label">Cantidad </label>
                                                <input type="text" id="cantidad" class="form-control" min="1" onkeypress="return filterFloat(event, this, true);">
                                                <div class="invalid-feedback"><b><span id="error-cantidad"></span></b></div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="col-form-label">Costo(Total)</label>
                                                <input type="text" name="costo" id="costo" class="form-control" onkeypress="return filterFloat(event, this, true);">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-none">
                                            <div class="form-group">
                                                <label class="col-form-label">lote</label>
                                                <input type="text" name="lote" id="lote" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 d-none">
                                            <div class="form-group">
                                                <label class="col-form-label">Fecha Vencimiento</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="fechavencimiento" name="fechavencimiento" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <div class="form-group">
                                            <a class="btn btn-block btn-warning enviar_detalle"
                                            style='color:white;'> <i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="table-responsive">
                                        <table
                                            class="table dataTables-ingreso table-striped table-bordered table-hover"
                                                onkeyup="return mayus(this)">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center">ACCIONES</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Lote</th>
                                                    <th class="text-center">Producto</th>
                                                    <th class="text-center">Fecha Vencimiento</th>
                                                    <th class="text-center">Costo U.</th>
                                                    <th class="text-center">Total</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7" class="text-center">TOTAL:</th>
                                                    <th class="text-right"><span id="total">0.00</span></th>

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
                            <a href="{{route('almacenes.nota_ingreso.index')}}" id="btn_cancelar"
                                class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" id="btn_grabar" form="enviar_ingresos" class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@include('almacenes.nota_ingresos.modal')
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




<script>
//Select2
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
    });


    // $('.input-group.date #fechavencimiento').datepicker({
    //     todayBtn: "linked",
    //     keyboardNavigation: false,
    //     forceParse: false,
    //     autoclose: true,
    //     language: 'es',
    //     format: "dd/mm/yyyy",
    // });
    // $('.modal_editar_detalle #fechavencimiento').datepicker({
    //     todayBtn: "linked",
    //     keyboardNavigation: false,
    //     forceParse: false,
    //     autoclose: true,
    //     language: 'es',
    //     format: "dd/mm/yyyy",
    // });




    $('#enviar_ingresos').submit(function(e) {
        e.preventDefault();
        let correcto = true;
        cargarDetalle();
        let detalles = JSON.parse($('#notadetalle_tabla').val());
        if (detalles.length < 1) {
            correcto = false;
            toastr.error('El documento debe tener almenos un producto de ingreso.');
        }
        console.log(detalles.length);
        if (correcto) {
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


    $(document).ready(function() {

        $('#lote').val('LT-{{ $fecha_actual }}');
        $('#fechavencimiento').val('{{$fecha_5}}');

        // DataTables
        table = $('.dataTables-ingreso').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [
            ],
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
                            "<a class='btn btn-warning btn-sm modificarDetalle btn-edit'  style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                            "<a class='btn btn-danger btn-sm' id='borrar_detalle' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                            "</div>";
                    }
                },
                {
                    "targets": [2],
                },
                {
                    "targets": [3],
                    className: "text-center",
                    "visible": false,
                },
                {
                    "targets": [4],
                    className: "text-center",
                },
                {
                    "targets": [5],
                    className: "text-center",
                    "visible": false,
                },
                {
                    "targets": [6],
                    className: "text-center"
                },
                {
                    "targets": [7],
                    className: "text-center"
                }

            ],

        });

    })

    //Borrar registro de articulos
    $(document).on('click', '#borrar_detalle', function(event) {

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
                var table = $('.dataTables-ingreso').DataTable();
                table.row($(this).parents('tr')).remove().draw();

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
    $(".enviar_detalle").click(function() {

        var enviar = true;
        var cantidad = $('#cantidad').val();
        var costo_aux = $('#costo').val();
        var lote= $('#lote').val();
        var producto= $('#producto').val();
        var fechavencimiento= $('#fechavencimiento').val();

        if ($('#producto').val() == '') {
            toastr.error('Seleccione Producto.', 'Error');
            enviar = false;
        } else {
            var existe = buscarproducto($('#producto').val())
            if (existe == true) {
                toastr.error('Producto con el mismo lote ya se encuentra ingresado.', 'Error');
                enviar = false;
            }
        }

        if(cantidad.length==0|| lote.length==0 || fechavencimiento.length==0)
        {
            toastr.error('Ingrese datos', 'Error');
            enviar = false;
        }


        if (enviar) {

            let aux = convertFloat(costo_aux) / convertFloat(cantidad);
            let costo = (aux).toFixed(4)
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

                    var detalle = {
                        cantidad: convertFloat($('#cantidad').val()).toFixed(2),
                        lote:$('#lote').val(),
                        producto:$( "#producto option:selected" ).text(),
                        fechavencimiento: $('#fechavencimiento').val(),
                        producto_id:$( "#producto" ).val(),
                        costo:costo

                    }
                    agregarTabla(detalle);
                    limpiarDetalle();

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

    function limpiarDetalle()
    {
        $('#cantidad').val('');
        $('#costo').val('');
        $('#lote').val('LT-{{ $fecha_actual }}');
        $('#fechavencimiento').val('{{$fecha_5}}');
        $('#producto').val($('#producto option:first-child').val()).trigger('change');
    }

    $(document).on('click', '.btn-edit', function(event) {
        var table = $('.dataTables-ingreso').DataTable();
        var data = table.row($(this).parents('tr')).data();
        $('#modal_editar_detalle #indice').val(table.row($(this).parents('tr')).index());
        $('#modal_editar_detalle #lote').val(data[3]);
        $('#modal_editar_detalle #cantidad').val(data[2]);
        $('#modal_editar_detalle #costo').val(data[6]);
        $('#modal_editar_detalle #prod_id').val(data[0]);
        $('#modal_editar_detalle #fechavencimiento').val(data[5]);
        $('#modal_editar_detalle').modal('show');
        $("#modal_editar_detalle #producto").val(data[0]).trigger('change');
    });


    function agregarTabla($detalle) {
        var t = $('.dataTables-ingreso').DataTable();
        t.row.add([
            $detalle.producto_id,'',
            $detalle.cantidad,
            $detalle.lote,
            $detalle.producto,
            $detalle.fechavencimiento,
            $detalle.costo,
            ($detalle.costo * $detalle.cantidad).toFixed(2),
        ]).draw(false);
        sumaTotal();
        cargarDetalle();
    }

    function cargarDetalle() {
        var notadetalle = [];
        var table = $('.dataTables-ingreso').DataTable();
        var data = table.rows().data();
        data.each(function(value, index) {
            let fila = {
                cantidad: value[2],
                lote: value[3],
                producto_id: value[0],
                fechavencimiento: value[5],
                costo: value[6],
                valor_ingreso: value[7],
            };

            notadetalle.push(fila);

        });
        $('#notadetalle_tabla').val(JSON.stringify(notadetalle))
    }

    function sumaTotal() {
        var total = 0;
        table.rows().data().each(function(el, index) {
            total = Number(el[7]) + total
        });
        $('#total').text(total.toFixed(2))
        $('#monto_total').val(total.toFixed(2))
    }

    function buscarproducto(id) {
        var existe = false;
        table.rows().data().each(function(el, index) {
            (el[0] == id) ? existe = true : ''
        });
        return existe
    }



</script>
@endpush
