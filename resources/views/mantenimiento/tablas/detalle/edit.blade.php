<div class="modal inmodal" id="modal_editar_tabla_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Tabla Detalle</h4>
                <small class="font-bold"  onkeyup="return mayus(this)">Modificar registro de la Tabla General: <strong>{{$tabla->descripcion}}</strong>.</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{route('mantenimiento.tabla.detalle.update')}}" method="POST" id="editar_tabla_detalle">
                    {{ csrf_field() }} {{method_field('PUT')}}

                   <input type="hidden" name="tabla_id" id="tabla_id_editar" value="{{old('tabla_id')}}">
                   <input type="hidden" name="descripcion_existe" id="descripcion_existe">

                   <div class="form-group">
                        <label class="required">Descripción:</label> 
                        <input type="text" class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}" name="descripcion" id="descripcion_editar" value="{{old('descripcion')}}" required  onkeyup="return mayus(this)">
                        
                        @if ($errors->has('descripcion'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-descripcion">{{ $errors->first('descripcion') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="form-group">
                        
                        <label class="required">Símbolo:</label>
                        <input type="text" class="form-control {{ $errors->has('simbolo') ? ' is-invalid' : '' }}" id="simbolo_editar" name="simbolo" value="{{old('simbolo')}}" required  onkeyup="return mayus(this)">

                        @if ($errors->has('simbolo'))
                        <span class="invalid-feedback" role="alert">
                            <strong id="error-simbolo">{{ $errors->first('simbolo') }}</strong>
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

    $('#editar_tabla_detalle').submit(function(e){

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
                'descripcion' : $('#descripcion_editar').val(),
                'id_general' : $('#tabla_id').val(),
                'id': $('#tabla_id_editar').val(),
            }
      
        }).done(function (result){
           if (result.existe == true) {
            toastr.error('El registro ya ha sido ingresado','Error');
           }else{
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

                           document.getElementById("editar_tabla_detalle").submit();
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