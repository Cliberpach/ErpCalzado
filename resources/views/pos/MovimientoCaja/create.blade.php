<div class="modal inmodal" id="modal_crear_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Caja</h4>
                <small class="font-bold">Apertura de Cajaa</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{ route('Caja.apertura') }}" method="POST" id="crear_caja_movimiento">
                    {{ csrf_field() }} {{ method_field('POST') }}
                    <div class="form-group">
                        <label for="">Cajas Disponible</label>
                        <select name="caja" id="caja" class="form-control select2_form" required>
                            <option value=""></option>
                            @foreach (cajas() as $caja)
                                <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required">Colaborador:</label>
                        <select class="form-control select2_form" style="text-transform: uppercase; width:100%"
                            name="colaborador_id" id="colaborador_id" required>
                            <option></option>
                            @foreach (colaboradoresDisponibles() as $colaborador)
                                <option value="{{ $colaborador->id }}">
                                    {{ $colaborador->persona->apellido_paterno . ' ' . $colaborador->persona->apellido_materno . ' ' . $colaborador->persona->nombres }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required">Turno:</label>
                        <select class="form-control select2_form" style="text-transform: uppercase; width:100%"
                            name="turno" id="turno" required>
                            <option ></option>
                            <option>Mañana</option>
                            <option>Tarde</option>
                            <option>Noche</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="required">Saldo Inicial:</label>
                            <input type="text"
                                class="form-control {{ $errors->has('saldo_inicial') ? ' is-invalid' : '' }}"
                                id="saldo_inicial" name="saldo_inicial" value="{{ old('saldo_inicial') }}" required>
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
