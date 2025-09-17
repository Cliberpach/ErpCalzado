<form role="form" action="" method="POST" id="frm_edit_egreso">
    @csrf
    <div class="row">
        <div class="col-12 col-md-6">
            <!-- CUENTA -->
            <div class="form-group">
                <label for="cuenta_edit" class="font-weight-bold required_field">CUENTA</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-primary">
                            <i class="fas fa-university"></i>
                        </span>
                    </div>
                    <select name="cuenta_edit" id="cuenta_edit" class="form-control" required>
                        <option value="">SELECCIONAR</option>
                        @foreach (cuentas() as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="cuenta_edit_error msgError"></span>
            </div>

            <!-- TIPO DOCUMENTO -->
            <div class="form-group">
                <label class="required_field font-weight-bold">TIPO DOCUMENTO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-danger">
                            <i class="fas fa-file-alt"></i>
                        </span>
                    </div>
                    <input required type="text" name="tipo_documento_edit" id="tipo_documento_edit" class="form-control tipo_documento_edit"
                        value="RECIBO" readonly>
                </div>
                <span class="tipo_documento_edit_error msgError"></span>
            </div>

            <!-- MONTO -->
            <div class="form-group">
                <label class="required_field font-weight-bold">MONTO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control monto_edit" value="0" id="monto_edit" name="monto_edit" required
                        readonly>
                </div>
                <span class="monto_edit_error msgError"></span>
            </div>

            <!-- DOCUMENTO -->
            <div class="form-group">
                <label class="required_field font-weight-bold">DOCUMENTO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-info">
                            <i class="fas fa-hashtag"></i>
                        </span>
                    </div>
                    <input required type="text" name="documento_edit" id="documento_edit" class="form-control">
                </div>
                <span class="documento_edit_error msgError"></span>
            </div>

            <!-- DESCRIPCION -->
            <div class="form-group">
                <label for="descripcion_edit" class="font-weight-bold">DESCRIPCIÓN</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-warning">
                            <i class="fas fa-align-left"></i>
                        </span>
                    </div>
                    <textarea maxlength="200" name="descripcion_edit" id="descripcion_edit" cols="30" rows="2" class="form-control"
                        placeholder="Máximo 200 caracteres"></textarea>
                </div>
                <span class="descripcion_edit_error msgError"></span>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <!-- EFECTIVO -->
            <div class="form-group">
                <label class="required font-weight-bold">EFECTIVO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                    </div>
                    <input type="text" value="0.00" class="form-control" id="efectivo_venta_edit"
                        onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivoEdit()" name="efectivo_edit"
                        required>
                </div>
                <span class="efectivo_edit_error msgError"></span>
            </div>

            <!-- METODO PAGO -->
            <div class="form-group">
                <label class="required font-weight-bold">MÉTODO PAGO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-primary">
                            <i class="fas fa-credit-card"></i>
                        </span>
                    </div>
                    <select name="modo_pago_edit" id="modo_pago_edit" class="select2_form form-control"
                        onchange="changeModoPagoEdit(this)" required>
                        <option value="">SELECCIONAR</option>
                        @foreach (modos_pago() as $modo)
                            <option value="{{ $modo->id }}">{{ $modo->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="modo_pago_edit_error msgError"></span>
            </div>

            <!-- IMPORTE -->
            <div class="form-group">
                <label class="required font-weight-bold">IMPORTE</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-success">
                            <i class="fas fa-coins"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="importe_venta_edit" value="0.00"
                        onkeypress="return filterFloat(event, this);" onkeyup="changeImporteEdit()" name="importe_edit"
                        required>
                </div>
                <span class="importe_edit_error msgError"></span>
            </div>

            <!-- CUENTA BANCARIA -->
            <div class="form-group">
                <label class="font-weight-bold lbl-cuenta-bancaria-edit">CUENTA BANCARIA</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-primary">
                            <i class="fas fa-university"></i>
                        </span>
                    </div>
                    <select name="cuenta_bancaria_edit" id="cuenta_bancaria_edit" class="select2_form form-control">
                        <option value="">SELECCIONAR</option>
                    </select>
                </div>
                <span class="cuenta_bancaria_edit_error msgError"></span>
            </div>

            <!-- NRO OPERACION -->
            <div class="form-group">
                <label class="required font-weight-bold">N° OPERACIÓN</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-info">
                            <i class="fas fa-receipt"></i>
                        </span>
                    </div>
                    <input type="text" name="nro_operacion_edit" id="nro_operacion_edit" class="form-control"
                        value="">
                </div>
                <span class="nro_operacion_edit_error msgError"></span>
            </div>

            <!-- FECHA OPERACION -->
            <div class="form-group">
                <label class="required font-weight-bold">FECHA OPERACIÓN</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="date" name="fecha_operacion_edit" id="fecha_operacion_edit" class="form-control"
                        value="{{ date('Y-m-d') }}">
                </div>
                <span class="fecha_operacion_edit_error msgError"></span>
            </div>

        </div>
    </div>
</form>
