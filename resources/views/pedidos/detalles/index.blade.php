@extends('layout') @section('content')

@section('pedidos-active', 'active')
@section('pedidos-detalles-active', 'active')
@include('pedidos.detalles.modals.modal_detalles_anteciones')
@include('pedidos.detalles.modals.modal_detalles_despachos')
<style>

    .overlay_pedidos_detalles {
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
    .loader_pedidos_detalles {
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
    .loader_pedidos_detalles:after {
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
        <h2 style="text-transform:uppercase"><b>Listado de Detalles de Pedidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos Detalles</strong>
            </li>
        </ol>
    </div>
</div>

<div class="overlay_pedidos_detalles">
    <span class="loader_pedidos_detalles"></span>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mb-3">
        <div class="col-9">
            <div class="row">
                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_inicio" style="font-weight: bold;">Fecha desde:</label>
                    <input type="date" class="form-control" id="filtroFechaInicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" onchange="filtrarDespachoFechaInic(this.value)">
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="fecha_fin" style="font-weight: bold;">Fecha hasta:</label>
                    <input type="date" class="form-control" id="filtroFechaFin" value="{{ now()->format('Y-m-d') }}" onchange="filtrarDespachoFechaFin(this.value)">
                </div> --}}
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                    <label for="pedido_estado" style="font-weight: bold;">ESTADO</label>
                    <select  id="pedido_detalle_estado" class="form-control select2_form" onchange="filtrarEstadoDetalle()" >
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIDO">ATENDIDO</option>
                    </select>
                </div> 

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="cliente_id" style="font-weight: bold;">CLIENTE</label>
                    <select  id="cliente_id" class="form-control select2_form" onchange="filtrarCliente()" >
                        <option value=""></option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->tipo_documento.':'.$cliente->documento.'-'.$cliente->nombre}}</option>
                        @endforeach
                    </select>
                </div> 

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-3">
                    <label for="modelo_id" style="font-weight: bold;">MODELO</label>
                    <select  id="modelo_id" class="form-control select2_form" onchange="getProductosByModelo(this)" >
                        <option value=""></option>
                        @foreach ($modelos as $modelo)
                            <option value="{{$modelo->id}}">{{$modelo->descripcion}}</option>
                        @endforeach
                    </select>
                </div> 

                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                    <label for="producto_id" style="font-weight: bold;">PRODUCTO</label>
                    <select  id="producto_id" class="form-control select2_form" onchange="filtrarProducto()" >
                        <option value=""></option>
                        {{-- @foreach ($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                        @endforeach --}}
                    </select>
                </div> 

            </div>
        </div>
       {{-- <div class="col-3 d-flex align-items-end justify-content-end">
        <button hidden class="btn btn-primary" onclick="llenarCantEnviada()">LLENAR CANT ENVIADA</button>
       </div> --}}
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="pedidos_detalles"
                            style="text-transform:uppercase" width="100%">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-vote-yea"></i></th>
                                    <th class="text-center">PED</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">VENDEDOR</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">COLOR</th>
                                    <th class="text-center">TALLA</th>
                                    <th class="text-center">CANT </th>
                                    <th class="text-center">PRECIO</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">CANT ATENDIDA</th>
                                    <th class="text-center">CANT PENDIENTE</th>
                                    <th class="text-center">CANT ENVIADA</th>
                                    <th class="text-center">CANT FABRICACION</th>
                                    <th class="text-center">CANT CAMBIO</th>
                                    <th class="text-center">CANT DEVOLUCION</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-success">
                <div class="panel-heading">PROGRAMACIÓN DE PRODUCCIÓN</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        @include('pedidos.detalles.tables.table_programacion_produccion')
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
    let dataTablePedidoDetalles         =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        loadDataTablePedidoDetalles();
        events();
    })

    function events(){
        document.addEventListener('change',(e)=>{
            if (e.target.classList.contains('checkProducto')) {

                const producto          =   getProducto(e.target);
                const checkProductoId   =   e.target.getAttribute('id');
                if(e.target.checked){
                    agregarProductoToProduccion(producto);
                    lstChecksProductosMarcados.push(checkProductoId);
                }
                if(!e.target.checked){
                    quitarProductoToProduccion(producto,checkProductoId);
                }
                pintarTableProgramacionProduccion();
                
            }
        })

        //======= EVENTO DE DATATABLE - PETICIÓN DE TRAER DATOS SERVER SIDE =====
        dataTablePedidoDetalles.on('preXhr.dt', function(e, settings, data) {
            mostrarAnimacionCotizacion();
        });

        //===== EVENTO DATATABLE - DATOS LLEGARON DEL SERVER SIDE ======
        dataTablePedidoDetalles.on('xhr.dt', function(e, settings, json, xhr) {
        });

        //===== EVENTO DATATABLE - LA TABLA HA TERMINADO DE DIBUJARSE ========
        dataTablePedidoDetalles.on('draw.dt', function() {
            remarcarCheckboxProductos();
            ocultarAnimacionCotizacion();
        });
    }

    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    function loadDataTablePedidoDetalles(){
        dataTablePedidoDetalles   =   $('#pedidos_detalles').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'Tablas Generales'
                },
                {
                    titleAttr: 'Imprimir',
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimir',
                    customize: function(win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide":true,
            "ajax": {
                "url": "{{ route('pedidos.pedidos_detalles.getTable') }}",
                "type": "GET",
                "data": function(d) {
                        d.pedido_detalle_estado = $('#pedido_detalle_estado').val();
                        d.cliente_id            = $('#cliente_id').val();
                        d.modelo_id             = $('#modelo_id').val();
                        d.producto_id           = $('#producto_id').val();
                    }
            },
            "columns": [
                {
                    data: null,
                    className: "text-center",
                    render: function (data, type, row) {
                        let etiqueta    =   `  <input class="form-control checkProducto" id="checkProducto_${row.pedido_id}_${row.producto_id}_${row.color_id}_${row.talla_id}" type="checkbox" data-modelo-id="${row.modelo_id}" data-producto-id="${row.producto_id}" data-color-id="${row.color_id}" data-talla-id="${row.talla_id}" 
                        data-modelo-nombre="${row.modelo_nombre}" data-producto-nombre="${row.producto_nombre}" data-color-nombre="${row.color_nombre}" data-talla-nombre="${row.talla_nombre}"  data-cant-pend="${row.cantidad_pendiente}">`;   

                        return etiqueta;
                    }
                },
                {
                    data: 'pedido_name_id',
                    className: "text-left",
                    render: function (data, type, row) {
                        return `<p style="font-weight:bold;">${data}</p>`;
                    }
                },
                {
                    data: 'pedido_fecha',
                    className: "text-center"
                },
                {
                    data: 'cliente_nombre',
                    className: "text-center"
                },
                {
                    data: 'vendedor_nombre',
                    className: "text-center"
                },
                {
                    data: 'producto_nombre',
                    className: "text-center"
                },
                {
                    data: 'color_nombre',
                    className: "text-center"
                },
                {
                    data: 'talla_nombre',
                    className: "text-left"
                },
                {
                    data: 'cantidad',
                    className: "text-left"
                },
                {
                    data: 'precio_unitario_nuevo',
                    className: "text-center"
                },
                {
                    data: 'importe_nuevo',
                    className: "text-center"
                },
                {
                    data: 'cantidad_atendida',
                    className: "text-center",
                    render: function (data, type, row) {
                        let etiqueta    =   ``;   

                        if(data > 0){
                            etiqueta    =   `<p style="cursor:pointer;font-weight:bold;margin:0;color:blue;" onclick="openMdlAtenciones(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;   
                        }

                        if(data == 0){
                            etiqueta    =   `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'cantidad_pendiente',
                    className: "text-left"
                },
                {
                    data: 'cantidad_enviada',
                    className: "text-center",
                    render: function (data, type, row) {
                        let etiqueta    =   ``;   

                        if(row.cantidad_atendida > 0){
                            etiqueta    =   `<p style="cursor:pointer;font-weight:bold;margin:0;" onclick="openMdlDespachos(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;   
                        }

                        if(row.cantidad_atendida == 0){
                            etiqueta    =   `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
                },
                {
                    data: 'detalle_id',
                    className: "text-center",
                    render: function (data, type, row) {
                        
                        return 'CANT FABRICACION';
                    }
                },
                {
                    data: 'detalle_id',
                    className: "text-center",
                    render: function (data, type, row) {
                        
                        return 'CANT CAMBIO';
                    }
                },
                {
                    data: 'detalle_id',
                    className: "text-center",
                    render: function (data, type, row) {
                        
                        return 'CANT DEVOLUCION';
                    }
                },
            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [],
        });
    }

    function getProducto(checkProducto){
        const modelo_id             =   checkProducto.getAttribute('data-modelo-id');
        const producto_id           =   checkProducto.getAttribute('data-producto-id');
        const color_id              =   checkProducto.getAttribute('data-color-id');
        const talla_id              =   checkProducto.getAttribute('data-talla-id');
        const modelo_nombre         =   checkProducto.getAttribute('data-modelo-nombre');  
        const producto_nombre       =   checkProducto.getAttribute('data-producto-nombre');  
        const color_nombre          =   checkProducto.getAttribute('data-color-nombre');    
        const talla_nombre          =   checkProducto.getAttribute('data-talla-nombre');    
        const cantidad_pendiente    =   checkProducto.getAttribute('data-cant-pend');    

        
        return {modelo_id,producto_id,color_id,talla_id,
            modelo_nombre,producto_nombre,color_nombre,talla_nombre,cantidad_pendiente};

    }


    //========= AGREGAR PRODUCTO AL LISTADO DE PRODUCCIÓN =======
    function agregarProductoToProduccion(producto_nuevo){
        //========= REVIZAR SI EL PRODUCTO YA EXISTE EN EL LISTADO ========
        const indiceProducto    =   lstProgramaProduccion.findIndex((item)=>{
            return  item.id == producto_nuevo.producto_id;
        })

        //====== EL PRODUCTO  ES NUEVO ======
        if(indiceProducto === -1){
            //======== INSTANCIAR PRODUCTO ======
            const instancia_producto    =   {id:producto_nuevo.producto_id,nombre:producto_nuevo.producto_nombre}

            //======== INSTANCIANDO MODELO ======
            const instancia_modelo      =   {id:producto_nuevo.modelo_id,nombre:producto_nuevo.modelo_nombre};

            //====== INSTANCIANDO COLOR =======
            const instancia_color       =   {id:producto_nuevo.color_id,nombre:producto_nuevo.color_nombre};

            //====== INSTANCIANDO TALLA ======
            const instancia_talla       =   {id:producto_nuevo.talla_id,nombre:producto_nuevo.talla_nombre,cantidad_pendiente:parseInt(producto_nuevo.cantidad_pendiente)};

            //====== FORMANDO =======
            instancia_producto.modelo               =   instancia_modelo;
            instancia_producto.colores              =   [instancia_color];
            instancia_producto.colores[0].tallas    =   [instancia_talla];

            lstProgramaProduccion.push(instancia_producto);
        }

        //===== EL PRODUCTO YA EXISTE =======
        if(indiceProducto !== -1){
            //===== VERIFICAR SI EXISTE EL COLOR =======
            const producto_existe   =   lstProgramaProduccion[indiceProducto];

            const indiceColor       =   producto_existe.colores.findIndex((item)=>{
                return  item.id ==  producto_nuevo.color_id;
            })

            //====== EL COLOR NO EXISTE ======
            if(indiceColor === -1){
                //====== INSTANCIAR COLOR =====
                const instancia_color   =   {id:producto_nuevo.color_id,nombre:producto_nuevo.color_nombre};

                //===== INSTANCIAR TALLA =======
                const instancia_talla       =   {id:producto_nuevo.talla_id,nombre:producto_nuevo.talla_nombre,cantidad_pendiente:parseInt(producto_nuevo.cantidad_pendiente)};
                
                //======= FORMANDO =======
                instancia_color.tallas      =   [instancia_talla];

                producto_existe.colores.push(instancia_color)
            }

            //======== EL COLOR YA EXISTE ======
            if(indiceColor !== -1){
                //==== VERIFICAR SI LA TALLA EXISTE =====
                const color_existe  =   producto_existe.colores[indiceColor];

                const indiceTalla   =   color_existe.tallas.findIndex((item)=>{
                    return  item.id == producto_nuevo.talla_id;
                })

                //==== SI LA TALLA ES NUEVA ======
                if(indiceTalla === -1){
                    //===== INSTANCIAR TALLA =======
                    const instancia_talla       =   {id:producto_nuevo.talla_id,nombre:producto_nuevo.talla_nombre,cantidad_pendiente:parseInt(producto_nuevo.cantidad_pendiente)};
                    
                    //======= FORMANDO =======
                    color_existe.tallas.push(instancia_talla);
                }

                //===== SI LA TALLA YA EXISTE =====
                if(indiceTalla !== -1){
                    color_existe.tallas[indiceTalla].cantidad_pendiente +=  parseInt(producto_nuevo.cantidad_pendiente);
                }
            }
        }
    }

    //========= QUITAR PRODUCTO DEL LISTADO DE PROGRAMACIÓN DE PRODUCCIÓN ======
    function quitarProductoToProduccion(producto_desmarcado,checkProductoId){
        //===== VERIFICAR SI EXISTE EL PRODUCTO ======
        const indiceProducto    =   lstProgramaProduccion.findIndex((producto)=>{
            return producto.id  == producto_desmarcado.producto_id;
        })

        //====== EN CASO EXISTA =======
        if(indiceProducto !== -1){
            //======= VERIFICAR SI EXISTE EL COLOR ========
            const producto_existe   =   lstProgramaProduccion[indiceProducto];
            const indiceColor       =   producto_existe.colores.findIndex((color)=>{
                return  color.id    ==  producto_desmarcado.color_id;
            })

            //====== EN CASO EL COLOR EXISTA =====
            if(indiceColor !== -1){
                const   color_existe    =   producto_existe.colores[indiceColor];

                //======== VERIFICAR QUE LA TALLA EXISTA ======
                const indiceTalla   =   color_existe.tallas.findIndex((talla)=>{
                    return talla.id == producto_desmarcado.talla_id;
                })

                //====== EN CASO EXISTA LA TALLA ======
                if(indiceTalla !== -1){
                    const talla_existe      =   color_existe.tallas[indiceTalla];

                    //====== CONTROLAR LA CANTIDAD AL RESTAR =======
                    let aux_cant_resultante =   talla_existe.cantidad_pendiente - parseInt(producto_desmarcado.cantidad_pendiente);
                    
                    //======== LA RESTA FUE CORRECTA =====
                    if(aux_cant_resultante >= 0){
                        //==== RESTAMOS =======
                        talla_existe.cantidad_pendiente -=  parseInt(producto_desmarcado.cantidad_pendiente);

                        //======= QUITAMOS EL CHECKBOX DEL LISTADO DE CHECKBOX MARCADOS ======
                        const indiceCheckMarcado    =   lstChecksProductosMarcados.findIndex((chkId)=>{
                            return chkId    === checkProductoId;
                        })
                        if(indiceCheckMarcado !== -1){
                            lstChecksProductosMarcados.splice(indiceCheckMarcado,1);
                        }

                        //======= ELIMINAR LA TALLA EN CASO SU CANTIDAD LLEGUE A SER 0 ========
                        if(talla_existe.cantidad_pendiente == 0){
                            color_existe.tallas.splice(indiceTalla,1);

                            //====== EN CASO EL COLOR SE QUEDE SIN TALLAS, ELIMINAR EL COLOR =========
                            if(color_existe.tallas.length === 0){
                                producto_existe.colores.splice(indiceColor,1);

                                //===== EN CASO EL PRODUCTO SE QUEDE SIN COLORES, ELIMINAR EL PRODUCTO =====
                                if(producto_existe.colores.length === 0){
                                    lstProgramaProduccion.splice(indiceProducto,1);
                                }
                            }
                        }


                    }

                    //====== RESTA INCORRECTA =======
                    if(aux_cant_resultante < 0){
                        toastr.error('ERROR AL RESTAR LA CANTIDAD DEL PRODUCTO DEL LISTADO DE PROGRAMACIÓN DE PRODUCCIÓN');
                    }
                }

                //==== EN CASO LA TALLA NO EXISTA ======
                if(indiceTalla === -1){
                    toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN','TALLA NO ENCONTRADA');
                }
            }

            //======== EN CASO EL COLOR NO EXISTA =====
            if(indiceColor === -1){
                toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN','COLOR NO ENCONTRADO');
            }
        }

        //====== EN CASO EL PRODUCTO NO EXISTA =====
        if(indiceProducto === -1){
            toastr.error('ERROR AL QUITAR EL PRODUCTO DEL LISTADO PROGRAMACIÓN DE PRODUCCIÓN','PRODUCTO NO ENCONTRADO');
        }
    }

    function pintarTableProgramacionProduccion(){
        let     filas       =   ``;
        const   tallasBD    =   @json($tallas);
        const   tbody       =   document.querySelector('#table_programacion_produccion tbody');

        lstProgramaProduccion.forEach((producto)=>{
            
            //===== COLORES =======
            producto.colores.forEach((color)=>{
                filas   +=  `<tr>
                                <th><div style="width:120px;">${producto.modelo.nombre}</div></th>
                                <td><div style="width:120px;">${producto.nombre}</div></td>
                                <td><div style="width:120px;">${color.nombre}</div></td>`;

                //====== RECORRIENDO EN BASE A LAS TALLAS DE LA BD =======
                tallasBD.forEach((tallaBD)=>{

                    //======== REVIZANDO EL COLOR TIENE ESTA TALLA =====
                    const indiceTalla   =   color.tallas.findIndex((t)=>{
                        return  t.id == tallaBD.id;
                    })

                    let elementCantPendiente    =   ``;
                    //===== SI TIENE LA TALLA =====
                    if(indiceTalla !== -1){
                        elementCantPendiente    =   `<p style="margin:0;font-weight:bold;">${color.tallas[indiceTalla].cantidad_pendiente}</p>`;
                    }

                    //========= AÑADIENDO A LA FILA =======
                    filas   +=  `<td>${elementCantPendiente}</td>`;

                })

            })

        })

        tbody.innerHTML =   filas;
    }

    async function openMdlAtenciones(pedido_id,producto_id,color_id,talla_id){
        //alert(`${pedido_id}-${producto_id}-${color_id}-${talla_id}`);
        try {
            mostrarAnimacionCotizacion();
            const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesAtenciones',{pedido_id,producto_id,color_id,talla_id}));
            console.log(res);
            if(res.data.success){
                pintarMdlDetallesAtenciones(res.data.documentos_atenciones);
                $('#modal_detalles_atenciones').modal('show');
                toastr.info('VISUALIZANDO ATENCIONES');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER ATENCIONES DEL DETALLE');
        }finally{
            ocultarAnimacionCotizacion();
        }

    }

    function pintarMdlDetallesAtenciones(lstAtenciones){
        const tbody =   document.querySelector('#table_detalles_atenciones tbody');
        let filas   =   ``;

        lstAtenciones.forEach((atencion)=>{
            filas   +=  `<tr>
                            <th>${atencion.serie}-${atencion.correlativo}</th>
                            <td>${atencion.cliente}</td>
                            <td>${atencion.usuario}</td>
                            <td>${atencion.created_at}</td>
                            <td>${atencion.cantidad}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

    async function llenarCantEnviada(){
        try {
            const res   =   await axios.post(route('pedidos.pedido.llenarCantEnviada'));
            console.log(res);
        } catch (error) {
            
        }
    }

    async function openMdlDespachos(pedido_id,producto_id,color_id,talla_id){
        //alert(`${pedido_id}-${producto_id}-${color_id}-${talla_id}`);

        try {
            mostrarAnimacionCotizacion();
            const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesDespachos',{pedido_id,producto_id,color_id,talla_id}));
            console.log(res);
            if(res.data.success){
                pintarMdlDetallesDespachos(res.data.despachos);
                $('#modal_detalles_despachos').modal('show');
                toastr.info('VISUALIZANDO DESPACHOS');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER DESPACHOS DEL DETALLE');
        }finally{
            ocultarAnimacionCotizacion();
        }
    }

    function pintarMdlDetallesDespachos(lstDespachos){
        const tbody =   document.querySelector('#table_detalles_despachos tbody');
        let filas   =   ``;
        console.log(lstDespachos);
        lstDespachos.forEach((despacho)=>{
            let badge_estado_despacho   =   ``;

            if(despacho.estado_despacho === "DESPACHADO"){
                badge_estado_despacho   =   `<span class="badge badge-success">${despacho.estado_despacho}</span>`;
            }

            if(despacho.estado_despacho === "PENDIENTE"){
                badge_estado_despacho   =   `<span class="badge badge-danger">${despacho.estado_despacho}</span>`;
            }

            if(despacho.estado_despacho === "EMBALADO"){
                badge_estado_despacho   =   `<span class="badge badge-warning">${despacho.estado_despacho}</span>`;
            }

            filas   +=  `<tr>
                            <th>${despacho.serie}-${despacho.correlativo}</th>
                            <td>${despacho.cliente}</td>
                            <td>${despacho.usuario}</td>
                            <td>${despacho.user_despachador_nombre}</td>
                            <td>${despacho.fecha_venta}</td>
                            <td>${badge_estado_despacho}</td>
                            <td>${despacho.fecha_despacho}</td>
                            <td>${despacho.cantidad}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

    function mostrarAnimacionCotizacion(){
      
        document.querySelector('.overlay_pedidos_detalles').style.visibility   =   'visible';
    }

    function ocultarAnimacionCotizacion(){
        
        document.querySelector('.overlay_pedidos_detalles').style.visibility   =   'hidden';
    }

    function filtrarEstadoDetalle(){
        $('#pedidos_detalles').DataTable().draw();
    }
    
    function filtrarCliente(){
        $('#pedidos_detalles').DataTable().draw();
    }

    async function  getProductosByModelo(e){
        filtrarModelo();
        mostrarAnimacionCotizacion();
        modelo_id                   =   e.value;
        
        if(modelo_id){
            try {
                const res   =   await axios.get(route('ventas.cotizacion.getProductosByModelo',{modelo_id}));
                if(res.data.success){
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS','OPERACIÓN COMPLETADA');
                }else{
                    ocultarAnimacionCotizacion();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                ocultarAnimacionCotizacion();
                toastr.error(error,'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            }finally{
                ocultarAnimacionCotizacion();
            }
               
        }else{
            ocultarAnimacionCotizacion();
        }
    }

    function pintarSelectProductos(productos){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto_id').empty();

        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto_id').append(option);
        });

        // Refrescar Select2
        $('#producto_id').trigger('change');
    }

    function filtrarModelo(){
        $('#pedidos_detalles').DataTable().draw();
    }

    function filtrarProducto(){
        $('#pedidos_detalles').DataTable().draw();
    }

    //======= MANTENER LOS CHECKBOX PINTADOS =====
    function remarcarCheckboxProductos(){
        lstChecksProductosMarcados.forEach((checkProductoId)=>{
            const checkProducto =   document.querySelector(`#${checkProductoId}`);
            if(checkProducto){
                checkProducto.checked   =   true;
            }
        })
    }
</script>

@endpush
