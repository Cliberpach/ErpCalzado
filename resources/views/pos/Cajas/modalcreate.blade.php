<div id="modal_create_caja" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_crear_caja"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('Caja.store') }}" method="post">
            @csrf
            <input type="hidden" value="{{$sede_id}}" name="sede_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_crear_caja">Crear Nueva Caja</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="" class="required">Caja</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i><small>Los campos marcados con asterisco (<label
                                class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i>
                            Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
