<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ErpCalzado | Siscom</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="icon" href="/img/siscom.ico" />  --}}

    <style>
        .swal2-cancel {
            margin-right: 10px;
        }

        .list-alerts {
            max-height: calc(100vh - 325px);
            overflow-y: auto;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: white;
        }

        .content-alert {
            min-height: 20px;
            max-height: 80px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .content-alert::-webkit-scrollbar,
        .list-alerts::-webkit-scrollbar {
            -webkit-appearance: none;
        }

        .content-alert::-webkit-scrollbar:vertical,
        .list-alerts::-webkit-scrollbar:vertical {
            width: 8px;
        }

        .content-alert::-webkit-scrollbar-button:increment,
        .content-alert::-webkit-scrollbar-button,
        .list-alerts::-webkit-scrollbar-button:increment,
        .list-alerts::-webkit-scrollbar-button {
            display: none;
        }

        .content-alert::-webkit-scrollbar:horizontal,
        .list-alerts::-webkit-scrollbar:horizontal {
            height: 10px;
        }

        .content-alert::-webkit-scrollbar-thumb,
        .list-alerts::-webkit-scrollbar-thumb {
            background-color: #6BBD99;
            border-radius: 20px;
            border: 1px solid #f1f2f3;
        }

        .content-alert::-webkit-scrollbar-track,
        .list-alerts::-webkit-scrollbar-track {
            border-radius: 10px;
        }
    </style>

    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">

    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/v/bs4/dt-2.3.2/r-3.0.5/datatables.min.css" rel="stylesheet" integrity="sha384-57j+ilFSg5URotSQqwt2DpHtNkoi7sy+Qj1phKYVWmfSRDx3biVnhnx2mzJTEhu+" crossorigin="anonymous">

    <link href="/Inspinia/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="/Inspinia/font-awesome/css/font-awesome.css" rel="stylesheet"> --}}

    <link href="/Inspinia/css/animate.css" rel="stylesheet">
    <link href="/Inspinia/css/style.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="/Inspinia/css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- FONTAWESOME 5.0 FREE -->
    <script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="/css/style.css" rel="stylesheet">

    <style>
        .overlay_animacion {
            position: fixed;
            /* Fija el overlay para que cubra todo el viewport */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* Color oscuro con opacidad */
            z-index: 99999999999 !important;
            /* Asegura que el overlay esté sobre todo */
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
            visibility: hidden;
        }

        /*========== LOADER SPINNER =======*/
        .loader_animacion {
            position: relative;
            width: 75px;
            height: 100px;
            background-repeat: no-repeat;
            background-image: linear-gradient(#DDD 50px, transparent 0),
                linear-gradient(#DDD 50px, transparent 0),
                linear-gradient(#DDD 50px, transparent 0),
                linear-gradient(#DDD 50px, transparent 0),
                linear-gradient(#DDD 50px, transparent 0);
            background-size: 8px 100%;
            background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px;
            animation: pillerPushUp 4s linear infinite;
        }

        .loader_animacion:after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 10px;
            height: 10px;
            background: #de3500;
            border-radius: 50%;
            animation: ballStepUp 4s linear infinite;
        }

        @keyframes pillerPushUp {

            0%,
            40%,
            100% {
                background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px
            }

            50%,
            90% {
                background-position: 0px 50px, 15px 58px, 30px 66px, 45px 78px, 60px 90px
            }
        }

        @keyframes ballStepUp {
            0% {
                transform: translate(0, 0)
            }

            5% {
                transform: translate(8px, -14px)
            }

            10% {
                transform: translate(15px, -10px)
            }

            17% {
                transform: translate(23px, -24px)
            }

            20% {
                transform: translate(30px, -20px)
            }

            27% {
                transform: translate(38px, -34px)
            }

            30% {
                transform: translate(45px, -30px)
            }

            37% {
                transform: translate(53px, -44px)
            }

            40% {
                transform: translate(60px, -40px)
            }

            50% {
                transform: translate(60px, 0)
            }

            57% {
                transform: translate(53px, -14px)
            }

            60% {
                transform: translate(45px, -10px)
            }

            67% {
                transform: translate(37px, -24px)
            }

            70% {
                transform: translate(30px, -20px)
            }

            77% {
                transform: translate(22px, -34px)
            }

            80% {
                transform: translate(15px, -30px)
            }

            87% {
                transform: translate(7px, -44px)
            }

            90% {
                transform: translate(0, -40px)
            }

            100% {
                transform: translate(0, 0);
            }
        }
    </style>

    <style>
        .scaling-squares-spinner,
        .scaling-squares-spinner * {
            box-sizing: border-box;
        }

        .scaling-squares-spinner {
            height: 80px;
            width: 80px;
            position: relative;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            animation: scaling-squares-animation 1250ms;
            animation-iteration-count: infinite;
            transform: rotate(0deg);
        }

        .scaling-squares-spinner .square {
            height: calc(65px * 0.25 / 1.3);
            width: calc(65px * 0.25 / 1.3);
            margin-right: auto;
            margin-left: auto;
            border: calc(65px * 0.04 / 1.3) solid #007148;
            position: absolute;
            animation-duration: 1250ms;
            animation-iteration-count: infinite;
        }

        .scaling-squares-spinner .square:nth-child(1) {
            animation-name: scaling-squares-spinner-animation-child-1;
        }

        .scaling-squares-spinner .square:nth-child(2) {
            animation-name: scaling-squares-spinner-animation-child-2;
        }

        .scaling-squares-spinner .square:nth-child(3) {
            animation-name: scaling-squares-spinner-animation-child-3;
        }

        .scaling-squares-spinner .square:nth-child(4) {
            animation-name: scaling-squares-spinner-animation-child-4;
        }


        @keyframes scaling-squares-animation {

            50% {
                transform: rotate(90deg);
            }

            100% {
                transform: rotate(180deg);
            }
        }

        @keyframes scaling-squares-spinner-animation-child-1 {
            50% {
                transform: translate(150%, 150%) scale(2, 2);
            }
        }

        @keyframes scaling-squares-spinner-animation-child-2 {
            50% {
                transform: translate(-150%, 150%) scale(2, 2);
            }
        }

        @keyframes scaling-squares-spinner-animation-child-3 {
            50% {
                transform: translate(-150%, -150%) scale(2, 2);
            }
        }

        @keyframes scaling-squares-spinner-animation-child-4 {
            50% {
                transform: translate(150%, -150%) scale(2, 2);
            }
        }
    </style>

    <link rel="stylesheet" href="/css/appNotify.css">
    @yield('vue-css')
    
    @stack('styles')

    @routes
</head>

<body>

    <div class="overlay_animacion">
        <span class="loader_animacion"></span>
    </div>

    <div id="">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <!-- Sidebar  Menu -->
                    @include('partials.nav')
                    <!-- /.Sidebar Menu -->
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links">
                        <li>
                            <a href="{{ route('ventas.documento.create') }}" title="DOC. DE VENTA">
                                <i class="fa fa-plus"></i> DV
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reporte.producto.informe') }}" title="PRODUCTO INFORME">
                                <i class="fa fa-plus"></i> PI
                            </a>
                        </li>
                        @if (auth()->check() && auth()->user()->roles()->where('name', 'ADMIN')->exists())
                            <li>
                                <a href="javascript:void(0);" onclick="restaurarStock()" title="RESTAURAR STOCK">
                                    <i class="fas fa-balance-scale"></i> RESTAURAR STOCK
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{route('descargarBD')}}" title="DESCARGAR BD">
                                    <i class="fa-solid fa-database"></i> DATABASE
                                </a>
                            </li>                         --}}
                        @endif
                    </ul>
                    <ul class="nav navbar-top-links navbar-right" id="appNotify">
                        <notify-component></notify-component>
                        <li>
                            <div style="display:flex;flex-direction:column;">
                                <span class="m-r-sm text-muted welcome-message">
                                    <b>{{ auth()->user()->usuario }}</b>
                                </span>
                                <span>
                                    <b>

                                        {{ auth()->user()->sede->nombre }}

                                    </b>
                                </span>
                            </div>

                        </li>
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="fa fa-sign-out"></i> Cerrar Sesión
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>

            {{-- <div class="row" style="background-color:white;" id="row-loading-spinner">
                <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="scaling-squares-spinner" :style="spinnerStyle" id="loading-spinner">
                            <div class="square"></div>
                            <div class="square"></div>
                            <div class="square"></div>
                            <div class="square"></div>
                        </div>
                </div>
            </div>
          --}}

            <div class="loader-spinner">
                <div class="centrado" id="onload">
                    <div class="loadingio-spinner-blocks-zcepr5tohl">
                        <div class="ldio-6fqlsp2qlpd">
                            <div style='left:38px;top:38px;animation-delay:0s'></div>
                            <div style='left:80px;top:38px;animation-delay:0.125s'></div>
                            <div style='left:122px;top:38px;animation-delay:0.25s'></div>
                            <div style='left:38px;top:80px;animation-delay:0.875s'></div>
                            <div style='left:122px;top:80px;animation-delay:0.375s'></div>
                            <div style='left:38px;top:122px;animation-delay:0.75s'></div>
                            <div style='left:80px;top:122px;animation-delay:0.625s'></div>
                            <div style='left:122px;top:122px;animation-delay:0.5s'></div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="content-system" style="display:block;">
                @yield('content')
                <div id="appTables"></div>
            </div>


            {{-- <div id="content-system" style="display:block;">
                @yield('content')
                @if (!isset($kardex7668))
                    <div id="appTables"></div>
                @endif
            </div> --}}

            <div class="footer">
                <div class="float-right" onkeyup="return mayus(this)">
                    DEVELOPER <strong>SISCOM SAC</strong>
                </div>
                <div onkeyup="return mayus(this)">
                    <strong>Copyright</strong> SisCom SAC &copy; 2020-2021
                </div>
            </div>




            {{-- <lottie-player src="{{asset('/lottiefile/OCCHcmMEMS.json')}}" background="transparent"
            speed="1" style="height:200px;" loop autoplay>
            </lottie-player> --}}

        </div>

    </div>

    <div class="position-fixed d-none"
        style="bottom:50px; top:auto; right:30px; left:auto; -webkit-box-shadow: 8px 8px 3px 0px rgba(0,0,0,0.75); -moz-box-shadow: 8px 8px 3px 0px rgba(0,0,0,0.75); box-shadow: 6px 6px 4px 0px rgba(0,0,0,0.75); border-radius: 50%;">
        <a class="d-sm-block" href="{{ route('configuracion.index') }}" target="_blank">
            {{-- <img tag src="/img/config_.png" style="width: 50px"> --}}
        </a>
    </div>


    <script src="{{ '/js/appNotify.js?v=' . rand() }}"></script>
    @stack('scripts-vue-js')
    @yield('vue-js')

    {{-- <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> --}}



    <!-- Mainly scripts -->
    <script src="/Inspinia/js/jquery-3.1.1.min.js"></script>
    <script src="/Inspinia/js/popper.min.js"></script>
    <script src="/Inspinia/js/bootstrap.js"></script>
    <script src="/Inspinia/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    {{-- <script src="/Inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js"></script> --}}

    <!-- Custom and plugin javascript -->
    <script src="/Inspinia/js/inspinia.js"></script>

    {{-- <script src="/Inspinia/js/plugins/pace/pace.min.js"></script> --}}

    <!-- jQuery UI -->
    {{-- <script src="/Inspinia/js/plugins/jquery-ui/jquery-ui.min.js"></script> --}}

    <!-- Toastr script -->
    <script src="/Inspinia/js/plugins/toastr/toastr.min.js"></script>

    <!-- Propio scripts -->
    <script src="/Inspinia/js/scripts.js"></script>

    <!-- SweetAlert -->
    <script src="/SweetAlert/sweetalert2@10.js"></script>


    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/v/bs4/dt-2.3.2/r-3.0.5/datatables.min.js" integrity="sha384-9m1/ul4UUfv6yoZjjPpf4EtIPDGd505EmvdZmpntp4ljXDaH5wT57N/Z2jXTXg2/" crossorigin="anonymous"></script>

    <script src="{{ asset('js/utils.js') }}"></script>

    @stack('scripts')

    <script>
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}")
        @endif

        @if (Session::has('GUIA_ENVIO_ESTADO'))
            toastr.success("{{ Session::get('GUIA_ENVIO_ESTADO') }}")
        @endif

        @if (Session::has('GUIA_ID_SUNAT'))
            toastr.success("{{ Session::get('GUIA_ID_SUNAT') }}")
        @endif


        //Mensaje de Session
        @if (session('guardar') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('eliminar') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('cerrada') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Caja Chica Cerrada',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('modificar') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Modificado',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('concretar') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Concretada',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('enviar') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Enviado',
                text: '¡Acción realizada satisfactoriamente!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('error_caja') == 'success')
            Swal.fire({
                icon: 'error',
                title: 'Caja Chica',
                text: '¡Caja Chica esta siendo utilizada en algun pago!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('exitosa') == 'success')
            Swal.fire({
                icon: 'success',
                title: 'Acción Exitosa',
                text: '¡Puede ingresar nuevo tipo de pago!',
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        @if (session('sunat_existe') == 'error')
            Swal.fire({
                icon: 'error',
                title: 'Documento de Venta',
                text: 'Existe un comprobante electronico',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (session('error_orden_produccion') == 'error')
            Swal.fire({
                icon: 'error',
                title: 'Orden de Producción',
                text: 'Falta la confirmación del Área de Producción',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (session('error_orden_almacen') == 'error')
            Swal.fire({
                icon: 'error',
                title: 'Orden de Producción',
                text: 'Falta la confirmación del Área de Almacen',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (session('error_orden_areas') == 'error')
            Swal.fire({
                icon: 'error',
                title: 'Orden de Producción',
                text: 'Falta la confirmación de las Áreas de Almacen y Producción',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (Session::has('toastrError'))
            Swal.fire({
                icon: 'error',
                title: 'ERROR AL DESCARGAR LA BD',
                text: "{{ Session::get('toastrError') }}",
                showConfirmButton: false,
                timer: 3000
            })
        @endif
    </script>

    <script>
        function consultaExitosa() {

            Swal.fire({
                icon: 'success',
                title: '¡Búsqueda Exitosa!',
                text: 'Datos ingresados.',
                customClass: {
                    container: 'my-swal'
                },
                showConfirmButton: false,
                timer: 1500
            })
        }
        //Loader
        window.addEventListener("load",function(){
             $('.loader-spinner').hide();
             $("#content-system").css("display", "");
        })

        async function restaurarStock() {
            try {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: "Desea emparejar el stock?",
                    text: "Esta acción no es reversible!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí!",
                    cancelButtonText: "No, cancelar!",
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {

                        const res = await axios.post(route('restaurarStock'));
                        console.log(res);
                        if (res.data.success) {
                            const message = res.data.message;
                            toastr.success(message, 'OPERACIÓN COMPLETADA');
                        } else {
                            const message = res.data.message;
                            const exception = res.data.exception;

                            toastr.error(`${message} - ${exception}`, 'ERROR');
                        }

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire({
                            title: "Operación cancelada",
                            text: "No se realizaron acciones",
                            icon: "error"
                        });
                    }
                });


            } catch (error) {
                console.log(error)
                toastr.error(`${error.response.data.message}`, 'ERROR EN EL SERVIDOR');
            }
        }

        /*setTimeout(() => {
            //document.querySelector('#row-loading-spinner').style.display = "none";
            $('.loader-spinner').hide();
            $("#content-system").css("display", "");

        }, 0);*/


        function mostrarAnimacion() {
            document.querySelector('.overlay_animacion').style.visibility = 'visible';
        }

        function ocultarAnimacion() {
            document.querySelector('.overlay_animacion').style.visibility = 'hidden';
        }
    </script>
</body>

</html>
