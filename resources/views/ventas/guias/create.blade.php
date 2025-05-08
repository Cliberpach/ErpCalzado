@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA GUIA DE REMISION</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('ventas.guiasremision.index')}}">Guias de Remision</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-bottom: 0px;">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    @include('ventas.guias.forms.form_guia_create')
                </div>
            </div>
        </div>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-top:0px;">

    <div class="row">
        <div class="col-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h3 class="font-weight-bold text-primary">
                                <i class="fas fa-box-open"></i> PRODUCTOS
                            </h3>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-3">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                            <label class="required" style="font-weight: bold;">CATEGORÍA - MARCA - MODELO - PRODUCTO</label>
                            <select 
                                id="producto"
                                class=""
                                onchange="getColoresTallas()" >
                                <option value=""></option>
                            </select>
                        </div>
                       
                        <div class="col-12 mb-3">
                            @include('ventas.guias.table-stocks')
                        </div>           
                        <div class="col-lg-2 col-xs-12">
                            <button  type="button" id="btn_agregar_detalle" class="btn btn-warning btn-block">
                                <i class="fa fa-plus"></i> AGREGAR
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>DETALLE DE LA GUÍA</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="row">
                                        <div class="col-12">
                                            @include('ventas.guias.table-detalle')
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button class="btn btn-primary" type="submit" form="formRegistrarGuia">
                                                <i class="fas fa-save"></i> REGISTRAR
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
    </div>

</div>





@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')

<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>

let dtGuiaStocks            =   null
const carrito               =   [];

const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');
const tableDetalleBody      =   document.querySelector('#table-detalle-guia tbody');      
const tfoot_cantidadTotal   =   document.querySelector('#tfoot_cantidadTotal');   

document.addEventListener('DOMContentLoaded',()=>{
    loadSelect2();
    $('#modalidad_traslado').val('02').trigger('change');
    dtGuiaStocks    =   iniciarDataTable('table-stocks');
    events();
})

function events(){

    document.addEventListener('click',(e)=>{

        if (e.target.classList.contains('chkTipoVehiculo')) { 
            const marcado   =   e.target.checked;
            if(marcado){
                document.querySelector('#divTransportista').style.display = 'none';
                document.querySelector('#vehiculo').removeAttribute('required');
                document.querySelector('#conductor').removeAttribute('required');
            }else{
                document.querySelector('#divTransportista').style.display   = 'flex';
                document.querySelector('#vehiculo').setAttribute('required', 'required');
                document.querySelector('#conductor').setAttribute('required', 'required');
            }
        }

        if(e.target.classList.contains('delete-product')){

            const producto_id   =   e.target.getAttribute('data-producto');
            const color_id      =   e.target.getAttribute('data-color');

            const item          =   carrito.filter((p)=>{
                return p.producto_id==producto_id && p.color_id==color_id;
            })[0];

            const index_item    =   carrito.findIndex((p)=> {
                return p.producto_id==producto_id && p.color_id==color_id; 
            })
                
            eliminarItem(item,index_item);
        }

    })

    btnAgregarDetalle.addEventListener('click',()=>{ 

        toastr.clear();
        mostrarAnimacion();
        agregarProducto();
        reordenarCarrito();
        pintarDetalle();
        toastr.info('PRODUCTOS AGREGADOS');
        ocultarAnimacion();
        
    })

    document.querySelector('#formRegistrarGuia').addEventListener('submit',(e)=>{
        e.preventDefault();
        toastr.clear();

        if(carrito.length === 0){
            toastr.error('EL DETALLE DE LA GUÍA ESTÁ VACÍO');
            return;
        }

        registrarGuia(e.target);

    })
}

function loadSelect2(){

    $(".select2_form").select2({
        placeholder: "SELECCIONAR", 
        allowClear: true,          
        width: '100%',            
    });

    $('#producto').select2({
        width:'100%',
        placeholder: "Buscar producto...",
        allowClear: true,
        language: {
            inputTooShort: function(args) {
                var min = args.minimum;
                return "Por favor, ingrese " + min + " o más caracteres";
            },
            searching: function() {
                return "BUSCANDO...";
            },
            noResults: function() {
                return "No se encontraron productos";
            }
        },
        ajax: {
            url: '{{route("ventas.guiasremision.getProductos")}}', 
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                return {
                    search: params.term,
                    almacen_id: $('#almacen').val(),
                    page: params.page || 1  
                };
            },
            processResults: function(data,params) {
                if(data.success){
                    params.page     =   params.page || 1;
                    const productos =   data.productos;
                    return {
                         results: productos.map(item => ({
                            id: item.producto_id,
                            text: item.producto_completo 
                        })),
                        pagination: {
                            more: data.more 
                        }
                    };
                }else{
                    toastr.error(data.message,'ERROR EN EL SERVIDOR');
                    return {
                        results:[]
                    }
                }    
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: function(data) {
            if (data.loading) {
                return $('<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>');
            }
            return data.text;
        },
    });

}

    function formarProducto(ic){
        const producto_id           =   ic.getAttribute('data-producto-id');
        const producto_nombre       =   ic.getAttribute('data-producto-nombre');
        const color_id              =   ic.getAttribute('data-color-id');
        const color_nombre          =   ic.getAttribute('data-color-nombre');
        const talla_id              =   ic.getAttribute('data-talla-id');
        const talla_nombre          =   ic.getAttribute('data-talla-nombre');
        const cantidad              =   ic.value?ic.value:0;
        const producto              =   {producto_id,producto_nombre,color_id,color_nombre,
                                        talla_id,talla_nombre,cantidad};
        return producto;
    }

    //=========== CALCULAR LA CANTIDAD DE PRODUCTOS ===============
    function calcularCantidad(lista){
        if(lista.length!==0){
            let cantidadTotal =   0;
            lista.forEach((p)=>{
                p.tallas.forEach((t)=>{
                    cantidadTotal += parseInt(t.cantidad);                
                })
            })
            return cantidadTotal;
        }else{
            return 0;
        }
    }

     //============= ELIMINAR PRODUCTO COLOR DEL CARRITO ===============
     function eliminarItem(item,index) {
             
        toastr.clear();
        //========== ACTUALIZAR CARRITO =======
        carrito.splice(index, 1);
        carrito.length>0?asegurarCierre=1:0;
        //======= REORDENAR CARRITO =======
        reordenarCarrito();
        //====== PINTAR CARRITO ==========
        pintarDetalle();
        //======= ALERTA ==========
        toastr.success('Producto eliminado')
                              
         
    }

     //============  REORDENAR CARRITO ============
     function reordenarCarrito(){
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    function pintarDetalle(){
        let fila            =   ``;
        let cantidadTotal   =   calcularCantidad(carrito);
        
        carrito.forEach((p)=>{
            const carritoFiltrado = carrito.filter((c) => {
                return c.producto_id == p.producto_id && c.color_id == p.color_id;
            });

            const cantidadColor =   calcularCantidad(carritoFiltrado);
            fila += `   
                        <tr>
                            <th scope="row"> 
                                <i class="fas fa-trash-alt btn btn-primary delete-product"
                                data-producto="${p.producto_id}" data-color="${p.color_id}"></i>
                            </th>
                            <td>${cantidadColor}</td>
                            <td>${p.producto_nombre} - ${p.color_nombre}</td> 
                    `;
            let descripcion =   ``;
            p.tallas.forEach((t)=>{
                descripcion += `[${t.talla_nombre}/${t.cantidad}]`
            })

            fila+=  `
                            <td>${descripcion}</td>
                            <td>0.0</td>
                        </tr>
                    `;
        })

        tableDetalleBody.innerHTML          =   fila;
        tfoot_cantidadTotal.textContent     =   cantidadTotal;
    }

function cambiarModalidadTraslado(modalidad){

    const conductores       =   @json($conductores);
    let   conductoresNew    =   [];
    let   selectConductor   =   $('#conductor');


    if(modalidad === '02'){
        conductoresNew  =   conductores.filter((c)=>{
            return c.modalidad_transporte   === 'PRIVADO';
        })
        document.querySelector('#divCategoriaML').style.display     =   'flex';
        document.querySelector('.chkTipoVehiculo').checked          =   false;
    }

    if(modalidad === '01'){
        conductoresNew  =   conductores.filter((c)=>{
            return c.modalidad_transporte   === 'PUBLICO';
        })
        document.querySelector('#divTransportista').style.display   = 'flex';
        document.querySelector('#divCategoriaML').style.display     = 'none';
    }

    selectConductor.val(null).trigger('change'); 
    selectConductor.select2('destroy');
    selectConductor.empty();

    conductoresNew.forEach(opcion => {
        const texto =   `${opcion.tipo_documento_nombre}:${opcion.nro_documento} - ${opcion.nombres}`;
        let newOption = new Option(texto, opcion.id, false, false);
        selectConductor.append(newOption);
    });

    selectConductor.select2({
        placeholder: 'Seleccione un conductor',
        allowClear: true,
        width:'100%'
    });

}

    //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
    async function getColoresTallas(){
        mostrarAnimacion();
        const producto_id   =   $('#producto').val();
        const almacen_id    =   $('#almacen').val();

        if(producto_id && almacen_id){
            try {
                const res   =   await   axios.get(route('ventas.guiasremision.getColoresTallas',{almacen_id,producto_id}));
                if(res.data.success){

                    destruirDataTable(dtGuiaStocks);
                    limpiarTabla('table-stocks');
                    pintarTableStocks(res.data.producto_color_tallas);
                    dtGuiaStocks    =   iniciarDataTable('table-stocks');
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }finally{
                ocultarAnimacion();
            }
        }else{
            destruirDataTable(dtGuiaStocks);
            limpiarTabla('table-stocks');
            dtGuiaStocks    =   iniciarDataTable('table-stocks');
            ocultarAnimacion();
        }
    }

    function agregarProducto(){

        const inputsCantidad = document.querySelectorAll('.inputCantidad');
            
        for (const ic of inputsCantidad) {

                const cantidad              = ic.value ? ic.value : null;
                const producto              = formarProducto(ic);
                const indiceProductoColor   = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                if (cantidad) {
                        
                    //===== PRODUCTO NUEVO =====
                    if (indiceProductoColor == -1) {

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

                        const productoModificar         = carrito[indiceProductoColor];
                        productoModificar.precio_venta  = producto.precio_venta;

                        const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                        if (indexTalla !== -1) {
                            const cantidadAnterior                          = productoModificar.tallas[indexTalla].cantidad;
                            productoModificar.tallas[indexTalla].cantidad   = producto.cantidad;
                            carrito[indiceProductoColor]                           = productoModificar;
                        } else {
                            const objTallaProduct = {
                                talla_id: producto.talla_id,
                                talla_nombre: producto.talla_nombre,
                                cantidad: producto.cantidad
                            };
                            carrito[indiceProductoColor].tallas.push(objTallaProduct);
                        }

                    }

                } else {

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
    }


    const pintarTableStocks = (producto)=>{

        let filas = ``;
        const   tableStocksBody     =   document.querySelector('#table-stocks tbody');

        producto.colores.forEach((color)=>{
            filas   +=  `  <tr>
                            <th scope="row" data-producto=${producto.id} data-color=${color.id} >
                                <div style="width:200px;">${producto.nombre}</div>
                            </th>
                            <th scope="row">${color.nombre}</th>
                        `;

            color.tallas.forEach((talla)=>{
                filas += `<td style="background-color: rgb(210, 242, 242);">
                        <p style="margin:0;width:20px;text-align:center;${talla.stock != 0 ? 'font-weight:bold' : ''};">
                            ${talla.stock}
                        </p>
                      </td>`;

            if (talla.stock != 0) {
                filas += `<td width="8%">
                                <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                    id="inputCantidad_${producto.id}_${color.id}_${talla.id}" 
                                    data-producto-id="${producto.id}"
                                    data-producto-nombre="${producto.nombre}"
                                    data-color-nombre="${color.nombre}"
                                    data-talla-nombre="${talla.nombre}"
                                    data-color-id="${color.id}" 
                                    data-talla-id="${talla.id}" 
                                    data-producto-codigo="${producto.codigo}">
                            </td>`;
                } else {
                    filas += `<td width="8%"></td>`; 
                }
            })

            filas   +=  `</tr>`;
           
        })

        tableStocksBody.innerHTML = filas;

    }

    function registrarGuia(formRegistrarGuia){

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Deseas registrar la Guía de Remisión?",
        text: "Se creará un nuevo registro!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
           
            Swal.fire({
                title: "Registrando guía...",
                text: "Por favor, espere",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData  =   new FormData(formRegistrarGuia);
                formData.append('lstGuia',JSON.stringify(carrito));
                formData.append('sede_id',@json($sede_id));
                formData.append('registrador_id',@json($registrador->id));

                const res       =   await axios.post(route('ventas.guiasremision.store'),formData);
                if(res.data.success){
                    window.location =  route('ventas.guiasremision.index');
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                }else{
                    Swal.close();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 422) {
                        const errors = error.response.data.errors;
                        pintarErroresValidacion(errors, 'error');
                        Swal.close();
                        toastr.error("ERRORES DE VALIDACIÓN!!!");
                    } else {
                        Swal.close();
                        toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } else if (error.request) {
                    Swal.close();
                    toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.', 'ERROR DE CONEXIÓN');
                } else {
                    Swal.close();
                    toastr.error(error.message, 'ERROR DESCONOCIDO');
                }    
            }

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
    }

    function cambiarMotivoTraslado(motivo_traslado){

        toastr.clear();
        if(motivo_traslado === '01'){ //========= VENTA ======

            document.querySelector('#sede_destino').removeAttribute('required');
            document.querySelector('#divSedeDestino').style.display = 'none';

            document.querySelector('#divCliente').style.display = 'block';
            document.querySelector('#cliente').setAttribute('required', 'required');

            toastr.info('PUNTO LLEGADA CLIENTE');

        }

        if(motivo_traslado === '04'){ //========= TRASLADO INTERNO ======

            document.querySelector('#cliente').removeAttribute('required');
            document.querySelector('#divCliente').style.display = 'none';

            document.querySelector('#divSedeDestino').style.display = 'block';
            document.querySelector('#sede_destino').setAttribute('required', 'required');

            toastr.info('PUNTO LLEGADA SEDE');

        }

    }

      
</script>
@endpush