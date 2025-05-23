@extends('layout') 
@section('content')
@include('ventas.despachos.modal-detalles-doc')
@include('ventas.despachos.modal-bultos')
@section('ventas-active', 'active')
@section('despachos-active', 'active')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10 col-md-10">
            <h2 style="text-transform:uppercase"><b>Listado de Despachos</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Panel de Control</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Despachos</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="row mb-5">
                            <div class="col-3">
                                <label for="filtroEstado" style="font-weight: bold;">ESTADO:</label>
                                <select id="filtroEstado" class="form-control select2_form" onchange="dtDespachos.ajax.reload();">
                                    <option value="PENDIENTE">PENDIENTE</option>
                                    <option value="EMBALADO">EMBALADO</option>
                                    <option value="DESPACHADO">DESPACHADO</option>
                                </select>
                            </div>
                            
                            <div class="col-3">
                                <label for="filtroCliente" style="font-weight: bold;">CLIENTE:</label>
                                <select class="select2_form"
                                    style="text-transform: uppercase; width:100%"  name="filtroCliente"
                                    id="filtroCliente" required onchange="dtDespachos.ajax.reload();">
                                    <option value="{{$cliente_varios[0]->id}}">{{$cliente_varios[0]->nombre}}</option>
                                </select>
                                {{-- <select id="filtroCliente" class="form-control select2_form" onchange="dtDespachos.ajax.reload();">
                                    @foreach ($clientes as $cliente)
                                        <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                          
                            <div class="col-3">
                                <label for="filtroFechaInicio" style="font-weight: bold;">FEC INICIO:</label>
                                <input value="<?php echo date('Y-m-d'); ?>" type="date" class="form form-control" id="filtroFechaInicio" onchange="filtrarDespachoFechaInic()">
                            </div>
                            <div class="col-3">
                                <label for="filtroFechaFin" style="font-weight: bold;">FEC FIN:</label>
                                <input value="<?php echo date('Y-m-d'); ?>" type="date" class="form form-control" id="filtroFechaFin" onchange="filtrarDespachoFechaFin()">
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">

                                    @include('ventas.despachos.tables.tbl_list_despachos')
                                   
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
    <style>
        .letrapequeña {
            font-size: 11px;
        }

        .envio-despachado{
            background-color: rgb(220, 255, 255) !important;
        }

        .envio-embalado{
            background-color: rgb(239, 244, 213) !important;
        }

        .col-estado-pendiente {
            border: 2px solid #FF5A5F;
            background-color: #FF5A5F;
            color: #FFFFFF;
            font-weight: bold;
            border-radius: 7px; 
            padding: 0 5px; 
            text-align: center;
            display: inline-block;
        }

        .col-estado-despachado {
            border: 2px solid #014c5b; 
            background-color: #014c5b; 
            color: #FFFFFF;
            font-weight: bold;
            border-radius: 7px; 
            padding: 0 5px; 
            text-align: center;
            display: inline-block; 
        }

        .col-estado-embalado {
            border: 2px solid #566003;
            background-color: #566003; 
            color: #FFFFFF;
            font-weight: bold;
            border-radius: 7px; 
            padding: 0 5px; 
            text-align: center; 
            display: inline-block;
        }

    </style>
@endpush

@push('scripts')
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        let detallesDataTable;
        let dtDespachos =   null;

        document.addEventListener('DOMContentLoaded',()=>{
            iniciarDataTableDespachos();
            events();
            iniciarSelect2();
            detallesDataTable   =   dataTableDetalles();   
        })

        function events(){
            eventsModalBultos();
        }

        function iniciarSelect2(){
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            $('#filtroCliente').select2({
                width:'100%',
                placeholder: "Buscar Cliente...",
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
                        return "No se encontraron clientes";
                    }
                },
                ajax: {
                    url: '{{route("utilidades.getClientes")}}', 
                    dataType: 'json',
                    delay: 250, 
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1  
                        };
                    },
                    processResults: function(data,params) {
                        if(data.success){
                            params.page     =   params.page || 1;
                            const clientes  =   data.clientes;
                            return {
                                results: clientes.map(item => ({
                                    id: item.id,
                                    text: item.descripcion 
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

        function iniciarDataTableDespachos(){

            dtDespachos =   new DataTable('#dataTables-despacho',{
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'Clientes'
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
                    "url": "{{ route('ventas.despachos.getTable') }}",
                    "type": "GET",
                    "beforeSend": function() {
                        mostrarAnimacion();
                    },
                    "data": function(d) {
                        d.fecha_inicio  =   $('#filtroFechaInicio').val();
                        d.fecha_fin     =   $('#filtroFechaFin').val();
                        d.estado        =   $('#filtroEstado').val();
                        d.cliente_id    =   $('#filtroCliente').val();
                    },
                    "complete": function() {
                        ocultarAnimacion();
                    }
                },
                "columns": [{
                        data: 'documento_nro',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'cliente_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'cliente_celular',
                        className: "text-left letrapequeña"
                    },
                   
                    {
                        data: 'user_vendedor_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'almacen_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'sede_origen_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'sede_despachadora_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'user_despachador_nombre',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'fecha_envio_propuesta',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'fecha_envio',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'fecha_registro',
                        className: "text-left letrapequeña"
                    },
                    {
                        data: 'tipo_envio',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'empresa_envio_nombre',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'sede_envio_nombre',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'ubigeo',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'entrega_domicilio',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'direccion_entrega',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'destinatario_nombre',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'destinatario_nro_doc',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'tipo_pago_envio',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'monto_envio',
                        className: "text-center letrapequeña"
                    },
                  
                   
                    {
                        data: 'obs_despacho',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'estado',
                        className: "text-center letrapequeña",
                        render: function(data) {
                            let estado  =   '';
                            if(data == "PENDIENTE"){
                                estado  =   `<div class="col-estado-pendiente">${data}</div>`;  
                            }
                            if(data == "EMBALADO"){
                                estado  =   `<div class="col-estado-embalado">${data}</div>`;  
                            }
                            if(data == "DESPACHADO"){
                                estado  =   `<div class="col-estado-despachado">${data}</div>`;  
                            }
                            return estado;
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            //Ruta Detalle
                            var url_detalle = '{{ route('ventas.despachos.showDetalles', ':id') }}';
                            url_detalle     = url_detalle.replace(':id', data.id);

                           
                                //======== ACCIONES ========
                                let acciones    =   `<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button>
                                                        <ul class='dropdown-menu'>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="verDetalles(${data.documento_id})" title='Modificar' ><b><i class='fa fa-eye'></i> Detalle</a></b></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="imprimirEnvio(${data.documento_id},${data.id})" title='Imprimir' ><b><i class="fas fa-print"></i> Imprimir</a></b></li>
                                                       `;
                                                    
                                if(data.estado == "PENDIENTE"){
                                    acciones+=  `<li class='dropdown-divider'></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="embalar(${data.documento_id},${data.id})" title='Embalar' ><b><i class="fas fa-tape"></i> Embalar</a></b></li>
                                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="despachar(${data.documento_id},${data.id})" title='Despachar' ><b><i class="fas fa-people-carry"></i> Despachar</a></b></li>
                                                    </ul>
                                                    </div>`;
                                }

                                if(data.estado == "EMBALADO"){
                                    acciones+=  `<li class='dropdown-divider'></li>
                                                <li><a class='dropdown-item' href='javascript:void(0);' onclick="despachar(${data.documento_id},${data.id})" title='Despachar' ><b><i class="fas fa-people-carry"></i> Despachar</a></b></li>
                                                </ul></div>`;
                                }

                                if(data.estado == "DESPACHADO"){
                                    acciones+=  `</ul></div>`;
                                }

                            return acciones;
                        }
                    }

                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.estado === 'EMBALADO') {
                        $(row).addClass('envio-embalado');
                    }
                    if (data.estado === 'DESPACHADO') {
                        $(row).addClass('envio-despachado');
                    }
                    $('td', row).css('vertical-align', 'middle');
                },
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [],
                "columnDefs": [
                    { "searchable": false, "targets": 5 } 
                ]
                
            });
         
           
        }

    
        //Modal Eliminar
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

     
        
        async function verDetalles(documento_id){
            try {
                mostrarAnimacion();
                const res    =   await   axios.get(route('ventas.despachos.showDetalles',documento_id));

                if(res.data.success){
                    const detalles_doc_venta    =   res.data.detalles_doc_venta;
                  
                    $("#modal_detalles_doc").modal("show");
                    pintarDetallesDoc(detalles_doc_venta);
                    pintarMaestroDoc(res.data.documento)

                }else{
                    toastr.error(`${res.data.message} - ${res.data.exception}`,"ERROR");
                }
            } catch (error) {
                toasr.error(error,'ERROR EN LA PETICIÓN MOSTRAR DETALLE')
            }finally{
                ocultarAnimacion();
            }
        }

        function pintarMaestroDoc(documento){
            document.querySelector('#info_documento').textContent           =   `${documento.serie}-${documento.correlativo}`;
            document.querySelector('#info_almacen_despacho').textContent    =   `${documento.almacen_despacho}`;
            document.querySelector('#info_sede_despacho').textContent       =   `${documento.sede_despacho}`;
        }

        function pintarDetallesDoc(detalles_doc_venta){
            
            detallesDataTable.clear();

            detalles_doc_venta.forEach((ddc) => {
                detallesDataTable.row.add([
                    ddc.nombre_modelo,
                    ddc.nombre_producto,
                    ddc.nombre_color,
                    ddc.nombre_talla,
                    parseInt(ddc.cantidad),
                    parseInt(ddc.cantidad_cambiada),
                    parseInt(ddc.cantidad_sin_cambio)
                ]);
            });

            detallesDataTable.draw();

        }

        function imprimirEnvio(documento_id,despacho_id){
            document.querySelector('#documento_id').value   = documento_id;   
            document.querySelector('#despacho_id').value    = despacho_id;   

            $('#modal-bultos').modal('show'); 

        }

        //========= EMBALAR =========
        function embalar(documento_id,despacho_id){
            //======= OBTENER LOS DATOS DEL DESPACHO ======
            var miTabla = dtDespachos;

            const fila = miTabla.rows().data().filter(function (value, index) {
                return value['id'] == despacho_id;
            });
          
            let descripcion =   ``;

            if(fila.length>0){
                descripcion +=  `DESTINO: ${fila[0].ubigeo}
                                DESTINATARIO: ${fila[0].destinatario_nombre} - ${fila[0].destinatario_nro_doc}`;
            }

            //======== ALERTA =========
            Swal.fire({
                title: "Desea embalar el envío?",
                text: descripcion,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, embalar!",
                showLoaderOnConfirm: true, 
                allowOutsideClick: false, 
                preConfirm: async () => {
                    const res = await setEmbalaje(despacho_id, documento_id);
                    return res;
                }
                }).then((result) => {
                    if (result.value.success) {
                        
                        Swal.fire(result.value.message, descripcion, "success");
                    }else{
                        Swal.fire(`${result.value.message} - ${result.value.exception}`, descripcion, "error");
                    }
                });
        }


        async function setEmbalaje(despacho_id,documento_id){
            try {
                
                const res   =   await axios.post(route('ventas.despachos.setEmbalaje'),{
                    despacho_id,
                    documento_id
                })

                if(res.data.success){
                    //======= PINTANDO ESTADO EN DATATABLE ======
                    const fila          =   dtDespachos.row((idx,data) => data['id'] == despacho_id);
                    const indiceFila    =   dtDespachos.row((idx,data) => data['id'] == despacho_id).index();
                    await fila.cell(indiceFila,0).data('EMBALADO').draw();
                    //toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                }

                return res.data;

            } catch (error) {
                
            }
        }


        function despachar(documento_id,despacho_id){
            //======= OBTENER LOS DATOS DEL DESPACHO ======
            var miTabla = dtDespachos;

            const fila = miTabla.rows().data().filter(function (value, index) {
                return value['id'] == despacho_id;
            });

            let descripcion =   ``;

            if(fila.length>0){
                descripcion +=  `DESTINO: ${fila[0].ubigeo}
                                DESTINATARIO: ${fila[0].destinatario_nombre} - ${fila[0].destinatario_nro_doc}`;
            }

            //======== ALERTA =========
            Swal.fire({
                title: "Desea despachar el envío?",
                text: descripcion,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, despachar!",
                showLoaderOnConfirm: true, 
                allowOutsideClick: false, 
                preConfirm: async () => {
                    const res = await setDespacho(despacho_id, documento_id);
                    return res;
                }
                }).then((result) => {
                    if (result.value.success) {
                        
                        Swal.fire(result.value.message, descripcion, "success");
                    }else{
                        Swal.fire(`${result.value.message} - ${result.value.exception}`, descripcion, "error");
                    }
                });
         }


        async function setDespacho(despacho_id,documento_id){
            try {
                
                const res   =   await axios.post(route('ventas.despachos.setDespacho'),{
                    despacho_id,
                    documento_id
                })

                if(res.data.success){
                    dtDespachos.ajax.reload(null, false);
                    //======= PINTANDO ESTADO EN DATATABLE ======

                    // const fila          =   dtDespachos.row((idx,data) => data['id'] == despacho_id);
                    // const indiceFila    =   dtDespachos.row((idx,data) => data['id'] == despacho_id).index();
                    // await fila.cell(indiceFila,0).data('DESPACHADO').draw();
                }

                return res.data;

            } catch (error) {
                
            }
        }

      
        function filtrarDespachoFechaInic(fecha_inicio){

            const fi    =   document.querySelector('#filtroFechaInicio').value;
            const ff    =   document.querySelector('#filtroFechaFin').value;

            if((fi.toString().trim().length >0 && ff.toString().trim().length >0) & (fi > ff) ){
                document.querySelector('#filtroFechaInicio').value  =   '';
                toastr.error('FECHA INICIO DEBE SER MENOR O IGUAL A FECHA FIN','ERROR FECHAS');
                dtDespachos.ajax.reload();

                return;
            }

            dtDespachos.ajax.reload();
        }
        
        function filtrarDespachoFechaFin(fecha_fin){
            const fi    =   document.querySelector('#filtroFechaInicio').value;
            const ff    =   document.querySelector('#filtroFechaFin').value;

            if((fi.toString().trim().length >0 && ff.toString().trim().length >0) & (ff < fi) ){
                document.querySelector('#filtroFechaFin').value  =   '';
                toastr.error('FECHA FIN DEBE SER MAYOR O IGUAL A FECHA INICIO','ERROR FECHAS');
                dtDespachos.ajax.reload();
                return;
            }

            dtDespachos.ajax.reload();
        }
        

    </script>
@endpush
