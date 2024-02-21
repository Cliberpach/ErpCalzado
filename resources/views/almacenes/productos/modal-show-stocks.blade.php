<div class="modal inmodal" id="modal_show_stocks" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
      <div class="modal-content animated bounceInRight">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">
                  <span aria-hidden="true">&times;</span>
                  <span class="sr-only">Close</span>
              </button>
              <i class="fa fa-cogs modal-icon"></i>
              <h4 class="modal-title">Stocks</h4>
              <small class="font-bold">Listado de stocks por color y talla.</small>
          </div>
          <div class="modal-body" style="height: 50vh;overflow-y: auto;">
           
            @include('almacenes.productos.table-color-tallas')

          </div>
              
                  <div class="modal-footer">
                      <div class="col-md-6 text-left" style="color:#fcbc6c">
                          <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                      </div>
                      <div class="col-md-6 text-right">
                          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                      </div>
                  </div>
              
             
      </div>
  </div>
</div>