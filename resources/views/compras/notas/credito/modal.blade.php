<div class="modal fade inmodal" id="modal_editar_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" onclick="limpiarForm()" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title title">Detalle</h4>
                <small class="font-bold detail"></small>
            </div>
            <div class="modal-body">
                <form id="frm-detalle">
                    <div class="form-group row d-none">
                        <div class="col-12 col-md-6">
                            <label class="required">Indice</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" id="indice" onkeypress="return isNumber(event)" required readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="required">Cantidad a devolver</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" id="cantidad_devolver" onkeypress="return filterFloat(event, this, false);" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="required">Descripción</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <textarea type="text" class="form-control" id="descripcion" rows="2" required readonly></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="required">Precio unitario</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" id="precio_unitario" onkeypress="return filterFloat(event, this);" readonly required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="required">Descuento por devolución</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" value="0.00" id="descuento_dev" onkeypress="return filterFloat(event, this);" readonly required>
                        </div>
                    </div>
                    <div class="form-group row @if($documento->tipo_venta == '129') d-none @endif">
                        <div class="col-12 col-md-6">
                            <label class="required">IGV 18%</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" id="monto_igv" onkeypress="return filterFloat(event, this);" readonly required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="required">Importe de venta</label>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" class="form-control" id="importe_venta" onkeypress="return filterFloat(event, this);" readonly required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" id="btn_editar_detalle" class="btn btn-primary btn-sm" form="frm-detalle"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" onclick="limpiarForm()" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#frm-detalle').submit(function(e){
        e.preventDefault();
        let index = $('#indice').val();
        let cantidad = $('#cantidad_devolver').val();
        let precio_unitario = $('#precio_unitario').val();
        let table = $('#tbl-detalles').DataTable();
        table.cell({
            row: index,
            column: 1
        }).data(cantidad).draw();

        table.cell({
            row: index,
            column: 3
        }).data(precio_unitario).draw();

        table.cell({
            row: index,
            column: 6
        }).data(1).draw();

        $('#modal_editar_detalle').modal('hide');
        sumaTotal();
    })
</script>
@endpush
