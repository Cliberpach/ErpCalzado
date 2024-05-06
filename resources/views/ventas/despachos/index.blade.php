@extends('layout') @section('content')
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
        <div class="col-lg-2 col-md-2">
            <button id="btn_añadir_cliente" class="btn btn-block btn-w-m btn-primary m-t-md">
                <i class="fa fa-plus-square"></i> Añadir nuevo
            </button>
            <button id="btn_file_cliente" class="btn btn-block btn-w-m btn-primary m-t-md">
                <i class="fa fa-file-excel-o"></i> Importar Excel
            </button>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-cliente table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    {{-- <tr>

                                        <th colspan="4" class="text-center">CLIENTES</th>
                                        <th colspan="4" class="text-center">UBICACIONES</th>
                                        <th colspan="1" class="text-center"></th>

                                    </tr> --}}
                                    <tr>
                                        <th class="text-center">DOC</th>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">FEC PROPUESTA</th>
                                        <th class="text-center">FEC ENVIO</th>
                                        <th class="text-center">FEC REGISTRO</th>
                                        <th class="text-center">TIPO ENVIO</th>
                                        <th class="text-center">EMPRESA</th>
                                        <th class="text-center">SEDE</th>
                                        <th class="text-center">UBIGEO</th>
                                        <th class="text-center">DESTINATARIO</th>
                                        <th class="text-center">TIPO PAGO</th>
                                        <th class="text-center">MONTO ENVÍO</th>
                                        <th class="text-center">ENVÍO DOMICILIO</th>
                                        <th class="text-center">DIRECCIÓN DOMICILIO</th>
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
    <!-- DataTable -->
    <link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <style>
        .letrapequeña {
            font-size: 11px;
        }

    </style>
@endpush

@push('scripts')
    <!-- DataTable -->
    <script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        let detallesDataTable;

        $(document).ready(function() {
            
            // DataTables
            $('.dataTables-cliente').DataTable({
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
                "ajax": "{{ route('ventas.despachos.getTable') }}",
                "columns": [{
                        data: 'documento_nro',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'cliente_nombre',
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
                        data: 'destinatario_nombre',
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
                        data: 'entrega_domicilio',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'direccion_entrega',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'estado',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            //Ruta Detalle
                            var url_detalle = '{{ route('ventas.despachos.showDetalles', ':id') }}';
                            url_detalle = url_detalle.replace(':id', data.id);

                            // //Ruta Modificar
                            // var url_editar = '{{ route('ventas.cliente.edit', ':id') }}';
                            // url_editar = url_editar.replace(':id', data.id);

                            // //Ruta Tiendas
                            // var url_tienda = '{{ route('clientes.tienda.index', ':id') }}';
                            // url_tienda = url_tienda.replace(':id', data.id);

                           /* return "<div class='btn-group'>" +
                                "<a class='btn btn-primary btn-sm' href='" + url_tienda +
                                "' title='Tiendas'><i class='fa fa-shopping-cart'></i></a>" +
                                "<a class='btn btn-success btn-sm' href='" + url_detalle +
                                "' title='Detalle'><i class='fa fa-eye'></i></a>" +
                                "<a class='btn btn-warning btn-sm modificarDetalle' href='" +
                                url_editar + "' title='Modificar'><i class='fa fa-edit'></i></a>" +
                                "<a class='btn btn-danger btn-sm' href='#' onclick='eliminar(" +
                                data.id + ")' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                                "</div>";*/

                            return `<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button>
                                        <ul class='dropdown-menu'>
                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="verDetalles(${data.documento_id})" title='Modificar' ><b><i class='fa fa-eye'></i>Detalle</a></b></li>
                                            <li class='dropdown-divider'></li>
                                            <li><a class='dropdown-item' href='javascript:void(0);' onclick="imprimirEnvio(${data.documento_id})" title='Imprimir' ><b><i class='fa fa-eye'></i>Imprimir</a></b></li>
                                        </ul>
                                    </div>`;
                        }
                    }

                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [],
            });

            detallesDataTable   =   dataTableDetalles();
        });

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

        //Modal Eliminar
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        // Funciones de Eventos
        function añadirCliente() {
            window.location = "{{ route('ventas.cliente.create') }}";
        }
        
        async function verDetalles(documento_id){
            try {
                const res    =   await   axios.get(route('ventas.despachos.showDetalles',documento_id));

                if(res.data.success){
                    const detalles_doc_venta    =   res.data.detalles_doc_venta;
                    console.log(detalles_doc_venta);
                    $("#modal_detalles_doc").modal("show");
                    pintarDetallesDoc(detalles_doc_venta);
                }else{
                    toastr.error(`${res.data.message} - ${res.data.exception}`,"ERROR");
                }
            } catch (error) {
                
            }
        }

        function pintarDetallesDoc(detalles_doc_venta){
            detallesDataTable.clear();

            detalles_doc_venta.forEach((ddc) => {
                detallesDataTable.row.add([
                    ddc.nombre_producto,
                    ddc.nombre_color,
                    ddc.nombre_talla,
                    ddc.cantidad
                ]);
            });

            detallesDataTable.draw();
        }

        function imprimirEnvio(){
            $('#modal-bultos').modal('show'); 
        }

        function eliminar(id) {
            Swal.fire({
                title: 'Opción Eliminar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    //Ruta Eliminar
                    var url_eliminar = '{{ route('ventas.cliente.destroy', ':id') }}';
                    url_eliminar = url_eliminar.replace(':id', id);
                    $(location).attr('href', url_eliminar);

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        }
        $("#btn_file_cliente").on('click', function () {
            $("#modal_file").modal('show');
        });

    </script>
@endpush
