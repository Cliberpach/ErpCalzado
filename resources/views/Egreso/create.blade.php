<div class="modal inmodal" id="modal_crear_egreso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title">Egreso</h4>
                <small class="font-bold">Nuevo Egreso</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{ route('Egreso.store') }}" method="POST" id="frm_egreso_create">
                    {{ csrf_field() }} {{ method_field('POST') }}
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="" class="required">Cuentas</label>
                                <select name="cuenta" id="cuenta" class="select2_form form-control" required>
                                        <option value=""></option>
                                    @foreach (cuentas() as $cuenta)
                                        <option value="{{$cuenta->id}}">{{$cuenta->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required">Tipo Documento:</label>
                                <input type="text" name="tipo_documento" id="tipo_documento" class="form-control" value="RECIBO" readonly>
                                {{-- <select name="tipo_documento" id="tipo_documento" class="form-control select2_form" required>
                                    <option value=""></option>
                                    @foreach (tipo_compra() as $documento)
                                            <option value="{{$documento->id}}">{{$documento->descripcion}}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                            <div class="form-group">
                                <label class="required">Monto:</label>
                                <input type="text" class="form-control" value="0" id="monto" name="monto" required readonly>
                            </div>
                            <div class="form-group">
                                <label>Documento:</label>
                                <input type="text" name="documento" id="documento" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="required">Descripcion:</label>
                                <textarea name="descripcion" id="descripcion" cols="30" rows="2"
                                    class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="col-form-label required">Efectivo</label>
                                <input type="text" value="0.00" class="form-control" id="efectivo_venta"
                                    onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo()"
                                    name="efectivo" required>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Modo de pago</label>
                                <select name="modo_pago" id="modo_pago" class="select2_form form-control"
                                    onchange="changeModoPago(this)" required>
                                    <option></option>
                                    @foreach (modos_pago() as $modo)
                                        <option value="{{ $modo->id }}">
                                            {{ $modo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Importe</label>
                                <input type="text" class="form-control" id="importe_venta" value="0.00"
                                    onkeypress="return filterFloat(event, this);" onkeyup="changeImporte()"
                                    name="importe" required>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm btn-submit-egreso"><i class="fa fa-save"></i> Guardar</button>
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
        $("#tipo_documento").on('change', function (e) {
            var tipoDocumento=$("#tipo_documento option:selected").text()
           if(tipoDocumento=="RECIBO")
           {
            $("#documento").attr('disabled',true)
           }
           else
           {
            $("#documento").attr('disabled',false)
           }
        });
        $("#frm_egreso_create").on('submit', function(e) {
            e.preventDefault();
            document.querySelector('.btn-submit-egreso').disabled   =   true;

            var efectivo_venta = $("#efectivo_venta").val();
            var importe_venta = $("#importe_venta").val();
            var cuenta = $("#cuenta").val();
            var cantidad = convertFloat(efectivo_venta) + convertFloat(importe_venta);
            var modo_pago = $("#modo_pago").val();

            let enviar = true;
            if (cantidad.length == 0 || cuenta.length == 0  || modo_pago.length == 0) {
                enviar = false;
                toastr.error('Ingrese todos los datos');
            }

            if(cantidad == 0 || cantidad < 0)
            {
                enviar = false;
                toastr.error('El monto de egreso debe ser mayor a 0.');
            }

            if(enviar)
            {
                axios.get("{{route('Caja.movimiento.verificarestado')}}").then((value) => {
                    let data = value.data
                    if (!data.success) {
                            toastr.error(data.mensaje);
                    }  else {
                        $('.btn-submit-egreso').attr('disabled',true);
                        $('.btn-submit-egreso').html('Cargando <span class="loading bullet"></span> ');
                        this.submit();
                    }
                })
            }else{
                document.querySelector('.btn-submit-egreso').disabled   =   false;
            }
        })

        function changeModoPago(b)
        {
            if(b.value == 1) {
                $("#efectivo_venta").attr('readonly',false)
                $("#importe_venta").attr('readonly',true)
                $("#importe_venta").val(0.00)
                changeEfectivo()
            }
            else{
                $("#efectivo_venta").attr('readonly',false)
                $("#importe_venta").attr('readonly',false)
            }
        }

        function changeEfectivo()
        {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#monto').val(suma.toFixed(2))
        }

        function changeImporte()
        {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#monto').val(suma.toFixed(2));
        }
    </script>
@endpush
