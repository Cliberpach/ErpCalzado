<!-- Carousel Bootstrap 4 tipo Grid con azul profesional y sin auto-slide -->
<div id="dashboardCarousel" class="carousel slide px-4" data-ride="carousel" data-interval="false">
    <div class="carousel-inner">

        <!-- Slide 1 -->
        <div class="carousel-item active">
            <div class="row justify-content-start">
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Utilidad Total</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Monto Facturación</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Monto Facturas</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Monto Boletas</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Promedio Venta/Cliente</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Clientes Activos</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item">
            <div class="row justify-content-start">
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envíos Realizados</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envíos Pendientes</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envios Embalados</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Ventas por Tipo</h6>
                            <p>Facturas: | Boletas: </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Ventas Hoy</h6>
                            <h4>15</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Ventas Última Semana</h6>
                            <h4></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Controles -->
    <a class="carousel-control-prev" href="#dashboardCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
        <span class="sr-only">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#dashboardCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
        <span class="sr-only">Siguiente</span>
    </a>
</div>

<style>
    .slider-card {
        background-color: #1257a1;
        /* azul profesional */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .slider-card h4 {
        font-size: 1.25rem;
        /* montos un poco más grandes */
    }

    .slider-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
    }

    #dashboardCarousel {
        position: relative;
        overflow: visible;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.4);
        border-radius: 50%;
    }

    .carousel-control-prev {
        left: -20px;
    }

    .carousel-control-next {
        right: -20px;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 100%, 100%;
    }
</style>

@push('scripts')
    <script>
        function loadCarousel(data) {

            // Slide 1
            const slide1 = `
            <div class="carousel-item active">
                <div class="row justify-content-start">

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Utilidad Total</h6>
                                <h4>${formatoMoneda(data.utilidad)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Monto Facturación</h6>
                                <h4>${formatoMoneda(data.total_ventas.total_ambos)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Monto Facturas</h6>
                                <h4>${formatoMoneda(data.total_ventas.total_facturas)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Monto Boletas</h6>
                                <h4>${formatoMoneda(data.total_ventas.total_boletas)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Promedio Venta/Cliente</h6>
                                <h4>${formatoMoneda(data.promedio_ventas_cliente)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                        <div class="card slider-card text-white text-center">
                            <div class="card-body">
                                <h6>Clientes Activos</h6>
                                <h4>${data.customer_actives ?? 0}</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        `;

            // Slide 2
            const slide2 = `
        <div class="carousel-item">
            <div class="row justify-content-start">

                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envíos Realizados</h6>
                            <h4>${data.envios.envios_realizados ?? 0}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envíos Pendientes</h6>
                            <h4>${data.envios.envios_pendientes ?? 0}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Envíos Embalados</h6>
                            <h4>${data.envios.envios_embalados ?? 0}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Facturas</h6>
                            <p>${data.ventas_facturas ?? 0}</p>
                        </div>
                    </div>
                </div>

              <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Boletas</h6>
                            <p> ${data.ventas_boletas ?? 0}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-4 mb-2">
                    <div class="card slider-card text-white text-center">
                        <div class="card-body">
                            <h6>Ventas Hoy</h6>
                            <h4>${data.ventas_hoy ?? 0}</h4>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    `;

            // pintar
            document.querySelector('#dashboardCarousel .carousel-inner').innerHTML = slide1 + slide2;
        }
    </script>
@endpush
