<div class="chart-container">
    <div id="loadingDelivery" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="chart_delivery_gauge"></div>
</div>

<style>
    #chart_delivery_gauge {
        height: 300px;
    }
</style>

@push('scripts')
    <script>
        function showLoadingDelivery() {
            document.getElementById('loadingDelivery').classList.remove('hidden');
        }

        function hideLoadingDelivery() {
            document.getElementById('loadingDelivery').classList.add('hidden');
        }

        async function reloadDeliveryTime() {
            showLoadingDelivery();

            try {
                const res = await axios.get(route('dashboard.getDeliveryTime', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadDeliveryGauge(res.data.data);
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingDelivery();
                removeCreditos();
            }
        }

        function loadDeliveryGauge(data) {

            const value = parseFloat(data.promedio || 0);

            if (!value) {
                document.getElementById('chart_delivery_gauge').innerHTML = `
                    <div style="height:300px; display:flex; align-items:center; justify-content:center; color:#999;">
                        No hay datos disponibles
                    </div>
                `;
                return;
            }

            _Highcharts.chart('chart_delivery_gauge', {

                chart: {
                    type: 'solidgauge',
                    height: 300
                },

                title: {
                    text: 'Tiempo Promedio de Entrega'
                },

                pane: {
                    center: ['50%', '65%'],
                    size: '100%',
                    startAngle: -90,
                    endAngle: 90,
                    background: {
                        backgroundColor: '#EEE',
                        innerRadius: '60%',
                        outerRadius: '100%',
                        shape: 'arc'
                    }
                },

                yAxis: {
                    min: 0,
                    max: 7, // 🔥 ajusta según negocio
                    stops: [
                        [0.3, '#28a745'],
                        [0.6, '#ffc107'],
                        [1.0, '#dc3545']
                    ],
                    lineWidth: 0,
                    tickWidth: 0,
                    labels: {
                        enabled: false
                    }
                },

                tooltip: {
                    enabled: false
                },

                plotOptions: {
                    solidgauge: {
                        dataLabels: {
                            y: -20,
                            borderWidth: 0,
                            useHTML: true
                        }
                    }
                },

                series: [{
                    name: 'Tiempo',
                    data: [value],
                    dataLabels: {
                        format: `
                    <div style="text-align:center">
                        <span style="font-size:28px; font-weight:bold;">{y}</span><br/>
                        <span style="font-size:14px; opacity:0.6;">Días</span>
                    </div>
                `
                    }
                }]
            });
        }
    </script>
@endpush
