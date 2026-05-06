<form role="form" method="POST" id="form_create_color">
    @csrf
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label class="required_field font-weight-bold text-primary">
                <i class="fas fa-palette mr-1"></i> Descripción:
            </label>
            <input type="text" class="form-control" name="descripcion" id="descripcion" value="{{ old('descripcion') }}"
                required>
            <p class="descripcion_error msgError"></p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label class="required_field font-weight-bold text-success">
                <i class="fas fa-barcode mr-1"></i> Código:
            </label>
            <input type="text" class="form-control" name="codigo" id="codigo" value="{{ old('codigo') }}"
                maxlength="12">
            <p class="codigo_error msgError"></p>
        </div>
    </div>

    <div class="form-group">
        <label for="imagen" class="font-weight-bold text-info">
            <i class="fas fa-image mr-1"></i> Imagen
        </label>

        <input type="file" id="imagen" name="imagen" class="form-control-file"
            accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">

        <span class="form-text text-muted">
            <i class="fas fa-info-circle"></i>
            Formatos permitidos: JPG, PNG, WEBP, AVIF. Tamaño máximo: 2MB.
        </span>

        <small class="text-danger font-weight-bold imagen_error"></small>
    </div>
</form>
