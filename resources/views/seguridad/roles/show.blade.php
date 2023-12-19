@extends('layout')

@section('content')

@section('seguridad-active', 'active')
@section('roles-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>VISUALIZAR ROL</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('role.index') }}">Roles</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Ver</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight" style="zoom: 90%;">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <form>
                        <div class="row">
                            <div class="col-lg-6 col-xs-12 b-r">
                                <h4><b>Datos Generales</b></h4>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-lg-6 col-xs-12">
                                        <label class="required">Nombre</label>
                                        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }} text-uppercase" value="{{old('name')? old('name') : $role->name}}" maxlength="50" required disabled>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-6 col-xs-12">
                                        <label class="required">Slug</label>
                                        <input type="text" id="slug" class="form-control {{ $errors->has('slug') ? ' is-invalid' : '' }} text-uppercase" name="slug" value="{{old('slug')? old('slug') : $role->slug}}" disabled>
                                        @if ($errors->has('slug'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('slug') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="required">Descripción</label>
                                    <textarea name="description" id="description" cols="30" rows="3" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}" required disabled>{{old('description')? old('description') : $role->description}}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif

                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 col-xs-12">
                                        <label class="required">Full-Access</label>
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="radio">
                                                    <input type="radio" name="full-access" id="full-access-si" value="SI"
                                                    {{old('full-access') ? (old('full-access') == "SI" ? "checked" : "") : ($role['full-access']=="SI" ? "checked" : "")}} disabled>
                                                    <label for="full-access-si" title="Acceso total sin restricciones">
                                                        SI
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="radio">
                                                    <input type="radio" name="full-access" id="full-access-no" value="NO"
                                                    @if(old('full-access')==null && $role['full-access']==null)
                                                    checked
                                                    @endif
                                                    {{old('full-access') ? (old('full-access') == "NO" ? "checked" : "") : ($role['full-access']=="NO" ? "checked" : "")}} disabled>
                                                    <label for="full-access-no" title="Permisos definidos">
                                                        NO
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xs-12">
                                        <label class="required">Punto-Venta</label>
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="radio">
                                                    <input type="radio" name="punto-venta" id="punto-venta-si" value="SI"
                                                    {{old('punto-venta') ? (old('punto-venta') == "SI" ? "checked" : "") : ($role['punto-venta']=="SI" ? "checked" : "")}} disabled>
                                                    <label for="punto-venta-si" title="Acceso a cualquier caja aperturada.">
                                                        SI
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6">
                                                <div class="radio">
                                                    <input type="radio" name="punto-venta" id="punto-venta-no" value="NO"
                                                    @if(old('punto-venta')==null && $role['punto-venta']==null)
                                                    checked
                                                    @endif
                                                    {{old('punto-venta') ? (old('punto-venta') == "NO" ? "checked" : "") : ($role['punto-venta']=="NO" ? "checked" : "")}} disabled>
                                                    <label for="punto-venta-no" title="Sin acceso a cualquier caja aperturada.">
                                                        NO
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-xs-12">
                                <h4><b>Permisos</b></h4>
                                <hr>
                                <div class="form-group row" style="overflow-y: auto;height:330px;">
                                    <div class="col-lg-12 col-xs-12">
                                        <div class="row">
                                            @foreach($permissions as $permission)
                                            <div class="col-lg-6 col-xs-6">
                                                <div class="checkbox">
                                                    <input type="checkbox" id="permission{{$permission->id}}" name="permission[]" value="{{$permission->id}}"
                                                        @if(is_array($permission_role) && in_array("$permission->id",$permission_role))
                                                        checked
                                                        @elseif(is_array(old('permission')) && in_array("$permission->id",old('permission')))
                                                        checked
                                                        @endif disabled>
                                                    <label for="permission{{$permission->id}}" title="{{$permission->description}}">
                                                        {{$permission->name}}
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
                                        <a href="{{route('role.index')}}" id="btn_cancelar"
                                           class="btn btn-w-m btn-default">
                                            <i class="fa fa-arrow-left"></i> Regresar
                                        </a>
                                        <a href="{{route('role.edit',$role->id)}}" id="btn_grabar" class="btn btn-w-m btn-primary">
                                            <i class="fa fa-pencil"></i> Editar
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
