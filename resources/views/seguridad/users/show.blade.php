@extends('layout')

@section('content')

@section('seguridad-active', 'active')
@section('users-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>VISUALIZAR USUARIO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('user.index') }}">Usuarios</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Ver</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <form>
                        <div class="row">
                            <div class="col-lg-6 col-xs-12 b-r">
                                <h4><b>Datos Generales</b></h4>
                                <hr>
                                <div class="form-group">
                                    <label class="required">Usuario</label>
                                    <input type="text" id="usuario" name="usuario" class="form-control {{ $errors->has('usuario') ? ' is-invalid' : '' }}" value="{{old('usuario')? old('usuario') : $user->usuario}}" maxlength="50" onkeyup="return mayus(this)" disabled>
                                    @if ($errors->has('usuario'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('usuario') }}</strong>
                                        </span>
                                    @endif                                     
                                </div>
                                
                                <div class="form-group">
                                    <label class="required">Email</label>
                                    <input type="text" id="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{old('email')? old('email') : $user->email}}"   onkeyup="return mayus(this)" disabled>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="">Colaborador</label>
                                    <select name="colaborador_id" id="colaborador_id" class="form-control select2_form {{ $errors->has('colaborador_id') ? ' is-invalid' : '' }}" disabled>
                                        <option></option>
                                        @foreach($colaboradores as $colaborador)
                                            <option value="{{$colaborador->id}}"  {{old('colaborador_id') ? (old('colaborador_id') == $colaborador->id ? "selected" : "") : ($user->colaborador->id == $colaborador->id ? "selected" : "")}}>{{$colaborador->colaborador}} - {{$colaborador->area}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('colaborador_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('colaborador_id') }}</strong>
                                    </span>
                                    @endif
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
                                                        @endif disabled>
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
                                        @can('haveaccess', 'user.edit')
                                        <a href="{{route('user.edit',$user->id)}}" id="btn_guardar"
                                            class="btn btn-w-m btn-primary">
                                             <i class="fa fa-pencil"></i> Editar
                                         </a>
                                        @endcan
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
@endpush
@stop