<div class="chart-container">
    <div id="loadingRankingVendedores" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="ranking-vendedores"></div>

    {{-- Se muestra cuando el periodo elegido no tiene ninguna venta: una tabla
         vacía sin explicación se lee como un error del sistema. --}}
    <div id="ranking-vendedores-vacio" class="text-center text-muted p-4" style="display:none;">
        <i class="fa fa-info-circle fa-2x mb-2"></i>
        <p class="mb-1"><strong>Sin ventas en el periodo seleccionado</strong></p>
        <p class="small mb-0">Prueba con otro mes, otro año u otra sede.</p>
    </div>
</div>

<style>
    #ranking-vendedores .rv-barra {
        background: #e9ecef;
        border-radius: 3px;
        height: 6px;
        overflow: hidden;
    }

    #ranking-vendedores .rv-barra > span {
        background: #1ab394;
        display: block;
        height: 100%;
    }

    #ranking-vendedores .rv-puesto {
        color: #999;
        font-weight: 700;
        width: 32px;
    }

    #ranking-vendedores td {
        vertical-align: middle;
    }
</style>

@push('scripts')
    <script>
        function showLoadingRankingVendedores() {
            document.getElementById('loadingRankingVendedores').classList.remove('hidden');
        }

        function hideLoadingRankingVendedores() {
            document.getElementById('loadingRankingVendedores').classList.add('hidden');
        }

        async function reloadRankingVendedores() {
            showLoadingRankingVendedores();

            try {
                const res = await axios.get(route('dashboard.getRankingVendedores', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadRankingVendedores(res.data.data);
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingRankingVendedores();
                removeCreditos();
            }
        }

        function loadRankingVendedores(data) {
            const tabla = document.getElementById('ranking-vendedores');
            const vacio = document.getElementById('ranking-vendedores-vacio');

            if (!data || data.length === 0) {
                tabla.innerHTML = '';
                tabla.style.display = 'none';
                vacio.style.display = 'block';
                return;
            }

            tabla.style.display = '';
            vacio.style.display = 'none';

            // La barra es relativa al primero, que ya viene ordenado por monto.
            const maximo = Number(data[0].monto) || 0;

            const soles = (n) => Number(n).toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const filas = data.map((v, i) => {
                const monto = Number(v.monto) || 0;
                const ancho = maximo > 0 ? (monto / maximo) * 100 : 0;

                return `
                    <tr>
                        <td class="rv-puesto">${i + 1}</td>
                        <td>
                            <div>${escapeHtmlRanking(v.vendedor)}</div>
                            <div class="rv-barra mt-1"><span style="width:${ancho.toFixed(1)}%"></span></div>
                        </td>
                        <td class="text-right">${Number(v.ventas).toLocaleString('es-PE')}</td>
                        <td class="text-right">${Number(v.pares).toLocaleString('es-PE')}</td>
                        <td class="text-right"><strong>S/ ${soles(monto)}</strong></td>
                    </tr>`;
            }).join('');

            tabla.innerHTML = `
                <table class="table table-sm mb-1">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>Vendedor</th>
                            <th class="text-right" style="width:110px;">Ventas</th>
                            <th class="text-right" style="width:110px;">Pares</th>
                            <th class="text-right" style="width:160px;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>${filas}</tbody>
                </table>
                <p class="small text-muted mb-0">
                    Incluye ventas al contado y al crédito, por eso no cuadra con el arqueo de caja.
                </p>`;
        }

        // El nombre sale de users.usuario, que lo teclea una persona: se escapa
        // antes de meterlo en el HTML.
        function escapeHtmlRanking(texto) {
            const div = document.createElement('div');
            div.textContent = texto == null ? '' : texto;
            return div.innerHTML;
        }
    </script>
@endpush
