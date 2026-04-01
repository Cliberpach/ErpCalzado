<div class="mb-2">
    <select id="filtroTipo" onchange="loadHPares()">
        <option value="year">Por Mes</option>
        <option value="month">Por Día</option>
    </select>
</div>
<div class="chart-container">
    <div id="loadingChart" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="pares"></div>
</div>

<style>
    .loader {
        animation: rotate 1s infinite;
        height: 50px;
        width: 50px;
    }

    .loader:before,
    .loader:after {
        border-radius: 50%;
        content: "";
        display: block;
        height: 20px;
        width: 20px;
    }

    .loader:before {
        animation: ball1 1s infinite;
        background-color: #3498db;
        /* azul */
        box-shadow: 30px 0 0 #ff3d00;
        margin-bottom: 10px;
    }

    .loader:after {
        animation: ball2 1s infinite;
        background-color: #ff3d00;
        box-shadow: 30px 0 0 #3498db;
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg) scale(0.8)
        }

        50% {
            transform: rotate(360deg) scale(1.2)
        }

        100% {
            transform: rotate(720deg) scale(0.8)
        }
    }

    @keyframes ball1 {
        0% {
            box-shadow: 30px 0 0 #ff3d00;
        }

        50% {
            box-shadow: 0 0 0 #ff3d00;
            margin-bottom: 0;
            transform: translate(15px, 15px);
        }

        100% {
            box-shadow: 30px 0 0 #ff3d00;
            margin-bottom: 10px;
        }
    }

    @keyframes ball2 {
        0% {
            box-shadow: 30px 0 0 #3498db;
        }

        50% {
            box-shadow: 0 0 0 #3498db;
            margin-top: -20px;
            transform: translate(15px, 15px);
        }

        100% {
            box-shadow: 30px 0 0 #3498db;
            margin-top: 0;
        }
    }
</style>

<style>
    .chart-container {
        position: relative;
        min-height: 350px;
    }

    #pares {
        height: 350px;
    }

    /* Loading centrado */
    .loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        background: rgba(255, 255, 255, 0.8);
        z-index: 10;

        transition: opacity 0.3s ease;
    }

    .loading.hidden {
        opacity: 0;
        pointer-events: none;
    }
</style>

@push('scripts')
    <script>
        function showLoading() {
            document.getElementById('loadingChart').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingChart').classList.add('hidden');
        }

        async function loadHPares() {
            const tipo = document.getElementById('filtroTipo').value;

            showLoading();

            try {
                const res = await axios.get(route('dashboard.getParesYearMonth', {
                    tipo: tipo,
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));
                if (res.data.success) {
                    loadChart(res.data.data, tipo);
                } else {

                }
            } catch (error) {

            } finally {
                hideLoading();
                removeCreditos();
            }
        }

        function loadChart(data, tipo) {
            _Highcharts.chart('pares', {
                chart: {
                    type: 'column',
                    height: 350
                },

                title: {
                    text: tipo === 'year' ?
                        'Pares Vendidos por Mes' : 'Pares Vendidos por Día'
                },

                xAxis: {
                    categories: data.categories
                },

                yAxis: {
                    title: {
                        text: 'Cantidad de Pares'
                    }
                },

                series: [{
                    name: 'Pares',
                    data: data.values
                }]
            });
        }
    </script>
@endpush
