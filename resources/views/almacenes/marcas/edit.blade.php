<div class="modal inmodal" id="modal_editar_marca" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Marca</h4>
                <small class="font-bold" onkeyup="return mayus(this)">Modificar Marca.</small>
            </div>
            <form role="form" action="{{ route('almacenes.marcas.update') }}" method="POST" id="editar_marca">
                <div class="modal-body">

                    {{ csrf_field() }} {{ method_field('PUT') }}

                    <input type="hidden" name="tabla_id" id="tabla_id_editar" value="{{ old('tabla_id') }}">
                    <input type="hidden" name="marca_existe" id="marca_existe">
                    <div class="form-group">
                        <label class="required">Marca:</label>
                        <input type="text" class="form-control {{ $errors->has('marca') ? ' is-invalid' : '' }}"
                            name="marca" id="marca_editar" value="{{ old('marca') }}" onkeyup="return mayus(this)"
                            required>

                        @if ($errors->has('marca'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-marca">{{ $errors->first('marca') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required">Procedencia:</label>
                        <input type="text" class="form-control {{ $errors->has('procedencia') ? ' is-invalid' : '' }}"
                            name="procedencia" id="procedencia_editar" value="{{ old('procedencia') }}"
                            onkeyup="return mayus(this)" required>

                        @if ($errors->has('procedencia'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-procedencia">{{ $errors->first('procedencia') }}</strong>
                            </span>
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                                class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i>
                            Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#marca_editar").on("change", validarNombre);
        })


        $('#editar_marca').submit(function(e) {
            e.preventDefault();
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    container: 'my-swal',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            })
            console.log($('#marca_existe').val())
            // if ($('#marca_existe').val() == '0') {
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

                        this.submit();

                    } else if (
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
            // }



        })

        function validarNombre() {
            // Consultamos nuestra BBDD
            $.ajax({
                dataType: 'json',
                type: 'post',
                url: '{{ route('almacenes.marcas.exist') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'marca': $(this).val(),
                    'id': $('#tabla_id_editar').val(),
                }
            }).done(function(result) {
                console.log(result);
                if (result.existe == true) {
                    toastr.error('La marca ya se encuentra registrada', 'Error');
                    $(this).focus();
                    $('#marca_existe').val('1')
                } else {
                    $('#marca_existe').val('0')
                }
            });
        }
    </script>
@endpush
