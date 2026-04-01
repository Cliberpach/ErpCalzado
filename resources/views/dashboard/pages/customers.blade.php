<div class="chart-container">
    <div id="loadingClients" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="chart_clients"></div>
</div>

<style>
    #chart_clients {
        height: 300px;
    }
</style>

@push('scripts')
    <script>
        let chartClients = null;

        function showLoadingClients() {
            document.getElementById('loadingClients').classList.remove('hidden');
        }

        function hideLoadingClients() {
            document.getElementById('loadingClients').classList.add('hidden');
        }

        async function reloadClients() {
            showLoadingClients();

            try {
                const res = await axios.get(route('dashboard.getCustomersActives', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadClientsData(res.data.data);
                } else {
                    toastr.error('Error al obtener clientes');
                }

            } catch (error) {
                toastr.error('Error en la petición');
                console.error(error);
            } finally {
                hideLoadingClients();
                removeCreditos();
            }
        }

        function loadClientsData(customers) {

            const nuevos = parseInt(customers.clientes_nuevos || 0);
            const recurrentes = parseInt(customers.clientes_recurrentes || 0);

            const total = nuevos + recurrentes;

            // 🔥 Si no hay datos
            if (total === 0) {
                document.getElementById('chart_clients').innerHTML = `
                    <div style="height:300px; display:flex; align-items:center; justify-content:center; color:#999;">
                        No hay datos disponibles
                    </div>
                `;
                return;
            }

            const data = [{
                    name: 'Nuevos',
                    y: nuevos,
                    color: '#28a745'
                },
                {
                    name: 'Recurrentes',
                    y: recurrentes,
                    color: '#007bff'
                }
            ];

            chartClients = _Highcharts.chart('chart_clients', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent',
                    height: 300
                },

                title: {
                    text: 'Clientes Nuevos vs Recurrentes',
                    align: 'center',
                    verticalAlign: 'top',
                    style: {
                        fontSize: '16px',
                        fontWeight: 'bold'
                    }
                },

                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },

                plotOptions: {
                    pie: {
                        innerSize: '60%', // 🔥 dona
                        depth: 45,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name} {point.percentage:.0f}%',
                            connectorColor: 'silver',
                            style: {
                                fontSize: '14px'
                            }
                        }
                    }
                },

                series: [{
                    name: 'Clientes',
                    data: data
                }]
            });
        }
    </script>
@endpush
