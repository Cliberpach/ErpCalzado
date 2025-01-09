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
                            
                            @include('almacenes.nota_ingresos.forms.form_nota_ingreso_create')


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

                                            <div class="form-group row mt-3 content-window">
                                                <div class="col-lg-12">
                                                    <div class="sk-spinner sk-spinner-wave hide-window" >
                                                        <div class="sk-rect1"></div>
                                                        <div class="sk-rect2"></div>
                                                        <div class="sk-rect3"></div>
                                                        <div class="sk-rect4"></div>
                                                        <div class="sk-rect5"></div>
                                                    </div>
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

<style>
    .talla-no-creada{
        color:rgb(201, 47, 9);
        font-weight: bold;
    }

div.content-window {
    position: relative;
}

div.content-window.sk__loading::after {
    content: '';
    background-color: rgba(255, 255, 255, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3000;
}

.content-window.sk__loading>.sk-spinner.sk-spinner-wave {
    margin: 0 auto;
    width: 50px;
    height: 30px;
    text-align: center;
    font-size: 10px;
}

.content-window.sk__loading>.sk-spinner {
    display: block;
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    z-index: 3500;
}

.content-window .sk-spinner.sk-spinner-wave.hide-window {
    display: none;
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




<script>
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
    });
</script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>

<script>
    const selectModelo  =  document.querySelector('#modelo');
    const tokenValue    = document.querySelector('input[name="_token"]').value;
    const tallas        = @json($tallas);
    const colores       = @json($colores);
    const bodyTablaProductos  =  document.querySelector('#table-productos tbody');
    const bodyTablaDetalle  =  document.querySelector('#table-detalle tbody');
    const btnAgregarDetalle = document.querySelector('#btn_agregar_detalle');
    const inputProductos=document.querySelector('#notadetalle_tabla');
   
    const formNotaIngreso   = document.querySelector('#enviar_ingresos');
    const btnGrabar         =   document.querySelector('#btn_grabar');
    const swalWithBootstrapButtons  =   Swal.mixin({
                                        customClass: {
                                            confirmButton: 'btn btn-success',
                                            cancelButton: 'btn btn-danger',
                                            },
                                            buttonsStyling: false
                                        })

    let modelo_id   = null;
    let carrito     = [];
    let table       = null;

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        //cargarDataTables();
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
        formNotaIngreso.addEventListener('submit',async (e)=>{
            e.preventDefault();
            btnGrabar.disabled          =   true;
            
            if(carrito.length>0){
                inputProductos.value    =   JSON.stringify(carrito);

                Swal.fire({
                    title: 'Generar etiquetas adhesivas?',
                    text: "Se generarán de acuerdo a la cantidad de cada talla, con un límite de 100 etiquetas - DEBE HABILITAR LAS VENTANAS EMERGENTES EN SU NAVEGADOR",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, generar',
                    cancelButtonText: "No",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('#generarAdhesivos').value   =   'SI';            
                    }else if (result.dismiss === Swal.DismissReason.cancel) {
                        document.querySelector('#generarAdhesivos').value   =   'NO';            
                    }
                    formNotaIngreso.submit();
                })
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
        console.log(carrito);
        carrito = carrito.filter((p)=>{
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    function mostrarAnimacion(){
        document.querySelector('.content-window').classList.add('sk__loading');
        document.querySelector('.sk-spinner').classList.remove('hide-window');
    }
    function ocultarAnimacion(){
        document.querySelector('.content-window').classList.remove('sk__loading');
        document.querySelector('.sk-spinner').classList.add('hide-window');
    }

    //============== FUNCIÓN OBTENER PRODUCTOS DE UN MODELO ==============
    async function getProductosByModelo(e){

        toastr.clear();
        modelo_id                   =   e.value;
        btnAgregarDetalle.disabled  =   true;
        
        const almacen_id    =   document.querySelector('#almacen_destino').value;

        if(!almacen_id){
            toastr.error('DEBE SELECCIONAR UN ALMACÉN DE DESTINO!!!');
            document.querySelector('#almacen_destino').focus();
            bodyTablaProductos.innerHTML = ``;
            return;
        }

        if(modelo_id){
            mostrarAnimacion();
            try {
                const res       =   await axios.get(route('almacenes.nota_ingreso.getProductos',{modelo_id,almacen_id}));
                if(res.data.success){
                    pintarTableStocks(tallas,res.data.productos);
                }else{
                    toastr.error(res.data.exception,res.data.message);
                }
            } catch (error) {
                toastr.error(error,'ERROR AL REALIZAR LA SOLICITUD DE PRODUCTOS');
            }finally{
                ocultarAnimacion();
            }
             
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

        const producto_color_procesados      =   [];

        productos.forEach((p)=>{

            const llave_producto_color     =   `${p.producto_id}-${p.color_id}`;

            if(!producto_color_procesados.includes(llave_producto_color)){
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

                            //======= BUSCAMOS SI EXISTE EL PRODUCTO-COLOR-TALLA ========
                            const existeProducto     =   productos.findIndex((item)=>{
                                return  item.producto_id == p.producto_id && item.color_id  ==  p.color_id && item.talla_id == t.id;
                            });

                            let classProducto   =   null;
                            let stock   =   0;
                            let message        =   null;

                            existeProducto == -1?stock=0:stock=productos[existeProducto].stock;
                            existeProducto == -1?classProducto='talla-no-creada':classProducto='talla-creada';
                            existeProducto == -1?message='AÚN NO SE HA CREADO ESTA TALLA':message=null;

                            let etiquetaStock   =   ``;

                            if(message){
                                etiquetaStock   =   `<td><p class="${classProducto}" title="${message}" >${stock}</p></td>`;
                            }else{
                                etiquetaStock   =   `<td><p class="${classProducto}" >${stock}</p></td>`; 
                            }
                            
                            htmlTallas +=   `   
                                                ${etiquetaStock}
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

                //======= MARCANDO PRODUCTO COLOR COMO PROCESADO ========
                producto_color_procesados.push(llave_producto_color);
            }
               
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
