<div id="modal_edit_caja" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="lbl_editar_caja" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <input type="hidden" id="edit_caja_id">
            <div class="modal-header">
                <h5 class="modal-title" id="lbl_editar_caja">
                    <i class="fas fa-edit mr-1"></i> Editar Caja
                </h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="required">Nombre de Caja</label>
                <input type="text" id="nombre_edit" class="form-control" maxlength="100" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCaja()">
                    <i class="fa fa-save mr-1"></i>Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
