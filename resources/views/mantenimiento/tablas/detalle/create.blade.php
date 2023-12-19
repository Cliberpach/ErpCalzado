<div class="modal inmodal" id="modal_crear_tabla_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Tabla Detalle</h4>
                <small class="font-bold">Crear nuevo detalle en: {{$tabla->descripcion}}.</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{route('mantenimiento.tabla.detalle.store')}}" method="POST" id="enviar_tabla">
                    {{ csrf_field() }} {{method_field('POST')}}

                    <input type="hidden" name="tabla_id" id="tabla_id" value="{{$tabla->id}}">
                    <input type="hidden" name="descripcion_existe" id="descripcion_existe">

                   <div class="form-group">
                        <label class="required">Descripción:</label> 
                        <input type="text" class="form-control {{ $errors->has('descripcion_guardar') ? ' is-invalid' : '' }}" name="descripcion_guardar" id="descripcion_guardar" value="{{old('descripcion_guardar')}}" required  onkeyup="return mayus(this)">

                        @if ($errors->has('descripcion_guardar'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-descripcion-guardar">{{ $errors->first('descripcion_guardar') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">
                        
                        <label class="required">Símbolo:</label>
                        <input type="text" class="form-control {{ $errors->has('simbolo_guardar') ? ' is-invalid' : '' }}" id="simbolo_guardar" name="simbolo_guardar" value="{{old('simbolo_guardar')}}" required  onkeyup="return mayus(this)">
                        
                        @if ($errors->has('simbolo_guardar'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-simbolo-guardar">{{ $errors->first('simbolo_guardar') }}</strong>
                        </span>
                        @endif
                    </div>
            </div>
                
                    <div class="modal-footer">
                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>
                
                </form>
        </div>
    </div>
</div>

@push('scripts')
<script>

    $('#enviar_tabla').submit(function(e){
        e.preventDefault();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                container: 'my-swal',
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        $.ajax({
            dataType : 'json',
            type : 'post',
            url : '{{ route('mantenimiento.tabla.detalle.exist') }}',
            data : {
                '_token' : $('input[name=_token]').val(),
                'descripcion' : $('#descripcion_guardar').val(),
                'id_general' : $('#tabla_id').val(),
                'id': null
            }
        }).done(function (result){
            if (result.existe == true) {
                toastr.error('El registro ya se ha ingresado','Error');
                $(this).focus();

            }else{
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
                                document.getElementById("enviar_tabla").submit();
                            }else if (
                            /* Read more about handling dismissals below */
                            result.dismiss === Swal.DismissReason.cancel
                        ) {
                            swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'La Solicitud se ha cancelado.',
                            'error'
                            )
                        }
                    })
            }
        });
     
    })

</script>
@endpush