<form action="" id="formEditTalla" method="POST">
    @csrf
    <div class="row">

        <!-- NOMBRE -->
        <div class="col-12">
            <label for="descripcion_edit" class="required_field mb-2" style="font-weight: 600;">
                <i class="fas fa-palette text-primary mr-1"></i>
                Nombre
            </label>

            <input required maxlength="191" id="descripcion_edit" name="descripcion_edit" type="text"
                class="form-control text-uppercase" placeholder="Ingrese un nombre">

            <small class="text-success d-block" style="font-style: italic;">
                máximo 191 caracteres
            </small>

            <span class="descripcion_edit_error msgError"></span>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer mt-3">
        <div class="col-md-6 text-left text-muted">
            <small><i class="fa fa-exclamation-circle"></i> campos obligatorios (*)</small>
        </div>

        <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary btn-sm" form="formEditTalla">
                <i class="fa fa-save"></i> Guardar
            </button>

            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancelar
            </button>
        </div>
    </div>

</form>
