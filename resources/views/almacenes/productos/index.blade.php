@extends('layout')

@section('almacenes-active', 'active')
@section('producto-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Productos')
@section('hero-title', 'Lista de Productos')
@section('hero-subtitle', 'Productos')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="añadirProducto()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@push('styles')
    <link href="{{ mix('css/filepond.css') }}" rel="stylesheet">
@endpush

@section('content')
    @include('almacenes.productos.modals.mdl_producto_stocks')
    @include('almacenes.productos.modals.mdl_imagenes')
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
                            <div class="col-12 text-right mb-2">
                                <a href="{{ route('almacenes.producto.getExcel') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel mr-1"></i> EXCEL
                                </a>
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
@endsection

@push('scripts')
    <script src="{{ mix('js/filepond.js') }}"></script>
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
                        name: "nombre",
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            const badge = row.total_imagenes > 0
                                ? `<span class="badge badge-success ml-1"
                                         title="${row.total_imagenes} imagen(es) subidas"
                                         style="font-size:0.65rem;">
                                       <i class="fas fa-images mr-1"></i>${row.total_imagenes}
                                   </span>`
                                : '';
                            return `<span>${data}</span>${badge}`;
                        }
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
                        data: 'colores_data',
                        defaultContent: "",
                        searchable: false,
                        orderable: false,
                        className: "text-center",
                        render: function(data, type, row) {
                            if (type !== 'display') return data || '';
                            if (!data) return '<span class="text-muted" style="font-size:0.75rem;">—</span>';

                            const PALETTE = [
                                '#e74c3c','#e67e22','#f39c12','#27ae60','#16a085',
                                '#2980b9','#8e44ad','#2c3e50','#c0392b','#1abc9c',
                                '#d35400','#7f8c8d','#6c5ce7','#00b894','#fd79a8',
                            ];
                            const MAX = 6;
                            const colores = data.split(';;').map(function(nombre) {
                                return { nombre: nombre.trim() };
                            });

                            let html = '<div style="display:flex;flex-wrap:wrap;gap:3px;justify-content:center;">';
                            colores.slice(0, MAX).forEach(function(c, i) {
                                const bg = PALETTE[i % PALETTE.length];
                                html += '<span style="'
                                    + 'background:' + bg + ';color:#fff;'
                                    + 'font-size:0.65rem;padding:2px 6px;border-radius:10px;'
                                    + 'white-space:nowrap;font-weight:600;letter-spacing:.3px;'
                                    + '">' + c.nombre + '</span>';
                            });
                            if (colores.length > MAX) {
                                html += '<span style="'
                                    + 'background:#6c757d;color:#fff;'
                                    + 'font-size:0.65rem;padding:2px 6px;border-radius:10px;font-weight:600;'
                                    + '">+' + (colores.length - MAX) + ' más</span>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'id',
                        defaultContent: "",
                        searchable: false,
                        orderable: false,
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
                        orderable: false,
                        className: "text-center",
                        render: function(data) {
                            //Ruta Detalle
                            var url_detalle = '{{ route('almacenes.producto.show', ':id') }}';
                            url_detalle = url_detalle.replace(':id', data.id);

                            //Ruta Modificar
                            var url_editar = '{{ route('almacenes.producto.edit', ':id') }}';
                            url_editar = url_editar.replace(':id', data.id);

                            const nombre = (data.nombre || '').substring(0, 40);

                            return `
                            <div class="btn-group" style="text-transform:capitalize;">
                                <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a class="dropdown-item" href="${url_detalle}" title="Detalle">
                                            <i class="fa fa-eye text-info mr-1"></i> Ver
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item modificarDetalle" href="${url_editar}" title="Modificar">
                                            <i class="fa fa-edit text-warning mr-1"></i> Editar
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="eliminar(${data.id})" title="Eliminar">
                                            <i class="fa fa-trash text-danger mr-1"></i> Eliminar
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
