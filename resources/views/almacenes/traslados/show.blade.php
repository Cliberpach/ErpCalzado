@extends('layout')
@section('content')

@section('almacenes-active', 'active')
@section('traslados-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>VER TRASLADO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.traslados.index') }}">Traslados</a>
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

                    <div class="row">

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="registrador_nombre" style="font-weight:bold;"
                                class="required">Registrador</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                                <input value="{{ $traslado->registrador_nombre }}" readonly name="registrador_nombre"
                                    id="registrador_nombre" type="text" class="form-control"
                                    placeholder="Registrador" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="aprobador_nombre" style="font-weight:bold;" class="required">Aprobador</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fas fa-user-alt"></i>
                                    </span>
                                </div>
                                <input value="{{ $traslado->aprobador_nombre }}" readonly name="aprobador_nombre"
                                    id="aprobador_nombre" type="text" class="form-control" placeholder=""
                                    aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="estado" style="font-weight:bold;" class="required">Estado</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa fa-signal"></i>
                                    </span>
                                </div>
                                <input value="{{ $traslado->estado }}" readonly name="estado" id="estado"
                                    type="text" class="form-control" placeholder="Registrador" aria-label="Username"
                                    aria-describedby="basic-addon1">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="fecha_registro" style="font-weight:bold;" class="required">Fecha
                                Registro</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input readonly type="date" id="fecha_registro" name="fecha_registro"
                                    class="form-control" value="{{ $traslado->created_at }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label for="fecha_traslado" style="font-weight:bold;" class="required">Fecha
                                Traslado</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input readonly type="date" id="fecha_traslado" name="fecha_traslado"
                                    class="form-control" value="{{ $traslado->fecha_traslado }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label class="required" style="font-weight: bold;">Almacén Principal Origen</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-warehouse"></i>
                                </span>
                                <input readonly type="text" id="almacen_origen" name="almacen_origen"
                                    class="form-control" value="{{ $almacen_origen->descripcion }}">
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <label class="required" style="font-weight: bold;">Almacén Principal Destino</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-warehouse"></i>
                                </span>
                                <input readonly type="text" id="almacen_destino" name="almacen_destino"
                                    class="form-control" value="{{ $almacen_destino->descripcion }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label style="font-weight: bold;">Observación</label>
                            <textarea readonly maxlength="200" type="text" name="observacion" rows="2" id="observacion"
                                class="form-control" placeholder="Observación">{{ $traslado->observacion }}</textarea>
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class=""><b>DETALLE DEL TRASLADO</b></h4>
                                </div>
                                <div class="panel-body">
                                    <hr>
                                    <div class="table-responsive">
                                        @include('almacenes.traslados.tables.tbl_traslado_show')
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
                            <a href="{{ route('almacenes.traslados.index') }}" id="btn_cancelar"
                                class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@stop

@push('styles')
@endpush

@push('scripts')

<script>
    let dtTrasladosShow = null;

    document.addEventListener('DOMContentLoaded', () => {

        cargarSelect2();
        pintarDetalleTraslado();
        dtTrasladosShow = iniciarDataTable('tbl_traslado_show');
    })


    function cargarSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
        });
    }

    function pintarDetalleTraslado() {
        const detalles = @json($detalle);
        const tallas = @json($tallas);
        const bodyTablaDetalles = document.querySelector('#tbl_traslado_show tbody');
        let fila = ``;
        const producto_color_procesado = [];

        detalles.forEach((d) => {
            if (!producto_color_procesado.includes(`${d.producto_id}-${d.color_id}`)) {
                let htmlTallas = ``;

                fila += `<tr>
                        <td style="font-weight:bold;">${d.producto_nombre} - ${d.color_nombre}</td>`;

                tallas.forEach((t) => {

                    let cantidad = detalles.filter((det) => {
                        return det.producto_id == d.producto_id &&
                            det.color_id == d.color_id &&
                            t.id == det.talla_id;
                    });

                    cantidad.length != 0 ? cantidad = cantidad[0].cantidad : cantidad = '';

                    htmlTallas += `<td>${cantidad}</td>`;
                });

                fila += htmlTallas;
                fila += `</tr>`;

                producto_color_procesado.push(`${d.producto_id}-${d.color_id}`);
            }
        });

        bodyTablaDetalles.innerHTML = fila;
    }
</script>
@endpush
