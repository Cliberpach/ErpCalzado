@extends('layout') @section('content')


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
                        <div class="table-responsive">
                            <table class="table dataTables-producto table-striped table-bordered table-hover"
                                style="text-transform:uppercase" id="table_productos">
                                <thead>
                                    <tr>
                                        <th class="text-center">CÓDIGO</th>
                                        <th class="text-center">PRODUCTO</th>
                                        <th class="text-center">COLOR</th>
                                        <th class="text-center">TALLA</th>
                                        <th class="text-center">MODELO</th>
                                        <th class="text-center">MARCA</th>
                                        <th class="text-center">STOCK</th>
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
                        <h5>Ingresos</h5>
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
                        <h5>Salidas</h5>
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
                                        <th class="text-center">MEDIDA</th>
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
    @include('reportes.almacenes.producto.modalEditCosto')
@stop
@push('styles')
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
    <!-- DataTable -->
    <script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
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

            loadTable();

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
                llenarVentas(producto.producto_id,producto.color_id,producto.talla_id);
                llenarSalidas(producto.producto_id,producto.color_id,producto.talla_id);
                
                llenarIngresos(producto.producto_id,producto.color_id,producto.talla_id);
                console.log(`${producto.producto_id}-${producto.color_id}-${producto.talla_id}`);
            });

        });

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

        function llenarVentas(producto_id,color_id,talla_id) {
            $('.dataTables-ventas').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarVentas', ['producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
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
                        data: 'usuario',
                        name: 'usuario',
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
                        data: 'precio_unitario_nuevo',
                        name: 'precio_unitario_nuevo',
                        className: "letrapequeña"
                    },
                    {
                        data: 'convertir',
                        name: 'convertir',
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

        function llenarSalidas(producto_id,color_id,talla_id) {
            $('.dataTables-salidas').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarSalidas', ['producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
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
                        data: 'origen',
                        name: 'origen',
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
                        data: 'medida',
                        name: 'medida',
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

        function llenarIngresos(producto_id,color_id,talla_id) {
            $('.dataTables-ingresos').dataTable().fnDestroy();
            let url = '{{ route('reporte.producto.llenarIngresos', ['producto_id' => ':producto_id', 'color_id' => ':color_id', 'talla_id' => ':talla_id']) }}';
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
                        data: 'origen',
                        name: 'origen',
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
                    [0, "desc"]
                ],


            });
        }

        function loadTable() {
            $('.dataTables-producto').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'CONSULTA PRODUCTOS'
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
                "ajax": "{{ route('reporte.producto.getProductos') }}",
                "columns": [{
                        data: 'producto_codigo',
                        className: "text-left",
                        name: "codigo"
                    },
                    {
                        data: 'producto_nombre',
                        className: "text-left",
                        name: "nombre"
                    },
                    {
                        data: 'color_nombre',
                        className: "text-left",
                        name: "colores.descripcion"
                    },
                    {
                        data: 'talla_nombre',
                        className: "text-left",
                        name: "tallas.descripcion"
                    },
                    {
                        data: 'modelo_nombre',
                        className: "text-left",
                        name: "modelos.descripcion"
                    },
                    {
                        data: 'categoria_nombre',
                        className: "text-left",
                        name: "categorias.descripcion"
                    },
                    {
                        data: 'stock',
                        className: "text-center",
                        name: "producto_color_tallas.stock"
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

        $(".dataTables-ingresos").on('dblclick', '.editCosto', function() {
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
    </script>
@endpush
