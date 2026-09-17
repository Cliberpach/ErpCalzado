<div class="chart-container">
    <div id="loadingRankingVendedores" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    {{-- Este widget vive dentro de la tarjeta "Estadísticas", a media anchura y
         sin cabecera propia, así que lleva su título dentro como los gráficos
         de al lado (que lo pintan desde Highcharts). --}}
    <h5 class="rv-titulo">Top 8 Vendedores</h5>

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
    .rv-titulo {
        color: #333;
        font-size: 18px;
        font-weight: 400;
        margin: 8px 0 14px;
        text-align: center;
    }

    #ranking-vendedores table {
        font-size: 13px;
        table-layout: fixed;
        width: 100%;
    }

    #ranking-vendedores th {
        border-top: 0;
        color: #888;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .03em;
        padding: 4px 6px;
        text-transform: uppercase;
    }

    #ranking-vendedores td {
        padding: 5px 6px;
        vertical-align: middle;
    }

    #ranking-vendedores .rv-puesto {
        color: #b0b0b0;
        font-weight: 700;
        text-align: center;
    }

    /* La barra va de fondo de la celda del nombre: a media anchura no hay sitio
       para una fila propia, y así no roba alto a la tabla. */
    #ranking-vendedores .rv-nombre {
        background-repeat: no-repeat;
        border-radius: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #ranking-vendedores .rv-num {
        font-variant-numeric: tabular-nums;
        text-align: right;
        white-space: nowrap;
    }

    #ranking-vendedores .rv-monto {
        font-weight: 600;
    }

    #ranking-vendedores .rv-pie {
        color: #999;
        font-size: 11px;
        line-height: 1.3;
        margin: 6px 0 0;
    }

    /* Ventana estrecha: Bootstrap 4 mantiene col-6 también en móvil, así que la
       tabla se queda a media pantalla. Se recorta lo prescindible antes de que
       las cifras empiecen a partirse. */
    @media (max-width: 991px) {
        #ranking-vendedores table {
            font-size: 12px;
        }

        #ranking-vendedores .rv-ventas {
            display: none;
        }
    }

    @media (max-width: 575px) {
        #ranking-vendedores .rv-pares {
            display: none;
        }
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

            const entero = (n) => Number(n).toLocaleString('es-PE', {
                maximumFractionDigits: 0
            });
            const conDecimales = (n) => Number(n).toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const filas = data.map((v, i) => {
                const monto = Number(v.monto) || 0;
                const pct = maximo > 0 ? Math.max(0, Math.min(100, (monto / maximo) * 100)) : 0;
                const nombre = escapeHtmlRanking(v.vendedor);

                // Degradado en vez de un <span>: se adapta solo al ancho de la
                // celda, que aquí cambia con la ventana.
                const fondo = `linear-gradient(to right, rgba(26,179,148,.20) ${pct.toFixed(1)}%, transparent ${pct.toFixed(1)}%)`;

                return `
                    <tr>
                        <td class="rv-puesto">${i + 1}</td>
                        <td class="rv-nombre" style="background-image:${fondo}" title="${nombre}">${nombre}</td>
                        <td class="rv-num rv-ventas">${entero(v.ventas)}</td>
                        <td class="rv-num rv-pares">${entero(v.pares)}</td>
                        <td class="rv-num rv-monto" title="S/ ${conDecimales(monto)}">S/&nbsp;${entero(monto)}</td>
                    </tr>`;
            }).join('');

            // Monto redondeado para que quepan las cinco columnas a media
            // anchura; el importe exacto queda en el title de la celda.
            tabla.innerHTML = `
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th style="width:26px;"></th>
                            <th>Vendedor</th>
                            <th class="rv-num rv-ventas" style="width:62px;">Ventas</th>
                            <th class="rv-num rv-pares" style="width:62px;">Pares</th>
                            <th class="rv-num" style="width:92px;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>${filas}</tbody>
                </table>
                <p class="rv-pie">Incluye ventas al contado y al crédito, por eso no cuadra con el arqueo de caja.</p>`;
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
