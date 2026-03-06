<form id="form_create_tipocliente" method="POST">
    @csrf

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="nombre">Nombre <span class="text-danger">*</span></label>

            <input type="text" id="nombre" name="nombre" class="form-control inputName"
                placeholder="Nombre" required>

            <small class="text-danger font-weight-bold nombre_error"></small>
        </div>
    </div>

</form>
