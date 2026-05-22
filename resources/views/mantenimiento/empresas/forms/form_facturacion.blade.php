<form action="" method="POST" enctype="multipart/form-data" id="formFacturacion">

    <div class="row">

        <!-- COLUMNA IZQUIERDA -->
        <div class="col-lg-6">

            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                <i class="fas fa-plug"></i> Credenciales API
            </h6>

            <!-- ID API -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-fingerprint text-primary"></i> ID API Guía de Remisión
                    </label>
                    <textarea class="form-control" name="id_api_guia_remision" rows="3"
                        placeholder="Ingrese el ID de la API..."></textarea>
                </div>
            </div>

            <!-- CLAVE API -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-key text-warning"></i> Clave API Guía de Remisión
                    </label>
                    <textarea class="form-control" name="clave_api_guia_remision" rows="3"
                        placeholder="Ingrese la clave de la API..."></textarea>
                </div>
            </div>

        </div>

        <!-- COLUMNA DERECHA -->
        <div class="col-lg-6">

            <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                <i class="fas fa-user-lock"></i> Acceso SOL / Certificado
            </h6>

            <!-- SOL USER -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-user text-success"></i> Usuario SOL
                    </label>
                    <input type="text" class="form-control" name="sol_user"
                        maxlength="100" placeholder="Usuario SOL (RUC + usuario)">
                </div>
            </div>

            <!-- SOL PASS -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-lock text-danger"></i> Contraseña SOL
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="sol_pass"
                            maxlength="100" placeholder="Contraseña SOL" id="sol_pass">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="
                                    var i = document.getElementById('sol_pass');
                                    i.type = i.type === 'password' ? 'text' : 'password';
                                    this.querySelector('i').classList.toggle('fa-eye');
                                    this.querySelector('i').classList.toggle('fa-eye-slash');
                                ">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CERTIFICADO -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>
                        <i class="fas fa-certificate text-info"></i> Certificado Digital
                        <small class="text-muted">(archivo .pfx / .p12)</small>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="certificado"
                            id="certificado" accept=".pfx,.p12,.pem,.cer">
                        <label class="custom-file-label" for="certificado">
                            Seleccionar archivo...
                        </label>
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>

<script>
    // Muestra el nombre del archivo seleccionado en el custom-file-input
    document.getElementById('certificado').addEventListener('change', function () {
        var fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
        this.nextElementSibling.textContent = fileName;
    });
</script>
