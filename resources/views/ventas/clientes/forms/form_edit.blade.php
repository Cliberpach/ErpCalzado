<form action="" id="formActualizarCliente" method="post">
    <div class="row">
        @csrf

        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label class="required_field font-weight-bold">TIPO DOCUMENTO</label>
            <select required name="type_identity_document" class="form-control select2_form" id="type_identity_document"
                data-placeholder="Seleccionar" onchange="changeTipoDoc()">
                <option></option>
                @foreach ($tipos_documento as $type_identity_document)
                    <option @if ($customer->tipo_documento_id == $type_identity_document->id) selected @endif value="{{ $type_identity_document->id }}">
                        {{ $type_identity_document->simbolo }}
                    </option>
                @endforeach
            </select>
            <span class="type_identity_document_error msgError text-danger"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label class="required_field font-weight-bold">Nro Doc</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button @if ($customer->tipo_documento_id != 6 && $customer->tipo_documento_id != 8) disabled @endif id="btn_consultar_documento"
                        class="btn btn-primary" type="button">
                        <i class="fas fa-search text-white"></i>
                    </button>
                </div>
                <input value="{{ $customer->documento }}" required id="nro_document" name="nro_document"
                    type="text" class="form-control" placeholder="Nro de Documento">
            </div>
            <span class="nro_document_error msgError text-danger"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label class="font-weight-bold">TIPO CLIENTE</label>
            <select required name="type_customer" class="form-control" id="type_customer" data-placeholder="Seleccionar">
                <option></option>
                @foreach ($tipos_clientes as $tipo_cliente)
                    <option
                    @if ($customer->tipo_cliente_id == $tipo_cliente->id)
                        selected
                    @endif
                    value="{{ $tipo_cliente->id }}">{{ $tipo_cliente->nombre }}</option>
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
                <input value="{{ $customer->nombre }}" required id="name" maxlength="160" name="name"
                    type="text" class="form-control" placeholder="Nombre">
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
                <input value="{{ $customer->direccion }}" maxlength="160" id="address" name="address" type="text"
                    class="form-control" placeholder="Dirección">
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
                <input value="{{ $customer->telefono_movil }}" maxlength="20" id="phone" name="phone" type="text"
                    class="form-control" placeholder="Teléfono">
            </div>
            <span class="phone_error msgError text-danger"></span>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 pb-2">
            <label class="font-weight-bold">Email</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-at"></i>
                    </span>
                </div>
                <input value="{{ $customer->email }}" maxlength="160" id="email" name="email" type="email"
                    class="form-control" placeholder="Email">
            </div>
            <span class="email_error msgError text-danger"></span>
        </div>

        <div class="col-12"></div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <label class="font-weight-bold">DEPARTAMENTO</label>
            <select name="department" class="form-control select2_form" id="department" data-placeholder="Seleccionar">
                <option></option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->nombre }}
                    </option>
                @endforeach
            </select>
            <span class="department_error msgError text-danger"></span>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <label class="font-weight-bold">PROVINCIA</label>
            <select name="province" class="form-control select2_form" id="province" data-placeholder="Seleccionar">
                <option></option>
            </select>
            <span class="province_error msgError text-danger"></span>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <label class="font-weight-bold">DISTRITO</label>
            <select name="district" class="form-control select2_form" id="district" data-placeholder="Seleccionar">
                <option></option>
            </select>
            <span class="district_error msgError text-danger"></span>
        </div>

    </div>
</form>
