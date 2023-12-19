<div class="modal inmodal" id="modal_pago_show" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title pago-title"></h4>
                <small class="font-bold pago-subtitle"></small>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-6 br">
                        <div class="form-group d-none">
                            <label class="col-form-label required">Venta</label>
                            <input type="text" class="form-control" id="venta_id" name="venta_id" disabled>
                        </div>
                        <div class="form-group d-none">
                            <label class="col-form-label required">Tipo Pago</label>
                            <input type="text" class="form-control" id="tipo_pago_id" name="tipo_pago_id" disabled>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label required">Monto</label>
                            <input type="text" class="form-control" id="monto_venta" name="monto_venta" disabled>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label required">Efectivo</label>
                            <input type="text" value="0.00" class="form-control" id="efectivo" name="efectivo" disabled>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label required">Modo de pago</label>
                            <select name="modo_pago" id="modo_pago" class="select2_form form-control" disabled>
                                <option></option>
                                @foreach (modos_pago() as $modo)
                                    <option value="{{$modo->id}}">{{$modo->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label  class="col-form-label required">Importe</label>
                            <input type="text" class="form-control" id="importe" name="importe" disabled>
                        </div>
                        <div class="form-group d-none" id="div_cuentas">
                            <label class="col-form-label">Cuentas</label>
                            <select name="cuenta_id" id="cuenta_id_show" class="select2_form form-control" disabled>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label id="imagen_label">Imagen:</label>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-6 align-content-center">
                                <div class="row justify-content-center">
                                    <p>
                                        <img class="imagen" src="{{asset('img/default.png')}}" alt="IMG">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <style>
        .imagen {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }

    </style>
@endpush
@push('scripts')
@endpush
