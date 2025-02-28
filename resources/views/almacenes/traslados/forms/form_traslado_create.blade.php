<form action="{{route('almacenes.traslados.store')}}" method="POST" id="formTrasladoStore">
    <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
    {{csrf_field()}}


        <div class="col-sm-12">
            <h4 class=""><b>Traslado</b></h4>
            <div class="row">
                <div class="col-md-12">
                    <p>Registrar datos del Traslado:</p>
                </div>
            </div>
            <div class="form-group row">

            
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <label for="registrador_nombre" style="font-weight:bold;" class="required">Registrador</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-user-alt"></i>
                        </span>
                        </div>
                        <input value="{{Auth::user()->usuario}}" readonly name="registrador_nombre" id="registrador_nombre" type="text" class="form-control" placeholder="Registrador" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>


                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <label for="fecha_registro" style="font-weight:bold;" class="required">Fecha Registro</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="date" id="fecha_registro" name="fecha_registro"
                            class="form-control {{ $errors->has('fecha_registro') ? ' is-invalid' : '' }}"
                            value="<?php echo date('Y-m-d'); ?>"
                            autocomplete="off" readonly required>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <label for="fecha_traslado" style="font-weight:bold;" class="required">Fecha Traslado</label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input type="date" id="fecha_traslado" name="fecha_traslado"
                            class="form-control {{ $errors->has('fecha_traslado') ? ' is-invalid' : '' }}"
                            value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <label class="required" style="font-weight: bold;">Almacén Principal Origen</label>
                    <select onchange="cambiarAlmacen(this);" name="almacen_origen" id="almacen_origen" class="select2_form form-control {{ $errors->has('almacen_origen') ? ' is-invalid' : '' }}" required>
                        <option value="">Seleccionar</option>
                        @foreach ($almacen_principal_origen as $almacen_origen)
                            <option  value="{{$almacen_origen->id}}">{{$almacen_origen->descripcion}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('almacen_origen'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('almacen_origen') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <label class="required" style="font-weight: bold;">Almacén Principal Destino</label>
                    <select onchange="cambiarAlmacen(this);" name="almacen_destino" id="almacen_destino" class="select2_form form-control {{ $errors->has('almacen_destino') ? ' is-invalid' : '' }}" required>
                        <option value="">Seleccionar</option>
                        @foreach ($almacenes_principales_destino as $almacen_destino)
                            <option  value="{{$almacen_destino->id}}">{{$almacen_destino->descripcion}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('almacen_destino'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('almacen_destino') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="col-12 col-md-3">
                    <label style="font-weight: bold;">Observación</label>
                    <textarea maxlength="200" type="text" name="observacion" rows="2" id="observacion" class="form-control" placeholder="Observación"></textarea>
                </div>

            </div>
        </div>

    <hr>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class=""><b>Seleccionar productos</b></h4>
                </div>
                <div class="panel-body">
                  
                    <div class="col-lg-3 col-xs-12 mb-3">
                        <label class="required">Modelo</label>
                        <select id="modelo"
                            class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                            onchange="getProductosByModelo(this.value)">
                            <option></option>
                            @foreach ($modelos as $modelo)
                                <option value="{{ $modelo->id }}"
                                    {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                    {{ $modelo->descripcion }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"><b><span
                                    id="error-producto"></span></b></div>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            @include('almacenes.nota_salidad.tables.tbl_ns_productos')
                        </div>
                    </div>

                    <div class="col-lg-2 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label" for="amount">&nbsp;</label>
                            <button type=button class="btn btn-block btn-warning" style='color:white;'
                                id="btn_agregar_detalle" disabled> <i class="fa fa-plus"></i>
                                AGREGAR</button>
                        </div>
                    </div> 
                   
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class=""><b>Detalle de la nota de salida</b></h4>
                </div>
                <div class="panel-body">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('almacenes.nota_salidad.tables.tbl_ns_detalle')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hr-line-dashed"></div>
    <div class="form-group row">
        <div class="col-md-6 text-left" style="color:#fcbc6c">
            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                (<label class="required"></label>) son obligatorios.</small>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{route('almacenes.traslados.index')}}" id="btn_cancelar"
                class="btn btn-w-m btn-default">
                <i class="fa fa-arrow-left"></i> Regresar
            </a>
            <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary" form="formTrasladoStore">
                <i class="fa fa-save"></i> Grabar
            </button>
        </div>
    </div>
</form>