<form role="form" action="{{ route('almacenes.categorias.update') }}" method="POST" id="editar_categoria"
    enctype="multipart/form-data">
    {{ csrf_field() }} {{ method_field('PUT') }}

    <input type="hidden" name="tabla_id" id="tabla_id_editar" value="{{ old('tabla_id') }}">
    <div class="form-group">
        <label class="required">Descripción:</label>
        <input type="text" class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
            name="descripcion" id="descripcion_editar" value="{{ old('descripcion') }}"
            onkeyup="return mayus(this)"required>

        @if ($errors->has('descripcion'))
            <span class="invalid-feedback" role="alert">
                <strong id="error-descripcion">{{ $errors->first('descripcion') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label>Imagen actual</label>

        <div class="mb-2">
            <img id="preview_imagen_editar" src="" style="max-width:150px; display:none; border-radius:8px;">
        </div>

        <div class="custom-file">
            <input type="file" class="custom-file-input" id="imagen_editar" name="imagen"
                accept=".jpg,.jpeg,.webp,.avif">

            <label class="custom-file-label" for="imagen_editar">
                Cambiar imagen (opcional)
            </label>
        </div>

        <small class="form-text text-muted">
            JPG, JPEG, WEBP o AVIF. Máx 1MB.
        </small>
    </div>
</form>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        const inputImagen = document.getElementById("imagen_editar");
        const preview = document.getElementById("preview_imagen_editar");

        inputImagen.addEventListener("change", function() {

            if (this.files && this.files[0]) {

                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };

                reader.readAsDataURL(this.files[0]);

            } else {
                preview.src = "";
                preview.style.display = "none";
            }

        });

    });
</script>
