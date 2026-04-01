<div class="chart-container">
    <div id="loadingColor" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="ventas-color"></div>
</div>

<style>
    #ventas-color {
        height: 350px;
    }
</style>

@push('scripts')
    <script>
        function showLoadingColor() {
            document.getElementById('loadingColor').classList.remove('hidden');
        }

        function hideLoadingColor() {
            document.getElementById('loadingColor').classList.add('hidden');
        }

        async function reloadVentasColor() {
            showLoadingColor();

            try {
                const res = await axios.get(route('dashboard.getSalesColor', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadVentasColor(res.data.data);
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingColor();
                removeCreditos();
            }
        }

        function loadVentasColor(data) {
            _Highcharts.chart('ventas-color', {
                chart: {
                    type: 'bar',
                    height: 350
                },

                title: {
                    text: 'Ventas por Color (Más Demandados)'
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
