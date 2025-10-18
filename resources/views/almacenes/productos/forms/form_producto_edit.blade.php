<form action="{{ route('almacenes.producto.update', $producto->id) }}" method="POST" id="form_actualizar_producto">
    @csrf
    @method('PUT')

    <div class="row">

        <div class="col-lg-6 col-xs-12 b-r">
            <h4><b>Datos Generales</b></h4>

            <input class="d-none" type="text" id="coloresJSON" name="coloresJSON">

            <div class="row">

                <div class="col-12 mb-3">
                    <label class="required" style="font-weight:bold;">Nombre</label>
                    <input type="text" id="nombre" name="nombre"
                        class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                        value="{{ old('nombre', $producto->nombre) }}" maxlength="191" onkeyup="return mayus(this)"
                        required>
                    @if ($errors->has('nombre'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('nombre') }}</strong>
                        </span>
                    @endif
                    <span style="font-weight: bold;color:red;" class="nombre_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label style="font-weight:bold;" class="required">Categoria</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_categoria"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select required id="categoria" name="categoria"
                        value="{{ old('categoria', $producto->categoria_id) }}"class="select2_form form-control {{ $errors->has('categoria') ? ' is-invalid' : '' }}">
                        <option></option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->descripcion }}</option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="categoria_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label style="font-weight:bold;" class="required">Marca</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_marca"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select id="marca" name="marca"
                        class="select2_form form-control {{ $errors->has('marca') ? ' is-invalid' : '' }}" required
                        value="{{ old('marca', $producto->marca_id) }}">
                        <option></option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}"
                                {{ old('marca', $producto->marca_id) == $marca->id ? 'selected' : '' }}>
                                {{ $marca->marca }}</option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="marca_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label style="font-weight:bold;" class="required">Modelo</label>
                    <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_modelo"
                        class="btn btn-primary" href="#">
                        <i class="fa fa-plus"></i>
                    </a>
                    <select required id="modelo" name="modelo"
                        value="{{ old('modelo', $producto->modelo_id) }}"class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}">
                        <option></option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->id }}"
                                {{ old('modelo', $producto->modelo_id) == $modelo->id ? 'selected' : '' }}>
                                {{ $modelo->descripcion }}</option>
                        @endforeach
                    </select>
                    <span style="font-weight: bold;color:red;" class="modelo_error msgErrorProducto"></span>
                </div>

            </div>
        </div>

        <div class="col-lg-6 col-xs-12">

            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="form-group">
                        <label style="font-weight:bold;" class="required">PRECIO 1</label>
                        <input required value="{{ $producto->precio_venta_1 }}" class="form-control" type="number"
                            step="0.01" inputmode="decimal" id="precio1" name="precio1" />
                        <span style="font-weight: bold;color:red;" class="precio1_error msgErrorProducto"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="form-group">
                        <label style="font-weight:bold;" class="required">PRECIO 2</label>
                        <input required value="{{ $producto->precio_venta_2 }}" class="form-control" type="number"
                            step="0.01" inputmode="decimal" id="precio2" name="precio2" />
                        <span style="font-weight: bold;color:red;" class="precio2_error msgErrorProducto"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="form-group">
                        <label style="font-weight:bold;" class="required">PRECIO 3</label>
                        <input required value="{{ $producto->precio_venta_3 }}" class="form-control" type="number"
                            step="0.01" inputmode="decimal" id="precio3" name="precio3" />
                        <span style="font-weight: bold;color:red;" class="precio3_error msgErrorProducto"></span>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <label style="font-weight:bold;" class="required">COSTO</label>
                    <input value="{{ $producto->costo }}"
                        class="form-control {{ $errors->has('costo') ? ' is-invalid' : '' }}" type="number"
                        step="0.01" inputmode="decimal" id="costo" name="costo"
                        value="{{ old('costo') }}" />
                    <span style="font-weight: bold;color:red;" class="precio3_error msgErrorProducto"></span>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mostrar_en_web" name="mostrar_en_web"
                            value="1"
                            {{ old('mostrar_en_web', $producto->mostrar_en_web ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="mostrar_en_web">
                            MOSTRAR EN WEB
                        </label>
                    </div>
                    <span style="font-weight: bold; color:red;" class="mostrar_en_web_error msgErrorProducto"></span>
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
                        @for ($i = 1; $i <= 5; $i++)
                            @php
                                $ruta = $producto["img{$i}_ruta"] ?? null;
                                $nombre = $producto["img{$i}_nombre"] ?? null;
                            @endphp

                            <div class="col-md-2 col-6 mb-3">
                                <div class="position-relative">
                                    <label for="imagen{{ $i }}" class="d-block" style="cursor:pointer;">
                                        <div class="d-flex align-items-center justify-content-center border rounded img-thumbnail"
                                            style="height:150px;">

                                            {{-- Mostrar preview si existe, si no mostrar + --}}
                                            @if ($ruta)
                                                <a id="link{{ $i }}" href="{{ asset($ruta) }}"
                                                    data-fancybox="galeria">
                                                    <img id="preview{{ $i }}" src="{{ asset($ruta) }}"
                                                        class="img-fluid img-preview">
                                                </a>
                                                <span id="plus{{ $i }}" class="d-none"
                                                    style="font-size:2rem; color:#aaa;">+</span>
                                            @else
                                                <span id="plus{{ $i }}"
                                                    style="font-size:2rem; color:#aaa;">+</span>
                                                <a id="link{{ $i }}" href=""
                                                    data-fancybox="galeria" class="d-none">
                                                    <img id="preview{{ $i }}"
                                                        class="img-fluid img-preview">
                                                </a>
                                            @endif
                                        </div>
                                    </label>

                                    {{-- Botón eliminar visible solo si ya existe --}}
                                    <button type="button" id="remove{{ $i }}"
                                        class="btn btn-sm btn-danger position-absolute {{ $ruta ? '' : 'd-none' }}"
                                        style="top:5px; right:5px;"
                                        onclick="removeImage('imagen{{ $i }}','preview{{ $i }}','plus{{ $i }}','remove{{ $i }}','filename{{ $i }}','link{{ $i }}')">
                                        ✖
                                    </button>

                                    {{-- Nombre de archivo --}}
                                    <small id="filename{{ $i }}"
                                        class="text-muted d-block mt-1 text-truncate">
                                        {{ $nombre ?? '' }}
                                    </small>
                                </div>

                                {{-- Input oculto --}}
                                <input type="file" class="d-none" id="imagen{{ $i }}"
                                    name="imagen{{ $i }}" accept=".jpg,.jpeg,.webp,.avif"
                                    onchange="previewImage(event, 'preview{{ $i }}', 'plus{{ $i }}','remove{{ $i }}','filename{{ $i }}','link{{ $i }}')">
                                <input type="hidden" id="remove_imagen{{ $i }}"
                                    name="remove_imagen{{ $i }}" value="0">

                                <p class="m-0 imagen{{ $i }}_error msgError"></p>
                            </div>
                        @endfor
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
                                        NUEVO COLOR <i class="fa fa-plus"></i>
                                    </a>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label style="font-weight: bold;">Almacén</label>

                                    <select data-placeholder="Seleccionar" onchange="getColoresProducto(this.value);"
                                        id="almacen" name="almacen"
                                        class="select2_form form-control {{ $errors->has('sub_familia') ? ' is-invalid' : '' }}">
                                        <option></option>
                                        @foreach ($almacenes as $almacen)
                                            <option value="{{ $almacen->id }}"
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
