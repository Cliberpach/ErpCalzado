<form action="{{ route('mantenimiento.empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data"
    id="form_edit_company">

    <div class="panel-body">
        <div class="row">

            <!-- ═══════════ COLUMNA IZQUIERDA ═══════════ -->
            <div class="col-lg-6">

                <!-- SECCIÓN: Identificación Fiscal -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-id-card"></i> Identificación Fiscal
                </h6>

                <!-- RUC -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="required">
                            <i class="fas fa-id-card text-primary"></i> RUC
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="ruc" id="ruc" maxlength="11"
                                value="{{ old('ruc', $empresa->ruc) }}" required>
                            <div class="input-group-append">
                                <button type="button" onclick="consultarRuc()" class="btn btn-success">
                                    <i class="fas fa-search"></i> Sunat
                                </button>
                            </div>
                        </div>
                        <span class="msgError ruc_error"></span>
                    </div>
                </div>

                <!-- RAZON SOCIAL -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="required">
                            <i class="fas fa-file-signature text-primary"></i> Razón Social
                        </label>
                        <input required type="text" class="form-control" name="razon_social" id="razon_social"
                            value="{{ old('razon_social', $empresa->razon_social) }}">
                        <span class="msgError razon_social_error"></span>
                    </div>
                </div>

                <!-- ABREVIADA + UBIGEO EMPRESA -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label>
                            <i class="fas fa-compress text-secondary"></i> Razón Social Abreviada
                        </label>
                        <input required type="text" class="form-control" name="razon_social_abreviada"
                            value="{{ old('razon_social_abreviada', $empresa->razon_social_abreviada) }}">
                        <span class="msgError razon_social_abreviada_error"></span>
                    </div>
                </div>

                <!-- SECCIÓN: Ubicación y Dirección -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-map-marked-alt"></i> Ubicación y Dirección
                </h6>

                <!-- DIRECCION FISCAL -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="required">
                            <i class="fas fa-map-marked-alt text-info"></i> Dirección Fiscal
                        </label>
                        <textarea required class="form-control" name="direccion_fiscal" id="direccion_fiscal">{{ old('direccion_fiscal', $empresa->direccion_fiscal) }}</textarea>
                        <span class="msgError direccion_fiscal_error"></span>
                    </div>
                </div>

                <!-- DIRECCION PLANTA -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="required">
                            <i class="fas fa-industry text-warning"></i> Dirección de Planta
                        </label>
                        <textarea class="form-control" name="direccion_llegada" id="direccion_llegada">{{ old('direccion_llegada', $empresa->direccion_llegada) }}</textarea>
                        <span class="msgError direccion_llegada_error"></span>
                    </div>
                </div>

                <!-- DEPARTAMENTO / PROVINCIA / DISTRITO -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="required">
                            <i class="fas fa-map text-primary"></i> Departamento
                        </label>
                        <select class="form-control" name="departamento" id="departamento">
                            <option value=""></option>
                            @foreach ($departments as $departamento)
                                <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="msgError departamento_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="required">
                            <i class="fas fa-map-pin text-success"></i> Provincia
                        </label>
                        <select class="form-control" name="provincia" id="provincia"></select>
                        <span class="msgError provincia_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="required">
                            <i class="fas fa-map-marker text-danger"></i> Distrito
                        </label>
                        <select class="form-control" name="distrito" id="distrito"></select>
                        <span class="msgError distrito_error"></span>
                    </div>
                </div>

                <!-- URBANIZACION / COD LOCAL / UBIGEO -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label><i class="fas fa-map text-secondary"></i> Urbanización</label>
                        <input type="text" class="form-control" name="urbanizacion"
                            value="{{ old('urbanizacion', $empresa->urbanizacion) }}">
                        <span class="msgError urbanizacion_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label><i class="fas fa-hashtag text-dark"></i> Cod. Local</label>
                        <input type="text" class="form-control" name="cod_local"
                            value="{{ old('cod_local', $empresa->cod_local) }}">
                        <span class="msgError cod_local_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label><i class="fas fa-barcode text-info"></i> Ubigeo</label>
                        <input type="text" class="form-control" name="ubigeo"
                            value="{{ old('ubigeo', $empresa->ubigeo) }}">
                        <span class="msgError ubigeo_error"></span>
                    </div>
                </div>

                <!-- SECCIÓN: Contacto -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-envelope"></i> Contacto
                </h6>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>
                            <i class="fas fa-envelope text-info"></i> Correo
                        </label>
                        <input type="email" class="form-control" name="correo"
                            value="{{ old('correo', $empresa->correo) }}">
                        <span class="msgError correo_error"></span>
                    </div>
                    <div class="col-md-3">
                        <label>
                            <i class="fas fa-phone text-secondary"></i> Teléfono
                        </label>
                        <input type="text" class="form-control" name="telefono"
                            value="{{ old('telefono', $empresa->telefono) }}">
                        <span class="msgError telefono_error"></span>
                    </div>
                    <div class="col-md-3">
                        <label>
                            <i class="fas fa-mobile-alt text-success"></i> Celular
                        </label>
                        <input type="text" class="form-control" name="celular"
                            value="{{ old('celular', $empresa->celular) }}">
                        <span class="msgError celular_error"></span>
                    </div>
                </div>

            </div>
            <!-- FIN COLUMNA IZQUIERDA -->

            <!-- ═══════════ COLUMNA DERECHA ═══════════ -->
            <div class="col-lg-6">

                <!-- SECCIÓN: Logo y Redes Sociales -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-share-alt"></i> Logo y Redes Sociales
                </h6>

                <!-- LOGO -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>
                            <i class="fas fa-image text-primary"></i> Logo de la Empresa
                        </label>
                        <input type="file" class="" id="logo" name="logo" accept="image/*">
                        @if ($empresa->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo"
                                    style="max-height: 80px;">
                            </div>
                        @endif
                        <span class="msgError logo_error"></span>
                    </div>
                </div>

                <!-- FACEBOOK -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>
                            <i class="fab fa-facebook text-primary"></i> Facebook
                        </label>
                        <input type="text" class="form-control" name="facebook"
                            value="{{ old('facebook', $empresa->facebook) }}">
                        <span class="msgError facebook_error"></span>
                    </div>
                </div>

                <!-- INSTAGRAM -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>
                            <i class="fab fa-instagram text-danger"></i> Instagram
                        </label>
                        <input type="text" class="form-control" name="instagram"
                            value="{{ old('instagram', $empresa->instagram) }}">
                        <span class="msgError instagram_error"></span>
                    </div>
                </div>

                <!-- WEB -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>
                            <i class="fas fa-globe text-info"></i> Web
                        </label>
                        <input type="text" class="form-control" name="web"
                            value="{{ old('web', $empresa->web) }}">
                        <span class="msgError web_error"></span>
                    </div>
                </div>

                <!-- SECCIÓN: Representante Legal y SUNARP -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-user-tie"></i> Representante Legal y SUNARP
                </h6>

                <!-- DNI + NOMBRE -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>
                            <i class="fas fa-id-badge text-primary"></i> DNI Representante
                        </label>
                        <input type="text" class="form-control" name="dni_representante"
                            value="{{ old('dni_representante', $empresa->dni_representante) }}">
                        <span class="msgError dni_representante_error"></span>
                    </div>
                    <div class="col-md-6">
                        <label>
                            <i class="fas fa-user-tie text-success"></i> Nombre Representante
                        </label>
                        <input type="text" class="form-control" name="nombre_representante"
                            value="{{ old('nombre_representante', $empresa->nombre_representante) }}">
                        <span class="msgError nombre_representante_error"></span>
                    </div>
                </div>

                <!-- PARTIDA + ASIENTO -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>
                            <i class="fas fa-book text-info"></i> N° Partida
                        </label>
                        <input type="text" class="form-control" name="num_partida"
                            value="{{ old('num_partida', $empresa->num_partida) }}">
                        <span class="msgError num_partida_error"></span>
                    </div>
                    <div class="col-md-6">
                        <label>
                            <i class="fas fa-layer-group text-warning"></i> N° Asiento
                        </label>
                        <input type="text" class="form-control" name="num_asiento"
                            value="{{ old('num_asiento', $empresa->num_asiento) }}">
                        <span class="msgError num_asiento_error"></span>
                    </div>
                </div>

                <!-- SECCIÓN: Configuración -->
                <h6 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-cogs"></i> Configuración
                </h6>

                <!-- IGV / ESTADO / FACTURACION -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label><i class="fas fa-percentage text-success"></i> IGV</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="igv"
                            value="{{ old('igv', $empresa->igv) }}" required>
                        <span class="msgError igv_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label><i class="fas fa-toggle-on text-primary"></i> Estado</label>
                        <select class="form-control" name="estado">
                            <option value="ACTIVO" {{ $empresa->estado == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                            <option value="ANULADO" {{ $empresa->estado == 'ANULADO' ? 'selected' : '' }}>ANULADO</option>
                        </select>
                        <span class="msgError estado_error"></span>
                    </div>
                    <div class="col-md-4">
                        <label><i class="fas fa-file-invoice text-warning"></i> Facturación</label>
                        <select class="form-control" name="estado_fe">
                            <option value="1" {{ $empresa->estado_fe == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ $empresa->estado_fe == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <span class="msgError estado_fe_error"></span>
                    </div>
                </div>


            </div>
            <!-- FIN COLUMNA DERECHA -->

        </div>
    </div>

</form>
