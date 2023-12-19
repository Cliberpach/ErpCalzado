@extends('layout') @section('content')

@section('utilidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-8">
       <h2  style="text-transform:uppercase"><b>Utilidad</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Panel</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="panel panel-primary" id="panel_detalle">
                <div class="panel-heading">
                    <h4 class=""><b>UTILIDAD</b></h4>
                </div>
                <div class="panel-body ibox-content">
                    <div class="sk-spinner sk-spinner-wave">
                        <div class="sk-rect1"></div>
                        <div class="sk-rect2"></div>
                        <div class="sk-rect3"></div>
                        <div class="sk-rect4"></div>
                        <div class="sk-rect5"></div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row align-items-end">
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <select name="mes" id="mes" class="select2_form form-control">
                                            <option value=""></option>
                                            <option value="01" {{ $mes == '01' ? 'selected' : '' }}>ENERO</option>
                                            <option value="02" {{ $mes == '02' ? 'selected' : '' }}>FEBRERO</option>
                                            <option value="03" {{ $mes == '03' ? 'selected' : '' }}>MARZO</option>
                                            <option value="04" {{ $mes == '04' ? 'selected' : '' }}>ABRIL</option>
                                            <option value="05" {{ $mes == '05' ? 'selected' : '' }}>MAYO</option>
                                            <option value="06" {{ $mes == '06' ? 'selected' : '' }}>JUNIO</option>
                                            <option value="07" {{ $mes == '07' ? 'selected' : '' }}>JULIO</option>
                                            <option value="08" {{ $mes == '08' ? 'selected' : '' }}>AGOSTO</option>
                                            <option value="09" {{ $mes == '09' ? 'selected' : '' }}>SEPTIEMBRE</option>
                                            <option value="10" {{ $mes == '10' ? 'selected' : '' }}>OCTUBRE</option>
                                            <option value="11" {{ $mes == '11' ? 'selected' : '' }}>NOVIEMBRE</option>
                                            <option value="12" {{ $mes == '12' ? 'selected' : '' }}>DICIEMBRE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <select name="anio" id="anio" class="select2_form form-control" >
                                            <option value=""></option>
                                            @foreach ($lstAnios as $anio)
                                            <option value="{{ $anio->value }}" {{ $anio_ == $anio->value ? 'selected' : '' }}>{{ $anio->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-2">
                                    <div class="form-group">
                                        <button class="btn btn-success btn-block" id="obtener-datos"><i class="fa fa-refresh"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" style="font-size: 2.5vw;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th class="text-center text-primary">DOLARES</th>
                                            <th class="text-center">SOLES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="text-center text-danger">INVERSION</th>
                                            <th class="text-center text-primary" id="inversion_dolar">0.00</th>
                                            <th class="text-center" id="inversion_soles">0.00</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-danger">VENTAS</th>
                                            <th class="text-center text-primary" id="ventas_dolar">0.00</th>
                                            <th class="text-center" id="ventas_soles">0.00</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-danger">PORCENTAJE</th>
                                            <th class="text-center text-primary" id="porcentaje_dolar">0.00%</th>
                                            <th class="text-center" id="porcentaje_soles">0.00%</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-danger">UTILIDAD</th>
                                            <th class="text-center text-primary" id="utilidad_dolar">0.00</th>
                                            <th class="text-center" id="utilidad_soles">0.00</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<link href="https://fonts.cdnfonts.com/css/dot-matrix" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset("js/utilidades.js") }}"></script>
@endpush
