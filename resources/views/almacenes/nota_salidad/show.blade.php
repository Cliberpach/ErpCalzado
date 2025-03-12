@extends('layout')
 @section('content')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>VER NOTA DE SALIDA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_salidad.index')}}">Nota de Salidad</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Vizualizar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">

                <div class="ibox-content">
                    <input type="hidden" id='asegurarCierre' >
                    <form id="enviar_ingresos">

                            <div class="col-sm-12">
                                <h4 class=""><b>Notas de Salidad</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la Nota de Salidad:</p>
                                    </div>
                                </div>
                            	<div class="form-group row">

                                    <div class="col-12 col-md-2"  id="fecha">
                                        <label>Fecha</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input type="date" id="fecha" name="fecha"
                                                class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha',$notasalidad->fecha)}}"
                                                autocomplete="off" readonly required>
                                            @if ($errors->has('fecha'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('fecha') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label>Origen</label>
                                        <input type="text" name="origen" id="origen" readonly value="ALMACEN DE PRODUCTO TERMINADO" class="form-control">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="required">Destino</label>
                                        <select name="destino" id="destino" class="form-control" disabled>
                                            <option value="">Seleccionar Destino</option>
                                            @foreach ($destinos as $tabla)

                                                <option {{ $notasalidad->destino == $tabla->descripcion ? 'selected' : '' }} 
                                                    value="{{$tabla->id}}">
                                                    {{$tabla->descripcion}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Observación</label>
                                        <textarea type="text" name="observacion" rows="2" id="observacion" class="form-control" placeholder="Observación" readonly>{{ $notasalidad->observacion }}</textarea>
                                    </div>


                                </div>
                            </div>


                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle de la Nota de Salida</b></h4>
                                    </div>
                                    <div class="panel-body">
                                        <hr>
                                        <div class="table-responsive">
                                            @include('almacenes.nota_salidad.tables.tbl_ns_detalle')
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.</small>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{route('almacenes.nota_salidad.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
@include('almacenes.nota_salidad.modal')
@include('almacenes.nota_salidad.modalLote')
@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">


<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

@endpush

@push('scripts')
<!-- Data picker -->
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!-- Date range use moment.js same as full calendar plugin -->
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<!-- Date range picker -->
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script>


document.addEventListener('DOMContentLoaded',()=>{
    cargarSelect2();
    pintarDetalleNotaSalida();
})


function cargarSelect2(){
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
    });
}

function pintarDetalleNotaSalida(){
    const detalles          =   @json($detalle);
    const tallas            =   @json($tallas);
    const bodyTablaDetalles =   document.querySelector('#tabla_ns_detalle tbody');
    let fila              =   ``;
 
    detalles.forEach((d)=>{
        fila    +=  `<tr>
                        <th scope="row"></th>
                        <td style="font-weight:bold;">${d.producto_nombre} - ${d.color_nombre}</td>`;

        tallas.forEach((t)=>{
            if(t.id == d.talla_id ){
                fila    +=  `<td style="font-weight:bold;">${d.cantidad}</td>`;
            }else{
                fila    +=  `<td></td>`;
            }
        })

        fila    +=      `</tr>`;  
    })
    bodyTablaDetalles.innerHTML      =   fila;
}

</script>
@endpush
