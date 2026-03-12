<form id="formConsultarComprobante">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="tipoDocumento" class="form-label">Tipo Documento <span class="text-danger">*</span></label>
            <select required name="tipo_doc" id="tipoDocumento" class="form-select">
                <option value=""></option>
                <option value="01">FACTURA ELECTRÓNICA</option>
                <option value="03">BOLETA ELECTRÓNICA</option>
                <option value="07">NOTA DE CRÉDITO</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="fechaEmision" class="form-label">Fecha de emisión <span class="text-danger">*</span></label>
            <input required name="fecha_emision" type="date" id="fechaEmision" class="form-control"
                value="<?= date('Y-m-d') ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="serie" class="form-label">Serie <span class="text-danger">*</span></label>
            <input required name="serie" type="text" id="serie" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="numero" class="form-label">Número <span class="text-danger">*</span></label>
            <input required name="correlativo" type="text" id="numero" class="form-control inputEnteroPositivo">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="numeroCliente" class="form-label">Número Cliente (RUC/DNI/CE) <span
                    class="text-danger">*</span></label>
            <input required name="doc_cliente" type="text" id="numeroCliente" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="montoTotal" class="form-label">Monto total <span class="text-danger">*</span></label>
            <input required name="monto_total" type="text" id="montoTotal" class="form-control inputDecimalPositivo">
        </div>
    </div>
    <div class="text-end">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
</form>
