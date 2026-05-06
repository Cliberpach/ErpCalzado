<form action="" id="formCreateAlmacen" method="POST">
    @csrf

    <input type="hidden" value="{{ $sede_id }}" name="sede_id" id="sede_id">

    <div class="row">

        <!-- NOMBRE DEL ALMACÉN -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="descripcion" class="required_field mb-2" style="font-weight: 600;">
                <i class="fas fa-warehouse text-primary mr-1"></i>
                Nombre del almacén
            </label>

            <input required maxlength="160" id="descripcion" name="descripcion" type="text"
                class="form-control text-uppercase" placeholder="ingrese nombre del almacén">

            <small class="text-success d-block" style="font-style: italic;">
                máximo 160 caracteres
            </small>

            <span class="descripcion_error msgError"></span>
        </div>

        <!-- UBICACIÓN -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="ubicacion" class="required_field mb-2" style="font-weight: 600;">
                <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                Ubicación
            </label>

            <input required maxlength="191" id="ubicacion" name="ubicacion" type="text" class="form-control text-uppercase"
                placeholder="ingrese ubicación del almacén">

            <small class="text-success d-block" style="font-style: italic;">
                máximo 191 caracteres
            </small>

            <span class="ubicacion_error msgError"></span>
        </div>

        <!-- TIPO -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="tipo_almacen" class="required_field mb-2" style="font-weight: 600;">
                <i class="fas fa-layer-group text-info mr-1"></i>
                Tipo de almacén
            </label>

            <select required name="tipo_almacen" id="tipo_almacen" class="form-control">
                @if (!$sede_have_principal)
                    <option value="PRINCIPAL">PRINCIPAL</option>
                @endif

                <option value="SECUNDARIO">SECUNDARIO</option>
            </select>

            <small class="text-success d-block" style="font-style: italic;">
                principal o secundario
            </small>

            <span class="tipo_almacen_error msgError"></span>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer mt-3">
        <div class="col-md-6 text-left text-muted">
            <small><i class="fa fa-exclamation-circle"></i> campos obligatorios (*)</small>
        </div>

        <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary btn-sm" form="formCreateAlmacen">
                <i class="fa fa-save"></i> Guardar
            </button>

            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancelar
            </button>
        </div>
    </div>

</form>
