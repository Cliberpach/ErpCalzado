<form id="form_create_categoria" method="POST">
    @csrf

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="descripcion">Nombre <span class="text-danger">*</span></label>

            <input type="text" id="descripcion" name="descripcion" class="form-control inputName" placeholder="Nombre"
                required>

            <small class="text-danger font-weight-bold descripcion_error"></small>
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

    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="mostrar_en_web" name="mostrar_en_web" value="1">
            <label class="form-check-label fw-bold" for="mostrar_en_web">
                MOSTRAR EN WEB
            </label>
        </div>
        <small class="text-danger font-weight-bold mostrar_en_web_error"></small>
    </div>

</form>
