<form role="form" action="" method="POST" id="frm_egreso_create">
    @csrf
    <div class="row">
        <div class="col-12 col-md-6">
            <!-- CUENTA -->
            <div class="form-group">
                <label for="cuenta" class="font-weight-bold required_field">CUENTA</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-primary">
                            <i class="fas fa-university"></i>
                        </span>
                    </div>
                    <select name="cuenta" id="cuenta" class="form-control" required>
                        <option value="">SELECCIONAR</option>
                        @foreach (cuentas() as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="cuenta_error msgError"></span>
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
                    <input required type="text" name="tipo_documento" id="tipo_documento" class="form-control tipo_documento"
                        value="RECIBO" readonly>
                </div>
                <span class="tipo_documento_error msgError"></span>
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
                    <input type="text" class="form-control monto" value="0" id="monto" name="monto" required
                        readonly>
                </div>
                <span class="monto_error msgError"></span>
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
                    <input required type="text" name="documento" id="documento" class="form-control">
                </div>
                <span class="documento_error msgError"></span>
            </div>

            <!-- DESCRIPCION -->
            <div class="form-group">
                <label class="font-weight-bold">DESCRIPCIÓN</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-warning">
                            <i class="fas fa-align-left"></i>
                        </span>
                    </div>
                    <textarea maxlength="200" name="descripcion" id="descripcion" cols="30" rows="2" class="form-control"
                        placeholder="Máximo 200 caracteres"></textarea>
                </div>
                <span class="descripcion_error msgError"></span>
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
                    <input type="text" value="0.00" class="form-control" id="efectivo_venta"
                        onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo()" name="efectivo"
                        required>
                </div>
                <span class="efectivo_error msgError"></span>
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
                    <select name="modo_pago" id="modo_pago" class="select2_form form-control"
                        onchange="changeModoPago(this)" required>
                        <option value="">SELECCIONAR</option>
                        @foreach (modos_pago() as $modo)
                            <option value="{{ $modo->id }}">{{ $modo->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="modo_pago_error msgError"></span>
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
                    <input type="text" class="form-control" id="importe_venta" value="0.00"
                        onkeypress="return filterFloat(event, this);" onkeyup="changeImporte()" name="importe"
                        required>
                </div>
                <span class="importe_error msgError"></span>
            </div>

            <!-- CUENTA BANCARIA -->
            <div class="form-group">
                <label class="font-weight-bold lbl-cuenta-bancaria">CUENTA BANCARIA</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-primary">
                            <i class="fas fa-university"></i>
                        </span>
                    </div>
                    <select name="cuenta_bancaria" id="cuenta_bancaria" class="select2_form form-control">
                        <option value="">SELECCIONAR</option>
                    </select>
                </div>
                <span class="cuenta_bancaria_error msgError"></span>
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
                    <input type="text" name="nro_operacion" id="nro_operacion" class="form-control"
                        value="">
                </div>
                <span class="nro_operacion_error msgError"></span>
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
                    <input type="date" name="fecha_operacion" id="fecha_operacion" class="form-control"
                        value="{{ date('Y-m-d') }}">
                </div>
                <span class="fecha_operacion_error msgError"></span>
            </div>

        </div>
    </div>
</form>
