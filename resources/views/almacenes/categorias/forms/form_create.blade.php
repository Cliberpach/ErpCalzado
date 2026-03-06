<form id="form_create_categoria" method="POST">
    @csrf

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>

            <input type="text" id="nombre" name="nombre" class="form-control inputName" placeholder="Nombre"
                required>

            <small class="text-danger font-weight-bold nombre_error"></small>
        </div>
    </div>

    <div class="form-group">

        <label for="imagen">Imagen</label>

        <input type="file" id="imagen" name="imagen" class="form-control-file"
            accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">

        <span class="form-text text-muted">
            Formatos permitidos: JPG, PNG, WEBP, AVIF. Tamaño máximo: 2MB.
        </span>

        <small class="text-danger font-weight-bold imagen_error"></small>

    </div>

</form>
