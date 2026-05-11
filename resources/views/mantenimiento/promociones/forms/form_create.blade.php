<form action="" id="formCreatePromocion" method="POST">
    @csrf

    <div class="row">

        <!-- NOMBRE -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="nombre" class="required_field mb-2 font-weight-bold">
                <i class="fas fa-tags text-success mr-1"></i>
                Nombre de la promoción
            </label>

            <input required maxlength="160" id="nombre" name="nombre" type="text"
                class="form-control text-uppercase" placeholder="Ingrese nombre de la promoción">

            <small class="text-success d-block font-italic">
                máximo 160 caracteres
            </small>

            <span class="nombre_error msgError"></span>
        </div>

        <!-- DESCRIPCIÓN -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label for="descripcion" class="mb-2 font-weight-bold">
                <i class="fas fa-align-left text-info mr-1"></i>
                Descripción
            </label>

            <input maxlength="255" id="descripcion" name="descripcion" type="text"
                class="form-control text-uppercase" placeholder="Descripción opcional">

            <small class="text-success d-block font-italic">
                opcional
            </small>

            <span class="descripcion_error msgError"></span>
        </div>

        <!-- TIPO PROMOCION -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="tipo_promocion" class="required_field mb-2 font-weight-bold">
                <i class="fas fa-bullhorn text-warning mr-1"></i>
                Tipo de promoción
            </label>

            <select required name="tipo_promocion" id="tipo_promocion" class="form-control">

                <option value="precio_total">
                    PRECIO TOTAL POR CANTIDAD
                </option>

                <option value="descuento_fijo">
                    DESCUENTO FIJO POR PAR
                </option>

                <option value="descuento_porcentaje">
                    DESCUENTO POR PORCENTAJE
                </option>

            </select>

            <small class="text-success d-block font-italic">
                define cómo se aplicará la promoción
            </small>

            <span class="tipo_promocion_error msgError"></span>
        </div>

        <!-- VALOR -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="valor" class="required_field mb-2 font-weight-bold">

                <i class="fas fa-hand-holding-usd text-success mr-1"></i>

                <span id="label-valor">
                    Valor promoción
                </span>

            </label>

            <input required id="valor" name="valor" type="number" step="0.01" class="form-control"
                placeholder="Ingrese valor">

            <small id="help-valor" class="text-success d-block font-italic">

                monto del descuento/promoción

            </small>

            <span class="valor_error msgError"></span>
        </div>

        <!-- CANTIDAD -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="cantidad_minima" class="required_field mb-2 font-weight-bold">
                <i class="fas fa-boxes text-danger mr-1"></i>
                Cantidad mínima (pares)
            </label>

            <input required id="cantidad_minima" name="cantidad_minima" type="number" class="form-control"
                placeholder="Ej: 3">

            <small class="text-success d-block font-italic">
                desde cuántos pares aplica la promoción
            </small>

            <span class="cantidad_minima_error msgError"></span>
        </div>

        <!-- FECHA INICIO -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="fecha_inicio" class="mb-2 font-weight-bold">
                <i class="fas fa-calendar-alt text-success mr-1"></i>
                Fecha inicio
            </label>

            <input id="fecha_inicio" name="fecha_inicio" type="date" class="form-control">

            <span class="fecha_inicio_error msgError"></span>
        </div>

        <!-- FECHA FIN -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="fecha_fin" class="mb-2 font-weight-bold">
                <i class="fas fa-calendar-check text-success mr-1"></i>
                Fecha fin
            </label>

            <input id="fecha_fin" name="fecha_fin" type="date" class="form-control">

            <span class="fecha_fin_error msgError"></span>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="modal-footer mt-3">

        <div class="col-md-12 text-left text-muted">
            <small>
                <i class="fa fa-exclamation-circle"></i>
                campos obligatorios (*)
            </small>
        </div>

        <div class="col-md-12 text-right">

            <button type="submit" class="btn btn-success btn-sm" form="formCreatePromocion">

                <i class="fa fa-save"></i>
                Guardar

            </button>

            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">

                <i class="fa fa-times"></i>
                Cancelar

            </button>

        </div>

    </div>

</form>

<script>
    const tipoPromocion = document.getElementById('tipo_promocion');

    const labelValor = document.getElementById('label-valor');

    const helpValor = document.getElementById('help-valor');


    tipoPromocion.addEventListener('change', changePromotionUI);


    function changePromotionUI() {

        const value = tipoPromocion.value;

        switch (value) {

            case 'descuento_fijo':

                labelValor.innerText =
                    'Descuento por par';

                helpValor.innerText =
                    'monto que se descontará a cada par';

                break;


            case 'descuento_porcentaje':

                labelValor.innerText =
                    'Porcentaje descuento';

                helpValor.innerText =
                    'porcentaje que se descontará por par';

                break;


            case 'precio_total':

                labelValor.innerText =
                    'Precio total promoción';

                helpValor.innerText =
                    'precio final total por la cantidad indicada';

                break;
        }
    }


    changePromotionUI();
</script>
