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
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row head-nota-ingreso">
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
                                            </div>

                                            <div class="form-group row mt-3">
                                                <div class="col-lg-12">
                                                    @include('almacenes.nota_ingresos.tabla-productos')
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
                                            @include('almacenes.nota_ingresos.tabla-productos',[
                                                "carrito" => "carrito"
                                            ])
                                        </div>
                                    </div> 
                                </div>
                                {{-- <div class="panel-body">
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
                                </div> --}}
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




    // $('#enviar_ingresos').submit(function(e) {
    //     e.preventDefault();
    //     let correcto = true;
    //     cargarDetalle();
    //     let detalles = JSON.parse($('#notadetalle_tabla').val());
    //     if (detalles.length < 1) {
    //         correcto = false;
    //         toastr.error('El documento debe tener almenos un producto de ingreso.');
    //     }
    //     console.log(detalles.length);
    //     if (correcto) {
    //         const swalWithBootstrapButtons = Swal.mixin({
    //             customClass: {
    //                 confirmButton: 'btn btn-success',
    //                 cancelButton: 'btn btn-danger',
    //             },
    //             buttonsStyling: false
    //         })

    //         Swal.fire({
    //             title: 'Opción Guardar',
    //             text: "¿Seguro que desea guardar cambios?",
    //             icon: 'question',
    //             showCancelButton: true,
    //             confirmButtonColor: "#1ab394",
    //             confirmButtonText: 'Si, Confirmar',
    //             cancelButtonText: "No, Cancelar",
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 this.submit();
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


    //  $(document).ready(function() {

    //     $('#lote').val('LT-{{ $fecha_actual }}');
    //     $('#fechavencimiento').val('{{$fecha_5}}');

         // DataTables
    //      table = $('#table-productos').DataTable({
    //          "dom": '<"html5buttons"B>lTfgitp',
    //          "buttons": [
    //         ],
    //          "bPaginate": true,
    //          "bLengthChange": true,
    //          "bFilter": true,
    //          "bInfo": true,
    //          "bAutoWidth": false,
    //          "language": {
    //              "url": "{{asset('Spanish.json')}}"
    //          },

    //          "columnDefs": [{
    //                  "targets": [0],
    //                  "visible": false,
    //                  "searchable": false
    //              },
    //              {

    //                  "targets": [1],
    //                  className: "text-center",
    //                  render: function(data, type, row) {
    //                      return "<div class='btn-group'>" +
    //                          "<a class='btn btn-warning btn-sm modificarDetalle btn-edit'  style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
    //                          "<a class='btn btn-danger btn-sm' id='borrar_detalle' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
    //                          "</div>";
    //                  }
    //              },
    //              {
    //                  "targets": [2],
    //              },
    //              {
    //                 "targets": [3],
    //                  className: "text-center",
    //                  "visible": false,
    //              },
    //              {
    //                  "targets": [4],
    //                  className: "text-center",
    //              },
    //              {
    //                  "targets": [5],
    //                  className: "text-center",
    //                  "visible": false,
    //              },
    //              {
    //                  "targets": [6],
    //                  className: "text-center"
    //              },
    //              {
    //                  "targets": [7],
    //                  className: "text-center"
    //              }

    //         ],

    //     });

    // })

    // //Borrar registro de articulos
    // $(document).on('click', '#borrar_detalle', function(event) {

    //     const swalWithBootstrapButtons = Swal.mixin({
    //         customClass: {
    //             confirmButton: 'btn btn-success',
    //             cancelButton: 'btn btn-danger',
    //         },
    //         buttonsStyling: false
    //     })

    //     Swal.fire({
    //         title: 'Opción Eliminar',
    //         text: "¿Seguro que desea eliminar Artículo?",
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonColor: "#1ab394",
    //         confirmButtonText: 'Si, Confirmar',
    //         cancelButtonText: "No, Cancelar",
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             var table = $('.dataTables-ingreso').DataTable();
    //             table.row($(this).parents('tr')).remove().draw();

    //         } else if (
    //             /* Read more about handling dismissals below */
    //             result.dismiss === Swal.DismissReason.cancel
    //         ) {
    //             swalWithBootstrapButtons.fire(
    //                 'Cancelado',
    //                 'La Solicitud se ha cancelado.',
    //                 'error'
    //             )
    //         }
    //     })



    // });


    // //Validacion al ingresar tablas
    // $(".enviar_detalle").click(function() {

    //     var enviar = true;
    //     var cantidad = $('#cantidad').val();
    //     var costo_aux = $('#costo').val();
    //     var lote= $('#lote').val();
    //     var producto= $('#producto').val();
    //     var fechavencimiento= $('#fechavencimiento').val();

    //     if ($('#producto').val() == '') {
    //         toastr.error('Seleccione Producto.', 'Error');
    //         enviar = false;
    //     } else {
    //         var existe = buscarproducto($('#producto').val())
    //         if (existe == true) {
    //             toastr.error('Producto con el mismo lote ya se encuentra ingresado.', 'Error');
    //             enviar = false;
    //         }
    //     }

    //     if(cantidad.length==0|| lote.length==0 || fechavencimiento.length==0)
    //     {
    //         toastr.error('Ingrese datos', 'Error');
    //         enviar = false;
    //     }


    //     if (enviar) {

    //         let aux = convertFloat(costo_aux) / convertFloat(cantidad);
    //         let costo = (aux).toFixed(4)
    //         const swalWithBootstrapButtons = Swal.mixin({
    //             customClass: {
    //                 confirmButton: 'btn btn-success',
    //                 cancelButton: 'btn btn-danger',
    //             },
    //             buttonsStyling: false
    //         })

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

    //                 var detalle = {
    //                     cantidad: convertFloat($('#cantidad').val()).toFixed(2),
    //                     lote:$('#lote').val(),
    //                     producto:$( "#producto option:selected" ).text(),
    //                     fechavencimiento: $('#fechavencimiento').val(),
    //                     producto_id:$( "#producto" ).val(),
    //                     costo:costo

    //                 }
    //                 agregarTabla(detalle);
    //                 limpiarDetalle();

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
    // });

    // function limpiarDetalle()
    // {
    //     $('#cantidad').val('');
    //     $('#costo').val('');
    //     $('#lote').val('LT-{{ $fecha_actual }}');
    //     $('#fechavencimiento').val('{{$fecha_5}}');
    //     $('#producto').val($('#producto option:first-child').val()).trigger('change');
    // }

    // $(document).on('click', '.btn-edit', function(event) {
    //     var table = $('.dataTables-ingreso').DataTable();
    //     var data = table.row($(this).parents('tr')).data();
    //     $('#modal_editar_detalle #indice').val(table.row($(this).parents('tr')).index());
    //     $('#modal_editar_detalle #lote').val(data[3]);
    //     $('#modal_editar_detalle #cantidad').val(data[2]);
    //     $('#modal_editar_detalle #costo').val(data[6]);
    //     $('#modal_editar_detalle #prod_id').val(data[0]);
    //     $('#modal_editar_detalle #fechavencimiento').val(data[5]);
    //     $('#modal_editar_detalle').modal('show');
    //     $("#modal_editar_detalle #producto").val(data[0]).trigger('change');
    // });


    // function agregarTabla($detalle) {
    //     var t = $('.dataTables-ingreso').DataTable();
    //     t.row.add([
    //         $detalle.producto_id,'',
    //         $detalle.cantidad,
    //         $detalle.lote,
    //         $detalle.producto,
    //         $detalle.fechavencimiento,
    //         $detalle.costo,
    //         ($detalle.costo * $detalle.cantidad).toFixed(2),
    //     ]).draw(false);
    //     sumaTotal();
    //     cargarDetalle();
    // }

    // function cargarDetalle() {
    //     var notadetalle = [];
    //     var table = $('.dataTables-ingreso').DataTable();
    //     var data = table.rows().data();
    //     data.each(function(value, index) {
    //         let fila = {
    //             cantidad: value[2],
    //             lote: value[3],
    //             producto_id: value[0],
    //             fechavencimiento: value[5],
    //             costo: value[6],
    //             valor_ingreso: value[7],
    //         };

    //         notadetalle.push(fila);

    //     });
    //     $('#notadetalle_tabla').val(JSON.stringify(notadetalle))
    // }

    // function sumaTotal() {
    //     var total = 0;
    //     table.rows().data().each(function(el, index) {
    //         total = Number(el[7]) + total
    //     });
    //     $('#total').text(total.toFixed(2))
    //     $('#monto_total').val(total.toFixed(2))
    // }

    // function buscarproducto(id) {
    //     var existe = false;
    //     table.rows().data().each(function(el, index) {
    //         (el[0] == id) ? existe = true : ''
    //     });
    //     return existe
    // }

</script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>

<script>
    const selectModelo =  document.querySelector('#modelo');
    const tokenValue = document.querySelector('input[name="_token"]').value;
    const tallas     = @json($tallas);
    const colores     = @json($colores);
    const bodyTablaProductos  =  document.querySelector('#table-productos tbody');
    const bodyTablaDetalle  =  document.querySelector('#table-detalle tbody');
    const btnAgregarDetalle = document.querySelector('#btn_agregar_detalle');
    const inputProductos=document.querySelector('#notadetalle_tabla');
    const formNotaIngreso= document.querySelector('#enviar_ingresos');
    const btnGrabar     =   document.querySelector('#btn_grabar');

    let modelo_id   = null;
    let carrito = [];
    let table = null;

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        cargarDataTables();
    })

    function events(){
        //========= EVENTO AGREGAR DETALLE ============
        btnAgregarDetalle.addEventListener('click',()=>{
            
            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            inputsCantidad.forEach((ic)=>{
                const cantidad = ic.value?ic.value:0;
                if(cantidad != 0){
                    const producto = formarProducto(ic);
                    const indiceExiste  = carrito.findIndex((p)=>{
                    return p.producto_id==producto.producto_id && p.color_id==producto.color_id && p.talla_id==producto.talla_id})
                    
                    if(indiceExiste == -1){
                        carrito.push(producto);
                    }else{
                        const productoModificar = carrito[indiceExiste];
                        productoModificar.cantidad = producto.cantidad;
                        carrito[indiceExiste] = productoModificar;
                    
                    }
                }else{
                    const producto = formarProducto(ic);
                    const indiceExiste  = carrito.findIndex((p)=>{
                    return p.producto_id==producto.producto_id && p.color_id==producto.color_id && p.talla_id==producto.talla_id})
                    if(indiceExiste !== -1){
                        carrito.splice(indiceExiste, 1);
                    }
                }
                  
            })
            reordenarCarrito();
            pintarDetalleNotaIngreso(carrito);
            
        })

        //======== EVENTO ELIMINAR PRODUCTO DEL CARRITO ============
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');
                eliminarProducto(productoId,colorId);
                pintarDetalleNotaIngreso(carrito);
            }
        })

        //============ EVENTO ENVIAR FORMULARIO =============
        formNotaIngreso.addEventListener('submit',(e)=>{
            e.preventDefault();
            btnGrabar.disabled=true;

            if(carrito.length>0){
                inputProductos.value=JSON.stringify(carrito);
                const formData = new FormData(formNotaIngreso);
                formData.forEach((valor, clave) => {
                    console.log(`${clave}: ${valor}`);
                });
                formNotaIngreso.submit();
            }else{
                toastr.error('El detalle de la nota de ingreso está vacío!!!')
                btnGrabar.disabled = false;
            }
           
        })

        //============== EVENTO VALIDACIÓN INPUT CANTIDADES ==========
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }
        })
    }

    //========= FUNCIÓN ELIMINAR PRODUCTO DEL CARRITO =============
    const eliminarProducto = (productoId,colorId)=>{
        carrito = carrito.filter((p)=>{
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    //============== FUNCIÓN OBTENER PRODUCTOS DE UN MODELO ==============
     
    function getProductosByModelo(e){
        modelo_id = e.value;
        btnAgregarDetalle.disabled=true;
       
        if(modelo_id){
            const url = `/get-productos-nota-ingreso/${modelo_id}`;
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
                    //console.log(data);
                    if (table) {
                        table.destroy();
                    }
                    pintarTableStocks(tallas,data.productos);
                    cargarDataTables();
                })

                .catch(error => console.error('Error:', error));
            
        }else{
            bodyTablaProductos.innerHTML = ``;
        }
    }

    //============ REORDENAR CARRITO ===============
    const reordenarCarrito= ()=>{
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    //=============== FORMAR OBJETO PRODUCTO PARA INSERTAR EN EL CARRITO POSTERIORMENTE =============
    const formarProducto = (ic)=>{
        const producto_id = ic.getAttribute('data-producto-id');
        const producto_nombre = ic.getAttribute('data-producto-nombre');
        const color_id = ic.getAttribute('data-color-id');
        const color_nombre = ic.getAttribute('data-color-nombre');
        const talla_id = ic.getAttribute('data-talla-id');
        const talla_nombre = ic.getAttribute('data-talla-nombre');
        const cantidad     = ic.value?ic.value:0;
        const producto = {producto_id,producto_nombre,color_id,color_nombre,
                            talla_id,talla_nombre,cantidad};
        return producto;
    }

    //============ RENDERIZAR TABLA DE CANTIDADES ============
    const pintarTableStocks = (tallas,productos)=>{
        let options =``;

        productos.forEach((p)=>{
           
                options+=`  <tr>
                                <th scope="row"  data-color=${p.color_id} >
                                    ${p.color_nombre} 
                                </th>
                                <th scope="row" data-producto=${p.producto_id}>
                                    ${p.producto_nombre} 
                                </th>
                        `;

                        let htmlTallas = ``;

                        tallas.forEach((t)=>{
                        
                            htmlTallas +=   `
                                                <td >
                                                    <input type="text" class="form-control inputCantidad" 
                                                    data-producto-id="${p.producto_id}"
                                                    data-producto-nombre="${p.producto_nombre}"
                                                    data-color-nombre="${p.color_nombre}"
                                                    data-talla-nombre="${t.descripcion}"
                                                    data-color-id="${p.color_id}" data-talla-id="${t.id}"></input>    
                                                </td>
                                            `;   
                        })
                htmlTallas += `</tr>`;
                options += htmlTallas;
           
        })

        bodyTablaProductos.innerHTML = options;
        btnAgregarDetalle.disabled = false;
    }

    //================== LIMPIAR TABLA DETALLE ===============
    function clearDetalleCotizacion(){
        while (bodyTablaDetalle.firstChild) {
            bodyTablaDetalle.removeChild(bodyTablaDetalle.firstChild);
        }
    }

    //====== RENDERIZAR TABLA DETALLE NOTA INGRESO ==============
    function pintarDetalleNotaIngreso(carrito){
        let fila= ``;
        let htmlTallas= ``;
        const producto_color_procesado=[];
        clearDetalleCotizacion();

        carrito.forEach((c)=>{
            htmlTallas=``;
            if (!producto_color_procesado.includes(`${c.producto_id}-${c.color_id}`)) {
                fila+= `<tr>   
                            <td>
                                <i class="fas fa-trash-alt btn btn-primary delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>                            
                            </td>
                            <th>${c.producto_nombre} - ${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t)=>{
                    let cantidad = carrito.filter((ct)=>{
                        return ct.producto_id==c.producto_id && ct.color_id==c.color_id && t.id==ct.talla_id;
                    });
                    cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad=0;
                    htmlTallas += `<td>${cantidad}</td>`; 
                })


            
                fila+=htmlTallas;
                bodyTablaDetalle.innerHTML=fila;
                producto_color_procesado.push(`${c.producto_id}-${c.color_id}`)
            }
        })
    }

    function cargarDataTables(){
        table = new DataTable('#table-productos',
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
        
        const tableStocks   = document.querySelector('#table-productos');
        if(tableStocks.children[1]){
            tableStocks.children[1].remove();
        }
    }

</script>

@endpush
