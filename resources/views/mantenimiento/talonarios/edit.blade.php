<div class="modal inmodal" id="modal_editar_talonario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Talonario</h4>
                <small class="font-bold">Modificar talonario</small>
            </div>
            <form role="form" action="{{route('mantenimiento.talonario.update')}}" method="POST" id="editar_talonario">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id_editar" id="id_editar" value="{{old('id_editar')}}">
                    <div class="form-group row">
                        <div class="col-lg-12 col-xs-12">
                            <label class="required">Empresa</label>
                            <select id="empresa_editar" name="empresa_editar" class="select2_form form-control {{ $errors->has('empresa_editar') ? ' is-invalid' : '' }}">
                                <option></option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ (old('empresa_editar') == $empresa->id ? "selected" : "") }} >{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('empresa_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-empresa-editar">{{ $errors->first('empresa_editar') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-8 col-xs-12">
                            <label class="required">Tipo de documento</label>
                            <select id="tipo_documento_editar" name="tipo_documento_editar" class="select2_form form-control {{ $errors->has('tipo_documento_editar') ? ' is-invalid' : '' }}">
                                <option></option>
                                @foreach(tipos_documentos_tributarios() as $tipo_documento)
                                    <option value="{{ $tipo_documento->simbolo }}" {{ (old('tipo_documento_editar') == $tipo_documento->simbolo ? "selected" : "") }} >{{ $tipo_documento->descripcion.' ('.$tipo_documento->simbolo.')' }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('tipo_documento_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-tipo-documento-editar">{{ $errors->first('tipo_documento') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Serie</label>
                            <input type="text" class="form-control {{ $errors->has('serie_editar') ? ' is-invalid' : '' }}" id="serie_editar" name="serie_editar" value="{{ old('serie_editar') }}" onkeyup="return mayus(this)" required>
                            @if ($errors->has('serie_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-serie-editar">{{ $errors->first('serie_editar') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Nro. Inicial</label>
                            <input type="text" class="form-control {{ $errors->has('numero_inicio_editar') ? ' is-invalid' : '' }}" id="numero_inicio_editar" name="numero_inicio_editar" value="{{ old('numero_inicio_editar') }}" onkeypress="return isNumber(event)" maxlength="11" required>
                            @if ($errors->has('numero_inicio_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-inicio-editar">{{ $errors->first('numero_inicio_editar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label>Nro. Final</label>
                            <input type="text" class="form-control {{ $errors->has('numero_final_editar') ? ' is-invalid' : '' }}" id="numero_final_editar" name="numero_final_editar" value="{{ old('numero_final_editar') }}" onkeypress="return isNumber(event)" maxlength="11">
                            @if ($errors->has('numero_final_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-final-editar">{{ $errors->first('numero_final_editar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-4 col-xs-12">
                            <label class="required">Nro. Actual</label>
                            <input type="text" class="form-control {{ $errors->has('numero_actual_editar') ? ' is-invalid' : '' }}" id="numero_actual_editar" name="numero_actual_editar" value="{{ old('numero_actual_editar') }}" onkeypress="return isNumber(event)" maxlength="11" required>
                            @if ($errors->has('numero_actual_editar'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-numero-actual-editar">{{ $errors->first('numero_actual_editar') }}</strong>
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
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> editar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
