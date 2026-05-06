<form action="" id="formCreateCategoria" method="POST">
    <div class="row">

        <!-- NOMBRE DEL ALMACÉN -->
        <div class="col-12">
            <label for="descripcion_mdlcategory" class="required_field mb-2" style="font-weight: 600;">
                <i class="fas fa-warehouse text-primary mr-1"></i>
                Nombre
            </label>

            <input required maxlength="160" id="descripcion_mdlcategory" name="descripcion_mdlcategory" type="text"
                class="form-control text-uppercase" placeholder="Ingrese un nombre">

            <small class="text-success d-block" style="font-style: italic;">
                máximo 160 caracteres
            </small>

            <span class="descripcion_mdlcategory_error msgError"></span>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer mt-3">
        <div class="col-md-6 text-left text-muted">
            <small><i class="fa fa-exclamation-circle"></i> campos obligatorios (*)</small>
        </div>

        <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary btn-sm" form="formCreateCategoria">
                <i class="fa fa-save"></i> Guardar
            </button>

            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancelar
            </button>
        </div>
    </div>

</form>
