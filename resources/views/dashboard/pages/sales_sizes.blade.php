<div class="chart-container">
    <div id="loadingSizes" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="ventas-tallas"></div>
</div>

<style>
    #ventas-tallas {
        height: 350px;
    }
</style>

@push('scripts')
    <script>
        function showLoadingSizes() {
            document.getElementById('loadingSizes').classList.remove('hidden');
        }

        function hideLoadingSizes() {
            document.getElementById('loadingSizes').classList.add('hidden');
        }

        async function reloadVentasTallas() {
            showLoadingSizes();

            try {
                const res = await axios.get(route('dashboard.getSalesSizes', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadVentasTallas(res.data.data);
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingSizes();
                removeCreditos();
            }
        }

        function loadVentasTallas(data) {

            const hasData = data.values.some(v => v > 0);

            if (!hasData) {
                document.getElementById('ventas-tallas').innerHTML = `
                <div style="height:350px; display:flex; align-items:center; justify-content:center; color:#999;">
                    No hay datos disponibles
                </div>
            `;
                return;
            }

            _Highcharts.chart('ventas-tallas', {
                chart: {
                    type: 'bar',
                    height: 350
                },

                title: {
                    text: 'Ventas por Talla (Más Demandadas)'
                },

                xAxis: {
                    categories: data.categories
                },

                yAxis: {
                    min: 0,
                    title: {
                        text: 'Pares Vendidos',
                        align: 'high'
                    }
                },

                tooltip: {
                    valueSuffix: ' pares'
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
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
