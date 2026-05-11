<form action="" id="formEditPromocion">

    @csrf

    <div class="row">

        <!-- NOMBRE -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="nombre_edit"
                class="required_field mb-2 font-weight-bold">

                <i class="fas fa-tags text-success mr-1"></i>

                Nombre de la promoción

            </label>

            <input required
                maxlength="160"
                id="nombre_edit"
                name="nombre_edit"
                type="text"
                class="form-control text-uppercase"
                placeholder="Ingrese nombre de la promoción">

            <small class="text-success d-block font-italic">

                máximo 160 caracteres

            </small>

            <span class="nombre_edit_error msgError"></span>

        </div>


        <!-- DESCRIPCION -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="descripcion_edit"
                class="mb-2 font-weight-bold">

                <i class="fas fa-align-left text-info mr-1"></i>

                Descripción

            </label>

            <input maxlength="255"
                id="descripcion_edit"
                name="descripcion_edit"
                type="text"
                class="form-control text-uppercase"
                placeholder="Descripción opcional">

            <small class="text-success d-block font-italic">

                opcional

            </small>

            <span class="descripcion_edit_error msgError"></span>

        </div>


        <!-- TIPO PROMOCION -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="tipo_promocion_edit"
                class="required_field mb-2 font-weight-bold">

                <i class="fas fa-bullhorn text-warning mr-1"></i>

                Tipo de promoción

            </label>

            <select required
                name="tipo_promocion_edit"
                id="tipo_promocion_edit"
                class="form-control">

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

            <span class="tipo_promocion_edit_error msgError"></span>

        </div>


        <!-- VALOR -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="valor_edit"
                class="required_field mb-2 font-weight-bold">

                <i class="fas fa-hand-holding-usd text-success mr-1"></i>

                <span id="label-valor-edit">
                    Valor promoción
                </span>

            </label>

            <input required
                id="valor_edit"
                name="valor_edit"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Ingrese valor">

            <small id="help-valor-edit"
                class="text-success d-block font-italic">

                monto del descuento/promoción

            </small>

            <span class="valor_edit_error msgError"></span>

        </div>


        <!-- CANTIDAD -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="cantidad_minima_edit"
                class="required_field mb-2 font-weight-bold">

                <i class="fas fa-boxes text-danger mr-1"></i>

                Cantidad mínima (pares)

            </label>

            <input required
                id="cantidad_minima_edit"
                name="cantidad_minima_edit"
                type="number"
                class="form-control"
                placeholder="Ej: 3">

            <small class="text-success d-block font-italic">

                desde cuántos pares aplica la promoción

            </small>

            <span class="cantidad_minima_edit_error msgError"></span>

        </div>


        <!-- FECHA INICIO -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="fecha_inicio_edit"
                class="mb-2 font-weight-bold">

                <i class="fas fa-calendar-alt text-success mr-1"></i>

                Fecha inicio

            </label>

            <input id="fecha_inicio_edit"
                name="fecha_inicio_edit"
                type="date"
                class="form-control">

            <span class="fecha_inicio_edit_error msgError"></span>

        </div>


        <!-- FECHA FIN -->
        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">

            <label for="fecha_fin_edit"
                class="mb-2 font-weight-bold">

                <i class="fas fa-calendar-check text-success mr-1"></i>

                Fecha fin

            </label>

            <input id="fecha_fin_edit"
                name="fecha_fin_edit"
                type="date"
                class="form-control">

            <span class="fecha_fin_edit_error msgError"></span>

        </div>

    </div>


    <!-- FOOTER -->
    <div class="modal-footer mt-3 d-flex justify-content-between align-items-center">

        <!-- INFO -->
        <div class="text-muted">

            <small>

                <i class="fa fa-info-circle text-success"></i>

                Campos obligatorios (*)

            </small>

        </div>

        <!-- BOTONES -->
        <div class="btn-group">

            <button type="button"
                class="btn btn-white btn-sm"
                data-dismiss="modal">

                <i class="fa fa-times text-danger"></i>

                Cancelar

            </button>

            <button type="submit"
                class="btn btn-success btn-sm"
                form="formEditPromocion">

                <i class="fa fa-save"></i>

                Actualizar

            </button>

        </div>

    </div>

</form>


<script>

    const tipoPromocionEdit =
        document.getElementById(
            'tipo_promocion_edit'
        );

    const labelValorEdit =
        document.getElementById(
            'label-valor-edit'
        );

    const helpValorEdit =
        document.getElementById(
            'help-valor-edit'
        );


    tipoPromocionEdit.addEventListener(
        'change',
        changePromotionEditUI
    );


    function changePromotionEditUI() {

        const value = tipoPromocionEdit.value;


        switch (value) {

            // DESCUENTO FIJO
            case 'descuento_fijo':

                labelValorEdit.innerText =
                    'Descuento por par';

                helpValorEdit.innerText =
                    'monto que se descontará a cada par';

                break;


            // DESCUENTO %
            case 'descuento_porcentaje':

                labelValorEdit.innerText =
                    'Porcentaje descuento';

                helpValorEdit.innerText =
                    'porcentaje que se descontará por par';

                break;


            // PRECIO TOTAL
            case 'precio_total':

                labelValorEdit.innerText =
                    'Precio total promoción';

                helpValorEdit.innerText =
                    'precio final total por la cantidad indicada';

                break;
        }
    }


    changePromotionEditUI();

</script>
