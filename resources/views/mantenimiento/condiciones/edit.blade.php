<div class="modal inmodal" id="modal_editar_condicion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Condición</h4>
                <small class="font-bold">Modificar Condición.</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{route('mantenimiento.condiciones.update')}}" method="POST" id="editar_condicion">
                    {{ csrf_field() }} {{method_field('PUT')}}

                   <input type="hidden" name="tabla_id" id="tabla_id_editar" value="{{old('tabla_id')}}">

                   <div class="form-group">
                        <label class="required">Descripción:</label>
                        <select name="tabladetalle_id" id="tabladetalle_id"
                            class="select2_form form-control {{ $errors->has('tabladetalle_id') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" value="{{ old('tabladetalle_id') }}" required>
                            <option value=""></option>
                            @foreach (forma_pago() as $pago)
                                <option value="{{ $pago->id }}" descripcion="{{ $pago->descripcion }}">{{ $pago->descripcion }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('tabladetalle_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-tabladetalle_id">{{ $errors->first('tabladetalle_id') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">

                        <label class="required">Dias:</label>
                        <input type="text" class="form-control {{ $errors->has('dias') ? ' is-invalid' : '' }}" id="dias_editar" name="dias" value="{{old('dias')}}" onkeyup="return isNumber(event)" required>

                        @if ($errors->has('dias'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-dias">{{ $errors->first('dias') }}</strong>
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
                    <button type="submit" style="color:white" class="btn btn-primary btn-sm" form="editar_condicion"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
@endpush
@push('scripts')
<!-- Select2 -->
<script src="{{asset('Inspinia/js/plugins/select2/select2.full.min.js')}}"></script>

<script>

    // $(document).ready(function() {
    //     $("#descripcion_editar").on("change", validarNombre);
    // })
    //Select2
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });

    $('#tabladetalle_id').on('change', function(e){
        e.preventDefault();

        $('#dias_editar').attr('readonly', false);
        $('#dias_editar').val('');
        let descripcion = $('#tabladetalle_id option:selected').attr('descripcion');
        console.log(descripcion)
        if(descripcion == 'CONTADO')
        {
            $('#dias_editar').val(0);
            $('#dias_editar').attr('readonly', true);
        }
    })

    $('#editar_condicion').submit(function(e){
        e.preventDefault();
        $.ajax({
            dataType : 'json',
            type : 'post',
            url : '{{ route('mantenimiento.condiciones.exist') }}',
            data : {
                '_token' : $('input[name=_token]').val(),
                'dias' : $('#dias_editar').val(),
                'tabladetalle_id' : $('#tabladetalle_id').val(),
                'id':  $('#tabla_id_editar').val(),
            }
        }).done(function (result){
            console.log(result)
            if (result.existe) {
                toastr.error('La condicion ya se encuentra registrada','Error');
                document.getElementById('editar_condicion').focus();

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
                    title: 'Opción Modificar',
                    text: "¿Seguro que desea modificar los cambios?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('editar_condicion').submit();
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
                        // 'Cancelado',
                        // 'La Solicitud se ha cancelado.',
                        // 'error'
                        // )

                    }
                })
            }
        });
    })

</script>

@endpush
