<div class="modal inmodal" id="modal_pedido_detalles" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width:900px;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-info-circle modal-icon"></i>
                <h4 class="modal-title">DETALLES</h4>
                <p class="font-bold">PEDIDO # <span class="pedido_id_span_pd"></span></p>
            </div>
            <div class="modal-body content_cliente">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('pedidos.pedido.tables-historial.table-pedido-detalles')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    //====== DATATABLE PEDIDO DETALLES ========
    let detalles_data_table = null;

    function eventsMdlPedidoShow() {

        $('#modal_pedido_detalles').on('show.bs.modal', async function(event) {

            var button = $(event.relatedTarget)
            const pedidoId = button.data('pedido-id');

            document.querySelector('.pedido_id_span_pd').textContent = pedidoId;

            //===== OBTENIENDO DETALLES DEL PEDIDO =======
            await getPedidoShow(pedidoId);

        })

    }

    async function getPedidoShow(pedidoId) {
        try {

            mostrarAnimacion();
            const res = await axios.get(route('pedidos.pedido.getPedidoDetalles', {
                pedido_id:pedidoId
            }));

            if (res.data.success) {
                const pedido_detalles = res.data.pedido_detalles;
                pintarTablePedidoDetalles(pedido_detalles);
            }

            if (!res.data.success) {
                const message = res.data.message;
                const exception = res.data.exception;

                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN VER DETALLE DEL PEDIDO');
        }finally{
            ocultarAnimacion();
        }
    }

    function pintarTablePedidoDetalles(pedido_detalles) {
        const bodyPedidoDetalles = document.querySelector('#table-pedido-detalles tbody');

        if (detalles_data_table) {
            detalles_data_table.destroy();
        }

        bodyPedidoDetalles.innerHTML = '';
        let body = ``;

        pedido_detalles.forEach((pd) => {
            body += `<tr>
                <th scope="row">${pd.producto_nombre}</th>
                <td scope="row">${pd.color_nombre}</td>
                <td scope="row">${pd.talla_nombre}</td>
                <td scope="row">${pd.cantidad}</td>
                <td scope="row">${pd.cantidad_atendida}</td>
                <td scope="row">${pd.cantidad_pendiente}</td>
                <td scope="row">${pd.cantidad_enviada}</td>
                <td scope="row">${pd.cantidad_devuelta}</td>
                <td scope="row">${pd.cantidad_fabricacion}</td>
            </tr>`;
        })

        bodyPedidoDetalles.innerHTML = body;

        detalles_data_table = new DataTable('#table-pedido-detalles', {
            "order": [
                [0, 'desc']
            ],
            buttons: [{
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
                processing: "Procesando datos...",
                search: "BUSCAR: ",
                lengthMenu: "MOSTRAR _MENU_ ITEMS",
                info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                infoEmpty: "MOSTRANDO 0 ITEMS",
                infoFiltered: "(FILTRADO de _MAX_ ITEMS)",
                infoPostFix: "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable: "NO HAY ITEMS DISPONIBLES",
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
