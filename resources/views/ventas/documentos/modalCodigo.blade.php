<div class="modal inmodal" id="modal-codigo-precio" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">CODIGO</h4>
                <small class="font-bold">Ingresar</small>
            </div>
            <div class="modal-body">
                <form id="frmCodigo">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" class="form-control" id="codigo_precio_menor_json" value="{{codigoPrecioMenor()}}">
                            @if (codigoPrecioMenor()->estado_precio_menor == '1')
                            <div class="form-group">
                                <label class="required">Código para vender a menor precio</label>
                                <input type="password" class="form-control" id="codigo_precio_menor" placeholder="Código" autocomplete="off" required>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="frmCodigo" style="color:white;"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal inmodal" id="modal-codigo-precio-editar" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">CODIGO</h4>
                <small class="font-bold">Ingresar</small>
            </div>
            <div class="modal-body">
                <form id="frmCodigoEditar">
                    <div class="row">
                        <div class="col-12">
                            @if (codigoPrecioMenor()->estado_precio_menor == '1')
                            <div class="form-group">
                                <label class="required">Código para vender a menor precio</label>
                                <input type="password" class="form-control" id="codigo_precio_menor_editar" placeholder="Código" autocomplete="off" required>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="frmCodigoEditar" style="color:white;"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#frmCodigo').on('submit', function(e){
            e.preventDefault();
            agregarDetalle('MODAL');
        })

        $('#frmCodigoEditar').on('submit', function(e){
            e.preventDefault();
            agregarDetalleEditar('MODAL');
        })
    </script>
@endpush
