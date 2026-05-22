<div id="modal_create_caja" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="lbl_crear_caja" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lbl_crear_caja">
                    <i class="fas fa-cash-register mr-1"></i> Crear Nueva Caja
                </h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="required">Nombre de Caja</label>
                <input type="text" id="nombre_create" class="form-control" placeholder="Ej: CAJA 01"
                    maxlength="100" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="guardarCaja()">
                    <i class="fa fa-save mr-1"></i>Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
