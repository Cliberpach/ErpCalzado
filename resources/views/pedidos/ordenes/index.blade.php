@extends('layout') 
@section('content')

@section('pedidos-active', 'active')
@section('ordenes-pedido-active', 'active')
@include('pedidos.ordenes.modals.modal_ver_detalle')
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
        <h2 style="text-transform:uppercase"><b>Listado de Órdenes de Pedido</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Órdenes de Pedido</strong>
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
                        <table class="table table-striped table-bordered table-hover" id="table_ordenes_pedido"
                            style="text-transform:uppercase" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">USUARIO</th>
                                    <th class="text-center">FECHA PROPUESTA</th>
                                    <th class="text-center">OBSERVACIÓN</th>
                                    <th class="text-center">ACCIONES</th>
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
            mostrarAnimacion();
        });

        //===== EVENTO DATATABLE - DATOS LLEGARON DEL SERVER SIDE ======
        dataTablePedidoDetalles.on('xhr.dt', function(e, settings, json, xhr) {
        });

        //===== EVENTO DATATABLE - LA TABLA HA TERMINADO DE DIBUJARSE ========
        dataTablePedidoDetalles.on('draw.dt', function() {
            ocultarAnimacion();
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
        dataTablePedidoDetalles   =   $('#table_ordenes_pedido').DataTable({
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
                "url": "{{ route('pedidos.ordenes_pedido.getTable') }}",
                "type": "GET"
            },
            "columns": [
                {
                    data: 'op_id_name',
                    className: "text-left",
                    render: function (data, type, row) {
                        return `<p style="font-weight:bold;">${data}</p>`;
                    }
                },
                {
                    data: 'user_nombre',
                    className: "text-center"
                },
                {
                    data: 'fecha_propuesta_atencion',
                    className: "text-center"
                },
                {
                    data: 'observacion',
                    className: "text-center"
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        var url_pdf = '{{ route('pedidos.ordenes_pedido.pdf', ':id') }}';
                        url_pdf     = url_pdf.replace(':id', data.id);

                        let options =   "";
                        options +=`
                            <div class='btn-group' style='text-transform:capitalize;'>
                                <button data-toggle='dropdown' class='btn btn-primary btn-sm dropdown-toggle'>
                                <i class='fa fa-bars'></i>
                                </button>
                                <ul class='dropdown-menu'>
                                     <li>
                                        <a target='_blank' class='dropdown-item' href='${url_pdf}' title='PDF'>
                                            <b><i class="fas fa-file-pdf"></i> PDF</b>
                                        </a>
                                    </li>
                                    <li>
                                        <a class='dropdown-item' href='javascript:void(0);'onclick="verDetalleOrdenPedido(${data.id})" title='Detalle'>
                                            <b><i class="fas fa-eye"></i> Detalle</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                                `;    
                        return options;

                    
                    }
                }
            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [],
        });
    }

    function mostrarAnimacion(){
        document.querySelector('.overlay_pedidos_detalles').style.visibility   =   'visible';
    }

    function ocultarAnimacion(){
        document.querySelector('.overlay_pedidos_detalles').style.visibility   =   'hidden';
    }

    //======= VER DETALLE DE LA ORDEN DE PEDIDO =======
    async function verDetalleOrdenPedido(orden_pedido_id){
        try {
            mostrarAnimacion();
            const res   =   await axios.get(route('pedidos.ordenes_pedido.getDetalle',{orden_pedido_id}));
            if(res.data.success){
                pintarDetalleOrden(res.data.orden_pedido_detalle);
                $('#modal_detalle_orden').modal('show');
                toastr.info('VISUALIZANDO DETALLE DE LA ORDEN');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VISUALIZAR DETALLE DE LA ORDEN');
        }finally{
            ocultarAnimacion();
        }
    }

   
</script>

@endpush
