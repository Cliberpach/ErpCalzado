@extends('layout') @section('content')

    <div class="middle-box text-center animated fadeInDown align-items-center">
        <h2>NO AUTORIZADO</h2>
        <h3 class="font-bold">Usuario no autorizado</h3>

        <div class="error-desc">
            Lo sentimos, pero está usted no está autorizado para esta función del sistema. Intente verificar la URL en busca de errores, luego presione el botón actualizar en su navegador o intente encontrar algo más en nuestro sistema. Comunicarse con administrador del sistema.

            <div class="form-group mt-4 row">
                <div class="col-md-6">
                    <a class="btn btn-primary btn-block" href="{{url()->previous() }}"> <i class="fa fa-undo"></i> Regresar</a>
                </div>

                <div class="col-md-6">
                    <a class="btn btn-success btn-block " href="{{route('home')}}"> <i class="fa fa-refresh"></i> Panel de Control</a>
                </div>
            </div>

        </div>
    </div>

@stop
