<div class="chart-container">
    <div id="loadingSales" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="h-sales"></div>
</div>

<style>
    .chart-container {
        position: relative;
        min-height: 350px;
    }

    #h-sales {
        height: 350px;
    }
</style>
@push('scripts')
    <script>
        let chartSalesYear = null;

        function showLoadingSales() {
            document.getElementById('loadingSales').classList.remove('hidden');
        }

        function hideLoadingSales() {
            document.getElementById('loadingSales').classList.add('hidden');
        }

        async function reloadSales() {
            showLoadingSales();

            try {
                const res = await axios.get(route('dashboard.getSales', {
                    year: document.querySelector('#filter_year').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadSalesYear(res.data.data);
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingSales();
                removeCreditos();
            }
        }

        function loadSalesYear(data) {
            const dataFormatted = formatDataSalesYear(data);

            const hasData = dataFormatted.some(v => v > 0);

            if (!hasData) {
                document.getElementById('h-sales').innerHTML = `
                    <div style="height:350px; display:flex; align-items:center; justify-content:center; color:#999;">
                        No hay datos disponibles
                    </div>
                `;
                return;
            }

            chartSalesYear = _Highcharts.chart('h-sales', {
                chart: {
                    type: 'area',
                    height: 350
                },

                title: {
                    text: 'Ventas por mes'
                },

                xAxis: {
                    categories: [
                        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                    ]
                },

                yAxis: {
                    title: {
                        text: 'Monto de Ventas (S/)'
                    }
                },

                tooltip: {
                    pointFormat: 'S/ <b>{point.y:,.2f}</b>'
                },

                plotOptions: {
                    area: {
                        dataLabels: {
                            enabled: true
                        },
                        marker: {
                            enabled: true
                        }
                    }
                },

                series: [{
                    name: 'Ventas',
                    data: dataFormatted
                }]
            });
        }

        function formatDataSalesYear(data) {
            let dataFormat = new Array(12).fill(0);

            data.forEach(item => {
                const mesIndex = item.mes - 1;
                dataFormat[mesIndex] = parseFloat(item.total_mes);
            });

            return dataFormat;
        }
    </script>
@endpush
