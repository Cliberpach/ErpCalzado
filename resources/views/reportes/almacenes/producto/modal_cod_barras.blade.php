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
                        <span style="font-weight: bold;">MODELO: </span><p class="modelo_nombre"></p>
                        <span style="font-weight: bold;">PRODUCTO: </span><p class="producto_nombre"></p>
                        <span style="font-weight: bold;">COLOR: </span><p class="color_nombre"></p>
                        <span style="font-weight: bold;">TALLA: </span><p class="talla_nombre"></p>
                        <span style="font-weight: bold;">STOCK: </span><p class="producto_stock"></p>
                        <span style="font-weight: bold;">STOCK_LÓGICO: </span><p class="producto_stock_logico"></p>
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

<script>
    function setData(producto){
        document.querySelector('.modelo_nombre').textContent            =   producto.producto.modelo_nombre;
        document.querySelector('.producto_nombre').textContent          =   producto.producto.producto_nombre;
        document.querySelector('.color_nombre').textContent             =   producto.producto.color_nombre;
        document.querySelector('.talla_nombre').textContent             =   producto.producto.talla_nombre;
        document.querySelector('.producto_stock').textContent           =   producto.producto.stock;
        document.querySelector('.producto_stock_logico').textContent    =   producto.producto.stock_logico;
    }

    function clearData(){
        document.querySelector('.modelo_nombre').textContent            =   '';
        document.querySelector('.producto_nombre').textContent          =   '';
        document.querySelector('.color_nombre').textContent             =   '';
        document.querySelector('.talla_nombre').textContent             =   '';
        document.querySelector('.producto_stock').textContent           =   '';
        document.querySelector('.producto_stock_logico').textContent    =   '';
    }
</script>