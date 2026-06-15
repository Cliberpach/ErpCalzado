<form method="POST" enctype="multipart/form-data" id="formFacturacion">
    @csrf

    <div class="row">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-lg-6">

            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                <i class="fas fa-plug"></i> Credenciales API Guía de Remisión
            </h6>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-fingerprint text-primary"></i> ID API Guía de Remisión</label>
                    <textarea class="form-control" name="id_api_guia_remision" rows="3"
                        placeholder="Ingrese el ID de la API...">{{ $greenter_config->id_api_guia_remision ?? '' }}</textarea>
                    <span class="id_api_guia_remision_error text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-key text-warning"></i> Clave API Guía de Remisión</label>
                    <textarea class="form-control" name="clave_api_guia_remision" rows="3"
                        placeholder="Ingrese la clave de la API...">{{ $greenter_config->clave_api_guia_remision ?? '' }}</textarea>
                    <span class="clave_api_guia_remision_error text-danger small"></span>
                </div>
            </div>

            <h6 class="text-success font-weight-bold border-bottom pb-2 mb-3 mt-4">
                <i class="fas fa-search"></i> Credenciales Consulta CPE SUNAT
            </h6>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-id-badge text-success"></i> Client ID CPE</label>
                    <input type="text" class="form-control" name="cpe_client_id"
                        placeholder="client_id de la app OAuth2 SUNAT"
                        value="{{ $greenter_config->cpe_client_id ?? '' }}">
                    <small class="text-muted">Obtenido en api.sunat.gob.pe → Mis Aplicaciones</small>
                    <span class="cpe_client_id_error text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-key text-success"></i> Client Secret CPE</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="cpe_client_secret"
                            id="cpe_client_secret"
                            placeholder="client_secret de la app OAuth2 SUNAT"
                            value="{{ $greenter_config->cpe_client_secret ?? '' }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('cpe_client_secret', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <span class="cpe_client_secret_error text-danger small"></span>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-lg-6">

            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                <i class="fas fa-user-lock"></i> Acceso SOL / Certificado
            </h6>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-user text-success"></i> Usuario SOL</label>
                    <input type="text" class="form-control" name="sol_user" maxlength="100"
                        placeholder="Usuario SOL (RUC + usuario)"
                        value="{{ $greenter_config->sol_user ?? '' }}">
                    <span class="sol_user_error text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label><i class="fas fa-lock text-danger"></i> Contraseña SOL</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="sol_pass"
                            id="sol_pass" maxlength="100" placeholder="Contraseña SOL"
                            value="{{ $greenter_config->sol_pass ?? '' }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('sol_pass', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <span class="sol_pass_error text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-certificate text-info"></i> Certificado Digital
                        <small class="text-muted">(.pfx / .p12 / .pem)</small>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="certificado"
                            id="certificado" accept=".pfx,.p12,.pem">
                        <label class="custom-file-label" for="certificado" id="lbl-certificado">
                            @if(!empty($greenter_config->nombre_certificado))
                                {{ $greenter_config->nombre_certificado }} (actual)
                            @else
                                Seleccionar archivo...
                            @endif
                        </label>
                    </div>
                    <small class="text-muted">Dejar vacío para mantener el certificado actual.</small>
                    <span class="certificado_error text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-unlock-alt text-warning"></i> Contraseña del Certificado
                        <span id="span-contra-requerida" class="badge badge-danger ml-1" style="display:none;">Obligatorio</span>
                        <small class="text-muted" id="span-contra-opcional">(opcional para .pem)</small>
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="contra_certificado"
                            id="contra_certificado" placeholder="Requerida para .pfx / .p12 — vacío para .pem">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('contra_certificado', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <span class="contra_certificado_error text-danger small"></span>
                </div>
            </div>

            @if(!empty($greenter_config->ruta_certificado))
                <div class="alert alert-info py-2" style="font-size:0.82rem;">
                    <i class="fas fa-info-circle"></i>
                    Certificado actual: <code>{{ $greenter_config->ruta_certificado }}</code>
                </div>
            @endif

        </div>

    </div>

</form>
