@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('nota_ingreso-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>ACTUALIZAR NOTA DE INGRESO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_ingreso.index')}}">NOTA DE INGRESOS</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>ACTUALIZAR</strong>
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
                            <form action="{{route('almacenes.nota_ingreso.update',$notaingreso->id)}}" method="POST" id="enviar_ingresos">
                                {{method_field('PUT')}}
                                {{csrf_field()}}
                                <div class="col-sm-12">
                                    <h4 class=""><b>Nota de Ingresos</b></h4>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Actualizar Nota de Ingresos :</p>
                                        </div>
                                    </div>
                                    <div class="row">


                                        <div class="col-12 col-md-3"  id="fecha">
                                            <div class="form-group">
                                                <label>Fecha</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="fecha" name="fecha"
                                                        class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}"
                                                        value="{{old('fecha',$notaingreso->fecha)}}"
                                                        autocomplete="off" readonly required>
                                                    @if ($errors->has('fecha'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('fecha') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label class="required">Moneda: </label>
                                            <select
                                                disabled
                                                class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                style="text-transform: uppercase; width:100%"
                                                value="{{old('moneda',$notaingreso->moneda)}}" name="moneda" id="moneda" required>
                                                <option></option>
                                                @foreach (tipos_moneda() as $moneda)
                                                <option value="{{$moneda->descripcion}}" {{ old('moneda') == $moneda->descripcion || $notaingreso->moneda == $moneda->descripcion ?  'selected' : ''}}
                                                    >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                                                @endforeach
                                                @if ($errors->has('moneda'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('moneda') }}</strong>
                                                </span>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="required">Origen</label>
                                                <select name="origen" id="origen" class="select2_form form-control {{ $errors->has('origen') ? ' is-invalid' : '' }}">
                                                    <option value="">Seleccionar Origen</option>
                                                    @foreach ($origenes as  $tabla)
                                                        <option {{ $notaingreso->origen == $tabla->descripcion ? 'selected' : '' }}  value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Destino</label>
                                                <select name="destino" id="destino" class="select2_form form-control {{ $errors->has('destino') ? ' is-invalid' : '' }}">
                                                    <option value="">Seleccionar Destino</option>
                                                    @foreach ($destinos as $tabla)
                                                        <option {{ $notaingreso->destino == $tabla->descripcion ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
                                <input type="hidden" id="notadetalle" name="notadetalle" value="{{$detalle}}">
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
                                    {{-- <div class="row">
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
                                    </div> --}}
                                    <div class="row m-t-sm" style="text-transform:uppercase">
                                        <div class="col-lg-12">
                                            @include('almacenes.nota_ingresos.tabla-productos',[
                                                "carrito" => "carrito"
                                            ])
                                        </div>
                                    </div> 
                                </div>
                                {{-- <div class="panel-body">
                                    <div class="row align-items-end d-none">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="col-form-label">Cantidad </label>
                                                <input type="number" id="cantidad" class="form-control" min="1" onkeypress="return isNumber(event)">
                                                <div class="invalid-feedback"><b><span id="error-cantidad"></span></b></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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
                                                <label class="col-form-label">lote</label>
                                                <input type="text" name="lote" id="lote" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                            <a class="btn btn-block btn-warning enviar_detalle"
                                            style='color:white;'> <i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>

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
                                                    <th class="text-center">Costo U</th>
                                                    <th class="text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7" class="text-center">TOTAL:</th>
                                                    <th class="text-right"><span id="total"></span></th>
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
                            {{-- <button type="submit" id="btn_grabar" form="enviar_ingresos" class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@include('almacenes.nota_ingresos.modal_edit')
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


// $(document).ready(function() {
//     $('#lote').val('LT-{{ $fecha_actual }}');
//     $('#fechavencimiento').val('{{$fecha_5}}');

//     // DataTables
//     table = $('.dataTables-ingreso').DataTable({
//         "bPaginate": true,
//         "bLengthChange": true,
//         "bFilter": true,
//         "bInfo": true,
//         "bAutoWidth": false,
//         "language": {
//             "url": "{{asset('Spanish.json')}}"
//         },
//         "columnDefs": [
//             {
//                 "targets": [0],
//                 "visible": false,
//                 "searchable": false
//             },
//             {

//                 "targets": [1],
//                 className: "text-center",
//                 render: function(data, type, row) {
//                     return "<div class='btn-group'>" +
//                         "<a class='btn btn-warning btn-sm modificarDetalle btn-edit'  style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
//                         "<a class='btn btn-danger btn-sm d-none' id='borrar_detalle' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
//                         "</div>";
//                 }
//             },
//             {
//                 "targets": [2],
//             },
//             {
//                 "targets": [3],
//                 className: "text-center",
//                 "visible": false,
//             },
//             {
//                 "targets": [4],
//                 className: "text-center",
//             },
//             {
//                 "targets": [5],
//                 className: "text-center",
//                 "visible": false,

//             },
//             {
//                 "targets": [6],
//                 className: "text-center",
//             },
//             {
//                 "targets": [7],
//                 className: "text-center",
//             },
//             {
//                 "targets": [8],
//                 "visible": false,
//             }
//         ],

//     });

//     var detalle=JSON.parse($("#notadetalle").val());
//     var t = $('.dataTables-ingreso').DataTable();
//     for (var i = 0; i < detalle.length; i++) {
//         t.row.add([
//             detalle[i].producto_id,
//             '',
//             detalle[i].cantidad,
//             detalle[i].lote,
//             detalle[i].producto,
//             detalle[i].fechavencimiento,
//             detalle[i].costo,
//             detalle[i].valor_ingreso,
//             detalle[i].id,
//         ]).draw(false);
//     }
//     sumaTotal();
//     cargarDetalle()

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

// $(".enviar_detalle").click(function() {

//     var enviar = false;
//     var cantidad= $('#cantidad').val();
//                     var lote= $('#lote').val();
//                     var producto= $('#producto').val();
//                     var fechavencimiento= $('#fechavencimiento').val();

//     if(cantidad.length==0|| lote.length==0 || producto.length==0|| fechavencimiento.length==0)
//     {
//         toastr.error('Ingrese datos', 'Error');
//         enviar=true;
//     }


//     if (enviar != true) {
//         const swalWithBootstrapButtons = Swal.mixin({
//             customClass: {
//                 confirmButton: 'btn btn-success',
//                 cancelButton: 'btn btn-danger',
//             },
//             buttonsStyling: false
//         })

//         Swal.fire({
//             title: 'Opción Agregar',
//             text: "¿Seguro que desea agregar Artículo?",
//             icon: 'question',
//             showCancelButton: true,
//             confirmButtonColor: "#1ab394",
//             confirmButtonText: 'Si, Confirmar',
//             cancelButtonText: "No, Cancelar",
//         }).then((result) => {
//             if (result.isConfirmed) {

//                 var detalle = {
//                 	cantidad:  convertFloat($('#cantidad').val()).toFixed(2),
//                     lote:$('#lote').val(),
//                     producto:$( "#producto option:selected" ).text(),
//                     fechavencimiento: $('#fechavencimiento').val(),
//                     producto_id:$( "#producto" ).val()

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
// })


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
//     $('#modal_editar_detalle #detalle_id').val(data[8]);
//     $('#modal_editar_detalle #fechavencimiento').val(data[5]);
//     $('#modal_editar_detalle').modal('show');
//     $("#modal_editar_detalle #producto").val(data[0]).trigger('change');
// });

// function agregarTabla($detalle) {
//     var t = $('.dataTables-ingreso').DataTable();
//     t.row.add([
//         $detalle.producto_id,
//         '',
//     	$detalle.cantidad,
//     	$detalle.lote,
//     	$detalle.producto,
//         $detalle.fechavencimiento,
//         $detalle.costo,
//         ($detalle.costo * $detalle.cantidad).toFixed(2),
//         $detalle.id,
//     ]).draw(false);
//     sumaTotal()
//     cargarDetalle()
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
//             id: value[8],
//         };

//         notadetalle.push(fila);

//     });
//     $('#notadetalle_tabla').val(JSON.stringify(notadetalle))
//     console.log($('#notadetalle_tabla').val())
// }

// function sumaTotal() {
//     var total = 0;
//     table.rows().data().each(function(el, index) {
//         total = Number(el[7]) + total
//     });
//     $('#total').text(total.toFixed(2))
//     $('#monto_total').val(total.toFixed(2))
// }
</script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>

<script>
    const selectModelo =  document.querySelector('#modelo');
    const tokenValue = document.querySelector('input[name="_token"]').value;
    const tallas     = @json($tallas);
    const detalleNotaIngresoPrevia = @json($detalle);
    const bodyTablaProductos  =  document.querySelector('#table-productos tbody');
    const bodyTablaDetalle  =  document.querySelector('#table-detalle tbody');
    const btnAgregarDetalle = document.querySelector('#btn_agregar_detalle');
    const inputProductos=document.querySelector('#notadetalle_tabla');
    const formNotaIngreso= document.querySelector('#enviar_ingresos');


    let modelo_id   = null;
    let carrito = [];

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        console.log(detalleNotaIngresoPrevia);
        cargarDetalleNotaIngresoPrevio();
    })

    function events(){

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
            inputProductos.value=JSON.stringify(carrito);
             const formData = new FormData(formNotaIngreso);
             formData.forEach((valor, clave) => {
                 console.log(`${clave}: ${valor}`);
             });
            formNotaIngreso.submit();
        })

        //============== EVENTO VALIDACIÓN INPUT CANTIDADES ==========
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }
        })
    }

    //========== CARGAR DETALLE NOTA INGRESO PREVIA =============
    const cargarDetalleNotaIngresoPrevio = ()=>{
        carrito = JSON.parse(detalleNotaIngresoPrevia);
        pintarDetalleNotaIngreso(carrito);
    }

    //========= FUNCIÓN ELIMINAR PRODUCTO DEL CARRITO =============
    const eliminarProducto = (productoId,colorId)=>{
        carrito = carrito.filter((p)=>{
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
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
                            <td>                          </td>
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
</script>
@endpush
