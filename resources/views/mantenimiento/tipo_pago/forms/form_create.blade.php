<form action="" id="formRegistrarMetodoPago" method="post">
    <div class="row">
        @csrf
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="descripcion" style="font-weight: bold;" class="required_field">Nombre</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-tags"></i>
                    </span>
                </div>
                <input maxlength="160" required id="descripcion" name="descripcion" type="text" class="form-control"
                    placeholder="Tipo de Pago" aria-label="Tipo de Pago"
                    aria-describedby="basic-addon1">
            </div>
            <span class="descripcion_error msgError" style="color:red;"></span>
        </div>
    </div>
</form>
