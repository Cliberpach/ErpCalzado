<style>
    .title-form {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 5px;
    }

    .subtitle-form {
        color: #6b7280;
        font-size: 14px;
    }

    .section-divider {
        border: none;
        height: 3px;
        border-radius: 20px;
        background: linear-gradient(to right, #2563eb, #7c3aed);
        margin-bottom: 25px;
    }

    .input-group {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        transition: all .2s ease;
    }

    .input-group:focus-within {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(37, 99, 235, .15);
    }

    .input-group-text {
        border: none;
        color: white;
        width: 48px;
        justify-content: center;
    }

    .form-control {
        border-left: none;
        min-height: 46px;
    }

    .form-control:focus {
        box-shadow: none;
    }

    .form-select {
        min-height: 46px;
        border-radius: 12px;
    }

    .label-title {
        font-weight: 700;
        margin-bottom: 8px;
        color: #1f2937;
    }

    .field-info {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #6b7280;
    }

    .required::after {
        content: " *";
        color: #dc2626;
    }

    .msgError {
        font-size: 13px;
        font-weight: 600;
    }

    .icon-primary {
        background: #2563eb;
    }

    .icon-success {
        background: #16a34a;
    }

    .icon-danger {
        background: #dc2626;
    }

    .icon-warning {
        background: #f59e0b;
    }

    .icon-info {
        background: #0891b2;
    }

    .icon-purple {
        background: #7c3aed;
    }

    .icon-dark {
        background: #374151;
    }

    .img-preview-box {
        min-height: 320px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        transition: all .2s ease;
    }

    .img-preview-box:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .btn-remove-image {
        border-radius: 10px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-save {
        border-radius: 12px;
        padding: 11px 28px;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, .25);
    }
</style>

<form action="" id="formActualizarSede" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">

        <!-- LEFT -->
        <div class="col-lg-6">

            <!-- NOMBRE -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-signature text-primary mr-1"></i>
                    NOMBRE
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-primary">
                        <i class="fas fa-file-signature"></i>
                    </span>

                    <input required maxlength="160" name="nombre" id="nombre" type="text" class="form-control"
                        placeholder="Ingrese nombre de la sede" value="{{ $sede->nombre }}">
                </div>

                <small class="field-info">
                    Máximo 160 caracteres
                </small>

                <span class="nombre_error msgError text-danger"></span>
            </div>

            <!-- DIRECCION -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-map-marked-alt text-danger mr-1"></i>
                    DIRECCIÓN
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-danger">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>

                    <input required maxlength="191" name="direccion" id="direccion" type="text" class="form-control"
                        placeholder="Ingrese dirección" value="{{$sede->direccion}}">
                </div>

                <small class="field-info">
                    Máximo 191 caracteres
                </small>

                <span class="direccion_error msgError text-danger"></span>
            </div>

            <!-- TELEFONO -->
            <div class="mb-3">
                <label class="label-title">
                    <i class="fas fa-phone-alt text-success mr-1"></i>
                    TELÉFONO
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-success">
                        <i class="fas fa-phone"></i>
                    </span>

                    <input maxlength="191" name="telefono" id="telefono" type="text" class="form-control"
                        placeholder="Ingrese teléfono" value="{{$sede->telefono}}">
                </div>

                <small class="field-info">
                    Máximo 191 caracteres
                </small>

                <span class="telefono_error msgError text-danger"></span>
            </div>

            <!-- CORREO -->
            <div class="mb-3">
                <label class="label-title">
                    <i class="fas fa-envelope text-warning mr-1"></i>
                    CORREO
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-warning">
                        <i class="fas fa-envelope-open-text"></i>
                    </span>

                    <input maxlength="191" name="correo" id="correo" type="email" class="form-control"
                        placeholder="correo@empresa.com" value="{{$sede->correo}}">
                </div>

                <small class="field-info">
                    Máximo 191 caracteres
                </small>

                <span class="correo_error msgError text-danger"></span>
            </div>

            <!-- DEPARTAMENTO -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-globe-americas text-info mr-1"></i>
                    DEPARTAMENTO
                </label>

                <select required name="departamento" class="form-select select2_form" id="department"
                    onchange="changeDepartment(this.value)">

                    <option></option>

                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">
                            {{ $departamento->nombre }}
                        </option>
                    @endforeach
                </select>

                <span class="departamento_error msgError text-danger"></span>
            </div>

            <!-- PROVINCIA -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-city text-purple mr-1"></i>
                    PROVINCIA
                </label>

                <select required name="provincia" class="form-select select2_form" id="province"
                    onchange="changeProvince(this.value)">

                    <option></option>
                </select>

                <span class="provincia_error msgError text-danger"></span>
            </div>

            <!-- DISTRITO -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-map text-success mr-1"></i>
                    DISTRITO
                </label>

                <select required name="distrito" class="form-select select2_form" id="district">

                    <option></option>
                </select>

                <span class="distrito_error msgError text-danger"></span>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-6">

            <!-- IMAGEN -->
            <div class="mb-3">

                <label class="label-title d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-image text-primary mr-1"></i>
                        IMAGEN
                    </span>
                </label>

                <input id="img_empresa" name="img_empresa" type="file">

                <small class="field-info">
                    Formatos permitidos: JPG, JPEG, WEBP, AVIF | Máximo 2MB
                </small>

            </div>

            <!-- URBANIZACION -->
            <div class="mb-3">
                <label class="label-title">
                    <i class="fas fa-road text-info mr-1"></i>
                    URBANIZACIÓN
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-info">
                        <i class="fas fa-road"></i>
                    </span>

                    <input maxlength="200" name="urbanizacion" id="urbanizacion" type="text"
                        class="form-control" placeholder="Ingrese urbanización" value="{{$sede->urbanizacion}}">
                </div>

                <small class="field-info">
                    Máximo 200 caracteres
                </small>

                <span class="urbanizacion_error msgError text-danger"></span>
            </div>

            <!-- CODIGO LOCAL -->
            <div class="mb-3">
                <label class="required label-title">
                    <i class="fas fa-barcode text-danger mr-1"></i>
                    CÓDIGO LOCAL
                </label>

                <div class="input-group">
                    <span class="input-group-text icon-purple">
                        <i class="fas fa-hashtag"></i>
                    </span>

                    <input required maxlength="191" name="codigo_local" id="codigo_local" type="text"
                        class="form-control" placeholder="Ingrese código local" value="{{$sede->codigo_local}}">
                </div>

                <small class="field-info">
                    Máximo 191 caracteres
                </small>

                <span class="codigo_local_error msgError text-danger"></span>
            </div>

        </div>

        <!-- BUTTON -->
        <div class="col-12 mt-4 d-flex justify-content-end">
            <button class="btn btn-primary btn-save">
                <i class="fas fa-save mr-2"></i>
                ACTUALIZAR
            </button>
        </div>

    </div>

</form>
