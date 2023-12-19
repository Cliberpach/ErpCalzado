<div class="modal inmodal" id="modal_costo_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title pago-title"></h4>
                <small class="font-bold pago-subtitle"></small>
            </div>
            <div class="modal-body">
                <form action="{{ route('reporte.producto.updateIngreso') }}" id="update_costo" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="col-form-label required">Producto</label>
                                <input type="text" class="form-control" id="producto" name="producto" readonly>
                                <input type="hidden" class="form-control" id="detalle_id" name="id" readonly>
                                <input type="hidden" class="form-control" id="nota_ingreso_id" name="nota_ingreso_id" readonly>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Moneda</label>
                                <input type="text" value="0.00" class="form-control" id="moneda" name="moneda" readonly>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Total</label>
                                <input type="text" value="0.00" class="form-control" id="total" name="total" onkeypress="return filterFloat(event, this);" disabled>
                            </div>
                            <div class="form-group">
                                <label  class="col-form-label required">Costo</label>
                                <input type="text" class="form-control" id="costo" name="costo" onkeypress="return filterFloat(event, this);">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="update_costo"><i class="fa fa-pencil"></i> Editar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
