<div class="row">
    <div class="col-6">
        <label for="filter_color" style="font-weight: bold;">COLOR</label>
        <select id="filter_color" class="form-control form-control-sm" data-placeholder="Seleccionar"
            onchange="getDataTopProducts()">
            <option value=""></option>
            @foreach ($colores as $color)
                <option value="{{ $color->id }}">{{ $color->descripcion }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-6">
        <label for="filter_talla" style="font-weight: bold;">TALLA</label>
        <select id="filter_talla" class="form-control form-control-sm" data-placeholder="Seleccionar"
            onchange="getDataTopProducts()">
            <option value=""></option>
            @foreach ($tallas as $talla)
                <option value="{{ $talla->id }}">{{ $talla->descripcion }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <div class="chart-container">
            <div id="loadingProducts" class="loading">
                <span class="loader"></span>
                <p class="mt-2">Cargando...</p>
            </div>

            <div id="chart_products"></div>
        </div>
    </div>
</div>

<style>
    #chart_products {
        height: 350px;
    }
</style>

@push('scripts')
    <script>
        function showLoadingProducts() {
            document.getElementById('loadingProducts').classList.remove('hidden');
        }

        function hideLoadingProducts() {
            document.getElementById('loadingProducts').classList.add('hidden');
        }

        async function getDataTopProducts() {
            showLoadingProducts();

            try {
                const res = await axios.get(route('dashboard.getTopProducts', {
                    color: document.querySelector('#filter_color').value,
                    talla: document.querySelector('#filter_talla').value,
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadTopProducts(res.data.data);
                } else {
                    toastr.error('Error en la petición');
                }

            } catch (error) {
                toastr.error('Error en la petición');
                console.error(error);
            } finally {
                hideLoadingProducts();
                removeCreditos();
            }
        }

        function loadTopProducts(data) {

            if (!data || data.length === 0) {
                document.getElementById('chart_products').innerHTML = `
                    <div style="height:350px; display:flex; align-items:center; justify-content:center; color:#999;">
                        No hay datos disponibles
                    </div>
                `;
                return;
            }

            const dataFormatted = formatDataTopProducts(data);

            chartTopProducts = new _Highcharts.Chart({
                chart: {
                    renderTo: 'chart_products',
                    type: 'column',
                    height: 350,
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: 'Top 10 Productos Más Vendidos'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Cantidad Vendida'
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: 'Ventas: {point.y}'
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        depth: 25,
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    name: 'Productos',
                    colorByPoint: true,
                    data: dataFormatted
                }]
            });
        }

        function formatDataTopProducts(data) {
            return data.map(item => [
                item.nombre_producto,
                parseFloat(item.total_vendido)
            ]);
        }
    </script>
@endpush
