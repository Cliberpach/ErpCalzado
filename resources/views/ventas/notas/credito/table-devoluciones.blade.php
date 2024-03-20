<div class="row">
    <div class="col-12">
        <div class="panel panel-primary" id="panel_detalle">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-10">
                        <h4><b>Detalle de la nota de @if(isset($nota_venta)) devolución @else crédito @endif</b></h4>
                    </div>
                    {{-- <div class="col-2 text-right">
                        <button type="button" class="ladda-button ladda-button-demo btn btn-secondary btn-sm" onclick="actualizarData({{ $documento->id }})" data-style="zoom-in"><i class="fa fa-refresh"></i></button>
                    </div> --}}
                </div>
            </div>
            <div class="panel-body ibox-content">
                <div class="sk-spinner sk-spinner-wave">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="tbl-detalles-devolucion" class="table table-hover tbl-detalles" style="width: 100%; text-transform:uppercase;">
                                <thead>
                                    <th></th>
                                    <th>Cant.</th>
                                    <th>Descripcion</th>
                                    <th>P. Unit</th>
                                    <th>Total</th>
                                    <th class="tbl-devolucion-opciones">Opciones</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-md-8"></div>
                            <div class="col-12 col-md-4">
                                <div class="form-group row @if($documento->tipo_venta == '129') d-none @endif">
                                    <div class="col-12 col-md-6">
                                        <label class="required">Sub Total</label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="sub_total_nuevo" id="sub_total_nuevo" value="" readonly>
                                    </div>
                                </div>
                                <div class="form-group row @if($documento->tipo_venta == '129') d-none @endif">
                                    <div class="col-12 col-md-6">
                                        <label class="required">IGV {{$documento->igv }}%</label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="total_igv_nuevo" id="total_igv_nuevo" value="" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12 col-md-6">
                                        <label class="required">Total</label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="total_nuevo" id="monto_total_devolucion" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>