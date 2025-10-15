<div class="modal inmodal" id="modal_historial_atenciones" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-concierge-bell modal-icon"></i>
                <h4 class="modal-title">HISTORIAL ATENCIONES</h4>
                <p class="font-bold">PEDIDO # <span class="pedido_id_span"></span></p>
            </div>
            <div class="modal-body content_cliente">
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>ATENCIONES</h5>
                                @include('pedidos.pedido.tables-historial.table-atenciones')

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5>DETALLES DEL DOCUMENTO</h5>
                                @include('pedidos.pedido.tables-historial.table-atenciones-detalles')
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

@push('scripts')
    <script>
        function eventsModalAtenciones() {

            document.addEventListener('click', (e) => {
                const filaCercana = e.target.closest('tr');
                if (filaCercana && filaCercana.classList.contains('rowAtencion')) {
                    const pedido_id = filaCercana.getAttribute('data-pedido-id');
                    const documento_id = filaCercana.getAttribute('data-documento-id');

                    getAtencionDetalles(pedido_id, documento_id);
                }
            })

            $('#modal_historial_atenciones').on('show.bs.modal', async function(event) {


                var button = $(event.relatedTarget)
                const pedido_id = button.data('pedido-id');


                document.querySelector('.pedido_id_span').textContent = pedido_id;

                //===== OBTENIENDO ATENCIONES DEL PEDIDO =======
                try {
                    const res = await axios.get(route('pedidos.pedido.getAtenciones', {
                        pedido_id
                    }));
                    console.log(res);
                    if (res.data.success) {
                        pintarTablePedidoAtenciones(res.data.pedido_atenciones);
                        toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    }else{
                        toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    toastr.error(error,'ERROR EN LA PETICIÓN OBTENER ATENCIONES');
                }

            })

        }


        async function getAtencionDetalles(pedido_id, documento_id) {
            //===== OBTENIENDO DETALLES DEL PEDIDO =======
            try {
                const res = await axios.get(route('pedidos.pedido.getAtencionDetalles', {
                    pedido_id,
                    documento_id
                }));
                console.log(res);
                const type = res.data.type;
                if (type == 'success') {
                    const atencion_detalles = res.data.atencion_detalles;
                    pintarTableAtencionesDetalles(atencion_detalles);
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN MOSTRAR DETALLES DE LA ATENCIÓN');
            }
        }

        function pintarTableAtencionesDetalles(atencion_detalles) {
            const bodyPedidoDetalles = document.querySelector('#table-atenciones-detalles tbody');

            bodyPedidoDetalles.innerHTML = '';
            let body = ``;

            atencion_detalles.forEach((ad) => {
                body += `<tr><th scope="row">${ad.producto_nombre}</th>
            <td scope="row">${ad.color_nombre}</td>
            <td scope="row">${ad.talla_nombre}</td>
            <td scope="row">${ad.cantidad}</td>
            </tr>`;
            })

            bodyPedidoDetalles.innerHTML = body;
        }

        function pintarTablePedidoAtenciones(pedido_atenciones) {
            const bodyPedidoDetalles = document.querySelector('#table-atenciones-detalles tbody');
            bodyPedidoDetalles.innerHTML = '';

            const bodyPedidoAtenciones = document.querySelector('#table-pedido-atenciones tbody');

            if (atenciones_data_table) {
                atenciones_data_table.destroy();
            }

            bodyPedidoAtenciones.innerHTML = '';
            let body = ``;

            pedido_atenciones.forEach((pa) => {
                body += `<tr class="rowAtencion" data-pedido-id="${pa.pedido_id}" data-documento-id=${pa.documento_id}>
                <th scope="row">${pa.documento_serie}-${pa.documento_correlativo}</th>
                <td scope="row">${pa.fecha_atencion}</td>
                <td scope="row">${pa.documento_usuario}</td>
                <td scope="row">${pa.documento_monto_envio}</td>
                <td scope="row">${pa.documento_monto_embalaje}</td>
                <td scope="row">${pa.documento_total_pagar}</td>
            </tr>`;
            })

            bodyPedidoAtenciones.innerHTML = body;

            atenciones_data_table = new DataTable('#table-pedido-atenciones', {
                "order": [
                    [0, 'desc']
                ],
                buttons: [{
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
@endpush
