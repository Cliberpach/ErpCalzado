<div class="modal inmodal" id="modal_detalles_colaboradores" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{route('Caja.liberarColaborades')}}" method="post">
          @method('post')
          @csrf
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <i class="fa fa-cogs modal-icon d-none"></i>
                    <h4 class="modal-title">Detalles Colaboradores</h4>
                    <small class="font-bold"> Colaboradores</small>
                    <button type="submit" class="btn btn-block btn-w-m  btn-primary m-t-md">Liberar Colaborades </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <h3 class="text-center"> Colaboradores </h3>
                        <table class="table table-sm table-striped table-bordered table-hover"
                            style="text-transform:uppercase" id="usuarios_venta">
                            <thead>
                                <tr>
    
                                    <th class="text-center">
    
                                    </th>
                                    <th class="text-center">USUARIO</th>
    
                                    <th class="text-center">Fecha Entrada</th>
    
    
                                </tr>
                            </thead>
                            <tbody>
    
    
    
                            </tbody>
                            <tbody>
    
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i> <small>Seleccionar a los colaborades que desea liberar de caja</small>
                    </div>
                </div>
    
            </div>
        </form>
        
    </div>
</div>
