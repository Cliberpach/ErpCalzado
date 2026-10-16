
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
            <div class="modal-body content_cliente">
                
                
                <div class="row">
                    {{-- La sede manda: de ella salen la serie de boleta que se busca y la
                         serie R del resumen. Sin el permiso sólo aparece la propia. --}}
                    <div class="col-4">
                        <label for="sede_resumen">Sede</label>
                        <select id="sede_resumen" class="form-control" @if(!$puede_todas_sedes) disabled @endif>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" @if($sede->id == $sede_id) selected @endif>{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Ya no se pide una fecha: se traen TODAS las pendientes y se
                         agrupan por fecha de emisión, porque SUNAT no admite varias
                         fechas en un mismo resumen. El tope evita que un dato viejo
                         dispare una decena de resúmenes de golpe. --}}
                    <div class="col-3">
                        <label for="dias_pendientes">Días hacia atrás</label>
                        <input id="dias_pendientes" type="number" min="1" max="365" class="form-control" value="30">
                    </div>
                    <div class="col-5 d-flex align-items-end">
                        <button class="btn btn-primary" id="btn-get-comprobantes">Buscar pendientes</button>
                    </div>
                </div>

                {{-- Qué se va a emitir, dicho antes de emitir nada --}}
                <div id="plan_resumenes" class="alert alert-info mt-3" style="display:none;"></div>
                <div id="aviso_anteriores" class="alert alert-warning mt-3" style="display:none;"></div>
                <div id="aviso_anuladas" class="alert alert-danger mt-3" style="display:none;"></div>

                {{-- Un bloque por fecha: de cada uno sale un resumen --}}
                <div id="grupos_pendientes" class="mt-3"></div>

              
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