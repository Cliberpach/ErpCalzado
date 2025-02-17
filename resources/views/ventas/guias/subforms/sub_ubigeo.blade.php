<div class="row mb-3">
    <!-- Punto de Partida -->
    <div class="col-12">
        <div class="p-2 bg-primary text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE PARTIDA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">SEDE PARTIDA</label>
        <select class="form-control select2_form">
            <option value="KGM">KILOGRAMOS</option>
            <option value="TNE">TONELADAS</option>
        </select>
    </div>

    <div class="col-12"></div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="required font-weight-bold">DIRECCIÓN</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Dirección de partida" required>
        </div>
        <span class="text-danger nombre_error msgError"></span>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">UBIGEO</label>
        <select class="form-control select2_form">
            <option value="KGM">KILOGRAMOS</option>
            <option value="TNE">TONELADAS</option>
        </select>
    </div>

    <!-- Punto de Llegada -->
    <div class="col-12 mt-3">
        <div class="p-2 bg-success text-white rounded shadow-sm">
            <i class="fas fa-map-marker-alt"></i> <strong>PUNTO DE LLEGADA</strong>
        </div>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">SEDE DESTINO</label>
        <select class="form-control select2_form">
            <option value="KGM">KILOGRAMOS</option>
            <option value="TNE">TONELADAS</option>
        </select>
    </div>
    <div class="col-12"></div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="required font-weight-bold">DIRECCIÓN</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Dirección de llegada" required>
        </div>
        <span class="text-danger nombre_error msgError"></span>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-6 mb-3">
        <label class="required font-weight-bold">UBIGEO</label>
        <select class="form-control select2_form">
            <option value="KGM">KILOGRAMOS</option>
            <option value="TNE">TONELADAS</option>
        </select>
    </div>
</div>
