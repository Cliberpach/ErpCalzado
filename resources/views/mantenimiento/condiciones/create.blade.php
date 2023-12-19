<div class="modal inmodal" id="modal_crear_condicion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Condición</h4>
                <small class="font-bold">Crear nueva condición</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{ route('mantenimiento.condiciones.store') }}" method="POST" id="crear_condicion">
                    {{ csrf_field() }} {{ method_field('POST') }}

                    <input type="hidden" name="condicion_existe" id="condicion_existe">

                    <div class="form-group">
                        <label class="required">Descripción:</label>
                        <select name="tabladetalle_id_guardar" id="tabladetalle_id_guardar"
                            class="select2_form form-control {{ $errors->has('tabladetalle_id_guardar') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('tabladetalle_id_guardar') }}" required>
                            <option value=""></option>
                            @foreach (forma_pago() as $pago)
                                <option value="{{ $pago->id }}" descripcion="{{ $pago->descripcion }}">{{ $pago->descripcion }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('tabladetalle_id_guardar'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-tabladetalle_id-guardar">{{ $errors->first('tabladetalle_id_guardar') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required">Días:</label>
                        <input type="text" class="form-control {{ $errors->has('dias_guardar') ? ' is-invalid' : '' }}" id="dias_guardar" name="dias_guardar" value="{{old('dias_guardar')}}" onkeyup="return isNumber(event)" required>

                        @if ($errors->has('dias_guardar'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-dias-guardar">{{ $errors->first('dias_guardar') }}</strong>
                        </span>
                        @endif
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" style="color:white;" form="crear_condicion"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>

    $('#tabladetalle_id_guardar').on('change', function(e){
        e.preventDefault();
        $('#dias_guardar').attr('readonly', false);
        $('#dias_guardar').val('');
        if($('#tabladetalle_id_guardar').val())
        {
            let descripcion = $('#tabladetalle_id_guardar option:selected').attr('descripcion');
            if(descripcion.toUpperCase() == 'CONTADO')
            {
                $('#dias_guardar').val(0);
                $('#dias_guardar').attr('readonly', true);
            }
        }
    })

    $('#crear_condicion').on('submit',function(e){
        e.preventDefault();
        $.ajax({
            dataType : 'json',
            type : 'post',
            url : '{{ route('mantenimiento.condiciones.exist') }}',
            data : {
                '_token' : $('input[name=_token]').val(),
                'tabladetalle_id' : $('#tabladetalle_id_guardar').val(),
                'dias' : $('#dias_guardar').val(),
                'id':  null
            }
        }).done(function (result){
            console.log(result)
            if (result.existe) {
                toastr.error('La condicion ya se encuentra registrada','Error');
                document.getElementById('crear_condicion').focus();

            }else{
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger',
                    },
                    buttonsStyling: false
                })
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    title: 'Opción Guardar',
                    text: "¿Seguro que desea guardar cambios?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('crear_condicion').submit();
                    }else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cancelado',
                            text: 'La Solicitud se ha cancelado.',
                            customClass: {
                                container: 'my-swal'
                            },
                            showConfirmButton: false,
                            timer: 2500
                        });
                        // swalWithBootstrapButtons.fire(
                        //     'Cancelado',
                        //     'La Solicitud se ha cancelado.',
                        //     'error'
                        // )

                    }
                });
            }
        });
    })


</script>
@endpush
