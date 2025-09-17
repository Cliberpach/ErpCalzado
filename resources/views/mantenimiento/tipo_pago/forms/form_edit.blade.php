<form action="" id="formActualizarMetodoPago" method="post">
    <div class="row">
        @csrf
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="descripcion_edit" style="font-weight: bold;" class="required_field">Nombre</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-tags"></i>
                    </span>
                </div>
                <input required id="descripcion_edit" name="descripcion_edit" type="text" class="form-control"
                    placeholder="Método de pago" aria-label="Método de pago" aria-describedby="basic-addon1">
            </div>
            <span class="descripcion_edit_error msgError_edit" style="color:red;"></span>
        </div>
    </div>
</form>
