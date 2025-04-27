@extends('layout')
@section('content')
@include('reportes.almacenes.producto.modal_cod_barras')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12 col-md-12">
            <h2 style="text-transform:uppercase"><b>Productos</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Panel de Control</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Informe de producto: compra y venta</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight" id="div_productos">
        <div class="row">
            <div class="col-12 text-warning">
                <span><b>Instrucciones:</b> Doble click en el registro del producto a ver informacion.</span>
            </div>
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Productos</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <label for="sede" style="font-weight: bold;">SEDE</label>
                                <select onchange="cambiarSede(this.value)" name="sede" id="sede" class="select2_sede">
                                    @foreach ($sedes as $sede)
                                        <option value="{{$sede->id}}">{{$sede->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <label for="almacen" style="font-weight: bold;">ALMACÉN</label>
                                <select onchange="cambiarAlmacen();" 
                                name="almacen" id="almacen" class="select2_almacen">
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @include('reportes.almacenes.producto.tables.tbl_pi_productos')
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Compras</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-compras table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">PROVEEDOR</th>
                                        <th class="text-center">DOCUMENTO</th>
                                        <th class="text-center">NUMERO</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">PREC. DOC</th>
                                        <th class="text-center">COSTO FLETE</th>
                                        <th class="text-center">PREC. COMPRA</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Ventas</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-ventas table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">DOCUMENTO</th>
                                        <th class="text-center">NUMERO</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">PRECIO</th>
                                        <th class="text-center">CONVERTIDO</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Notas de Crédito</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-notas-credito table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">DOC AFEC</th>
                                        <th class="text-center">NUMERO</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">PRECIO</th>
                                        <th class="text-center">MOTIVO</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Notas Ingreso</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-ingresos table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">DESTINO</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">FECHA</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Notas Salida</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-salidas table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        
                                        <th class="text-center">ORIGEN</th>
                                        <th class="text-center">DESTINO</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">FECHA</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Ingresos por traslado</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-trasladoIngreso table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ORIGEN</th>
                                        <th class="text-center">DESTINO</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">FECHA</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Salidas por traslado</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link d-none">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-trasladoSalida table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ORIGEN</th>
                                        <th class="text-center">DESTINO</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-center">USUARIO</th>
                                        <th class="text-center">FECHA</th>
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
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <!-- DataTable -->
    <link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <style>
        @media (min-width: 992px) {
            .modal-lg {
                max-width: 1200px;
            }
        }

        #table_productos div.dataTables_wrapper div.dataTables_filter {
            text-align: left !important;
        }

        #table_productos tr[data-href] {
            cursor: pointer;
        }

        #table_productos tbody .fila_lote.selected {
            /* color: #151515 !important;*/
            font-weight: 400;
            color: white !important;
            background-color: #18a689 !important;
            /* background-color: #CFCFCF !important; */
        }


        .azulito-leve {
            background-color: #d4e4f1 !important; 
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <!-- DataTable -->
    <script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

    <script>

    function cargarSelect2(){
        $(".select2_sede").select2({
            placeholder: "SELECCIONAR",
            allowClear: false,
            width: '100%'
        });
        $(".select2_almacen").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%'
        });
    }

    function cambiarAlmacen(){
        $('.dataTables-producto').DataTable().ajax.reload();
    }

    function cambiarSede(sede_id){

        if(sede_id){

            const almacenesBD       =   @json($almacenes);
            console.log(almacenesBD);
            const almacenesSede     =   almacenesBD.filter((a)=>{
                return a.sede_id    ==  sede_id;
            })      

            let selectAlmacen = $('#almacen');
            selectAlmacen.select2('destroy');
            selectAlmacen.empty();

            almacenesSede.forEach((as)=>{
                selectAlmacen.append(new Option(as.descripcion, as.id));
            })

            $("#almacen").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                width: '100%'
            });

        }

        $('.dataTables-producto').DataTable().ajax.reload();

    }

        $(document).ready(function() {

            // DataTables
            var productos = [];
            $('.dataTables-compras').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA COMPRAS'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });
            $('.dataTables-ventas').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA VENTAS'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });

            $('.dataTables-notas-credito').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA NOTAS CRÉDITO'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });

            $('.dataTables-salidas').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA SALIDAS'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });

            $('.dataTables-ingresos').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA INGRESOS'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });


            $('.dataTables-trasladoIngreso').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA TRASLADO SALIDA'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });


       
            $('.dataTables-trasladoSalida').dataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA TRASLADO SALIDA'
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
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });


            loadTable();

            cargarSelect2();
            $('#sede').val(1).trigger('change');

            $('buttons-html5').removeClass('.btn-default');

            $('.dataTables-producto tbody').on('click', 'tr', function() {
                $('.dataTables-producto').DataTable().$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            });

            //DOBLE CLICK EN LOTES
            $('.dataTables-producto').on('click', 'tbody td', function() {
                var instancia   = $('.dataTables-producto').DataTable();
                var producto    = instancia.row(this).data();
                llenarCompras(producto.producto_id,producto.color_id,producto.talla_id);
                llenarVentas(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id);
                llenarNotasCredito(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id);
                llenarSalidas(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id);
                
                llenarIngresos(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id);

                llenarTrasladoIngreso(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id)

                llenarTrasladoSalida(producto.almacen_id,producto.producto_id,producto.color_id,producto.talla_id)

                console.log(`${producto.producto_id}-${producto.color_id}-${producto.talla_id}`);
            });

            document.addEventListener('click',async (e)=>{
                if(e.target.classList.contains('btn-ver-cod-barras')){
                    
                    mostrarAnimacion();
                    toastr.clear();
                    
                    const producto_id   =   e.target.getAttribute('data-producto');
                    const color_id      =   e.target.getAttribute('data-color');
                    const talla_id      =   e.target.getAttribute('data-talla');

                    const   res_generarBarCode  = await generarBarCode(producto_id,color_id,talla_id);
                    clearData();
                    if(res_generarBarCode.success){
                        const cod           =   res_generarBarCode.producto.codigo_barras;
                        const img_cod       =   res_generarBarCode.producto.ruta_cod_barras;

                        document.querySelector('#img_cod_barras').src       = `#`;  
                        document.querySelector('#p_cod_barras').textContent = 'NO TIENE CÓDIGO DE BARRAS';  

                        if(img_cod){
                            const partes    = img_cod.split("public/");
                            const subcadena = partes[1];
                            const base_path_1   =   @json(asset(`storage/`));
                            document.querySelector('#img_cod_barras').src       = base_path_1+'storage/'+subcadena;  
                            document.querySelector('#p_cod_barras').textContent = cod;   

                            //========= ESTABLECIENDO RUTA =======
                            let rutaGenerarAdhesivos = "{{ route('reporte.producto.getAdhesivos', ['producto_id' => 'valor_producto_id', 'color_id' => 'valor_color_id', 'talla_id' => 'valor_talla_id']) }}";
                            rutaGenerarAdhesivos = rutaGenerarAdhesivos.replace('valor_producto_id', producto_id);
                            rutaGenerarAdhesivos = rutaGenerarAdhesivos.replace('valor_color_id', color_id);
                            rutaGenerarAdhesivos = rutaGenerarAdhesivos.replace('valor_talla_id', talla_id);

                            const enlaceGenerarAdhesivos = document.getElementById("ahesivos_item");
                            enlaceGenerarAdhesivos.setAttribute("href", rutaGenerarAdhesivos);
                            toastr.info(res_generarBarCode.message,'OPERACIÓN COMPLETADA');

                            setData(res_generarBarCode);
                            $('#modal_cod_barras').modal('show');
                        }

                    }else{
                        toastr.error(res_generarBarCode.message,res_generarBarCode.exception,{timeOut:0});
                    }
                    ocultarAnimacion();

                }
            })

        });

        async function generarBarCode(producto_id,color_id,talla_id){    
            try {
                
                const res       =   await axios.post(route('reporte.producto.obtenerBarCode'),{
                    producto_id,color_id,talla_id
                });
                const data  =   res.data;
                return data;
            } catch (error) {
                toastr.error(error,'ERROR EN LA SOLICITUD AL EMITIR ADHESIVOS');
                return {'success':false};
            }
        }


        function llenarCompras(producto_id,color_id,talla_id) {
            $('.dataTables-compras').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarCompras', ['producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url = url.replace(":producto_id", producto_id);
            url = url.replace(":color_id", color_id);
            url = url.replace(":talla_id", talla_id);
            
            $('.dataTables-compras').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA COMPRAS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'proveedor',
                        name: 'proveedor',
                        className: "letrapequeña"
                    },
                    {
                        data: 'documento',
                        name: 'documento',
                        className: "letrapequeña"
                    },
                    {
                        data: 'numero',
                        name: 'numero',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha_emision',
                        name: 'fecha_emision',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'precio_doc',
                        name: 'precio_soles',
                        className: "letrapequeña"
                    },
                    {
                        data: 'costo_flete',
                        name: 'costo_flete',
                        className: "letrapequeña"
                    },
                    {
                        data: 'precio_compra',
                        name: 'precio_compra',
                        className: "letrapequeña"
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],


            });
        }

        function llenarVentas(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-ventas').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarVentas', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url = url.replace(":almacen_id", almacen_id);
            url = url.replace(":producto_id", producto_id);
            url = url.replace(":color_id", color_id);
            url = url.replace(":talla_id", talla_id);
            
            $('.dataTables-ventas').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA VENTAS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'cliente',
                        name: 'cliente',
                        className: "letrapequeña"
                    },
                    {
                        data: 'registrador_nombre',
                        name: 'registrador_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'documento',
                        name: 'documento',
                        className: "letrapequeña"
                    },
                    {
                        data: 'serie',
                        name: 'serie',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'precio_unitario_nuevo',
                        name: 'precio_unitario_nuevo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'convert_en_serie',
                        name: 'convert_en_serie',
                        className: "letrapequeña"
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [3, "desc"]
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.convert_en_serie) {
                        $(row).addClass('azulito-leve');
                    }
                }


            });
        }


        function llenarNotasCredito(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-notas-credito').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarNotasCredito', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url = url.replace(":almacen_id", almacen_id);
            url = url.replace(":producto_id", producto_id);
            url = url.replace(":color_id", color_id);
            url = url.replace(":talla_id", talla_id);
            
            $('.dataTables-notas-credito').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA NOTAS CRÉDITO'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'cliente',
                        name: 'cliente',
                        className: "letrapequeña"
                    },
                    {
                        data: 'usuario',
                        name: 'usuario',
                        className: "letrapequeña"
                    },
                    {
                        data: 'doc_afec',
                        name: 'doc_afec',
                        className: "letrapequeña"
                    },
                    {
                        data: 'numero',
                        name: 'numero',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha_emision',
                        name: 'fecha_emision',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'precio_unitario_nuevo',
                        name: 'precio_unitario_nuevo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'motivo',
                        name: 'motivo',
                        className: "letrapequeña"
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [3, "desc"]
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.convertir !== '-') {
                        $(row).addClass('azulito-leve');
                    }
                }


            });
        }

        function llenarSalidas(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-salidas').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarSalidas', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url = url.replace(":almacen_id", almacen_id);
            url = url.replace(":producto_id", producto_id);
            url = url.replace(":color_id", color_id);
            url = url.replace(":talla_id", talla_id);
            $('.dataTables-salidas').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA SALIDAS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'codigo',
                        name: 'codigo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_origen_nombre',
                        name: 'almacen_origen_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_destino_nombre',
                        name: 'destino',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'registrador_nombre',
                        name: 'registrador_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: "letrapequeña"
                    },
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });
        }

        function llenarTrasladoSalida(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-trasladoSalida').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarTrasladoSalida', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url     = url.replace(":almacen_id", almacen_id);
            url     = url.replace(":producto_id", producto_id);
            url     = url.replace(":color_id", color_id);
            url     = url.replace(":talla_id", talla_id);
            $('.dataTables-trasladoSalida').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA SALIDAS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'codigo',
                        name: 'codigo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_origen_nombre',
                        name: 'almacen_origen_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_destino_nombre',
                        name: 'destino',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'registrador_nombre',
                        name: 'registrador_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: "letrapequeña"
                    },
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],


            });
        }

        function llenarTrasladoIngreso(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-trasladoIngreso').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarTrasladoIngreso', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url     = url.replace(":almacen_id", almacen_id);
            url     = url.replace(":producto_id", producto_id);
            url     = url.replace(":color_id", color_id);
            url     = url.replace(":talla_id", talla_id);
            $('.dataTables-trasladoIngreso').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA SALIDAS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'codigo',
                        name: 'codigo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_origen_nombre',
                        name: 'almacen_origen_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'almacen_destino_nombre',
                        name: 'destino',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'registrador_nombre',
                        name: 'registrador_nombre',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: "letrapequeña"
                    },
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],


            });
        }

        function llenarIngresos(almacen_id,producto_id,color_id,talla_id) {
            $('.dataTables-ingresos').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarIngresos', ['almacen_id' => ':almacen_id','producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
            url = url.replace(":almacen_id", almacen_id);
            url = url.replace(":producto_id", producto_id);
            url = url.replace(":color_id", color_id);
            url = url.replace(":talla_id", talla_id);

            $('.dataTables-ingresos').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA INGRESOS'
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
                "ajax": url,
                "columns": [

                    {
                        data: 'codigo',
                        name: 'codigo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'destino',
                        name: 'destino',
                        className: "letrapequeña"
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: "letrapequeña"
                    },
                    {
                        data: 'usuario',
                        name: 'usuario',
                        className: "letrapequeña"
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        className: "letrapequeña"
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [4, "desc"]
                ],


            });
        }

        function loadTable() {
            $('.dataTables-producto').DataTable({
               "processing": true,
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": 
                [
                    {
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            titleAttr: 'Excel',
                            title: 'CONSULTA PRODUCTOS',
                            action: function(e, dt, node, config) {
                                
                            },
                            init: function(api, node, config) {
                                $(node).on('click', excelProductosPI);
                            }
                    }
                ],
                "bPaginate": true,
                "serverSide": true,
                "processing": true,
                "bLengthChange": true,
                "bFilter": true,
                "order": [
                    [0, "asc"]
                ],
                "ordering": true,
                "bInfo": true,
                'bAutoWidth': false,
                "ajax": {
                    "url": "{{ route('reporte.producto.getProductos') }}",
                    "type": "GET",  
                    "data": function(d) {
                        d.sede_id       = $('#sede').val(); 
                        d.almacen_id    = $('#almacen').val(); 
                    }
                },
                "columns": [
                    {
                        data: 'almacen_nombre',
                        className: "text-left",
                        name: "a.descripcion"
                    },
                    {
                        data: 'producto_nombre',
                        className: "text-left",
                        name: "p.nombre"
                    },
                    {
                        data: 'color_nombre',
                        className: "text-left",
                        name: "co.descripcion"
                    },
                    {
                        data: 'talla_nombre',
                        className: "text-left",
                        name: "t.descripcion"
                    },
                    {
                        data: 'modelo_nombre',
                        className: "text-left",
                        name: "m.descripcion"
                    },
                    {
                        data: 'categoria_nombre',
                        className: "text-left",
                        name: "ca.descripcion"
                    },
                    {
                        data: 'stock',
                        className: "text-center",
                        name: "stock",
                        searchable: false 
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function (data, type, row) {
                            return `<button class="btn btn-primary btn-ver-cod-barras"  data-producto="${row.producto_id}" 
                            data-color="${row.color_id}" data-talla="${row.talla_id}">GENERAR</button>`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                createdRow: function(row, data, dataIndex, cells) {
                    $(row).addClass('fila_lote');
                    $(row).attr('data-href', "");
                },
            });
            return false;
        }

        $(".dataTables-ingresos").on('click', '.editCosto', function() {
            var data = $(".dataTables-ingresos").dataTable().fnGetData($(this).closest('tr'));
            $('#modal_costo_update .pago-title').html(data.nombre)
            $('#modal_costo_update .pago-subtitle').html(data.numero);
            $('#modal_costo_update #producto').val(data.nombre);
            $('#modal_costo_update #detalle_id').val(data.id);
            $('#modal_costo_update #nota_ingreso_id').val(data.nota_ingreso_id);
            $('#modal_costo_update #moneda').val(data.moneda);
            $('#modal_costo_update #costo').val(data.costo);
            $('#modal_costo_update #total').val(data.total);
            $('#modal_costo_update').modal('show');
        });

        function excelProductosPI(){
            let sedeId      = document.querySelector('#sede').value;
            let almacenId   = document.querySelector('#almacen').value;

            let url = '{{ route('reporte.producto.excelProductos') }}' + '?sedeId=' + sedeId + '&almacenId=' + almacenId;
            window.open(url, '_blank');
        }

      
    </script>

@endpush
