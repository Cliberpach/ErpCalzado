<div class="modal inmodal" id="modal_ingreso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiar()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Nota Ingreso</h4>
                <small class="font-bold">Nuevo</small>
            </div>
            <div class="modal-body">
                <form role="form" id="nota_ingreso" action="{{ route('almacenes.nota_ingreso.storeFast') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="required">Cantidad</label>
                                <input type="text" min="1" class="form-control" name="cantidad" id="cantidad_fast" required placeholder="Ingrese cantidad" onkeypress="return filterFloat(event, this, false);">
                                <input type="hidden" class="form-control" name="producto_id" id="producto_id_fast" required placeholder="Ingrese producto_id">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="required">Costo Total</label>
                                <input type="text" min="1" class="form-control" name="costo" id="costo_fast" required placeholder="Ingrese costo" onkeypress="return filterFloat(event, this, false);">
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                                class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="row">
                            <div class="col-md-6 mt-1">
                                <button type="submit" class="btn btn-block btn-primary btn-sm editarRegistro" form="nota_ingreso"><i class="fa fa-save"></i> Guardar</button>
                            </div>
                            <div class="col-md-6 mt-1">
                                <button type="button"data-dismiss="modal" class="btn btn-block btn-danger btn-sm"><i class="fa fa-times"></i> Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    function limpiar()
    {

    }
</script>
@endpush
