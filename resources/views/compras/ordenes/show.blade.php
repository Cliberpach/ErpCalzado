@extends('layout') @section('content')
@section('compras-active', 'active')
@section('orden-compra-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
       <h2  style="text-transform:uppercase"><b>Detalle de la Orden de Compra: # {{$orden->id}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('compras.orden.index')}}">Ordenes de Compra</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detalle</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <div class="title-action">
            <a href="{{route('compras.orden.edit', $orden->id)}}" class="btn btn-warning btn-sm"><i
                    class="fa fa-edit"></i> Editar</a>
            <a href="#" onclick="generarReporte()" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf-o "></i>
                Reporte </a>
            <a href="#" onclick="enviarReporte()" class="btn btn-primary btn-sm"><i class="fa fa-send "></i> Enviar </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox-content p-xl">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-12 text-left" style="margin-left:100px;">
                                @if($orden->empresa->ruta_logo)
                                <img src="{{Storage::url($orden->empresa->ruta_logo)}}" class="img-fluid m-b"
                                    width="190px" height="190px">
                                @else
                                <img src="{{asset('storage/empresas/logos/default.png')}}" class="img-fluid m-b"
                                    width="190px" height="190px">
                                @endif
                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6 text-right">
                        <div class="row">
                            <div class="col-md-12 m-t">
                                <strong>{{$orden->empresa->razon_social}}</strong>
                                <br>
                                RUC:{{$orden->empresa->ruc}}
                                <br>
                                {{$orden->empresa->direccion_fiscal}}
                            </div>

                        </div>
                    </div>

                </div>

                <hr>


                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-fecha">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">ORDEN DE COMPRA</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left">N°:</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">OC - {{ $orden->id }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA EMISION</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/y') }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA ENTREGA</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/y') }}</span>
                                        </th>

                                    </tr>

                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="col-md-8 text-right" style="text-transform: uppercase;" id="contacto">
                        <b>CONTACTO</b>
                        <br>
                        <span>{{$nombre_completo}}</span>
                        <br>
                        <span>{{$orden->usuario->user->persona->telefono_movil}}</span>
                        <br>
                        <a href="mailto:{{$orden->usuario->user->persona->correo_electronico}}">{{$orden->usuario->user->persona->correo_electronico}}</a>

                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-proveedor">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">DATOS DEL PROVEEDOR</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left" style="width:20%">RAZON SOCIAL:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal"> {{$orden->proveedor->descripcion}}</span>
                                        </th>
                                    </tr>

                                    @if($orden->proveedor->ruc)
                                    <tr>
                                        <th class="text-left" style="width:20%">RUC:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal"> {{$orden->proveedor->ruc}}</span>
                                        </th>
                                    </tr>
                                    @else
                                    <tr>
                                        <th class="text-left" style="width:20%">DNI:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$orden->proveedor->dni}}</span>
                                        </th>
                                    </tr>
                                    @endif
                                    
                                    
                                    <tr>
                                        <th class="text-left" style="width:20%">DIRECCION:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$orden->proveedor->direccion}}</span>
                                        </th>

                                    </tr>

                                    <tr>
                                        <th class="text-left" style="width:20%">CONTACTO:</th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">
                                            @if($orden->proveedor->contacto)
                                                {{$orden->proveedor->contacto}}
                                            @else
                                                -
                                            @endif
                                            </span>
                                        </th>

                                    </tr>

                                    <tr>
                                        <th class="text-left" style="width:20%">TELEFONO:</th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">
                                            @if($orden->proveedor->telefono)
                                            {{$orden->proveedor->telefono}}
                                            @else
                                            -
                                            @endif
                                            </span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left" style="width:20%">CORREO:</th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">
                                            <a href="mailto:{{$orden->proveedor->correo}}">{{$orden->proveedor->correo}}</a>
                                            </span>
                                        </th>

                                    </tr>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>


                <hr>

                <div class="table-responsive">
                    <table class="table invoice-table table table-bordered"  onkeyup="return mayus(this)" id="tabla-producto">
                        <thead>
                            <tr>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">UNIDAD</th>
                                <th class="text-center">PRODUCTO</th>
                                <th class="text-center">PRECIO</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $detalle)
                            <tr>
                                <td class="text-center">{{$detalle->cantidad}}</td>
                                <td class="text-center">
                                    {{ $detalle->producto->getMedida() }}
                                </td>
                                <td class="text-left">
                                    {{$detalle->producto->nombre}}
                                </td>

                                
                                <td class="text-center">{{$moneda.'  '.$detalle->precio}}</td>
                                <td class="text-center subtotal">{{$moneda.'  '.number_format($detalle->cantidad * $detalle->precio, 2, '.', '')}}</td>

                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot  style="text-transform:uppercase">
                            <tr>
                                <td colspan="3" class="borde-title"></td>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>Sub Total</strong></th>
                                <th class="text-center"><span id="">{{$moneda.'  '.$subtotal}}</span></th>

                            </tr>
                            <tr>
                                <td colspan="3" class="borde-title"></td>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>IGV
                                        @if($orden->igv){{$orden->igv}}%@endif </strong></th>
                                <th class="text-center"><span id="">{{$moneda.'  '.$igv}}</span></th>

                            </tr>
                            <tr>
                                <td colspan="3" class="borde-title"></td>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>Total </strong></th>
                                <th class="text-center"><span id="total">{{$moneda.'  '.$total}}</span></th>

                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-transportista">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">DATOS DEL TRANSPORTISTA</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left" style="width:20%">
                                            RUC:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$orden->proveedor->ruc_transporte}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left" style="width:20%">
                                            EMPRESA:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$orden->proveedor->transporte}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left" style="width:20%">
                                            DIRECCION:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$orden->proveedor->direccion_transporte}}</span>
                                        </th>

                                    </tr>

                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-adicional">

                                <tbody>
                                    <tr>
                                        <th class="text-left title-adicional" style="width:20%">
                                            CONDICION DE ORDEN:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$orden->modo_compra}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left title-adicional" style="width:20%">
                                            OBSERVACION:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal"> {{$orden->observacion}}</span>
                                        </th>

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


@stop
@push('styles')
<style>
    #tabla-transportista thead th,
    #tabla-fecha thead th,
    #tabla-proveedor thead th,
    #tabla-producto thead th
    {
        background: #0f243e;
        color: #ffff;
        border: 2px solid #0f243e;
    }
    #tabla-transportista,
    #tabla-fecha,
    #tabla-proveedor,
    #tabla-producto ,
    #tabla-adicional
    {
        color: #000;
        background: #EEEEEE;
        border: 2px solid #0f243e;
    }
    #tabla-transportista tbody tr th,
    #tabla-fecha tbody tr th,
    #tabla-proveedor tbody tr th,
    #tabla-producto tbody tr td,
    #tabla-producto tfoot tr th,
    #tabla-adicional tbody tr th

    {
        /* padding: 8px; */
        border: 2px solid #0f243e;
    }

    #tabla-producto .title-producto {
        background: #0f243e;
        color: #ffff;
        border: 2px solid #0f243e;
        text-align:center;
    }
    #tabla-producto .borde-title{
        border:none;
        color: #000;
        background: white;
        border: 2px solid white;
    }
    #tabla-adicional .title-adicional{
        background: #0f243e;
        color: #ffff;
        border: 2px solid #0f243e;
    }

</style>
@endpush
@push('scripts')
<script>
function generarReporte() {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Reporte",
        text: "¿Seguro que desea generar reporte?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {
            window.location.href = "{{route('compras.orden.reporte', $orden->id)}}"
            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Generando Reporte',
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            })

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })

}
</script>
<script>
function enviarReporte() {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Reporte",
        text: "¿Seguro que desea enviar reporte por correo?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {
            window.location.href = "{{route('compras.orden.email', $orden->id)}}"
            Swal.fire({
                title: '¡Enviando!',
                type: 'info',
                text: 'El Reporte se esta enviando al correo: ' + "{{$orden->proveedor->correo}}",
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            })

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })

}
</script>


@endpush