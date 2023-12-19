@extends('layout')

@section('content')

@section('seguridad-active', 'active')
@section('users-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>MODIFICAR USUARIO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('user.index') }}">Usuarios</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Modificar</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <form action="{{route('user.update',$user->id)}}" method="POST">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-lg-6 col-xs-12 b-r">
                                <h4><b>Datos Generales</b></h4>
                                <hr>
                                <div class="form-group">
                                    <label class="required">Usuario</label>
                                    <input type="text" id="usuario" name="usuario" class="form-control {{ $errors->has('usuario') ? ' is-invalid' : '' }} text-uppercase" value="{{old('usuario') ? old('usuario') : $user->usuario}}" maxlength="50" required>
                                    @if ($errors->has('usuario'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('usuario') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="required">Email</label>
                                    <input type="text" id="email" id="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }} text-uppercase" name="email" value="{{old('email')? old('email') : $user->email}}" required>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="">Colaborador</label>
                                    <select name="colaborador_id" id="colaborador_id" class="form-control select2_form {{ $errors->has('colaborador_id') ? ' is-invalid' : '' }}">
                                        <option></option>
                                        @foreach($colaboradores as $colaborador)
                                            <option value="{{$colaborador->id}}"  {{old('colaborador_id') ? (old('colaborador_id') == $colaborador->id ? "selected" : "") : ($user->colaborador->persona_id == $colaborador->id ? "selected" : "")}} {{session('colaborador_id') == $colaborador->id ? "selected" : ""}}>{{$colaborador->colaborador}} - {{$colaborador->area}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('colaborador_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('colaborador_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="required">Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" id="password" class="form-control {{ session('password') ? ' is-invalid' : '' }} text-uppercase" name="password" value="{{session('password') ? session('password') : $user->contra}}" required>
                                            <span class="input-group-append"><button onclick="password1()" type="button" class="btn btn-default" ><i id="pass" class="fa fa-eye"></i></button></span>
                                            @if (session('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ session('mpassword') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required">Confirmar contraseña</label>
                                        <div class="input-group">
                                            <input type="password" id="confirm_password" class="form-control {{ session('confirm_password') ? ' is-invalid' : '' }} text-uppercase" name="confirm_password" value="{{session('confirm_password') ? session('confirm_password') : $user->contra}}" required>
                                            <span class="input-group-append"><button type="button" onclick="confirm_password1()" class="btn btn-default" ><i id="passcon" class="fa fa-eye"></i></button></span>
                                            @if (session('confirm_password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ session('mpassword') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-xs-12">
                                <h4><b>Roles</b></h4>
                                <hr>
                                <div class="form-group row" style="overflow-y: auto;height:300px;">
                                    <div class="col-lg-12 col-xs-12">
                                        <div class="row">
                                            @foreach($roles as $role)
                                            <div class="col-lg-4 col-xs-4">
                                                <div class="checkbox">
                                                    <input type="checkbox" id="role{{$role->id}}" name="role[]" value="{{$role->id}}"
                                                        @if(is_array($role_user) && in_array("$role->id",$role_user))
                                                        checked
                                                        @elseif(is_array(old('role')) && in_array("$role->id",old('role')))
                                                        checked
                                                        @elseif(is_array(session('role')) && in_array("$role->id",session('role')))
                                                        checked
                                                        @endif>
                                                    <label for="role{{$role->id}}" title="{{$role->description}}">
                                                        {{$role->name}}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group row">
                                    <div class="col-md-6 text-left">
                                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco
                                            (<label class="required"></label>) son obligatorios.</small>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{route('user.index')}}" id="btn_cancelar"
                                           class="btn btn-w-m btn-default">
                                            <i class="fa fa-arrow-left"></i> Regresar
                                        </a>
                                        <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                            <i class="fa fa-save"></i> Grabar
                                        </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
    <style>
        .logo {
            width: 190px;
            height: 190px;
            border-radius: 10%;
            position: absolute;
        }
    </style>
@endpush

@push('scripts')

    <!-- iCheck -->
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- Data picker -->
    <script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <!-- Steps -->
    <script src="{{ asset('Inspinia/js/plugins/steps/jquery.steps.min.js') }}"></script>
    <script>

        @if(session('usuario'))
        $('#usuario').val('');
        $('#usuario').val("{{session('usuario')}}");
        @endif


        @if(session('email'))
        $('#email').val('');
        $('#email').val("{{session('email')}}");
        @endif


        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });

        function password1(){
            var tipo = document.getElementById("password");
            if(tipo.type == "password"){
                $('#pass').removeClass('fa fa-eye');
                $('#pass').addClass('fa fa-eye-slash');
                tipo.type = "text";
            }else{
                $('#pass').removeClass('fa fa-eye-slash');
                $('#pass').addClass('fa fa-eye');
                tipo.type = "password";
            }
        }

        function confirm_password1(){
            var tipo1 = document.getElementById("confirm_password");
            if(tipo1.type == "password"){
                $('#passcon').removeClass('fa fa-eye');
                $('#passcon').addClass('fa fa-eye-slash');
                tipo1.type = "text";
            }else{
                $('#passcon').removeClass('fa fa-eye-slash');
                $('#passcon').addClass('fa fa-eye');
                tipo1.type = "password";
            }
        }
    </script>
@endpush
@stop
