<div class="modal inmodal" id="modal_create_metodo_entrega" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa-solid fa-truck-front modal-icon"></i>             
                <h4 class="modal-title">NUEVO MÉTODO DE ENTREGA</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body">
                @include('components.overlay_search')
                @include('components.overlay_save')
                <form id="frmMetodoEntrega" class="formulario">
                    <div class="row">
                        <div class="col">
                            <label for="empresa">EMPRESA <span style="color:orange;font-weight:bold;">*</span></label>
                            <input name="empresa" required type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col mt-3">
                        <label for="tipo_envio" style="font-weight: bold;">
                            TIPO ENVÍO<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <select required name="tipo_envio" id="tipo_envio" class="select2_form form-control">
                            @foreach ($tipos_envio as $tipo_envio)
                                <option value="{{$tipo_envio->id}}">{{$tipo_envio->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="frmMetodoEntrega" style="color:white;"><i
                            class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function eventsCreate(){
        
        document.querySelector('#frmMetodoEntrega').addEventListener('submit',async (e)=>{
            e.preventDefault();
           
            const formData  =   new FormData(e.target);

            const res   =   await axios.post(route('mantenimiento.metodo_entrega.store'),formData);
            console.log(res);

            if(res.data.success){
                $('.dataTables-metodos_entrega').DataTable().ajax.reload();
                $('#modal_create_metodo_entrega').modal('hide');
                e.target.reset();
                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(`${res.data.message} - ${res.data.exception}`,'ERROR');
            }

        })
    }
</script>


