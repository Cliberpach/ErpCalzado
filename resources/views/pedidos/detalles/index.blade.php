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
      z-index: 9999; /* Asegura que el overlay esté sobre todo */
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
                {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label for="pedido_estado" style="font-weight: bold;">Estado</label>
                    <select  id="pedido_estado" class="form-control select2_form" onchange="filtrarDespachosEstado(this.value)">
                        <option value=""></option>
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIENDO">ATENDIENDO</option>
                        <option value="FINALIZADO">FINALIZADO</option>
                    </select>
                </div> --}}

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
                                    <th class="text-center">PED</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">COLOR</th>
                                    <th class="text-center">TALLA</th>
                                    <th class="text-center">CANT </th>
                                    <th class="text-center">PRECIO</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">CANT ATENDIDA</th>
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

</div>
@stop

@push('styles')
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded',()=>{
        loadDataTablePedidoDetalles();
    })

    function loadDataTablePedidoDetalles(){
        $('#pedidos_detalles').DataTable({
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
            "ajax": "{{ route('pedidos.pedidos_detalles.getTable') }}",
            "columns": [
                {
                    data: 'pedido_name_id',
                    className: "text-left",
                    render: function (data, type, row) {
                        return `<p style="font-weight:bold;">${data}</p>`;
                    }
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
                            etiqueta    =   `<p style="cursor:pointer;font-weight:bold;margin:0;" onclick="openMdlAtenciones(${row.pedido_id}, ${row.producto_id}, ${row.color_id}, ${row.talla_id})" style="font-weight:bold;">${data}</p>`;   
                        }

                        if(data == 0){
                            etiqueta    =   `<p style="margin:0;">${data}</p>`
                        }

                        return etiqueta;
                    }
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
</script>

@endpush
