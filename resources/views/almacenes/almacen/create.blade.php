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
                @include('almacenes.almacen.forms.form_almacen_create')
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
                                    
                                    const tipo_almacen =   document.querySelector('#tipo_almacen').value;

                                    if(!tipo_almacen){
                                        toastr.clear();
                                        toastr.error('DEBE SELECCIONAR UN TIPO DE ALMACÉN!!!');
                                        return;
                                    }

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