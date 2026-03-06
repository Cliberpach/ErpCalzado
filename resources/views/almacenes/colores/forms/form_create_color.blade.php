<form role="form" method="POST" id="form_create_color">
    {{ csrf_field() }}
    {{ method_field('POST') }}

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label class="required_field" style="font-weight: bold;">Descripción:</label>
            <input type="text" class="form-control" name="descripcion" id="descripcion"
                value="{{ old('descripcion') }}"required>
            <p class="descripcion_error msgError"></p>
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

<style>
    input[type="color"].form-control {
        height: 38px;
        padding: 0;
        width: 100%;
    }
</style>
