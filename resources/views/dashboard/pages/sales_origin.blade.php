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

    {{-- Se muestra cuando el servidor responde disponible=false: la venta aún
         no guarda el origen, así que no hay nada que graficar. --}}
    <div id="ventas-origen-aviso" class="text-center text-muted p-4" style="display:none;">
        <i class="fa fa-info-circle fa-2x mb-2"></i>
        <p class="mb-1"><strong>Ventas por Origen no disponible</strong></p>
        <p class="small mb-0">Este informe necesita el campo «origen de venta» en los
            documentos, que todavía no se registra. Se activará cuando empiece a guardarse.</p>
    </div>
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
                if (res.data.success && res.data.disponible === false) {
                    // El dato no existe todavía: aviso en lugar de una tarta vacía,
                    // que se leería como "no hubo ventas".
                    document.getElementById('ventas-origen').style.display = 'none';
                    document.getElementById('ventas-origen-aviso').style.display = 'block';
                } else if (res.data.success) {
                    document.getElementById('ventas-origen').style.display = '';
                    document.getElementById('ventas-origen-aviso').style.display = 'none';
                    loadVentasOrigen(res.data.data);
                } else {
                    //toastr.error(res.data.message, 'Error en el servidor');
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
