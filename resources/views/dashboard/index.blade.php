@extends('layout')

@section('hero-state', 'd-none')

@section('content')
    <div class="wrapper wrapper-content">

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Filtros</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-3">
                                <label for="filter_sede"><strong>Sede</strong></label>
                                <select id="filter_sede" class="form-control">
                                    <option value="">
                                    <option>
                                        @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}" @if ($sede->id == 1) selected @endif>
                                        {{ $sede->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <label><strong>Año</strong></label>
                                <select id="filter_year" class="form-control">
                                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Mes -->
                            <div class="col-3">
                                <label><strong>Mes</strong></label>
                                <select id="filter_month" class="form-control">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>

                            <div class="col-3">
                                <button class="btn btn-success" onclick="getData();">FILTRAR</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @include('dashboard.pages.carousel')
            </div>
        </div>

        <!-- CARD GRÁFICO -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Ventas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.sales')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.sales_origin')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Estadísticas</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.top_products')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.conversion_rate')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.dispatchs')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.customers')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.pares')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.sales_color')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.sales_sizes')
                            </div>
                            <div class="col-6 mb-3">
                                @include('dashboard.pages.delivery_time')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script src="{{ mix('js/tomselect.js') }}"></script>
    <script src="{{ mix('js/highcharts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const mesActual = new Date().getMonth() + 1;
            document.getElementById('filter_month').value = mesActual;
            loadSelectDashboard();
            await getData();
            events();
        })

        function events() {

        }

        function loadSelectDashboard() {
            window.sedeSelect = loadSimpleSelect('filter_sede', null, false);
            window.colorSelect = loadSimpleSelect('filter_color');
            window.tallaSelect = loadSimpleSelect('filter_talla');
        }

        async function getData() {
            try {
                mostrarAnimacion();
                const res = await axios.get(route('dashboard.getData', {
                    year: document.querySelector('#filter_year').value,
                    month: document.querySelector('#filter_month').value,
                    sede: document.querySelector('#filter_sede').value
                }));

                if (res.data.success) {
                    loadDispatchs(res.data.data.dispatchs);
                    loadCarousel(res.data.data.carousel);

                    reloadSales();
                    loadHPares();
                    getDataTopProducts();
                    reloadConversionRate();
                    reloadClients();
                    reloadVentasColor();
                    reloadVentasTallas();
                    reloadDeliveryTime();
                    reloadVentasOrigen();

                    removeCreditos();
                } else {

                }

            } catch (error) {
                toastr.error(error, 'Error en la petición obtener data');
            } finally {
                window.colorSelect.clear();
                window.tallaSelect.clear();
                ocultarAnimacion();
            }
        }

        function removeCreditos() {
            const credits = document.querySelectorAll('.highcharts-credits');
            credits.forEach((c) => {
                c.textContent = '';
            })
        }
    </script>
@endpush
