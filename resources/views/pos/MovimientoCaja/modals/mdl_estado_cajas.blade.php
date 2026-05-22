<div class="modal inmodal" id="modal_estado_cajas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a2e5c,#2e4a8e); color:white; border-bottom:none;">
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title mb-0">
                    <i class="fas fa-store-alt mr-2"></i>Estado de Cajas
                </h4>
            </div>

            <div class="modal-body">
                <div id="alerta_todo_cerrado" class="alert alert-success d-none" style="font-size:13px;">
                    <i class="fas fa-check-circle mr-2"></i>Todas las cajas están cerradas correctamente.
                </div>
                <div id="alerta_cajas_abiertas" class="alert alert-warning d-none" style="font-size:13px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Hay cajas <strong>abiertas</strong>. Por favor verifique quién las tiene y ciérrelas si ya no están en uso.
                </div>
                <table class="table table-sm table-bordered mb-0" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th style="background:#1a2e5c; color:white; border-color:#0e1f3d;">Caja</th>
                            <th class="text-center" style="background:#1a2e5c; color:white; border-color:#0e1f3d; width:110px;">Estado</th>
                            <th style="background:#1a2e5c; color:white; border-color:#0e1f3d;">Colaborador</th>
                            <th style="background:#1a2e5c; color:white; border-color:#0e1f3d;">Abierta desde</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_estado_cajas">
                        <tr><td colspan="4" class="text-center text-muted py-3">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Cargando...
                        </td></tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer" style="border-top:1px solid #dee2e6;">
                <small class="text-muted mr-auto" style="font-size:11px;">
                    <i class="fas fa-sync-alt mr-1"></i>
                    <a href="#" onclick="cargarEstadoCajas(); return false;" style="color:#2e4a8e;">Actualizar</a>
                </small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    async function abrirModalEstadoCajas() {
        document.getElementById('alerta_todo_cerrado').classList.add('d-none');
        document.getElementById('alerta_cajas_abiertas').classList.add('d-none');
        document.getElementById('tbody_estado_cajas').innerHTML =
            '<tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando...</td></tr>';
        $('#modal_estado_cajas').modal('show');
        await cargarEstadoCajas();
    }

    async function cargarEstadoCajas() {
        try {
            const res = await axios.get('{{ route("Caja.estadoCajas") }}');
            const cajas = res.data;

            let rows = '';
            let hayAbierta = false;

            cajas.forEach(function(c) {
                const esAbierta = c.estado_caja === 'ABIERTA';
                if (esAbierta) hayAbierta = true;
                const badge = esAbierta
                    ? '<span class="badge badge-danger" style="font-size:11px;">ABIERTA</span>'
                    : '<span class="badge badge-success" style="font-size:11px;">CERRADA</span>';
                const rowStyle = esAbierta ? 'background:#fff8e1;' : '';
                rows += `<tr style="${rowStyle}">
                    <td><strong>${c.nombre}</strong></td>
                    <td class="text-center">${badge}</td>
                    <td>${c.colaborador}</td>
                    <td style="font-size:12px;">${c.desde}</td>
                </tr>`;
            });

            document.getElementById('tbody_estado_cajas').innerHTML =
                rows || '<tr><td colspan="4" class="text-center text-muted">Sin cajas registradas</td></tr>';

            if (hayAbierta) {
                document.getElementById('alerta_cajas_abiertas').classList.remove('d-none');
            } else {
                document.getElementById('alerta_todo_cerrado').classList.remove('d-none');
            }
        } catch (e) {
            document.getElementById('tbody_estado_cajas').innerHTML =
                '<tr><td colspan="4" class="text-center text-danger py-3"><i class="fas fa-exclamation-circle mr-1"></i>Error al cargar los datos</td></tr>';
        }
    }
</script>
@endpush
