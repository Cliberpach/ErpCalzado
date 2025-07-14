<div class="modal inmodal" id="modal_editar_almacen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Almacen</h4>
                <small class="font-bold">Modificar Almacen.</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{route('almacenes.almacen.update')}}" method="POST" id="editar_almacen">
                    {{ csrf_field() }} {{method_field('PUT')}}

                   <input type="hidden" name="tabla_id" id="tabla_id_editar" value="{{old('tabla_id')}}">
                   <input type="hidden" value="{{$sede_id}}" name="sede_id" id="sede_id">

                   <div class="form-group">
                        <label class="required">Descripción:</label>
                        <input type="text" class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}" name="descripcion" id="descripcion_editar" value="{{old('descripcion')}}" onkeyup="return mayus(this)" required>

                        @if ($errors->has('descripcion'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-descripcion">{{ $errors->first('descripcion') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">

                        <label class="required">Ubicación:</label>
                        <input type="text" class="form-control {{ $errors->has('ubicacion') ? ' is-invalid' : '' }}" id="ubicacion_editar" name="ubicacion" value="{{old('ubicacion')}}" onkeyup="return mayus(this)" required>

                        @if ($errors->has('ubicacion'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-ubicacion">{{ $errors->first('ubicacion') }}</strong>
                        </span>
                        @endif
                    </div>
            </div>

                    <div class="modal-footer">
                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <a style="color:white" onclick="enviarFormulario2()" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</a>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>

                </form>
        </div>
    </div>
</div>

@push('styles')
@endpush
@push('scripts')

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
    $("#familia_id_editar").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });


    function enviarFormulario2() {

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
                        // this.submit();

                        $.ajax({
                            dataType : 'json',
                            type : 'post',
                            url : '{{ route('almacenes.almacen.exist') }}',
                            data : {
                                '_token' : $('input[name=_token]').val(),
                                'ubicacion' : $('#ubicacion_editar').val(),
                                'descripcion' : $('#descripcion_editar').val(),
                                'id':  $('#tabla_id_editar').val(),
                            }
                        }).done(function (result){
                            console.log(result)
                            if (result.existe == true) {
                                toastr.error('El almacen ya se encuentra registrado','Error');
                                $(this).focus();

                            }else{
                                // this.submit();
                                var url = $('#editar_almacen').attr('id');
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
