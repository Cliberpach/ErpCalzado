<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="fas fa-shipping-fast"></i>Despachos Pendientes</h5>
    </div>

    <div class="card-body p-2">
        <ul id="listDispatchs" class="list-group list-group-flush">

        </ul>
    </div>
</div>

@push('scripts')
<script>
    function loadDispatchs(dispatchs) {
        const container = document.getElementById('listDispatchs');
        container.innerHTML = '';

        dispatchs.forEach(envio => {
            // Icono y color según estado del despacho
            let badgeClass = 'secondary';
            let estadoIcon = 'fas fa-question-circle';
            if (envio.estado === 'PENDIENTE') {
                badgeClass = 'danger';
                estadoIcon = 'fas fa-exclamation-circle';
            } else if (envio.estado === 'EMBALADO') {
                badgeClass = 'warning';
                estadoIcon = 'fas fa-box';
            } else if (envio.estado === 'DESPACHADO') {
                badgeClass = 'success';
                estadoIcon = 'fas fa-truck';
            }

            // Icono de venta
            const ventaIcon = 'fas fa-file-invoice-dollar';

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.style.cursor = 'pointer';
            li.innerHTML = `
                <div>
                    <i class="${estadoIcon} mr-2"></i>
                    <strong>#${envio.id}</strong> - Cliente ${envio.cliente_nombre} <br>
                    <small class="text-muted">
                        <i class="${ventaIcon} mr-1"></i>
                        VENTA: ${envio.serie} - ${envio.correlativo} <br>
                        ${timeAgo(envio.created_at)}
                    </small>
                </div>
                <span class="badge badge-${badgeClass} badge-pill px-3 py-2">
                    ${capitalize(envio.estado)}
                </span>
            `;
            container.appendChild(li);
        });
    }

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Hace unos segundos';
        else if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
        else if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
        else return `Hace ${Math.floor(diff / 86400)} días`;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
</script>
@endpush
