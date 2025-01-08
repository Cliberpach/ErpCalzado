<div class="modal inmodal" id="modal_crear_almacen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Almacen</h4>
                <small class="font-bold">Crear nuevo Almacen.</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{route('almacenes.almacen.store')}}" method="POST" id="crear_almacen">
                    {{ csrf_field() }} {{method_field('POST')}}

                    <input type="hidden" name="almacen_existe" id="almacen_existe">
                    <input type="hidden" value="{{$sede_id}}" name="sede_id" id="sede_id">

                   <div class="form-group">
                        <label class="required">Descripción:</label> 
                        <input type="text" class="form-control {{ $errors->has('descripcion_guardar') ? ' is-invalid' : '' }}" name="descripcion_guardar" id="descripcion_guardar" value="{{old('descripcion_guardar')}}" onkeyup="return mayus(this)" required>

                        @if ($errors->has('descripcion_guardar'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-descripcion-guardar">{{ $errors->first('descripcion_guardar') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">
                        
                        <label class="required">Ubicación:</label>
                        <input type="text" class="form-control {{ $errors->has('ubicacion_guardar') ? ' is-invalid' : '' }}" id="ubicacion_guardar" name="ubicacion_guardar" value="{{old('ubicacion_guardar')}}" onkeyup="return mayus(this)" required>
                        
                        @if ($errors->has('ubicacion_guardar'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-ubicacion-guardar">{{ $errors->first('ubicacion_guardar') }}</strong>
                            </span>
                        @endif
                    </div>
            </div>
                
                    <div class="modal-footer">
                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <a class="btn btn-primary btn-sm" style="color:white;" onclick="crearFormulario()"><i class="fa fa-save"></i> Guardar</a>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>
                
                </form>
        </div>
    </div>
</div>


@push('scripts')
<script>

    function crearFormulario() {

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

                            $.ajax({
                                dataType : 'json',
                                type : 'post',
                                url : '{{ route('almacenes.almacen.exist') }}',
                                data : {
                                    '_token' : $('input[name=_token]').val(),
                                    'descripcion' : $('#descripcion_guardar').val(),
                                    'ubicacion' : $('#ubicacion_guardar').val(),
                                    'id':  null
                                }
                            }).done(function (result){
                                console.log(result)
                                if (result.existe == true) {
                                    toastr.error('El Almacen ya se encuentra registrado','Error');
                                    $(this).focus();
                                    
                                }else{
                                    // this.submit();
                                    var url = $('#crear_almacen').attr('id');
                                    var enviar = '#'+url;
                                    $(enviar).submit();
                                }
                            });


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


</script>
@endpush