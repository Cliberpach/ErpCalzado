<div class="modal inmodal" id="modal_cerrar_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title">Caja</h4>
                <small class="font-bold">Cerrar de Caja</small>
            </div>
            <div class="modal-body"> 
                <form role="form" action="{{ route('Caja.cerrar') }}" method="POST" >
                    {{ csrf_field() }} {{ method_field('POST') }}
                    <input type="hidden" name="movimiento_id" id="movimiento_id" >
                    <div class="form-group">
                        <label class="required">Caja:</label>
                        <input type="text" name="caja" id="caja" disabled class="form-control" placeholder="">
                    </div>
                    <div class="form-group">
                        <label class="required">Colaborador:</label>
                        <input type="text" name="colaborador" id="colaborador" disabled class="form-control" placeholder="">
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="required">Monto Inicial:</label>
                            <input type="text" name="monto_inicial" id="monto_inicial" disabled class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="required">Ingresos:</label>
                            <input type="text" name="ingreso" id="ingreso" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="required">Egresos:</label>
                            <input type="text" name="egreso" id="egreso" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="required">Saldo:</label>
                            <input type="text" name="saldo" id="saldo" class="form-control" >
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        //Select2
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    </script>
@endpush
