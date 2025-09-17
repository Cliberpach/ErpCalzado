<form action="" id="formRegistrarCuenta" method="POST">
    <div class="row">
        @csrf

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="titular" style="font-weight: bold;" class="required_field">Titular</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
                <input maxlength="160" required id="titular" name="titular" type="text" class="form-control"
                    placeholder="Titular" aria-label="Titular" aria-describedby="basic-addon1">
            </div>
            <span class="titular_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="moneda" style="font-weight: bold;" class="required_field">Moneda</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                </div>
                <select name="moneda" id="moneda" class="form-control select2_mdl_cuenta">
                    <option value="SOLES">SOLES</option>
                    <option value="DOLARES">DOLARES</option>
                </select>
            </div>
            <span class="moneda_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="nro_cuenta" style="font-weight: bold;" class="required_field">N° Cuenta</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-university"></i>
                    </span>
                </div>
                <input maxlength="160" required id="nro_cuenta" name="nro_cuenta" type="text" class="form-control"
                    placeholder="Cuenta" aria-label="Cuenta" aria-describedby="basic-addon1">
            </div>
            <span class="nro_cuenta_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="cci" style="font-weight: bold;" class="required_field">CCI</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-exchange-alt"></i>
                    </span>
                </div>
                <input maxlength="100" id="cci" name="cci" type="text" class="form-control"
                    placeholder="CCI" aria-label="CCI" aria-describedby="basic-addon1">
            </div>
            <span class="cci_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="celular" style="font-weight: bold;" class="required_field">Celular</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-mobile-alt"></i>
                    </span>
                </div>
                <input maxlength="100" id="celular" name="celular" type="text" class="form-control"
                    placeholder="Celular" aria-label="Celular" aria-describedby="basic-addon1">
            </div>
            <span class="celular_error msgError" style="color:red;"></span>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label for="banco_id" style="font-weight: bold;" class="required_field">Banco</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-building"></i>
                    </span>
                </div>
                <select name="banco_id" id="banco_id" class="form-control select2_mdl_cuenta">
                    @foreach ($bancos as $banco)
                        <option value="{{ $banco->id }}">{{ $banco->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <span class="banco_id_error msgError" style="color:red;"></span>
        </div>

    </div>
</form>
