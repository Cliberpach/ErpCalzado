<form enctype="multipart/form-data" method="POST" id="crear_rol">
    @csrf

    <div class="row">

        {{-- CARD DATOS GENERALES --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield mr-2"></i>
                        Gestión de Rol
                    </h5>
                </div>

                <div class="card-body">

                    {{-- TITULO --}}
                    <div class="mb-4">
                        <h5 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-info-circle text-success mr-1"></i>
                            Datos Generales
                        </h5>
                        <small class="text-muted">
                            Complete la información principal del rol.
                        </small>
                    </div>

                    {{-- NOMBRE / SLUG --}}
                    <div class="form-row">

                        <div class="form-group col-lg-6 col-md-12">
                            <label class="required font-weight-bold">
                                <i class="fas fa-tag text-success mr-1"></i>
                                Nombre
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   maxlength="50"
                                   required
                                   value="{{ old('name') ? old('name') : $role->name }}"
                                   class="form-control text-uppercase {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   placeholder="Ingrese nombre del rol">

                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group col-lg-6 col-md-12">
                            <label class="required font-weight-bold">
                                <i class="fas fa-link text-success mr-1"></i>
                                Slug
                            </label>

                            <input type="text"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug') ? old('slug') : $role->slug }}"
                                   class="form-control text-uppercase {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                                   placeholder="Ejemplo: administrador.general">

                            @if ($errors->has('slug'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('slug') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- DESCRIPCION --}}
                    <div class="form-group">
                        <label class="required font-weight-bold">
                            <i class="fas fa-align-left text-success mr-1"></i>
                            Descripción
                        </label>

                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  required
                                  class="form-control text-uppercase {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                  placeholder="Ingrese descripción del rol">{{ old('description') ? old('description') : $role->description }}</textarea>

                        @if ($errors->has('description'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- CONFIGURACIONES --}}
                    <div class="row mt-4">

                        {{-- FULL ACCESS --}}
                        <div class="col-lg-6 col-md-12 mb-3">
                            <div class="border rounded p-3 h-100 bg-light">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-2">
                                        <i class="fas fa-unlock-alt fa-lg text-success"></i>
                                    </div>

                                    <div>
                                        <h6 class="mb-0 font-weight-bold">
                                            Full Access
                                        </h6>

                                        <small class="text-muted">
                                            Acceso completo al sistema
                                        </small>
                                    </div>
                                </div>

                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio"
                                           id="full-access-si"
                                           name="full-access"
                                           value="SI"
                                           class="custom-control-input"
                                           {{ old('full-access') ? (old('full-access') == 'SI' ? 'checked' : '') : ($role['full-access'] == 'SI' ? 'checked' : '') }}>

                                    <label class="custom-control-label"
                                           for="full-access-si">
                                        SI
                                    </label>
                                </div>

                                <div class="custom-control custom-radio">
                                    <input type="radio"
                                           id="full-access-no"
                                           name="full-access"
                                           value="NO"
                                           class="custom-control-input"
                                           @if (old('full-access') == null && $role['full-access'] == null) checked @endif
                                           {{ old('full-access') ? (old('full-access') == 'NO' ? 'checked' : '') : ($role['full-access'] == 'NO' ? 'checked' : '') }}>

                                    <label class="custom-control-label"
                                           for="full-access-no">
                                        NO
                                    </label>
                                </div>

                            </div>
                        </div>

                        {{-- PUNTO VENTA --}}
                        <div class="col-lg-6 col-md-12 mb-3">
                            <div class="border rounded p-3 h-100 bg-light">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-2">
                                        <i class="fas fa-cash-register fa-lg text-info"></i>
                                    </div>

                                    <div>
                                        <h6 class="mb-0 font-weight-bold">
                                            Punto de Venta
                                        </h6>

                                        <small class="text-muted">
                                            Acceso a cajas aperturadas
                                        </small>
                                    </div>
                                </div>

                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio"
                                           id="punto-venta-si"
                                           name="punto-venta"
                                           value="SI"
                                           class="custom-control-input"
                                           {{ old('punto-venta') ? (old('punto-venta') == 'SI' ? 'checked' : '') : ($role['punto-venta'] == 'SI' ? 'checked' : '') }}>

                                    <label class="custom-control-label"
                                           for="punto-venta-si">
                                        SI
                                    </label>
                                </div>

                                <div class="custom-control custom-radio">
                                    <input type="radio"
                                           id="punto-venta-no"
                                           name="punto-venta"
                                           value="NO"
                                           class="custom-control-input"
                                           @if (old('punto-venta') == null && $role['punto-venta'] == null) checked @endif
                                           {{ old('punto-venta') ? (old('punto-venta') == 'NO' ? 'checked' : '') : ($role['punto-venta'] == 'NO' ? 'checked' : '') }}>

                                    <label class="custom-control-label"
                                           for="punto-venta-no">
                                        NO
                                    </label>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- TABLA PERMISOS --}}
        <div class="col-12">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-key mr-2"></i>
                        Gestión de Permisos
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        @include('seguridad.roles.table.tbl_permissions')
                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- FOOTER --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">

                    <div class="text-muted">
                        <i class="fas fa-exclamation-circle text-danger mr-1"></i>

                        <small>
                            Los campos marcados con
                            <span class="text-danger font-weight-bold">*</span>
                            son obligatorios.
                        </small>
                    </div>

                </div>

                <div class="col-lg-6 col-md-12 text-lg-right text-center">

                    <a href="{{ route('seguridad.role.index') }}"
                       id="btn_cancelar"
                       class="btn btn-outline-secondary px-4">

                        <i class="fas fa-arrow-left mr-1"></i>
                        Regresar
                    </a>

                    <button type="submit"
                            id="btn_grabar"
                            class="btn btn-success px-4">

                        <i class="fas fa-save mr-1"></i>
                        Grabar Rol
                    </button>

                </div>

            </div>

        </div>
    </div>

</form>
