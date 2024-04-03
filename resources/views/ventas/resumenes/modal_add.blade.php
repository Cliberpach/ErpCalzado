<!-- Modal -->
<div class="modal inmodal" id="modal_resumenes" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-book modal-icon"></i>                
                <h4 class="modal-title">REGISTRAR RESUMEN</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente" :class="{'sk__loading':loading}">
                <div class="row">
                    <div class="col-4">
                        <label for="fecha_comprobante">Fecha de Emisión de comprobantes</label>
                        <input id="fecha_comprobante" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-8 d-flex align-items-end">
                        <button class="btn btn-primary" id="btn-get-comprobantes">Buscar comprobantes</button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <table class="table table-search-comprobantes">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Número</th>
                                <th scope="col">Moneda</th>
                                <th scope="col">T.gravado</th>
                                <th scope="col">T.Igv</th>
                                <th scope="col">T.Total</th>
                                <th></th>
                              </tr>
                            </thead>
                            <tbody>
                             
                            </tbody>
                          </table>
                    </div>
                </div>

                {{-- <form id="frmCliente" class="formulario">
                   
                </form>
                <div class="sk-spinner sk-spinner-wave d-none">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm btn-guardar-resumen" form="frmCliente" style="color:white;">
                        <i class="fa fa-save btn-guardar-resumen"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>