<div class="modal inmodal" id="modal-bultos" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-xs">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-box-full modal-icon"></i>                
                <h4 class="modal-title">N° BULTOS</h4>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')

               <form action="">
                    <div class="row">
                        <div class="col-12">
                            <label for="inputNroBultos">NRO° BULTOS</label>
                            <input type="text" id="inputNroBultos" class="form-control">
                        </div>
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
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>

    </div>
</div>