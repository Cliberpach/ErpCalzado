<form id="form_edit_tipocliente" action="" method="POST">
    @csrf
    @method('PUT')

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="nombre_edit">Nombre <span class="text-danger">*</span></label>

            <input
                type="text"
                id="nombre_edit"
                name="nombre_edit"
                class="form-control inputName"
                placeholder="Nombre"
                oninput="this.value = this.value.toUpperCase()"
            >

            <small class="text-danger font-weight-bold msgError_edit nombre_edit_error"></small>
        </div>
    </div>

</form>
