@extends('layout') 
@section('content')

@section('pedidos-active', 'active')
@section('ordenes-pedido-active', 'active')
<style>

    .overlay_orden_produccion_create {
      position: fixed; /* Fija el overlay para que cubra todo el viewport */
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7); /* Color oscuro con opacidad */
      z-index: 99999999; /* Asegura que el overlay esté sobre todo */
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      font-size: 24px;
      visibility:hidden;
    }
    
    /*========== LOADER SPINNER =======*/
    .loader_orden_produccion {
        position: relative;
        width: 75px;
        height: 100px;
        background-repeat: no-repeat;
        background-image: linear-gradient(#DDD 50px, transparent 0),
                          linear-gradient(#DDD 50px, transparent 0),
                          linear-gradient(#DDD 50px, transparent 0),
                          linear-gradient(#DDD 50px, transparent 0),
                          linear-gradient(#DDD 50px, transparent 0);
        background-size: 8px 100%;
        background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px;
        animation: pillerPushUp 4s linear infinite;
      }
    .loader_orden_produccion:after {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 10px;
        height: 10px;
        background: #de3500;
        border-radius: 50%;
        animation: ballStepUp 4s linear infinite;
      }
    
    @keyframes pillerPushUp {
      0% , 40% , 100%{background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px}
      50% ,  90% {background-position: 0px 50px, 15px 58px, 30px 66px, 45px 78px, 60px 90px}
    }
    
    @keyframes ballStepUp {
      0% {transform: translate(0, 0)}
      5% {transform: translate(8px, -14px)}
      10% {transform: translate(15px, -10px)}
      17% {transform: translate(23px, -24px)}
      20% {transform: translate(30px, -20px)}
      27% {transform: translate(38px, -34px)}
      30% {transform: translate(45px, -30px)}
      37% {transform: translate(53px, -44px)}
      40% {transform: translate(60px, -40px)}
      50% {transform: translate(60px, 0)}
      57% {transform: translate(53px, -14px)}
      60% {transform: translate(45px, -10px)}
      67% {transform: translate(37px, -24px)}
      70% {transform: translate(30px, -20px)}
      77% {transform: translate(22px, -34px)}
      80% {transform: translate(15px, -30px)}
      87% {transform: translate(7px, -44px)}
      90% {transform: translate(0, -40px)}
      100% {transform: translate(0, 0);}
    }
        
        
</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Crear Orden de Producción</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('pedidos.ordenes_produccion.index') }}">Ordenes de Producción</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Crear Orden de Producción</strong>
            </li>
        </ol>
    </div>
</div>

<div class="overlay_orden_produccion_create">
    <span class="loader_orden_produccion"></span>
</div>

<div class="wrapper wrapper-content animated fadeInRight pb-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title bg-success">
                    <h5 style="font-weight: bold;">DATOS GENERALES</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mt-3">
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="usuario" style="font-weight: bold;">USUARIO</label>
                            <input class="form-control" id="usuario" type="text" value="{{Auth::user()->usuario}}" readonly>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="fecha_propuesta_atencion" style="font-weight: bold;">FECHA PROPUESTA ATENCIÓN</label>
                            <input class="form-control" id="fecha_propuesta_atencion" type="date">
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-3">
                            <label for="observacion" style="font-weight: bold;">OBSERVACIÓN</label>
                            <textarea maxlength="260" class="form-control" id="observacion" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight pt-0 pb-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title bg-success">
                    <h5 style="font-weight: bold;">SELECCIONAR PRODUCTOS</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label class="required" style="font-weight: bold;">MODELO</label>
                            <select id="modelo"
                                class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                onchange="getProductosByModelo(this)">
                                <option></option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}"
                                        {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                        {{ $modelo->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label class="required" style="font-weight: bold;">PRODUCTO</label>
                            <select id="producto" onchange="getColoresTallas()"
                                class="select2_form form-control {{ $errors->has('producto') ? ' is-invalid' : '' }}"
                                 >
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 mb-3">
                            <div class="table-responsive">
                                @include('pedidos.ordenes.tables.table_orden_produccion_productos')
                            </div>
                        </div>
                        <div class="col-lg-2 col-xs-12">
                            <button type="button" 
                                class="btn btn-warning btn-block btn_agregar_producto">
                                <i class="fa fa-plus"></i> AGREGAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight pt-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title bg-success">
                    <h5 style="font-weight: bold;">DETALLE</h5>
                </div>
                <div class="ibox-content">
                   
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('pedidos.ordenes.tables.table_orden_produccion_detalle')
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12" style="display:flex;justify-content:end;">
                            <a class="btn btn-danger mr-2" href="{{route('pedidos.ordenes_produccion.index')}}">
                                <i class="fas fa-sign-out-alt"></i> REGRESAR
                            </a>
                            <button class="btn btn-primary btn_grabar_orden_produccion">
                                <i class="fas fa-save"></i> GRABAR
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('styles')
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<script>
    const lstProgramaProduccion         =   [];
    const lstChecksProductosMarcados    =   [];
    let dataTableProductos              =   null;
    let dataTableDetalle                =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        loadDataTableProductos();
        loadDataTableDetalle();
        events();
    })

    function events(){

        document.addEventListener('click',(e)=>{

          if(e.target.classList.contains('btn_agregar_producto')){
            //======= VALIDAMOS =====

            const validacion    =   validacionAgregarProducto();

            if(validacion){
                //======= AGREGAR PRODUCTO ======
                mostrarAnimacion();
                agregarProducto();
                ordenarDetalle();
                pintarTableDetalle();
                loadDataTableDetalle();
                ocultarAnimacion();
            }

          }

          if(e.target.classList.contains('i-delete-product')){

            mostrarAnimacion();
            const producto_id   =   e.target.getAttribute('data-producto');
            const color_id      =   e.target.getAttribute('data-color');

            //======= VERIFICAR SI EXISTE EL PRODUCTO A BORRAR =======
            const indiceProducto    =   lstProgramaProduccion.findIndex((producto)=>{
                return  producto.producto_id == producto_id && producto.color_id == color_id; 
            })

            //====== EL PRODUCTO A BORRAR EXISTE =====
            if(indiceProducto !== -1){
                //====== ELIMINAR PRODUCTO ======
                lstProgramaProduccion.splice(indiceProducto,1);
                //===== REPINTAR LA TABLA DETALLE =====
                pintarTableDetalle();
                loadDataTableDetalle();
                //======== ACTUALIZAR CANTIDADES DEL TABLERO DE PRODUCTOS =====
                destruirDataTableProductos();
                cargarCantidadesPrevias();
                loadDataTableProductos();
            }else{
                toastr.error('EL PRODUCTO NO EXISTE EN EL DETALLE','ERROR AL ELIMINAR PRODUCTO');
            }

            ocultarAnimacion();

          }

          if (e.target.closest('.btn_grabar_orden_produccion')) {
            const validacion    =   validacionGrabarOrdenProduccion();
            if(validacion){
                grabarOrdenProduccion();
            }
          }

        })

        document.addEventListener('input',(e)=>{
            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }
        })

        //======= EVENTO DE DATATABLE - PETICIÓN DE TRAER DATOS SERVER SIDE =====
        // dataTableProductos.on('preXhr.dt', function(e, settings, data) {
        //     mostrarAnimacion();
        // });

        // //===== EVENTO DATATABLE - DATOS LLEGARON DEL SERVER SIDE ======
        // dataTableProductos.on('xhr.dt', function(e, settings, json, xhr) {
        // });

        // //===== EVENTO DATATABLE - LA TABLA HA TERMINADO DE DIBUJARSE ========
        // dataTableProductos.on('draw.dt', function() {
        //     ocultarAnimacion();
        // });
    }

    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //======== VALIDACIÓN ORDEN DE PRODUCCIÓN ====
    function validacionGrabarOrdenProduccion(){
        if(lstProgramaProduccion.length === 0){
            toastr.error('EL DETALLE DE LA ORDEN DE PRODUCCIÓN ESTÁ VACÍO','OPERACIÓN INCORRECTA');
            return false;
        }

        return true;
    }

    //======== ORDENAR DETALLE =====
    function ordenarDetalle(){
        lstProgramaProduccion.sort((a, b) => {
            // Primero, ordenar por producto_nombre
            if (a.producto_nombre < b.producto_nombre) return -1;
            if (a.producto_nombre > b.producto_nombre) return 1;

            // Si producto_nombre es igual, ordenar por color_nombre
            if (a.color_nombre < b.color_nombre) return -1;
            if (a.color_nombre > b.color_nombre) return 1;

            return 0;
        });
    }

    //======== GRABAR ORDEN DE PRODUCCIÓN ======
    function grabarOrdenProduccion(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea grabar la orden de producción?",
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, grabar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
          
            const loadingSwal = Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere mientras se realiza la operación.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const res   =   await   axios.post(route('pedidos.ordenes_produccion.store'),{
                    fecha_propuesta_atencion    :   document.querySelector('#fecha_propuesta_atencion').value,
                    observacion                 :   document.querySelector('#observacion').value,
                    lstProgramacionProduccion   :   JSON.stringify(lstProgramaProduccion)
                })

                if(res.data.success){
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    window.location.href = route('pedidos.ordenes_produccion.index');
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN GRABAR ORDEN DE PRODUCCIÓN');
            }finally{
                Swal.close();
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

    function agregarProducto(){
        //====== OBTENER TODOS LOS INPUTS DEL TABLERO PRODUCTOS ========
        const inputsCantidad = document.querySelectorAll('.inputCantidad');
            
        //===== RECORRER TODOS LOS INPUTS DEL TABLERO PRODUCTOS ======
        inputsCantidad.forEach((ic)=>{

            //===== OBTENER LA CANTIDAD DE CADA INPUT =====
            const cantidad = ic.value ? ic.value : null;

            //========= OBTENER LOS DATOS DEL PRODUCTO DEL INPUT =======
            const producto_nuevo      = formarProducto(ic);

            //====== EN CASO EXISTA UNA CANTIDAD INGRESADA EN EL INPUT =====
            if (cantidad) {

                //======= VERIFICAMOS SI YA EXISTE EN EL DETALLE ======
                const indiceExiste  = lstProgramaProduccion.findIndex(p => p.producto_id == producto_nuevo.producto_id && p.color_id == producto_nuevo.color_id);

                //===== EN CASO SEA UN PRODUCTO NUEVO =====
                if (indiceExiste === -1) {

                    //========== FORMAMOS EL OBJETO DEL PRODUCTO =====
                    const objProduct = {
                        producto_id:        producto_nuevo.producto_id,
                        color_id:           producto_nuevo.color_id,
                        producto_nombre:    producto_nuevo.producto_nombre,
                        color_nombre:       producto_nuevo.color_nombre,
                        tallas: [{
                                    talla_id:       producto_nuevo.talla_id,
                                    talla_nombre:   producto_nuevo.talla_nombre,
                                    cantidad:       producto_nuevo.cantidad
                                }]
                        };

                    //======= AGREGAR AL DETALLE =======
                    lstProgramaProduccion.push(objProduct);
                }  
                
                //===== EN CASO EL PRODUCTO YA EXISTA EN EL DETALLE ======
                if(indiceExiste !== -1){

                    //======= OBTENER EL PRODUCTO A MODIFICAR DEL DETALLE ===
                    const productoModificar = lstProgramaProduccion[indiceExiste];

                    //====== VERIFICAR SI EXISTE LA TALLA QUE QUEREMOS AGREGAR ===== 
                    const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto_nuevo.talla_id);

                    //====== EN CASO YA EXISTA LA TALLA =====
                    if (indexTalla !== -1) {

                        //====== REEMPLAZAMOS LA CANTIDAD ======
                        productoModificar.tallas[indexTalla].cantidad = producto_nuevo.cantidad;
                        //==== GUARDAR CAMBIOS ======
                        lstProgramaProduccion[indiceExiste] = productoModificar;
                    } 
                    
                    //===== EN CASO NO EXISTA LA TALLA ========
                    if(indexTalla === -1){

                        //===== FORMAR NUEVA TALLA ======
                        const objTallaProduct = {
                                                    talla_id:       producto_nuevo.talla_id,
                                                    talla_nombre:   producto_nuevo.talla_nombre,
                                                    cantidad:       producto_nuevo.cantidad
                                                };
                        
                        //====== AGREGAR TALLA AL PRODUCTO ======
                        lstProgramaProduccion[indiceExiste].tallas.push(objTallaProduct);
                    }
                }
            } 
            
            //======== EN CASO NO EXISTA UNA CANTIDAD EN EL INPUT =====
            if(!cantidad){

                //========= REVIZAR SI EL PRODUCTO EXISTE EN EL DETALLE ======
                const indiceProductoColor   =   lstProgramaProduccion.findIndex(p => p.producto_id == producto_nuevo.producto_id && p.color_id == producto_nuevo.color_id);

                //======= EN CASO EL PRODUCTO EXISTA EN EL DETALLE =====
                if (indiceProductoColor !== -1) {

                    //======== REVIZAR SI LA TALLA YA EXISTE EN EL PRODUCTO DEL DETALLE =======
                    const indiceTalla = lstProgramaProduccion[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto_nuevo.talla_id);

                    //======= EN CASO LA TALLA EXISTA EN EL PRODUCTO =======
                    if (indiceTalla !== -1) {

                        //========= ELIMINAR TALLA DEL PRODUCTO ======
                        lstProgramaProduccion[indiceProductoColor].tallas.splice(indiceTalla, 1);
                            
                        //======== OBTENER CANTIDAD DE TALLAS DEL PRODUCTO =====
                        const cantidadTallas = lstProgramaProduccion[indiceProductoColor].tallas.length;

                        //===== ELIMINAR PRODUCTO, EN CASO NO TENGA TALLAS =========
                        if (cantidadTallas == 0) {
                            lstProgramaProduccion.splice(indiceProductoColor, 1);
                        }

                    }

                }
            }
        })

    }

    function clearTableDetalle(){
        const tbody =   document.querySelector('#table_orden_produccion_detalle tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

    function clearTableProductos(){
        const tbody =   document.querySelector('#table_orden_produccion_productos tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

    //========== PINTAR TABLE DETALLE ========
    function pintarTableDetalle(){
        let filas       =   ``;
        let htmlTallas  =   ``;
        const tallasBD  =   @json($tallas);
        const tbody     =   document.querySelector('#table_orden_produccion_detalle tbody');

        destruirDataTableDetalle();
        clearTableDetalle();

        lstProgramaProduccion.forEach((producto)=>{
            htmlTallas=``;
                filas += `<tr>   
                            <td>
                                <i class="fas fa-trash-alt btn btn-danger i-delete-product"
                                data-producto="${producto.producto_id}" data-color="${producto.color_id}">
                                </i>                            
                            </td>
                            <td><div style="width:200px;">${producto.producto_nombre}</div></td>
                            <td><div style="width:200px;">${producto.color_nombre}</div></td>`;

                //tallas
                tallasBD.forEach((t)=>{
                    let cantidad = producto.tallas.filter((ct)=>{
                        return t.id ==   ct.talla_id;
                    });
                    cantidad.length!=0? cantidad = cantidad[0].cantidad : cantidad = '';
                    htmlTallas += `<td><p style="margin:0;font-weight:bold;">${cantidad}</p></td>`; 
                })

                filas += htmlTallas;
        })

        tbody.innerHTML  =   filas;
    }

    function destruirDataTableDetalle(){
        if(dataTableDetalle){
            dataTableDetalle.destroy();
        }
    }

    //========== FORMAR PRODUCTO =======
    const formarProducto = (ic)=>{
        const producto_id           = ic.getAttribute('data-producto-id');
        const producto_nombre       = ic.getAttribute('data-producto-nombre');
        const color_id              = ic.getAttribute('data-color-id');
        const color_nombre          = ic.getAttribute('data-color-nombre');
        const talla_id              = ic.getAttribute('data-talla-id');
        const talla_nombre          = ic.getAttribute('data-talla-nombre');
        const cantidad              = ic.value?ic.value:0;

        const producto = {producto_id,producto_nombre,color_id,color_nombre,
                            talla_id,talla_nombre,cantidad
                        };
        return producto;
    }

    //====== VALIDACIÓN AGREGAR PRODUCTO =======
    function validacionAgregarProducto() {
        const modelo_id     =   $('#modelo').val();
        const producto_id   =   $('#producto').val();

        if(!modelo_id){
            toastr.error('DEBE SELECCIONAR UN MODELO','OPERACIÓN INCORRECTA');
            return false;
        }
        if(!producto_id){
            toastr.error('DEBE SELECCIONAR UN PRODUCTO','OPERACIÓN INCORRECTA');
            return false;
        }

        //===== VALIDAR QUE HAYA CARGADO EL TABLERO DE PRODUCTOS ======
        const lstInputsCantidad =   document.querySelectorAll('.inputCantidad');
        if(lstInputsCantidad.length === 0){
            toastr.error('LA TABLA SELECCIONAR PRODUCTOS ESTÁ VACÍA!!','OPERACIÓN INCORRECTA');
            return false;
        }

        return true;
    }

    //======== OBTENER PRODUCTOS POR MODELO ========
    async function  getProductosByModelo(e){
        mostrarAnimacion();
        limpiarTableProductos();
        const modelo_id                   =   e.value;
        
        if(modelo_id){
            try {
                const res   =   await axios.get(route('pedidos.ordenes_produccion.getProductosByModelo',{modelo_id}));
                if(res.data.success){
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS','OPERACIÓN COMPLETADA');
                }else{
                    ocultarAnimacion();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                ocultarAnimacion();
                toastr.error(error,'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            }
               
        }else{
            ocultarAnimacionCotizacion();
        }
    }

     //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
     async function getColoresTallas(){
        mostrarAnimacion();
        const producto_id   =   $('#producto').val();
        if(producto_id){
            try {
                const res   =   await   axios.get(route('pedidos.ordenes_produccion.getColoresTallas',{producto_id}));
                if(res.data.success){
                    destruirDataTableProductos();
                    pintarTableProductos(res.data.producto_color_tallas);
                    cargarCantidadesPrevias();
                    loadDataTableProductos();
                    ocultarAnimacion();
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }
        }else{
            limpiarTableProductos();
            ocultarAnimacion();
        }
    }

    const pintarTableProductos = (producto)=>{
        let filas = ``;
        const tbody =   document.querySelector('#table_orden_produccion_productos tbody')

        clearTableProductos();

        producto.colores.forEach((color)=>{
            filas   +=  `  <tr>
                            <th scope="row" data-producto=${producto.id} data-color=${color.id} >
                                <div style="width:200px;">${producto.nombre}</div>
                            </th>
                            <th scope="row">${color.nombre}</th>
                        `;

            color.tallas.forEach((talla)=>{
                filas   +=  `<td style="background-color: rgb(210, 242, 242);">
                                        <p style="margin:0;width:20px;text-align:center;${talla.stock != 0?'font-weight:bold':''};">${talla.stock}</p>
                            </td>
                            <td width="8%">
                                <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                id="inputCantidad_${producto.id}_${color.id}_${talla.id}" 
                                data-producto-id="${producto.id}"
                                data-producto-nombre="${producto.nombre}"
                                data-color-nombre="${color.nombre}"
                                data-talla-nombre="${talla.nombre}"
                                data-color-id="${color.id}" data-talla-id="${talla.id}"></input>    
                            </td>`;
            })

            filas   +=  `</tr>`;
           
        })

        tbody.innerHTML = filas;
    }

    function loadDataTableProductos(){
        dataTableProductos =   $('#table_orden_produccion_productos').DataTable({
            language: {
                "sEmptyTable": "No hay datos disponibles en la tabla",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                },
                "oAria": {
                        "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function loadDataTableDetalle(){
        dataTableDetalle =   $('#table_orden_produccion_detalle').DataTable({
            language: {
                "sEmptyTable": "No hay datos disponibles en la tabla",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                },
                "oAria": {
                        "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }


    function mostrarAnimacion(){
        document.querySelector('.overlay_orden_produccion_create').style.visibility   =   'visible';
    }

    function ocultarAnimacion(){
        document.querySelector('.overlay_orden_produccion_create').style.visibility   =   'hidden';
    }

    function limpiarTableProductos(){
        if(dataTableProductos){
            dataTableProductos.destroy();
            dataTableProductos   =   null;
        }

        const tbody =   document.querySelector('#table_orden_produccion_productos tbody')
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

     //======== PINTAR SELECT PRODUCTOS =======
     function pintarSelectProductos(productos){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto').empty();

        if(productos.length === 0){
            ocultarAnimacionCotizacion();
        }

        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto').append(option);
        });

        // Refrescar Select2
        //$('#producto').val(null);
        $('#producto').trigger('change');
    }

    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL LISTADO DE PROGRAMACIÓN DE PRODUCCIÓN =========
    function cargarCantidadesPrevias(){
        clearInputsCantidad();

        lstProgramaProduccion.forEach((producto)=>{

            producto.tallas.forEach((talla)=>{
                let llave       =   `#inputCantidad_${producto.producto_id}_${producto.color_id}_${talla.talla_id}`;   
                const inputLoad =   document.querySelector(llave);
            
                if(inputLoad){
                    inputLoad.value = talla.cantidad;
                }
            })

        }) 

    }

    //======= DESTRUIR DATATABLE PRODUCTOS =======
    function destruirDataTableProductos(){
        if(dataTableProductos){
            dataTableProductos.destroy();
        }
    }

    //===== LIMPIAR INPUTS DEL TABLERO PRODUCTOS ======
    function clearInputsCantidad(){
        const inputsCantidad    =   document.querySelectorAll('.inputCantidad');
        inputsCantidad.forEach((inputCantidad)=>{
            inputCantidad.value =   '';
        })  
    }

   
</script>

@endpush
