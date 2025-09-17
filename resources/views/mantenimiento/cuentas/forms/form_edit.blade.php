<form action="" id="formActualizarCuenta" method="POST">
    <div class="row">
        @csrf

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="titular_edit" style="font-weight: bold;" class="required_field">Titular</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
                <input maxlength="160" required id="titular_edit" name="titular_edit" type="text"
                    class="form-control" placeholder="Titular" aria-label="Titular" aria-describedby="basic-addon1">
            </div>
            <span class="titular_edit_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="moneda_edit" style="font-weight: bold;" class="required_field">Moneda</label>

            <select name="moneda_edit" id="moneda_edit" class="form-control select2_mdl_cuenta_edit">
                <option value="SOLES">SOLES</option>
                <option value="DOLARES">DOLARES</option>
            </select>
            <span class="moneda_edit_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="nro_cuenta_edit" style="font-weight: bold;" class="required_field">N° Cuenta</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-university"></i>
                    </span>
                </div>
                <input maxlength="160" required id="nro_cuenta_edit" name="nro_cuenta_edit" type="text"
                    class="form-control" placeholder="N° Cuenta" aria-label="N° Cuenta" aria-describedby="basic-addon1">
            </div>
            <span class="nro_cuenta_edit_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="cci_edit" style="font-weight: bold;" class="required_field">CCI</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-exchange-alt"></i>
                    </span>
                </div>
                <input maxlength="100" id="cci_edit" name="cci_edit" type="text" class="form-control"
                    placeholder="CCI" aria-label="CCI" aria-describedby="basic-addon1">
            </div>
            <span class="cci_edit_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="celular_edit" style="font-weight: bold;" class="required_field">Celular</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-mobile-alt"></i>
                    </span>
                </div>
                <input maxlength="100" id="celular_edit" name="celular_edit" type="text" class="form-control"
                    placeholder="Celular" aria-label="Celular" aria-describedby="basic-addon1">
            </div>
            <span class="celular_edit_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="banco_id_edit" style="font-weight: bold;" class="required_field">Banco</label>

            <select name="banco_id_edit" id="banco_id_edit" class="form-control select2_mdl_cuenta_edit">
                @foreach ($bancos as $banco)
                    <option value="{{ $banco->id }}">{{ $banco->descripcion }}</option>
                @endforeach
            </select>

            <span class="banco_id_edit_error msgError" style="color:red;"></span>
        </div>

    </div>
</form>
