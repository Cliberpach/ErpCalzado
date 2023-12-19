<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>404 | Sistema de Producción</title>

    <link href="{{asset('Inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('Inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/css/style.css')}}" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('img/ecologo.ico')}}" />

</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1>404</h1>
        <h3 class="font-bold">Búsqueda no encontrada</h3>

        <div class="error-desc">
            Lo sentimos, pero la búsqueda que esta realizando no ha sido encontrada. Intente verificar la URL en busca de errores, luego presione el botón actualizar en su navegador o intente encontrar algo más en nuestro sistema.

            <div class="form-group mt-4">
                <a class="btn btn-primary btn-block" href="{{route('home')}}"> <i class="fa fa-refresh"></i> Panel de Control</a>
            </div>

        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{asset('Inspinia/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/popper.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/bootstrap.js')}}"></script>

</body>

</html>