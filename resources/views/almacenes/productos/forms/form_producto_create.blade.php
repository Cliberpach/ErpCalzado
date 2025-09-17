<form action="{{ route('almacenes.producto.store') }}" method="POST" id="form_registrar_producto">
    @csrf

    <div class="row">
        <div class="col-lg-6 col-xs-12 b-r">
            <h4><b>Datos Generales</b></h4>
            <input class="d-none" type="text" id="coloresJSON" name="coloresJSON">

            <div class="row">

                <div class="col-12 mb-3">
                    <label class="required">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre"
                        class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                        value="{{ old('nombre') }}" maxlength="191" onkeyup="return mayus(this)" required>
                    <span style="font-weight: bold;color:red;" class="nombre_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="required">Categoria</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_categoria"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select required id="categoria" name="categoria"
                        class="select2_form form-control {{ $errors->has('familia') ? ' is-invalid' : '' }}">
                        <option></option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->descripcion }}</option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="categoria_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12">
                    <label class="required">Marca</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_marca"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select id="marca" name="marca"
                        class="select2_form form-control {{ $errors->has('marca') ? ' is-invalid' : '' }}" required
                        value="{{ old('marca') }}">
                        <option></option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}" {{ old('marca') == $marca->id ? 'selected' : '' }}>
                                {{ $marca->marca }}</option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="marca_error msgErrorProducto"></span>
                </div>

            </div>
        </div>

        <div class="col-lg-6 col-xs-12">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="required">Modelo</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_modelo"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select required id="modelo" name="modelo"
                        class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}">
                        <option></option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->id }}"
                                {{ old('modelo') == $modelo->id ? 'selected' : '' }}>{{ $modelo->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="modelo_error msgErrorProducto"></span>
                </div>

                <div class="col-12"></div>

                <div class="col-lg-4 col-12 mb-3">
                    <label class="required form-label">PRECIO 1</label>
                    <input required class="form-control  {{ $errors->has('precio1') ? ' is-invalid' : '' }}"
                        type="number" step="0.01" inputmode="decimal" id="precio1" name="precio1"
                        value="{{ old('precio1') }}" />
                    <span style="font-weight: bold;color:red;" class="precio1_error msgErrorProducto"></span>
                </div>
                <div class="col-lg-4 col-12 mb-3">
                    <label class="required form-label">PRECIO 2</label>
                    <input required class="form-control  {{ $errors->has('precio2') ? ' is-invalid' : '' }}"
                        type="number" step="0.01" inputmode="decimal" id="precio2" name="precio2"
                        value="{{ old('precio2') }}" />
                    <span style="font-weight: bold;color:red;" class="precio2_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label class="required form-label">PRECIO 3</label>
                    <input required class="form-control {{ $errors->has('precio3') ? ' is-invalid' : '' }}"
                        type="number" step="0.01" inputmode="decimal" id="precio3" name="precio3"
                        value="{{ old('precio3') }}" />
                    <span style="font-weight: bold;color:red;" class="precio3_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="required">COSTO</label>
                    <input class="form-control {{ $errors->has('costo') ? ' is-invalid' : '' }}" type="number"
                        step="0.01" inputmode="decimal" id="costo" name="costo"
                        value="{{ old('costo') }}" />
                    <span style="font-weight: bold;color:red;" class="precio3_error msgErrorProducto"></span>
                </div>

            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><b>IMÁGENES</b></h4>
                </div>
                <div class="panel-body">
                    <div class="row text-center">

                        <!-- Imagen 1 -->
                        <div class="col-md-2 col-6 mb-3">
                            <div class="position-relative">
                                <label for="imagen1" class="d-block" style="cursor:pointer;">
                                    <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                        style="height:150px;">
                                        <span id="plus1" style="font-size:2rem; color:#aaa;">+</span>
                                        <a id="link1" href="" data-fancybox="galeria" class="d-none">
                                            <img id="preview1" class="img-fluid img-preview">
                                        </a>
                                    </div>
                                </label>
                                <button type="button" id="remove1"
                                    class="btn btn-sm btn-danger position-absolute d-none" style="top:5px; right:5px;"
                                    onclick="removeImage('imagen1','preview1','plus1','remove1','filename1','link1')">✖</button>
                                <small id="filename1" class="text-muted d-block mt-1 text-truncate"></small>
                            </div>
                            <input type="file" class="d-none" id="imagen1" name="imagen1"
                                accept=".jpg,.jpeg,.webp,.avif"
                                onchange="previewImage(event, 'preview1', 'plus1','remove1','filename1','link1')">
                        </div>

                        <!-- Imagen 2 -->
                        <div class="col-md-2 col-6 mb-3">
                            <div class="position-relative">
                                <label for="imagen2" class="d-block" style="cursor:pointer;">
                                    <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                        style="height:150px;">
                                        <span id="plus2" style="font-size:2rem; color:#aaa;">+</span>
                                        <a id="link2" href="" data-fancybox="galeria" class="d-none">
                                            <img id="preview2" class="img-fluid img-preview">
                                        </a>
                                    </div>
                                </label>
                                <button type="button" id="remove2"
                                    class="btn btn-sm btn-danger position-absolute d-none" style="top:5px; right:5px;"
                                    onclick="removeImage('imagen2','preview2','plus2','remove2','filename2','link2')">✖</button>
                                <small id="filename2" class="text-muted d-block mt-1 text-truncate"></small>
                            </div>
                            <input type="file" class="d-none" id="imagen2" name="imagen2"
                                accept=".jpg,.jpeg,.webp,.avif"
                                onchange="previewImage(event, 'preview2', 'plus2','remove2','filename2','link2')">
                        </div>

                        <!-- Imagen 3 -->
                        <div class="col-md-2 col-6 mb-3">
                            <div class="position-relative">
                                <label for="imagen3" class="d-block" style="cursor:pointer;">
                                    <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                        style="height:150px;">
                                        <span id="plus3" style="font-size:2rem; color:#aaa;">+</span>
                                        <a id="link3" href="" data-fancybox="galeria" class="d-none">
                                            <img id="preview3" class="img-fluid img-preview">
                                        </a>
                                    </div>
                                </label>
                                <button type="button" id="remove3"
                                    class="btn btn-sm btn-danger position-absolute d-none" style="top:5px; right:5px;"
                                    onclick="removeImage('imagen3','preview3','plus3','remove3','filename3','link3')">✖</button>
                                <small id="filename3" class="text-muted d-block mt-1 text-truncate"></small>
                            </div>
                            <input type="file" class="d-none" id="imagen3" name="imagen3"
                                accept=".jpg,.jpeg,.webp,.avif"
                                onchange="previewImage(event, 'preview3', 'plus3','remove3','filename3','link3')">
                        </div>

                        <!-- Imagen 4 -->
                        <div class="col-md-2 col-6 mb-3">
                            <div class="position-relative">
                                <label for="imagen4" class="d-block" style="cursor:pointer;">
                                    <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                        style="height:150px;">
                                        <span id="plus4" style="font-size:2rem; color:#aaa;">+</span>
                                        <a id="link4" href="" data-fancybox="galeria" class="d-none">
                                            <img id="preview4" class="img-fluid img-preview">
                                        </a>
                                    </div>
                                </label>
                                <button type="button" id="remove4"
                                    class="btn btn-sm btn-danger position-absolute d-none" style="top:5px; right:5px;"
                                    onclick="removeImage('imagen4','preview4','plus4','remove4','filename4','link4')">✖</button>
                                <small id="filename4" class="text-muted d-block mt-1 text-truncate"></small>
                            </div>
                            <input type="file" class="d-none" id="imagen4" name="imagen4"
                                accept=".jpg,.jpeg,.webp,.avif"
                                onchange="previewImage(event, 'preview4', 'plus4','remove4','filename4','link4')">
                        </div>

                        <!-- Imagen 5 -->
                        <div class="col-md-2 col-6 mb-3">
                            <div class="position-relative">
                                <label for="imagen5" class="d-block" style="cursor:pointer;">
                                    <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                        style="height:150px;">
                                        <span id="plus5" style="font-size:2rem; color:#aaa;">+</span>
                                        <a id="link5" href="" data-fancybox="galeria" class="d-none">
                                            <img id="preview5" class="img-fluid img-preview">
                                        </a>
                                    </div>
                                </label>
                                <button type="button" id="remove5"
                                    class="btn btn-sm btn-danger position-absolute d-none" style="top:5px; right:5px;"
                                    onclick="removeImage('imagen5','preview5','plus5','remove5','filename5','link5')">✖</button>
                                <small id="filename5" class="text-muted d-block mt-1 text-truncate"></small>
                            </div>
                            <input type="file" class="d-none" id="imagen5" name="imagen5"
                                accept=".jpg,.jpeg,.webp,.avif"
                                onchange="previewImage(event, 'preview5', 'plus5','remove5','filename5','link5')">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">

        <div class="col-12">

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><b>ASIGNAR COLORES (Se asignan por almacén)</b></h4>
                </div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-12">
                            <div class="row">

                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 d-flex align-items-center">
                                    <a style="padding:2.5px 4px;" data-toggle="modal"
                                        data-target="#modal_crear_color" class="btn btn-primary" href="#">
                                        NUEVO COLOR <i class="fas fa-plus"></i>
                                    </a>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label style="font-weight: bold;">Almacén</label>

                                    <select id="almacen" name="almacen"
                                        class="select2_form form-control {{ $errors->has('sub_familia') ? ' is-invalid' : '' }}">
                                        <option></option>
                                        @foreach ($almacenes as $almacen)
                                            <option @if ($almacen->tipo_almacen === 'PRINCIPAL') selected @endif
                                                value="{{ $almacen->id }}"
                                                {{ old('almacen') == $almacen->id ? 'selected' : '' }}>
                                                {{ $almacen->descripcion . '-' . $almacen->tipo_almacen }}</option>
                                        @endforeach
                                    </select>
                                    <span style="font-weight: bold;color:red;"
                                        class="almacen_error msgErrorProducto"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                @include('almacenes.productos.tables.tbl_producto_colores')
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="hr-line-dashed"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos marcados con asterisco
                        (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('almacenes.producto.index') }}" id="btn_cancelar"
                        class="btn btn-w-m btn-default">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </a>
                    <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                        <i class="fa fa-save"></i> Grabar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
