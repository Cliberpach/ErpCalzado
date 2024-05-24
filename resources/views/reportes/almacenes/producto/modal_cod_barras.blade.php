<div class="modal inmodal" id="modal_cod_barras" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-barcode modal-icon"></i>
                <h4 class="modal-title">CÓDIGO BARRAS</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 d-flex justify-content-center align-items-center flex-column">
                        <img src="" alt="" id="img_cod_barras" style="height: 50px;object-fit:contain;">
                        <p id="p_cod_barras" style="font-size: 20px;font-weight:bold;"></p>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a target="_blank" href="javascript:void(0);" class="btn btn-success text-white" id="ahesivos_item" > GENERAR ADHESIVOS</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>
