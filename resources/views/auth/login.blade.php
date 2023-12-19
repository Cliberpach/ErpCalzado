<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ErpSiscom | Iniciar Sesión</title>
    <link rel="icon" href="{{asset('img/siscom.ico')}}" />

    <link href="{{asset('Inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('Inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    <!-- Toastr style -->
    <link href="{{asset('Inspinia/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
    <style>

        @media only screen and (min-width: 992px) {
            .login{
                margin-top:100px;
            }
        }

        @media only screen and (max-width: 992px) {
            .login{
                margin-top:90px;
            }
        }


    </style>

</head>


<body class="gray-bg">
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
    <div id="content-system" style="display:none;">
        <div class="container-fluid">

            <div class="row" style="height:100vh;">

                <div class="col-lg-6 col-md-6 d-none d-md-block image-container" style="height:100vh;">
                </div>


                <div class="col-lg-6 col-md-6 form-container">


                    <div class="login">
                        <div class="text-center">

                            <img src="{{asset('img/erp_logo_short.jpeg')}} " width="250" class="img-responsive m-b">

                        </div>
                        <h3>Sistema de Comercializacion</h3>
                        <p>
                            Ingresa tus datos para Iniciar Sesión.
                        </p>
                        <form class="m-t container form-login" role="form" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group input_login">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Correo Electrónico" onkeyup="return mayus(this)" >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group input_login">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña" onkeyup="return mayus(this)">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="btn-iniciar input_login">
                                <button type="submit" class="btn btn-primary block full-width m-b btn-margin-sesion">Iniciar Sesión</button>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" ><small>¿Olvidaste tu Contraseña?</small></a>
                            @endif

                        </form>
                    </div>


                </div>



            </div>






        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="{{asset('Inspinia/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/popper.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/bootstrap.js')}}"></script>
    <!-- Toastr script -->
    <script src="{{asset('Inspinia/js/plugins/toastr/toastr.min.js')}}"></script>
    <script>
        @if(Session::has('usuario_anulado'))
            // toastr.error("{{ Session::get('usuario_anulado') }}")
            dd("sasas");
        @endif


        window.addEventListener("load",function(){
            $('.loader-spinner').hide();
            $("#content-system").css("display", "");
        })


    </script>

    <!-- Propio scripts -->
    <script src="{{ asset('Inspinia/js/scripts.js') }}"></script>

</body>

</html>






