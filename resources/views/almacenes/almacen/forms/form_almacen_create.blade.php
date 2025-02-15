<form role="form" action="{{route('almacenes.almacen.store')}}" method="POST" id="crear_almacen">
    {{ csrf_field() }} {{method_field('POST')}}

    <input type="hidden" name="almacen_existe" id="almacen_existe">
    <input type="hidden" value="{{$sede_id}}" name="sede_id" id="sede_id">

   <div class="form-group">
        <label class="required">Descripción:</label> 
        <input type="text" class="form-control {{ $errors->has('descripcion_guardar') ? ' is-invalid' : '' }}" name="descripcion_guardar" id="descripcion_guardar" value="{{old('descripcion_guardar')}}" onkeyup="return mayus(this)" required>

        @if ($errors->has('descripcion_guardar'))
        <span class="invalid-feedback" role="alert">
            <strong id="error-descripcion-guardar">{{ $errors->first('descripcion_guardar') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group">
        
        <label class="required">Ubicación:</label>
        <input type="text" class="form-control {{ $errors->has('ubicacion_guardar') ? ' is-invalid' : '' }}" id="ubicacion_guardar" name="ubicacion_guardar" value="{{old('ubicacion_guardar')}}" onkeyup="return mayus(this)" required>
        
        @if ($errors->has('ubicacion_guardar'))
            <span class="invalid-feedback" role="alert">
                <strong id="error-ubicacion-guardar">{{ $errors->first('ubicacion_guardar') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group">
        
        <label class="required">TIPO:</label>

        <select required name="tipo_almacen" id="tipo_almacen" class="select2_form">
            <option value=""></option>
            @if (!$sede_have_principal)
                <option value="PRINCIPAL">PRINCIPAL</option>
            @endif
            <option value="SECUNDARIO">SECUNDARIO</option>
        </select>

        @if ($errors->has('ubicacion_guardar'))
            <span class="invalid-feedback" role="alert">
                <strong id="error-tipo_almacen">{{ $errors->first('tipo_almacen') }}</strong>
            </span>
        @endif
    </div>

</div>

    <div class="modal-footer">
        <div class="col-md-6 text-left" style="color:#fcbc6c">
            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-primary btn-sm" style="color:white;" onclick="crearFormulario()"><i class="fa fa-save"></i> Guardar</a>
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
        </div>
    </div>

</form>