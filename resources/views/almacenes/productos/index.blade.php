@extends('layout')
@section('content')
@section('almacenes-active', 'active')
@section('producto-active', 'active')
@include('almacenes.productos.modals.mdl_producto_stocks')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Productos Terminados</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Productos</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_producto" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
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
                <div class="ibox-content" id="div_productos">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-1">
                            <div class="form-group">
                                <a href="{{ route('almacenes.producto.getExcel') }}"
                                    class="btn btn-primary btn-block"><i class="fa fa-file-excel-o"></i></a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('almacenes.productos.tables.tbl_list_productos')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop


@push('scripts')
<script>
    let dtProductos = null;
    let table = null;
    const bodyTableShowStocks = document.querySelector('#tableShowStocks tbody');
    let dtProductoColores = null;
    let dtProductoTallas = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDataTableProductos();
        cargarSelect2();
        events();

        dtProductoColores = iniciarDataTable('tbl_producto_colores');
        dtProductoTallas = iniciarDataTable('tbl_producto_tallas');
    })

    function events() {
        $('#btn_añadir_producto').on('click', añadirProducto);
        eventsMdlStocks();
    }

    function cargarSelect2() {
        $(".select2_almacen").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
            appendTo: "#mdl_producto_stocks .modal-body"
        });
    }

    function iniciarDataTableProductos() {
        // DataTables
        dtProductos = new DataTable('.dataTables-producto', {
            "responsive": true,
            "serverSide": true,
            "processing": true,
            "order": [],
            "ajax": "{{ route('almacenes.producto.getTable') }}",
            "columns": [{
                    data: 'codigo',
                    className: "text-left",
                    name: "codigo"
                },
                {
                    data: 'nombre',
                    className: "text-left",
                    name: "nombre"
                },
                {
                    data: 'modelo',
                    className: "text-left",
                    name: "modelo"
                },
                {
                    data: 'marca',
                    className: "text-left",
                    name: "marca"
                },
                {
                    data: 'categoria',
                    className: "text-left",
                    name: "categoria"
                },
                {
                    data: 'id',
                    defaultContent: "",
                    searchable: false,
                    className: "text-center",
                    render: function(data, type, row) {

                        const btnStock = `<a onclick="openMdlStocks(${data});"     data-id=${data} class='btn btn-primary' href='javascript:void(0);' title='STOCKS'>
                                                <i class='fa fa-eye ver-stocks-producto'></i> Ver
                                            </a>`;

                        return btnStock;
                    }
                },
                {
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle
                        var url_detalle = '{{ route('almacenes.producto.show', ':id') }}';
                        url_detalle = url_detalle.replace(':id', data.id);

                        //Ruta Modificar
                        var url_editar = '{{ route('almacenes.producto.edit', ':id') }}';
                        url_editar = url_editar.replace(':id', data.id);

                        return `
                            <div class="btn-group" style="text-transform:capitalize;">
                                <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="${url_detalle}" title="Detalle">
                                            <i class="fa fa-eye"></i> Ver
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item modificarDetalle" href="${url_editar}" title="Modificar">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="eliminar(${data.id})" title="Eliminar">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        `;

                    }
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
    }


    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

    //Modal Eliminar
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    });

    // Funciones de Eventos
    function añadirProducto() {
        window.location = "{{ route('almacenes.producto.create') }}";
    }

    function eliminar(id) {

        const fila = getRowById(dtProductos, id);
        const nombre = fila?.nombre || 'Sin nombre';
        const codigo = fila?.codigo || '—';
        const modelo = fila?.modelo || '—';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        Swal.fire({
            title: 'Desea eliminar el producto?',
            html: `
                <div style="text-align: center; font-size: 15px;">
                    <p><i class="fa fa-box text-primary"></i> <strong>Producto:</strong> ${nombre}</p>
                    <p><i class="fa fa-barcode text-secondary"></i> <strong>Código:</strong> ${codigo}</p>
                    <p><i class="fa fa-cube text-info"></i> <strong>Modelo:</strong> ${modelo}</p>
                    <hr>
                    <p style="color: #d9534f; font-weight: bold;">
                        <i class="fa fa-exclamation-triangle"></i> Se eliminará también el stock asociado a este producto.
                    </p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Eliminando producto...',
                    html: `
                        <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                            <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                            <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                        </div>
                    `,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });


                try {

                    const res = await axios.delete(route('almacenes.producto.destroy', id));
                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        dtProductos.ajax.reload();
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR PRODUCTO');
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    }

    $(".btn-modal-file").on('click', function() {
        $("#modal_file").modal("show");
    });

    function resetearTabla() {
        bodyTableShowStocks.innerHTML = '';
    }

    function cargarDataTables() {
        table = new DataTable('#tableShowStocks', {
            language: {
                processing: "Traitement en cours...",
                search: "BUSCAR: ",
                lengthMenu: "MOSTRAR _MENU_ ELEMENTOS",
                info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ ELEMENTOS",
                infoEmpty: "MOSTRANDO 0 ELEMENTOS",
                infoFiltered: "(FILTRADO de _MAX_ PRODUCTOS)",
                infoPostFix: "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable: "NO HAY PRODUCTOS DISPONIBLES",
                paginate: {
                    first: "PRIMERO",
                    previous: "ANTERIOR",
                    next: "SIGUIENTE",
                    last: "ÚLTIMO"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });

    }
</script>
@endpush
