<div class="mb-2">
    <select id="filtroTipoSalesOrigin" onchange="reloadVentasOrigen()">
        <option value="year">Por Año</option>
        <option value="month">Por Mes</option>
    </select>
</div>
<div class="chart-container">
    <div id="loadingVentasOrigen" class="loading">
        <span class="loader"></span>
        <p class="mt-2">Cargando...</p>
    </div>

    <div id="ventas-origen"></div>
</div>

<style>
    #ventas-origen {
        height: 350px;
    }
</style>

@push('scripts')
    <script>
        function showLoadingVentasOrigen() {
            document.getElementById('loadingVentasOrigen').classList.remove('hidden');
        }

        function hideLoadingVentasOrigen() {
            document.getElementById('loadingVentasOrigen').classList.add('hidden');
        }

        async function reloadVentasOrigen() {
            showLoadingVentasOrigen();

            try {

                const res = await axios.get(route('dashboard.getSalesOrigin', {
                    tipo: document.querySelector('#filtroTipoSalesOrigin').value,
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));
                if (res.data.success) {
                    loadVentasOrigen(res.data.data);
                } else {
                    toastr.error(res.data.message, 'Error en el servidor');
                }

            } catch (error) {
                console.error(error);
            } finally {
                hideLoadingVentasOrigen();
                removeCreditos();
            }
        }

        function loadVentasOrigen(data) {
            console.log('sales_origin', data);
            _Highcharts.chart('ventas-origen', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },

                title: {
                    text: 'Ventas por Origen'
                },

                tooltip: {
                    pointFormat: '<b>S/ {point.y}</b> ({point.percentage:.1f}%)'
                },

                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 45,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}: {point.percentage:.1f} %'
                        }
                    }
                },

                series: [{
                    name: 'Ventas por Origen',
                    data: data
                }]
            });
        }
    </script>
@endpush
