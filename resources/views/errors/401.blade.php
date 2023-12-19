<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>401 | Sistema de Producción</title>

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
        <h1>401</h1>
        <h3 class="font-bold">No autorizado</h3>

        <div class="error-desc">
           
                Usuario no autorizado: acceso denegado a este recurso.

            <div class="form-group mt-4">
                <a class="btn btn-primary btn-block" href="{{route('login')}}"> <i class="fa fa-refresh"></i> Iniciar Sesión </a>
            </div>

        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{asset('Inspinia/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/popper.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/bootstrap.js')}}"></script>

</body>

</html>