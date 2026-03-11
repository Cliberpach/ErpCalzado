<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="font-weight-bold">TIPO DOCUMENTO</label>
        <select required name="type_identity_document" class="form-control" id="type_identity_document"
            data-placeholder="Seleccionar" onchange="changeTipoDoc()">
            <option></option>
            @foreach ($tipos_documento as $tipo_documento)
                <option @if (isset($cliente) && $cliente->tipo_documento_id == $tipo_documento->id) selected @endif value="{{ $tipo_documento->id }}">
                    {{ $tipo_documento->simbolo }}</option>
            @endforeach
        </select>
        <span class="type_identity_document_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="required_field font-weight-bold">Nro Doc</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button id="btn_consultar_documento" @if (isset($cliente) && !in_array($cliente->tipo_documento_id, [6, 8])) disabled @endif
                    class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <input required @if (isset($cliente) && !in_array($cliente->tipo_documento_id, [6, 8])) readonly @endif id="nro_document" name="nro_document"
                type="text" class="form-control" placeholder="Nro de Documento"
                @if (isset($cliente)) value="{{ $cliente->documento }}"@else value="" @endif>
        </div>
        <span class="nro_document_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="font-weight-bold">TIPO CLIENTE</label>
        <select required name="type_customer" class="form-control" id="type_customer" data-placeholder="Seleccionar">
            <option></option>
            @foreach ($tipos_clientes as $tipo_cliente)
                <option @if ($tipo_cliente->nombre == 'UNIDAD' && !isset($cliente)) selected @endif value="{{ $tipo_cliente->id }}"
                    @if (isset($cliente) && $cliente->tipo_cliente_id == $tipo_cliente->id) selected @endif>
                    {{ $tipo_cliente->nombre }}</option>
            @endforeach
        </select>
        <span class="type_customer_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="required_field font-weight-bold">Nombre</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
            </div>
            <input required id="name" maxlength="160" name="name" type="text" class="form-control"
                placeholder="Nombre"
                @if (isset($cliente)) value="{{ $cliente->nombre }}"
                        @else
                            value="" @endif>
        </div>
        <span class="name_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="font-weight-bold">Dirección</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-address-book"></i>
                </span>
            </div>
            <input maxlength="160" id="address" name="address" type="text" class="form-control"
                placeholder="Dirección"
                @if (isset($cliente)) value="{{ $cliente->direccion }}"
                        @else
                            value="" @endif>
        </div>
        <span class="address_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="font-weight-bold">Teléfono</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-mobile-alt"></i>
                </span>
            </div>
            <input maxlength="20" id="phone" name="phone" type="text" class="form-control"
                placeholder="Teléfono"
                @if (isset($cliente)) value="{{ $cliente->telefono_movil }}"
                        @else
                            value="" @endif>
        </div>
        <span class="phone_error msgError text-danger"></span>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
        <label class="font-weight-bold">Correo</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-at"></i>
                </span>
            </div>
            <input maxlength="160" id="email" name="email" type="email" class="form-control"
                placeholder="Correo"
                @if (isset($cliente)) value="{{ $cliente->correo_electronico }}"
                        @else
                            value="" @endif>
        </div>
        <span class="email_error msgError text-danger"></span>
    </div>

    <div class="col-12"></div>

    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
        <label class="font-weight-bold">DEPARTAMENTO</label>
        <select required name="department" class="form-control" id="department" data-placeholder="Seleccionar">
            <option value=""></option>
            @foreach ($departments as $departamento)
                <option value="{{ $departamento->id }}">
                    {{ $departamento->nombre }}
                </option>
            @endforeach
        </select>
        <span class="department_error msgError text-danger"></span>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
        <label class="font-weight-bold">PROVINCIA</label>
        <select required name="province" class="form-control" id="province" data-placeholder="Seleccionar">
            <option value=""></option>
        </select>
        <span class="province_error msgError text-danger"></span>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
        <label class="font-weight-bold">DISTRITO</label>
        <select required name="district" class="form-control" id="district" data-placeholder="Seleccionar">
            <option value=""></option>
        </select>
        <span class="district_error msgError text-danger"></span>
    </div>

</div>
