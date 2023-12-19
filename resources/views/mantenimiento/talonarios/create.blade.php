<div class="modal inmodal" id="modal_crear_talonario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Talonario</h4>
                <small class="font-bold">Crear nuevo talonario</small>
            </div>
            <form role="form" action="{{route('mantenimiento.talonario.store')}}" method="POST" id="crear_talonario">
                @csrf @method('POST')
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-12 col-xs-12">
                            <label class="required">Empresa</label>
                            <select id="empresa_guardar" name="empresa_guardar" class="select2_form form-control {{ $errors->has('empresa_guardar') ? ' is-invalid' : '' }}">
                                <option></option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ (old('empresa_guardar') == $empresa->id ? "selected" : "") }} >{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('empresa_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-empresa-guardar">{{ $errors->first('empresa_guardar') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-8 col-xs-12">
                            <label class="required">Tipo de documento</label>
                            <select id="tipo_documento_guardar" name="tipo_documento_guardar" class="select2_form form-control {{ $errors->has('tipo_documento_guardar') ? ' is-invalid' : '' }}">
                                <option></option>
                                @foreach(tipos_documentos_tributarios() as $tipo_documento)
                                    <option value="{{ $tipo_documento->simbolo }}" {{ (old('tipo_documento_guardar') == $tipo_documento->simbolo ? "selected" : "") }} >{{ $tipo_documento->descripcion.' ('.$tipo_documento->simbolo.')' }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tipo_documento_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-tipo-documento-guardar">{{ $errors->first('tipo_documento') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Serie</label>
                            <input type="text" class="form-control {{ $errors->has('serie_guardar') ? ' is-invalid' : '' }}" id="serie_guardar" name="serie_guardar" value="{{ old('serie_guardar') }}" onkeyup="return mayus(this)" required>
                            @if ($errors->has('serie_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-serie-guardar">{{ $errors->first('serie_guardar') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Nro. Inicial</label>
                            <input type="text" class="form-control {{ $errors->has('numero_inicio_guardar') ? ' is-invalid' : '' }}" id="numero_inicio_guardar" name="numero_inicio_guardar" value="{{ old('numero_inicio_guardar') }}" onkeypress="return isNumber(event)" maxlength="11" required>
                            @if ($errors->has('numero_inicio_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-inicio-guardar">{{ $errors->first('numero_inicio_guardar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label>Nro. Final</label>
                            <input type="text" class="form-control {{ $errors->has('numero_final_guardar') ? ' is-invalid' : '' }}" id="numero_final_guardar" name="numero_final_guardar" value="{{ old('numero_final_guardar') }}" onkeypress="return isNumber(event)" maxlength="11">
                            @if ($errors->has('numero_final_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-final-guardar">{{ $errors->first('numero_final_guardar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Nro. Actual</label>
                            <input type="text" class="form-control {{ $errors->has('numero_actual_guardar') ? ' is-invalid' : '' }}" id="numero_actual_guardar" name="numero_actual_guardar" value="{{ old('numero_actual_guardar') }}" onkeypress="return isNumber(event)" maxlength="11" required>
                            @if ($errors->has('numero_actual_guardar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-actual-guardar">{{ $errors->first('numero_actual_guardar') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
