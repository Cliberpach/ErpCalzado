<form role="form" action="{{ route('almacenes.categorias.store') }}" method="POST" id="crear_categoria"
    enctype="multipart/form-data">
    {{ csrf_field() }} {{ method_field('POST') }}

    <div class="form-group">
        <label class="required">Descripción:</label>
        <input type="text" class="form-control {{ $errors->has('descripcion_guardar') ? ' is-invalid' : '' }}"
            name="descripcion_guardar" id="descripcion_guardar" value="{{ old('descripcion_guardar') }}"
            onkeyup="return mayus(this)" required>

        @if ($errors->has('descripcion_guardar'))
            <span class="invalid-feedback" role="alert">
                <strong id="error-descripcion-guardar">{{ $errors->first('descripcion_guardar') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        <label>Imagen (Opcional)</label>

        <div class="custom-file">
            <input type="file" class="custom-file-input {{ $errors->has('imagen') ? 'is-invalid' : '' }}"
                id="imagen" name="imagen" accept=".jpg,.jpeg,.webp,.avif,image/jpeg,image/webp,image/avif">

            <label class="custom-file-label" for="imagen">
                Seleccionar imagen...
            </label>

            @if ($errors->has('imagen'))
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $errors->first('imagen') }}</strong>
                </span>
            @endif
        </div>

        <small class="form-text text-muted">
            Formatos permitidos: JPG, WEBP, AVIF. Tamaño máximo: 1MB.
        </small>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            let fileName = e.target.files[0]?.name || "Seleccionar imagen...";
            e.target.nextElementSibling.innerText = fileName;
        });
    });
</script>
