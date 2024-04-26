<div class="modal inmodal" id="modal_pedido_detalles" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
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
                                @include('ventas.pedidos.tables-historial.table-pedido-detalles')
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
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>

    </div>
</div>