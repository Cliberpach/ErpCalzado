@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVAS NOTA DE SALIDA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_salidad.index')}}">Nota de Salida</a>
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
                    <input type="hidden" id='asegurarCierre' >
                    <form action="{{route('almacenes.nota_salidad.store')}}" method="POST" id="enviar_nota_salida">
                        <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
                        {{csrf_field()}}


                            <div class="col-sm-12">
                                <h4 class=""><b>Notas de Salidad</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la Nota de Salidad:</p>
                                    </div>
                                </div>
                            	<div class="form-group row">

                                    <input type="hidden" id="numero" name="numero" class="form-control" value="{{$ngenerado}}" >
                                    <div class="col-12 col-md-2"  id="fecha">
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
                                        <label>Origen</label>
                                        <input type="text" name="origen" id="origen" readonly value="ALMACEN" class="form-control">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="required">Destino</label>
                                        <select name="destino" id="destino" class="select2_form form-control {{ $errors->has('destino') ? ' is-invalid' : '' }}" required>
                                            <option value="">Seleccionar Destino</option>
                                            @foreach ($almacenes as $tabla)
                                                <option {{ old('destino') == $tabla->id ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('destino'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('destino') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Observación</label>
                                        <textarea type="text" name="observacion" rows="2" id="observacion" class="form-control" placeholder="Observación"></textarea>
                                    </div>

                                </div>
                            </div>


                          

                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Seleccionar productos</b></h4>
                                    </div>
                                    <div class="panel-body">
                                        {{-- <div class="row">

                                            <div class="col-lg-6 col-xs-12">
                                                <label class="col-form-label required">Producto-lote:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="producto_lote_form" readonly>
                                                    <span class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="buscarLotes" data-toggle="modal" data-target="#modal_lote"><i class='fa fa-search'></i> Buscar
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="invalid-feedback"><b><span id="error-producto_form"></span></b>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="col-form-label">Cantidad </label>
                                                <input type="text" id="cantidad_form" class="form-control" onkeypress="return filterFloat(event, this, false);">
                                                <div class="invalid-feedback"><b><span id="error-cantidad_form"></span></b>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <a class="btn btn-block btn-warning enviar_detalle"
                                                        style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
                                                </div>
                                            </div>

                                            <input type="hidden" name="producto" id="producto_form">
                                            <input type="hidden" name="lote" id="lote_form">
                                            <input type="hidden" name="cantidad_actual_form" id="cantidad_actual_form">

                                        </div> --}}

                                        {{-- <hr> --}}
                                        <div class="col-lg-3 col-xs-12 mb-3">
                                            <label class="required">Modelo</label>
                                            <select id="modelo"
                                                class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                                onchange="getProductosByModelo(this.value)">
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

                                        @include('almacenes.nota_salidad.table-stocks')
                                        <div class="col-lg-2 col-xs-12">
                                            <div class="form-group">
                                                <label class="col-form-label" for="amount">&nbsp;</label>
                                                <button type=button class="btn btn-block btn-warning" style='color:white;'
                                                    id="btn_agregar_detalle" disabled> <i class="fa fa-plus"></i>
                                                    AGREGAR</button>
                                            </div>
                                        </div> 
                                        {{-- <div class="table-responsive">
                                            <table
                                                class="table dataTables-ingreso table-striped table-bordered table-hover"
                                                 onkeyup="return mayus(this)">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">ACCIONES</th>
                                                        <th class="text-center">Cantidad</th>
                    									<th class="text-center">Producto-Lote</th>
                                                        <th></th>
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

                                            </table>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle de la nota de salida</b></h4>
                                    </div>
                                    <div class="panel-body">
                                        @include('almacenes.nota_salidad.table-detalles')
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
                                <a href="{{route('almacenes.nota_salidad.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary" form="enviar_nota_salida">
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
@include('almacenes.nota_salidad.modal')
@include('almacenes.nota_salidad.modalLote')
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
{{-- <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script> --}}




<script>
//Select2
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    width: '100%',
});


// $('#enviar_nota_salida').submit(function(e) {
//     e.preventDefault();
//     let correcto = true;
//     cargarDetalle();
//     let detalles = JSON.parse($('#notadetalle_tabla').val());
//     if (detalles.length < 1) {
//         correcto = false;
//         toastr.error('El documento debe tener almenos un producto de salida.');
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
//                 //HABILITAR EL CARGAR PAGINA
//                 $('#asegurarCierre').val(2)
//                 this.submit();
//             } else if (
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


// $(document).ready(function() {

//     // DataTables
//     obtenerLotesproductos();
//     $('.dataTables-ingreso').DataTable({
//         "bPaginate": true,
//         "bLengthChange": true,
//         "bFilter": true,
//         "bInfo": true,
//         "bAutoWidth": false,
//         "language": {
//             "url": "{{asset('Spanish.json')}}"
//         },

//         "columnDefs": [{
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
//                         "<a class='btn btn-danger btn-sm' id='borrar_detalle' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
//                         "</div>";
//                 }
//             },
//             {
//                 "targets": [2],
//                 className: "text-center",
//             },
//             {
//                 "targets": [3],
//                 className: "text-center",
//             },
//             {
//                 "targets": [4],
//                 "visible": false,
//                 "searchable": false
//             },
//             {
//                 "targets": [5],
//                 "visible": false,
//                 "searchable": false
//             },

//         ],
//     });

// })

//Borrar registro de articulos
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
//         text: "¿Seguro que desea eliminar Producto?",
//         icon: 'question',
//         showCancelButton: true,
//         confirmButtonColor: "#1ab394",
//         confirmButtonText: 'Si, Confirmar',
//         cancelButtonText: "No, Cancelar",
//     }).then((result) => {
//         if (result.isConfirmed) {
//             var table = $('.dataTables-ingreso').DataTable();
//             var data = table.row($(this).parents('tr')).data();
//             var detalle = {
//                 lote_id: data[5],
//                 cantidad: data[2],
//             }
//             //DEVOLVER LA CANTIDAD LOGICA
//             cambiarCantidad(detalle,'0')
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

//     let enviar = false;
//     let cantidad= $('#cantidad_form').val();
//     let lote= $('#lote_form').val();
//     let producto= $('#producto_form').val();


//     let cantidad_actual = convertFloat($('#cantidad_actual_form').val());


//     if(convertFloat(cantidad) > cantidad_actual)
//     {
//         toastr.warning('La cantidad debe ser menor o igual al stock actual: ' + cantidad_actual);

//         enviar=true;
//     }

//     if(convertFloat(cantidad) <= 0)
//     {
//         toastr.warning('La cantidad debe ser mayor a 0.');

//         enviar=true;
//     }

//     if(cantidad.length === 0 || lote.length === 0 || producto.length === 0)
//     {
//         toastr.error('Ingrese datos', 'Error');
//         enviar=true;
//     }
//     else {
//         var existe = buscarProducto($('#lote_form').val())
//         if (existe == true) {
//             toastr.error('Producto ya se encuentra ingresado.', 'Error');
//             enviar = true;
//         }
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
//             text: "¿Seguro que desea agregar Producto?",
//             icon: 'question',
//             showCancelButton: true,
//             confirmButtonColor: "#1ab394",
//             confirmButtonText: 'Si, Confirmar',
//             cancelButtonText: "No, Cancelar",
//         }).then((result) => {
//             if (result.isConfirmed) {

//                 var detalle = {
//                 	cantidad: cantidad,
//                     lote_id: lote,
//                     producto_id: producto,
//                     producto_lote: $('#producto_lote_form').val()
//                 }
//                 agregarTabla(detalle);
//                 cambiarCantidad(detalle,'1');
//                 $('#asegurarCierre').val(1)
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

// $(document).on('click', '.btn-edit', function(event) {
//     var table = $('.dataTables-ingreso').DataTable();
//     var data = table.row($(this).parents('tr')).data();

//     let indice = table.row($(this).parents('tr')).index();

//     $.ajax({
//         type : 'POST',
//         url : '{{ route('almacenes.nota_salidad.obtener.lote') }}',
//         data : {
//             '_token' : $('input[name=_token]').val(),
//             'lote_id' : data[5],
//         }
//     }).done(function (response){
//         if(response.success)
//         {
//             $('#modal_editar_detalle #indice').val(indice);
//             $('#modal_editar_detalle #cantidad').val(data[2]);
//             $('#modal_editar_detalle #cantidad_actual').val(data[2]);
//             $('#modal_editar_detalle #producto_lote').val(data[3]);
//             $("#modal_editar_detalle #producto").val(data[4]);
//             $("#modal_editar_detalle #lote").val(data[5])
//             $('#modal_editar_detalle').data("abierto","1")
//             $('#modal_editar_detalle').modal('show');

//             let suma_cant = parseFloat(response.lote.cantidad_logica) + parseFloat(data[2]);
//             //AGREGAR LIMITE A LA CANTIDAD SEGUN EL LOTE SELECCIONADO
//             $("#modal_editar_detalle #cantidad").attr({
//                 "max" : suma_cant,
//                 "min" : 1,
//             });
//         }
//         else{
//             toastr.warning('Ocurrió un error porfavor recargar la pagina.')
//         }
//     });
// });



// function agregarTabla($detalle) {
//     var t = $('.dataTables-ingreso').DataTable();
//     t.row.add([
//         $detalle.producto_id,'',
//     	$detalle.cantidad,
//     	$detalle.producto_lote,
//     	$detalle.producto_id,
//         $detalle.lote_id
//     ]).draw(false);
//     limpiarDetalle()
//     cargarDetalle()
// }

// function cargarDetalle() {
//     var notadetalle = [];
//     var table = $('.dataTables-ingreso').DataTable();
//     var data = table.rows().data();
//     data.each(function(value, index) {
//         let fila = {
//             cantidad: value[2],
//             lote_id: value[5],
//             producto_id: value[4],
//         };

//         notadetalle.push(fila);

//     });
//     $('#notadetalle_tabla').val(JSON.stringify(notadetalle))
// }

// //CAMBIAR LA CANTIDAD LOGICA DEL PRODUCTO
// function cambiarCantidad(detalle, condicion) {
//     $.ajax({
//         type : 'POST',
//         url : '{{ route('almacenes.nota_salidad.cantidad') }}',
//         data : {
//             '_token' : $('input[name=_token]').val(),
//             'producto_id' : detalle.lote_id,
//             'cantidad' : detalle.cantidad,
//             'condicion' : condicion,
//         }
//     }).done(function (result){
//         //alert('REVISAR')
//         console.log(result)
//     });
// }

// //DEVOLVER CANTIDADES A LOS LOTES
// function devolverCantidades() {
//     //CARGAR PRODUCTOS PARA DEVOLVER LOTE
//     cargarDetalle()
//     return $.ajax({
//         dataType : 'json',
//         type : 'post',
//         url : '{{ route('almacenes.nota_salidad.devolver.cantidades') }}',
//         data : {
//             '_token' : $('input[name=_token]').val(),
//             'cantidades' :  $('#notadetalle_tabla').val(),
//         },
//         async: true
//     }).responseText()
// }

// $('#cantidad_form').on('input', function() {
//     let max= convertFloat(this.max);
//     let valor = convertFloat(this.value);
//     if(valor>max){
//         toastr.error('La cantidad ingresada supera al stock del producto Max('+max+').', 'Error');
//         this.value = max;
//     }
// });

// function limpiarDetalle(){
//     $('#cantidad_form').val('');
//     $('#lote_form').val('');
//     $('#producto_form').val('');
//     $('#producto_lote_form').val('');
//     $("#cantidad_form").removeAttr('max');
// }

// function buscarProducto(id) {
//     var existe = false;
//     var t = $('.dataTables-ingreso').DataTable();
//     t.rows().data().each(function(el, index) {
//         if (el[5] == id) {
//             existe = true
//         }
//     });
//     return existe
// }
</script>
{{-- <script>
    window.onbeforeunload = () => {
        if ($('#asegurarCierre').val() == 1) {
            while (true) {
                devolverCantidades()
            }
        }
    }

</script> --}}
<script>
    const tallasBD              =   @json($tallas);
    const tableStocksBody       =   document.querySelector('#table-stocks-notasalida tbody');   
    const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');
    const  bodyTablaDetalle = document.querySelector('#table-detalle-notasalida tbody');
    let detallesSalida=[];
    let formNotaSalida= document.querySelector('#enviar_nota_salida');//boton que hace de formulario para enviar los registros salida
    const btnGrabar =document.querySelector('#btn_grabar');// boton que guardar los registros de salida
    const inputProductos=document.querySelector('#notadetalle_tabla');
   // let inputProductos= document.querySelector('#');
    let modelo_id   =   null;

    document.addEventListener('DOMContentLoaded', ()=>{
        events();
    })

    function events(){
        
        //Agrega los datos que son de salida
        btnAgregarDetalle.addEventListener('click',()=>{
            let inputsCantidad=document.querySelectorAll('.inputCantidad');
            inputsCantidad.forEach((ic)=>{
                const cantidad= ic.value?ic.value:0;
                if(cantidad != 0){
                    const producto= formarProducto(ic);
                    const indiceExiste= detallesSalida.findIndex((p)=>{
                        return p.producto_id==producto.producto_id && p.color_id==producto.color_id && p.talla_id==producto.talla_id
                    })
                    if(indiceExiste == -1){
                        detallesSalida.push(producto);
                    }else{
                        const productoModificar= detallesSalida[indiceExiste];
                        productoModificar.cantidad= producto.cantidad;
                        detallesSalida[indiceExiste]= productoModificar;
                    }
                }else{
                    const producto= formarProducto(ic);
                    const indiceExiste= detallesSalida.findIndex((p)=>{
                        return p.producto_id ==producto.producto_id && p.color_id == producto.color_id && p.talla_id == producto.talla_id})
                    if(indiceExiste !== -1){
                        detallesSalida.splice(indiceExiste,1);
                    }
                }
                // let producto =formarProducto(ic);
                // detallesSalida.push(producto);
            })

            reordenarDetallesSalida();
            pintarDetallesSalida(detallesSalida);
        })
        /////////////
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');
                eliminarProducto(productoId,colorId);
                pintarDetallesSalida(detallesSalida);
            }
        })

         //============ EVENTO ENVIAR FORMULARIO =============
         formNotaSalida.addEventListener('submit',(e)=>{
            e.preventDefault();
            btnGrabar.disabled=true;
            console.log(detallesSalida);
            if(detallesSalida.length>0){
                inputProductos.value=JSON.stringify(detallesSalida);
                console.log(inputProductos);
                const formData = new FormData(formNotaSalida);
                formData.forEach((valor, clave) => {
                    console.log(`${clave}: ${valor}`);
                });
                formNotaSalida.submit();
            }else{
                toastr.error('El detalle de la nota de ingreso está vacío!!!')
                btnGrabar.disabled = false;
            }
           
        })

        //======= validar el contenido input cantidad =======
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
                this.validarCantidadInstantanea(e);
            }
        })


    }

       //============ REORDENAR CARRITO ===============
       const reordenarDetallesSalida= ()=>{
        detallesSalida.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }


    function pintarDetallesSalida(detallesSalida){
        let fila=``;
        let htmlTallas=``;
        const producto_color_procesado=[];
        cleanDetailsSale();

        detallesSalida.forEach((c)=>{
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
                tallasBD.forEach((t)=>{
                    let cantidad = detallesSalida.filter((ct)=>{
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

      //========= FUNCIÓN ELIMINAR PRODUCTO DEL CARRITO =============
   const eliminarProducto= (productoId,colorId)=>{
    console.log(detallesSalida);
    detallesSalida= detallesSalida.filter((p)=>{
        return !(p.producto_id == productoId && p.color_id == colorId);
    }

    )
   }


    // LIMPIAR TABLA DETALLE SALIDA
    function cleanDetailsSale(){
        while (bodyTablaDetalle.firstChild) {
            bodyTablaDetalle.removeChild(bodyTablaDetalle.firstChild);
        }
    }

     //======= CARGAR STOCKS LOGICOS DE PRODUCTOS POR MODELO =======
     async function getProductosByModelo(idModelo){
        modelo_id = idModelo;
         btnAgregarDetalle.disabled=false;

        if(modelo_id){
            try {
                const url = `/get-producto-by-modelo/${modelo_id}`;
                const response = await axios.get(url);
                console.log(response.data);
                pintarTableStocks(response.data.stocks,tallasBD,response.data.producto_colores);
            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            }
        }else{
            tableStocksBody.innerHTML = ``;
        }
    }

   
    //======== VALIDAR CANTIDAD DE INPUTS AL ESCRIBIR =========   
    async function validarCantidadInstantanea(event) {
        btnAgregarDetalle.disabled  =   true;
        const cantidadSolicitada    =   event.target.value;
        try {
            if(cantidadSolicitada !== ''){
                const stock_logico  =  await this.getStockLogico(event.target);
                if(stock_logico < cantidadSolicitada){
                        event.target.classList.add('inputCantidadIncorrecto');
                        event.target.classList.remove('inputCantidadValido');
                        event.target.focus();

                        event.target.value = stock_logico;
                        toastr.error(`Cantidad de salida: ${cantidadSolicitada}, debe ser menor o igual
                        al stock : ${stock_logico}`,"Error");
                        btnAgregarDetalle.disabled  =   false;
                }else{
                        event.target.classList.add('inputCantidadValido');
                        event.target.classList.remove('inputCantidadIncorrecto');
                }                    
            }else{
                event.target.classList.remove('inputCantidadIncorrecto');
                event.target.classList.remove('inputCantidadValido');
                btnAgregarDetalle.disabled  =   false;
            }   
        } catch (error) {
            toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
            event.target.value='';
            console.error('Error al obtener stock logico:', error);
            btnAgregarDetalle.disabled  =   false;
        }
    }



        //=============== Da forma de objeto a los datos obtenidos de la tabla salida =============
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

    //=========== OBTENER STOCK LOGICO DESDE LA BD =======
    async function getStockLogico(inputCantidad){
            const producto_id           =   inputCantidad.getAttribute('data-producto-id');
            const color_id              =   inputCantidad.getAttribute('data-color-id');
            const talla_id              =   inputCantidad.getAttribute('data-talla-id');
            
            try {  
                const url = `/almacenes/nota_salidad/getStock/${producto_id}/${color_id}/${talla_id}`;
                const response = await axios.get(url);
                if(response.data.message=='success'){
                    const stock  =   response.data.data[0].stock;
                    return stock;
                }
                 
            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock:', error);
                return null;
            }
    }

     //========= PINTAR TABLA STOCKS ==========
     const pintarTableStocks = (stocks,tallas,producto_colores)=>{
        let options =``;
console.log(stocks);
        producto_colores.forEach((pc)=>{
            options+=`  <tr>
                            <th scope="row"  data-color=${pc.color_id} >
                                ${pc.color_nombre}
                            </th>
                            <th scope="row" data-producto=${pc.producto_id}  >
                                ${pc.producto_nombre} 
                            </th>
                        `;

            let htmlTallas = ``;
           
            tallas.forEach((t)=>{
                const stock = stocks.filter(st => st.producto_id == pc.producto_id && st.color_id == pc.color_id && st.talla_id == t.id)[0]?.stock || 0;
                
                htmlTallas +=   `
                                    <td style="background-color: rgb(210, 242, 242);">${stock}</td>
                                    <td width="8%">
                                        ${stock > 0 ? `
                                            <input type="text" class="form-control inputCantidad"
                                            id="inputCantidad_${pc.producto_id}_${pc.color_id}_${t.id}" 
                                            data-producto-id="${pc.producto_id}"
                                            data-producto-nombre="${pc.producto_nombre}"
                                            data-color-nombre="${pc.color_nombre}"
                                            data-talla-nombre="${t.descripcion}"
                                            data-color-id="${pc.color_id}" data-talla-id="${t.id}"
                                            data-lote-id="${t.id}"></input>    
                                        ` : ''}
                                    </td>
                                `;   
            })

           

            htmlTallas += `</tr>`;
            options += htmlTallas;
        })

        tableStocksBody.innerHTML = options;
        // btnAgregarDetalle.disabled = false;
    }

    
</script>

@endpush
