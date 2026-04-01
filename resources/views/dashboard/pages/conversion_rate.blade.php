<div class="chart-container">
    <div id="loadingConversion" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="chart_conversion"></div>
</div>

<style>
    #chart_conversion {
        height: 350px;
    }
</style>

@push('scripts')
    <script>
        let chartConversion = null;

        function showLoadingConversion() {
            document.getElementById('loadingConversion').classList.remove('hidden');
        }

        function hideLoadingConversion() {
            document.getElementById('loadingConversion').classList.add('hidden');
        }

        async function reloadConversionRate() {
            showLoadingConversion();

            try {
                const res = await axios.get(route('dashboard.getConversionRate', {
                    year: document.querySelector('#filter_year').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadConversionRate(res.data.data);
                } else {
                    toastr.error('Error al obtener conversión');
                }

            } catch (error) {
                toastr.error('Error en la petición');
                console.error(error);
            } finally {
                hideLoadingConversion();
                removeCreditos();
            }
        }

        function loadConversionRate(data) {

            const dataFormatted = formatDataConversion(data);

            const hasData =
                dataFormatted.cotizaciones.some(v => v > 0) ||
                dataFormatted.ventas.some(v => v > 0);

            // 🔥 Si no hay datos
            if (!hasData) {
                document.getElementById('chart_conversion').innerHTML = `
                    <div style="height:350px; display:flex; align-items:center; justify-content:center; color:#999;">
                        No hay datos disponibles
                    </div>
                `;
                return;
            }

            chartConversion = _Highcharts.chart('chart_conversion', {
                chart: {
                    zoomType: 'xy',
                    height: 350
                },

                title: {
                    text: 'Cotizaciones vs Ventas (Tasa de Conversión)'
                },

                xAxis: {
                    categories: [
                        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                    ]
                },

                yAxis: [{
                    title: {
                        text: 'Cantidad'
                    }
                }, {
                    title: {
                        text: '% Conversión'
                    },
                    labels: {
                        format: '{value}%'
                    },
                    opposite: true
                }],

                tooltip: {
                    shared: true
                },

                series: [{
                        name: 'Cotizaciones',
                        type: 'column',
                        data: dataFormatted.cotizaciones,
                        color: '#ffc107'
                    },
                    {
                        name: 'Ventas',
                        type: 'column',
                        data: dataFormatted.ventas,
                        color: '#28a745'
                    },
                    {
                        name: '% Conversión',
                        type: 'spline',
                        yAxis: 1,
                        data: dataFormatted.conversion,
                        tooltip: {
                            valueSuffix: ' %'
                        },
                        color: '#dc3545'
                    }
                ]
            });
        }

        function formatDataConversion(data) {

            let cotizaciones = new Array(12).fill(0);
            let ventas = new Array(12).fill(0);
            let conversion = new Array(12).fill(0);

            data.forEach(item => {
                const index = item.mes - 1;

                cotizaciones[index] = parseFloat(item.total_cotizaciones);
                ventas[index] = parseFloat(item.total_ventas);
                conversion[index] = parseFloat(item.conversion);
            });

            return {
                cotizaciones,
                ventas,
                conversion
            };
        }
    </script>
@endpush
