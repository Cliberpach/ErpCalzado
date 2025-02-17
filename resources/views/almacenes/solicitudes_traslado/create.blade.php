@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('traslados-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVO TRASLADO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_salidad.index')}}">Traslados</a>
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
                    @include('almacenes.traslados.forms.form_traslado_create')
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

<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    width: '100%',
});
</script>

<script>
    const tallasBD              =   @json($tallas);
    const tableStocksBody       =   document.querySelector('#tabla_ns_productos tbody');   
    const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');
    const  bodyTablaDetalle     =   document.querySelector('#tabla_ns_detalle tbody');
    let detallesSalida          =   [];
    let formTrasladoStore       =   document.querySelector('#formTrasladoStore');
    const btnGrabar             =   document.querySelector('#btn_grabar');
    const inputProductos        =   document.querySelector('#notadetalle_tabla');
    let modelo_id               =   null;
    let dtNsDetalle             =   null;
    let dtNsProductos           =   null;

    document.addEventListener('DOMContentLoaded', ()=>{
        events(); 
        dtNsDetalle     =   iniciarDataTable('tabla_ns_detalle');
        dtNsProductos   =   iniciarDataTable('tabla_ns_productos');
    })

    function events(){
        
        //====== AGREGAR  PRODUCTO AL DETALLE ======
        btnAgregarDetalle.addEventListener('click',()=>{
            let inputsCantidad = document.querySelectorAll('.inputCantidad');
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

            destruirDataTable(dtNsDetalle);
            limpiarTabla('tabla_ns_detalle');
            pintarDetallesSalida(detallesSalida);
            dtNsDetalle = iniciarDataTable('tabla_ns_detalle');
        })

        //========= ELIMINAR PRODUCTO DEL DETALLE =======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                const productoId    = e.target.getAttribute('data-producto');
                const colorId       = e.target.getAttribute('data-color');

                eliminarProducto(productoId,colorId);

                destruirDataTable(dtNsDetalle);
                limpiarTabla('tabla_ns_detalle');
                pintarDetallesSalida(detallesSalida);
                dtNsDetalle = iniciarDataTable('tabla_ns_detalle');
            }
        })

         //============ EVENTO ENVIAR FORMULARIO =============
         formTrasladoStore.addEventListener('submit',async (e)=>{
            e.preventDefault();
            btnGrabar.disabled  =   true;
            console.log(detallesSalida);
            if(detallesSalida.length>0){
                
                registrarTraslado(e.target);
               
            }else{
                toastr.error('El detalle del traslado está vacío!!!')
                btnGrabar.disabled = false;
            }
           
        })

        //======= VALIDAR INPUTS CANTIDAD =======
        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
                validarCantidadInstantanea(e);
            }
        })
    }

    function registrarTraslado(formRegistrarTraslado){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea registrar el traslado?",
        text: "Se registrará con estado PENDIENTE, se moverá el stock cuando en la sede destino confirmen el traslado!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí!",
        cancelButtonText: "No!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: "Registrando traslado...",
                text: "Por favor, espera mientras procesamos la solicitud.",
                allowOutsideClick: false, 
                allowEscapeKey: false,   
                didOpen: () => {
                    Swal.showLoading(); 
                },
            });

            try {
                    
                const formData  =   new FormData(formRegistrarTraslado);
                formData.append('sede_id',@json($sede_id));
                formData.append('detalle',JSON.stringify(detallesSalida));
                const res       =   await axios.post(route('almacenes.traslados.store'),formData);

                if(res.data.success){
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    window.location =  route('almacenes.traslados.index');
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {

                if (error.response) {
                    if (error.response.status === 422) {
                        const errors = error.response.data.errors;
                        pintarErroresValidacion(errors, 'error');
                        toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                    } else {
                        toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } else if (error.request) {
                    toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.', 'ERROR DE CONEXIÓN');
                } else {
                     toastr.error(error.message, 'ERROR DESCONOCIDO');
                }  

            }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
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
        detallesSalida = detallesSalida.filter((p)=>{
                return !(p.producto_id == productoId && p.color_id == colorId);
            }
        )
    }

     //======= CARGAR STOCKS LOGICOS DE PRODUCTOS POR MODELO =======
     async function getProductosByModelo(idModelo){

        mostrarAnimacion();
        toastr.clear();
        modelo_id                   =   idModelo;
        btnAgregarDetalle.disabled  =   false;
        const almacen_origen_id     =   $('#almacen_origen').val();

        if(!modelo_id){
            ocultarAnimacion();
            return;
        }

        if(almacen_origen_id.toString().length === 0){

            const selectElement = document.getElementById('modelo');
            $(selectElement).select2('destroy'); 
            selectElement.value     = null; 
            selectElement.onchange  = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR', 
                allowClear: true 
            });            
            selectElement.onchange  = function () {
                getProductosByModelo(this.value); 
            };

            toastr.error('DEBES SELECCIONAR UN ALMACÉN DE ORIGEN!!!');
            ocultarAnimacion();
            return;
        }

        if(modelo_id){
            try {
                const url       =   route('almacenes.traslados.getProductosAlmacen',
                                    {modelo_id,almacen_id:almacen_origen_id});
                const response  =   await axios.get(url);
           

                destruirDataTable(dtNsProductos);
                limpiarTabla('tabla_ns_productos');
                pintarTableStocks(response.data.stocks,tallasBD,response.data.producto_colores);
                dtNsProductos = iniciarDataTable('tabla_ns_productos');

            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            }
        }

        ocultarAnimacion();
    }

   
    //======== VALIDAR CANTIDAD DE INPUTS AL ESCRIBIR =========   
    async function validarCantidadInstantanea(event) {
        toastr.clear();
        btnAgregarDetalle.disabled  =   true;
        const cantidadSolicitada    =   event.target.value;
        try {
            if(cantidadSolicitada !== ''){
                const stock_logico  =  await getStockLogico(event.target);
                
                if(stock_logico < cantidadSolicitada){
                        event.target.classList.add('inputCantidadIncorrecto');
                        event.target.classList.remove('inputCantidadValido');
                        event.target.focus();

                        event.target.value = stock_logico;
                        toastr.error(`Cantidad de salida: ${cantidadSolicitada}, debe ser menor o igual
                        al stock : ${stock_logico}`,"Error");
                }else{
                        event.target.classList.add('inputCantidadValido');
                        event.target.classList.remove('inputCantidadIncorrecto');
                }                    
            }else{
                event.target.classList.remove('inputCantidadIncorrecto');
                event.target.classList.remove('inputCantidadValido');
            }   
        } catch (error) {
            toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
            event.target.value='';
            console.error('Error al obtener stock logico:', error);
        }finally{
            btnAgregarDetalle.disabled  =   false;
        }
    }


    //=============== Da forma de objeto a los datos obtenidos de la tabla salida =============
    const formarProducto = (ic)=>{
        const producto_id       =   ic.getAttribute('data-producto-id');
        const producto_nombre   =   ic.getAttribute('data-producto-nombre');
        const color_id          =   ic.getAttribute('data-color-id');
        const color_nombre      =   ic.getAttribute('data-color-nombre');
        const talla_id          =   ic.getAttribute('data-talla-id');
        const talla_nombre      =   ic.getAttribute('data-talla-nombre');
        const cantidad          =   ic.value?ic.value:0;
        const producto          =   {producto_id,producto_nombre,color_id,color_nombre,
                                    talla_id,talla_nombre,cantidad};
        return producto;
    }

    //=========== OBTENER STOCK LOGICO DESDE LA BD =======
    async function getStockLogico(inputCantidad){
            const producto_id           =   inputCantidad.getAttribute('data-producto-id');
            const color_id              =   inputCantidad.getAttribute('data-color-id');
            const talla_id              =   inputCantidad.getAttribute('data-talla-id');
            const almacen_id            =   document.querySelector('#almacen_origen').value;
            
            try {  
                const url       = `/almacenes/traslados/getStock/${producto_id}/${color_id}/${talla_id}/${almacen_id}`;
                const response  = await axios.get(url);

                
                if(response.data.message=='success'){
                    const stock  =   response.data.data[0].stock_logico;
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
        //btnAgregarDetalle.disabled = false;
    }


    function cambiarAlmacen(selectAlmacen){

        toastr.clear();
        const almacen_id        =   selectAlmacen.getAttribute('id');
        const almacen_origen_id =   $('#almacen_origen').val();
        const almacen_destino_id=   $('#almacen_destino').val();

        //========= SI EL ALMACÉN ORIGEN CAMBIAR, LIMPIAR TABLA PRODUCTOS Y DETALLE =======
        if(almacen_id === 'almacen_origen'){

            //======== LIMPIAR TABLA PRODUCTOS ======
            destruirDataTable(dtNsProductos);
            limpiarTabla('tabla_ns_productos');
            dtNsProductos   =    iniciarDataTable('tabla_ns_productos');

            //======= LIMPIAR TABLA DETALLE =======
            detallesSalida  =   [];
            destruirDataTable(dtNsDetalle);
            limpiarTabla('tabla_ns_detalle');
            pintarDetallesSalida(detallesSalida);
            dtNsDetalle = iniciarDataTable('tabla_ns_detalle');

            //======= LIMPIAR SELECT MODELO ========
            const selectElement = document.getElementById('modelo');
            $(selectElement).select2('destroy'); 
            selectElement.value     = null; 
            selectElement.onchange  = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR', 
                allowClear: true 
            });            
            selectElement.onchange  = function () {
                getProductosByModelo(this.value); 
            };
        }

        if(almacen_origen_id.toString().trim().length === 0 && almacen_destino_id.toString().trim().length === 0){
           return;
        }

        if(almacen_origen_id == almacen_destino_id){

            toastr.error('DEBES SELECCIONAR ALMACENES DIFERENTES!!!');
            const selectElement = document.getElementById(almacen_id);

            $(selectElement).select2('destroy'); 
            selectElement.value     = null; 
            selectElement.onchange  = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR', 
                allowClear: true 
            });            
            selectElement.onchange  = function () {
                cambiarAlmacen(this); 
            };

            return;
        }
    }

</script>
@endpush
