<div class="modal inmodal" id="modal_cerrar_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a5c35,#27ae60); color:white; border-bottom:none;">
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title mb-0">
                    <i class="fas fa-cash-register mr-2"></i>Cierre de Caja
                </h4>
            </div>

            <div class="modal-body pb-2">
                <form id="form-cerrar-caja" action="{{ route('Caja.cerrar') }}" method="POST">
                    {{ csrf_field() }}{{ method_field('POST') }}
                    <input type="hidden" name="movimiento_id" id="movimiento_id">
                    <input type="hidden" name="ingreso"       id="ingreso">
                    <input type="hidden" name="egreso"        id="egreso">
                    <input type="hidden" name="saldo"         id="saldo">

                    {{-- Info chips --}}
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <div class="p-2 rounded h-100" style="background:#eafaf1; border:1px solid #a9dfbf;">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-store-alt mr-1" style="color:#27ae60;"></i>CAJA
                                </small>
                                <span id="lbl_caja_nombre" class="font-weight-bold text-dark"></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-2 rounded h-100" style="background:#eafaf1; border:1px solid #a9dfbf;">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-user mr-1" style="color:#27ae60;"></i>COLABORADOR
                                </small>
                                <span id="lbl_colaborador" class="font-weight-bold text-dark"></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-2 rounded h-100" style="background:#eafaf1; border:1px solid #a9dfbf;">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-wallet mr-1" style="color:#27ae60;"></i>MONTO INICIAL
                                </small>
                                <span id="lbl_monto_inicial" class="font-weight-bold text-dark"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Resumen por método de pago --}}
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-uppercase mb-2"
                            style="color:#1a5c35; border-left:3px solid #27ae60; padding-left:8px; font-size:12px;">
                            <i class="fas fa-chart-bar mr-1"></i>Resumen por Método de Pago
                        </h6>
                        <table class="table table-sm table-bordered mb-0" style="font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="background:#1a5c35; color:white; border-color:#0e3d22;">Método</th>
                                    <th class="text-right" style="background:#1a5c35; color:#a9dfbf; border-color:#0e3d22;">
                                        Ingresos<br><small style="font-size:9px; opacity:.8;">(ventas + cobr.)</small>
                                    </th>
                                    <th class="text-right" style="background:#1a5c35; color:#f1948a; border-color:#0e3d22;">
                                        Egresos caja
                                    </th>
                                    <th class="text-right" style="background:#1a5c35; color:#f9a5a0; border-color:#0e3d22;">
                                        Pag. prov.
                                    </th>
                                    <th class="text-right" style="background:#1a5c35; color:white; border-color:#0e3d22;">
                                        Neto
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody_resumen_metodos">
                                <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 3 cards --}}
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded text-center" style="background:#d6eaf8; border:2px solid #2e86c1;">
                                <small class="d-block text-uppercase mb-1" style="color:#1a5276; font-size:10px;">
                                    <i class="fas fa-shopping-cart mr-1"></i>Total Venta del Día
                                    <br><span style="font-weight:normal; font-size:9px;">(ventas contado, todos los métodos)</span>
                                </small>
                                <h4 id="lbl_total_venta_dia" class="font-weight-bold mb-0" style="color:#1a5276;">—</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded text-center" style="background:#d5f5e3; border:2px solid #27ae60;">
                                <small class="d-block text-uppercase mb-1" style="color:#1a5c35; font-size:10px;">
                                    <i class="fas fa-money-bill-wave mr-1"></i>Efectivo neto del día
                                    <br><span style="font-weight:normal; font-size:9px;">(ventas + cobr. − egresos en efectivo)</span>
                                </small>
                                <h4 id="lbl_efectivo_neto" class="font-weight-bold mb-0" style="color:#1a5c35;">—</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded text-center" style="background:#fef9e7; border:2px solid #f39c12;">
                                <small class="d-block text-uppercase mb-1" style="color:#9a6d0a; font-size:10px;">
                                    <i class="fas fa-piggy-bank mr-1"></i>Saldo físico en caja
                                    <br><span style="font-weight:normal; font-size:9px;">(inicial + efectivo neto)</span>
                                </small>
                                <h4 id="lbl_saldo_caja" class="font-weight-bold mb-0" style="color:#9a6d0a;">—</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Saldo consolidado --}}
                    <div class="p-3 text-center rounded mt-1"
                         style="background:linear-gradient(135deg,#1a5c35,#148f55); color:white;">
                        <small class="d-block text-uppercase mb-1" style="font-size:10px; opacity:0.85;">
                            <i class="fas fa-balance-scale mr-1"></i>
                            Saldo Consolidado del Turno
                            <span style="font-size:9px; opacity:0.7;">
                                (inicial + ventas + cobranzas &minus; egresos caja &minus; pag. proveedor)
                            </span>
                        </small>
                        <h3 id="lbl_saldo_consolidado" class="font-weight-bold mb-0">—</h3>
                    </div>

                </form>
            </div>

            <div class="modal-footer" style="border-top:1px solid #dee2e6;">
                <small class="text-muted mr-auto">
                    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                    Revise los totales antes de confirmar.
                </small>
                <button type="button" id="btn_confirmar_cierre" class="btn btn-success btn-sm">
                    <i class="fas fa-lock mr-1"></i>Confirmar Cierre
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function eventsCerrarCaja() {
        document.getElementById('btn_confirmar_cierre').addEventListener('click', async function() {
            const confirm = await Swal.fire({
                title: '¿Cerrar caja?',
                text: 'Esta acción cerrará el turno definitivamente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#27ae60',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-lock"></i> Sí, cerrar',
                cancelButtonText: 'Cancelar',
                customClass: { container: 'my-swal' }
            });
            if (confirm.isConfirmed) {
                document.getElementById('form-cerrar-caja').submit();
            }
        });
    }

    function _fmtCierre(val) {
        return 'S/ ' + parseFloat(val || 0).toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function poblarModalCierre(data) {
        $('#ingreso').val(parseFloat(data.ingresos || 0).toFixed(2));
        $('#egreso').val(parseFloat(data.egresos  || 0).toFixed(2));
        $('#saldo').val(parseFloat(data.saldo     || 0).toFixed(2));

        $('#lbl_caja_nombre').text(data.caja);
        $('#lbl_colaborador').text(data.colaborador);
        $('#lbl_monto_inicial').text(_fmtCierre(data.monto_inicial));

        // Tabla resumen métodos (5 columnas)
        let rows = '';
        let sumIng = 0, sumEgr = 0, sumProv = 0;
        (data.resumenMetodos || []).forEach(function(f) {
            sumIng  += f.ingresos;
            sumEgr  += f.egresosCaja;
            sumProv += f.pagosProveedor;
            const neto   = f.neto;
            const clrIng  = f.ingresos       > 0 ? 'color:#1a5c35;font-weight:600;' : 'color:#bbb;';
            const clrEgr  = f.egresosCaja    > 0 ? 'color:#c0392b;font-weight:600;' : 'color:#bbb;';
            const clrProv = f.pagosProveedor > 0 ? 'color:#c0392b;font-weight:600;' : 'color:#bbb;';
            const clrNet  = neto >= 0             ? 'color:#1a5c35;font-weight:700;' : 'color:#c0392b;font-weight:700;';
            rows += '<tr>'
                  + '<td>' + f.nombre + '</td>'
                  + '<td class="text-right" style="' + clrIng  + '">' + _fmtCierre(f.ingresos)       + '</td>'
                  + '<td class="text-right" style="' + clrEgr  + '">' + _fmtCierre(f.egresosCaja)    + '</td>'
                  + '<td class="text-right" style="' + clrProv + '">' + _fmtCierre(f.pagosProveedor) + '</td>'
                  + '<td class="text-right" style="' + clrNet  + '">' + _fmtCierre(neto)             + '</td>'
                  + '</tr>';
        });
        const netTotal = sumIng - sumEgr - sumProv;
        rows += '<tr style="background:#d5f5e3;border-top:2px solid #27ae60;font-weight:700;">'
              + '<td>TOTAL</td>'
              + '<td class="text-right" style="color:#1a5c35;">'  + _fmtCierre(sumIng)    + '</td>'
              + '<td class="text-right" style="color:#c0392b;">'  + _fmtCierre(sumEgr)    + '</td>'
              + '<td class="text-right" style="color:#c0392b;">'  + _fmtCierre(sumProv)   + '</td>'
              + '<td class="text-right" style="color:#1a5c35;">'  + _fmtCierre(netTotal)  + '</td>'
              + '</tr>';
        document.getElementById('tbody_resumen_metodos').innerHTML = rows;

        $('#lbl_total_venta_dia').text(_fmtCierre(data.total_venta_dia));
        $('#lbl_efectivo_neto').text(_fmtCierre(data.resumenEfectivo.efectivoNeto));
        $('#lbl_saldo_caja').text(_fmtCierre(data.resumenEfectivo.saldoCajaDelDia));
        $('#lbl_saldo_consolidado').text(_fmtCierre(data.saldo_consolidado));

        $('#modal_cerrar_caja').modal('show');
    }
</script>
@endpush
