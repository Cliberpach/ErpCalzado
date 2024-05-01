<div class="modal inmodal" id="modal_edit_metodo_entrega" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa-solid fa-truck-front modal-icon"></i>             
                <h4 class="modal-title">EDITAR MÉTODO DE ENTREGA</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body">
                @include('components.overlay_search')
                @include('components.overlay_save')
                <form id="frmMetodoEntregaUpdate" class="formulario">
                    <input type="text" hidden name="empresa_envio_id" id="empresa_envio_id">
                    <div class="row">
                        <div class="col">
                            <label for="empresa">EMPRESA <span style="color:orange;font-weight:bold;">*</span></label>
                            <input id="empresa_edit" name="empresa_edit" required type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col mt-3">
                        <label for="tipo_envio" style="font-weight: bold;">
                            TIPO ENVÍO<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <select required name="tipo_envio_edit" id="tipo_envio_edit" class="select2_form form-control">
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
                    <button type="submit" class="btn btn-primary btn-sm" form="frmMetodoEntregaUpdate" style="color:white;"><i
                            class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function eventsUpdate(){
        
        document.querySelector('#frmMetodoEntregaUpdate').addEventListener('submit',async (e)=>{
            e.preventDefault();
    
            const formData          =   new FormData(e.target);
    
            const res               =   await axios.post(route('mantenimiento.metodo_entrega.update'),formData);
            console.log(res);
     
            if(res.data.success){
                $('.dataTables-metodos_entrega').DataTable().ajax.reload();
                $('#modal_edit_metodo_entrega').modal('hide');
                e.target.reset();
                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(`${res.data.message} - ${res.data.exception}`,'ERROR');
            }

        })
    }
</script>


