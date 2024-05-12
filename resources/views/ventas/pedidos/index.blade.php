@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('pedidos-active', 'active')
@include('ventas.pedidos.modal-historial-atenciones') 
@include('ventas.pedidos.modal-pedido-detalles') 


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Pedidos</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_cotizacion" class="btn btn-block btn-w-m btn-primary m-t-md" onclick="añadirPedido()">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row mb-3">
        <div class="col-9">
            <div class="row">
                <div class="col-6">
                    <label for="fecha_inicio" style="font-weight: bold;">Fecha desde:</label>
                    <input type="date" class="form-control" id="fecha_inicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" onchange="controlFechas(this)">
                </div>
                <div class="col-6">
                    <label for="fecha_fin" style="font-weight: bold;">Fecha hasta:</label>
                    <input type="date" class="form-control" id="fecha_fin" value="{{ now()->format('Y-m-d') }}" onchange="controlFechas(this)">
                </div>
            </div>
        </div>
       <div class="col-3 d-flex align-items-end justify-content-end">
            <button class="btn btn-primary" style="width: 80%" id="btn-filtrar">FILTRAR <i class="fas fa-filter"></i></button>
       </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="pedidos_table"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">USUARIO</th>
                                    <th class="text-center">ESTADO</th>
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
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">
<style>
.search-length-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.buttons-container{
    display: flex;
    justify-content:end;
}


.custom-button {
    background-color: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #dcdcdc !important;
    border-radius: 5px;
    padding: 8px 16px;
    font-size: 14px;
    margin: 8px 0px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 3s, color 3s; 
}

.custom-button:hover {
    background-color: #d7e9fb !important;
    color: #000000 !important;
    border-color: #d7e9fb !important;
}

.dropdown-menu {
    max-height: 140px;
    overflow-y: auto; 
}

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.js"></script>
<script>
    //======== DATATABLES ====
    let pedidos_data_table  =   null;
    //====== DATATABLE PEDIDO DETALLES ========
    let detalles_data_table     =   null;
    //===== DATATABLE ATENCIONES =======
    let atenciones_data_table   =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        eventsModalAtenciones();
        getTable();
    })

    function events(){
        document.querySelector('#btn-filtrar').addEventListener('click',(e)=>{
           getTable();
        })
    }

    function loadDataTable(data){
        if(!pedidos_data_table){
            pedidos_data_table = new DataTable('#pedidos_table',{
                "order": [
                            [0, 'desc']
                        ],
            
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'Pedidos'
                    },
                    {
                        extend: 'pdf',
                        className: 'custom-button btn-check', 
                        text: '<i class="fas fa-file-pdf"></i> Pdf',
                        title: 'Pedidos'
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'Pedidos'
                    }
                ],
                dom: '<"buttons-container"B><"search-length-container"lf>tp',
                bProcessing: true,
                data:data,
                columns: [
                    { data: 'id' },
                    { data: 'cliente_nombre' },
                    { data: 'fecha_registro' },
                    { data: 'total_pagar' },
                    { data: 'user_nombre' },
                    { data: 'estado' },
                    { data: 'id',
                        className: "text-center",
                        render: function(data, type, row) {
                            let url_reporte = '{{route("ventas.pedidos.reporte", ":id")}}';
                            url_reporte = url_reporte.replace(':id', data);

                            const url_atender   =   '{{route("ventas.pedidos.atender")}}';

                            return `
                            <div class="btn-group" style="text-transform:capitalize;">
                                <button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class="fa-solid fa-list-timeline"></i></button>
                                <ul class='dropdown-menu dropdown-menu-up'>

                            <li><a class='dropdown-item'  target='_blank' href="${url_reporte}" title='Detalle'><b><i class='fa fa-file-pdf-o'></i> Pdf</a></b></li>
                            <li><a class='dropdown-item' onclick="modificarPedido(${data})" href="javascript:void(0);" title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li> 
                            <li><a class='dropdown-item' onclick="eliminarPedido(${data})"  title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li> 
                            <li><a class='dropdown-item' data-toggle="modal" data-pedido-id="${data}" data-target="#modal_pedido_detalles"  title='Detalles'><b><i class="fas fa-info-circle"></i> Detalles</a></b></li> 
                            <div class="dropdown-divider"></div>
                            <li>
                                <form id="formAtenderPedido_${data}" method="POST" action="${url_atender}">
                                    @csrf
                                    <input hidden name="pedido_id" value="${data}"></input>
                                    <a class='dropdown-item' onclick="atenderPedido(${data})"  title='Atender'><b><i class="fas fa-concierge-bell"></i> Atender</a></b>
                                </form>
                            </li> 
                            <li><a class='dropdown-item' data-toggle="modal" data-pedido-id="${data}" data-target="#modal_historial_atenciones"  title='Eliminar'><b><i class="fas fa-history"></i> Historial Atenciones</a></b></li> 
                            </ul></div>
                            `;
                        }
                    }
                ],
                language: {
                    processing:     "Traitement en cours...",
                    search:         "BUSCAR: ",
                    lengthMenu:    "MOSTRAR _MENU_ PEDIDOS",
                    info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ PEDIDOS",
                    infoEmpty:      "MOSTRANDO 0 PEDIDOS",
                    infoFiltered:   "(FILTRADO de _MAX_ PEDIDOS)",
                    infoPostFix:    "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable:     "NO HAY PEDIDOS DISPONIBLES",
                    paginate: {
                        first:      "PRIMERO",
                        previous:   "ANTERIOR",
                        next:       "SIGUIENTE",
                        last:       "ÚLTIMO"
                    },
                    aria: {
                        sortAscending:  ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }}
            })
        }else{
            pedidos_data_table.clear().rows.add(data).draw(); 
        }

        document.querySelector('.dt-buttons').classList.add('btn-group');
    }

    async function getTable(){
        try {
            const fecha_inicio  =   document.querySelector('#fecha_inicio').value;
            const fecha_fin     =   document.querySelector('#fecha_fin').value;
            const res   =   await   axios.post("{{ route('ventas.pedidos.getTable') }}",{
                fecha_inicio,
                fecha_fin
            });
            console.log(res);
            loadDataTable(res.data.message);
        } catch (error) {
            
        }
    }

    function añadirPedido() {
        window.location = "{{ route('ventas.pedidos.create') }}";
    }

    async function modificarPedido(pedido_id) {
        
        //======== VALIDAR ESTADO DEL PEDIDO ======
        const pedido    =   pedidos_data_table.rows().data().filter(function (value, index) {
                    return value['id'] == pedido_id;
        });

        if(pedido.length >0){
            const estado    =   pedido[0].estado;
                   
            if(estado === "PENDIENTE"){
                window.location = `{{ route('ventas.pedidos.edit', ['id' => ':id']) }}`.replace(':id', pedido_id);
            }
            
            if(estado === "ATENDIENDO"){
                toastr.error('EL PEDIDO NO PUEDE SER MODIFICADO','PEDIDO EN PROCESO');
                return;
            }

            if(estado === "FINALIZADO"){
                toastr.error('EL PEDIDO NO PUEDE SER MODIFICADO','PEDIDO FINALIZADO');
                return;
            }

            if(estado === "ANULADO"){
                toastr.error('EL PEDIDO NO PUEDE SER MODIFICADO','PEDIDO ANULADO');
                return;
            }
               
        }else{
            toastr.error('ERROR EN EL ID DEL PEDIDO','PEDIDO NO ENCONTRADO');
        }      
         
    }

    function reportePedido(pedido_id){
        window.location = `{{ route('ventas.pedidos.reporte', ['id' => ':id']) }}`.replace(':id', pedido_id);
    }

    function atenderPedido(pedido_id){
        Swal.fire({
            title: "DESEA ATENDER EL PEDIDO?",
            text: "Se separará el stock disponible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "SÍ, atender el pedido!",
            cancelButtonText: "No, cancelar!",
            }).then((result) => {
            if (result.isConfirmed) {

                //======== VALIDAR ESTADO DEL PEDIDO ======
                const pedido    =   pedidos_data_table.rows().data().filter(function (value, index) {
                    return value['id'] == pedido_id;
                });

                if(pedido.length >0){
                    const estado    =   pedido[0].estado;
                   
                    if(estado === "FINALIZADO"){
                        toastr.error('EL PEDIDO NO PUEDE SER ATENDIDO','PEDIDO FINALIZADO');
                        return;
                    }

                    if(estado === "ANULADO"){
                        toastr.error('EL PEDIDO NO PUEDE SER ATENDIDO','PEDIDO ANULADO');
                        return;
                    }

                    if(estado === "PENDIENTE" || estado === "ATENDIENDO"){
                        //======== ATENDER EN EL CONTROLLER ========
                        document.querySelector(`#formAtenderPedido_${pedido_id}`).submit();
                    }
                          
                }else{
                    toastr.error('ERROR EN EL ID DEL PEDIDO','PEDIDO NO ENCONTRADO');
                }

            }
        });
    }

    $('#modal_historial_atenciones').on('show.bs.modal', async function (event) {
       

       var button = $(event.relatedTarget) 
       const pedido_id   = button.data('pedido-id');


        document.querySelector('.pedido_id_span').textContent    =   pedido_id;

        //===== OBTENIENDO ATENCIONES DEL PEDIDO =======
        try {
            const res   =   await axios.get(route('ventas.pedidos.getAtenciones',{pedido_id}));
            console.log(res);
            const type  =   res.data.type;
            if(type == 'success'){
                const pedido_atenciones   =   res.data.pedido_atenciones;
                pintarTablePedidoAtenciones(pedido_atenciones);
            }
        } catch (error) {
        
        }
        
    })


    $('#modal_pedido_detalles').on('show.bs.modal', async function (event) {
       
       var button = $(event.relatedTarget) 
       const pedido_id   = button.data('pedido-id');

        document.querySelector('.pedido_id_span_pd').textContent    =   pedido_id;

        //===== OBTENIENDO DETALLES DEL PEDIDO =======
        try {
            const res   =   await axios.get(route('ventas.pedidos.getPedidoDetalles',{pedido_id}));
            console.log(res);
            const type  =   res.data.type;

            if(type == 'success'){
                const pedido_detalles   =   res.data.pedido_detalles;
                pintarTablePedidoDetalles(pedido_detalles);
            }

            if(type == 'error'){
               const message    =   res.data.message;
               const exception  =   res.data.exception;
               
               toastr.error(`${message} - ${exception}`,'ERROR');
            }
        } catch (error) {
        
        }

    })

    function pintarTablePedidoDetalles(pedido_detalles) {
        const bodyPedidoDetalles    =   document.querySelector('#table-pedido-detalles tbody');

        if(detalles_data_table){
            detalles_data_table.destroy();
        }
        
        bodyPedidoDetalles.innerHTML    =   '';
        let body    =   ``;

        pedido_detalles.forEach((pd)=>{
            body    +=  `<tr>
                <th scope="row">${pd.producto_nombre}</th>  
                <td scope="row">${pd.color_nombre}</td> 
                <td scope="row">${pd.talla_nombre}</td>
                <td scope="row">${pd.cantidad}</td>
                <td scope="row">${pd.cantidad_atendida}</td>
                <td scope="row">${pd.cantidad_pendiente}</td>
            </tr>`;
        })

        bodyPedidoDetalles.innerHTML    =   body;

        detalles_data_table             =   new DataTable('#table-pedido-detalles',{
            "order": [
                        [0, 'desc']
            ],
            buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'DETALLES DEL PEDIDO',
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'DETALLES DEL PEDIDO'
                    },
                ], 
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            language: {
                    processing:     "Procesando datos...",
                    search:         "BUSCAR: ",
                    lengthMenu:    "MOSTRAR _MENU_ ITEMS",
                    info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                    infoEmpty:      "MOSTRANDO 0 ITEMS",
                    infoFiltered:   "(FILTRADO de _MAX_ ITEMS)",
                    infoPostFix:    "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable:     "NO HAY ITEMS DISPONIBLES",
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
    
    }

    function pintarTablePedidoAtenciones(pedido_atenciones) {
        const bodyPedidoAtenciones    =   document.querySelector('#table-pedido-atenciones tbody');
        
        if(atenciones_data_table){
            atenciones_data_table.destroy();
        }

        bodyPedidoAtenciones.innerHTML    =   '';
        let body    =   ``;

        pedido_atenciones.forEach((pa)=>{
            body    +=  `<tr class="rowAtencion" data-pedido-id="${pa.pedido_id}" data-atencion-id=${pa.atencion_id}>
                <th scope="row">${pa.documento_serie}-${pa.documento_correlativo}</th>  
                <td scope="row">${pa.fecha_atencion}</td> 
                <td scope="row">${pa.documento_usuario}</td>
                <td scope="row">${pa.documento_monto_envio}</td>
                <td scope="row">${pa.documento_monto_embalaje}</td>
                <td scope="row">${pa.documento_total_pagar}</td>
            </tr>`;
        })

        bodyPedidoAtenciones.innerHTML    =   body;

        atenciones_data_table             =   new DataTable('#table-pedido-atenciones',{
            "order": [
                        [0, 'desc']
            ],
            buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'ATENCIONES DEL PEDIDO'
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'ATENCIONES DEL PEDIDO'
                    },
                ], 
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            language: {
                    processing:     "Procesando datos...",
                    search:         "BUSCAR: ",
                    lengthMenu:    "MOSTRAR _MENU_ ITEMS",
                    info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                    infoEmpty:      "MOSTRANDO 0 ITEMS",
                    infoFiltered:   "(FILTRADO de _MAX_ ITEMS)",
                    infoPostFix:    "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable:     "NO HAY ITEMS DISPONIBLES",
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

    }

    function eliminarPedido(pedido_id) {
        Swal.fire({
            title: "ESTÁS SEGURO DE ELIMINARLO?",
            text: "NO SE PODRÁ REVERTIR ESTA ACCIÓN!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "SÍ, ELIMINAR EL PEDIDO!"
            }).then(async (result) => {
            if (result.isConfirmed) {
                const res = await axios.delete(`{{ route('ventas.pedidos.destroy', ['id' => ':id']) }}`.replace(':id', pedido_id));
                if(res.data.type == 'success'){
                    //====== ELIMINAR PEDIDO DEL DATATABLE ======
                    pedidos_data_table.rows((idx, data) => data.id == res.data.pedido_id).remove().draw()
                    //===== ALERTA ======
                    toastr.success(`PEDIDO NRO° ${res.data.pedido_id}`,'ELIMINADO');
                }
                if(res.data.type == 'error'){
                    toastr.error(res.data.message,'ERROR');
                }
                console.log(res);
                Swal.fire({
                title: "ELIMINADO!",
                text: "EL PEDIDO HA SIDO ELIMINADO.",
                icon: "success"
                });
            }
        });
    }

    function controlFechas(target){
        const id    =   target.getAttribute('id');

        if(id == "fecha_inicio"){
            const fecha_inicio  =   target.value;
            const fecha_fin     =   document.querySelector('#fecha_fin').value;

            if(fecha_inicio > fecha_fin && fecha_inicio){
                document.querySelector('#fecha_fin').value  =   fecha_inicio;
            }
            document.querySelector('#fecha_fin').setAttribute('min',fecha_inicio);
        }

        if(id == "fecha_fin"){
            const fecha_fin         =   target.value;
            const fecha_inicio      =   document.querySelector('#fecha_inicio').value;

            if(fecha_fin < fecha_inicio && fecha_fin){
                document.querySelector('#fecha_inicio').value  =   fecha_fin;
            }
            document.querySelector('#fecha_inicio').setAttribute('max',fecha_fin);
        }
    }
</script>
@endpush
