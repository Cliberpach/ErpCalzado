    @extends('layout') @section('content')

@section('mantenimiento-active', 'active')
@section('configuracion-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12">
       <h2  style="text-transform:uppercase"><b>Configuración</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Configuración</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        @foreach ($config as $item)
        @if ($item->slug == 'CEC')
        <div class="col-12 col-md-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>{{ $item->descripcion }}</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('configuracion.update', $item->id)}}" method="POST">
                        @csrf
                        @method('put')
                        <div class="form-group row">
                            <div class="col-9">
                                <input type="hidden" name="slug" id="" value="{{ $item->slug }}">
                                <select name="propiedad" id="propiedad"  class="select2_form form-control" required>
                                    <option value=""></option>
                                    <option value="SI" {{$item->propiedad == 'SI' ? 'selected' : ''}}>SI</option>
                                    <option value="NO" {{$item->propiedad == 'NO' ? 'selected' : ''}}>NO</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        <div class="col-12 col-md-4">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>CODIGO DE PRECIOS MENOR</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('configuracion.empresa.update')}}" method="POST">
                        @csrf
                        @method('put')
                        <div class="row align-items-end">
                            <div class="col-7">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="codigo_precio_menor" value="{{ $empresa->codigo_precio_menor }}" placeholder="Código">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label> <input type="checkbox" class="i-checks" name="estado_precio_menor" id="estado_precio_menor" value="1" {{ $empresa->estado_precio_menor == '1' ? 'checked' : ''}}> Activo </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/iCheck/custom.css' )}}" rel="stylesheet">
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        height: '200px',
        width: '100%',
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
</script>
@endpush
