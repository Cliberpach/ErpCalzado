@extends('layout') @section('content')
@section('ventas-active', 'active')
@section('documentos-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>DETALLE DEL DOCUMENTO DE VENTA: # {{$documento->id}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('ventas.documento.index')}}">Documento de Venta</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detalle</strong>
            </li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox-content p-xl">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-12 text-left" style="margin-left:80px;">
                                @if($documento->empresa->ruta_logo)
                                <img src="{{Storage::url($documento->empresa->ruta_logo)}}" class="img-fluid m-b"
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
                                <strong>{{$documento->empresa->razon_social}}</strong>
                                <br>
                                RUC:{{$documento->empresa->ruc}}
                                <br>
                                {{$documento->empresa->direccion_fiscal}}
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
                                        <th colspan="2" class="text-center">DOCUMENTO DE VENTA</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left">N°:</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">DV - 0{{ $documento->id }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA DOCUMENTO</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($documento->fecha_documento)->format('d/m/y') }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA ATENCION</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($documento->fecha_atencion)->format('d/m/y') }}</span>
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
                        <span>{{$documento->user->empleado->persona->telefono_movil}}</span>
                        <br>
                        <a href="mailto:{{$documento->user->empleado->persona->correo_electronico}}">{{$documento->user->empleado->persona->correo_electronico}}</a>

                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-proveedor">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">DATOS DEL CLIENTE</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left" style="width:20%">TIPO DE DOCUMENTO:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal"> {{$documento->cliente->tipo_documento}}</span>
                                        </th>
                                    </tr>

                                    
                                    <tr>
                                        <th class="text-left" style="width:20%">RUC:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal"> {{$documento->cliente->documento}}</span>
                                        </th>
                                    </tr>
                                    
                                    <tr>
                                        <th class="text-left" style="width:20%">NOMBRE:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$documento->cliente->nombre}}</span>
                                        </th>
                                    </tr>
                                    
                                    
                                    
                                    <tr>
                                        <th class="text-left" style="width:20%">DIRECCION:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$documento->cliente->direccion}}</span>
                                        </th>

                                    </tr>

                                    <tr>
                                        <th class="text-left" style="width:20%">CELULAR:</th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">
                                            @if($documento->cliente->telefono_movil)
                                                {{$documento->cliente->telefono_movil}}
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
                                            @if($documento->cliente->telefono_fijo)
                                            {{$documento->cliente->telefono_fijo}}
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
                                            <a href="mailto:{{$documento->cliente->correo_electronico}}">{{$documento->cliente->correo_electronico}}</a>
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
                                <th class="text-center">UNIDAD DE MEDIDA</th>
                                <th class="text-center">DESCRIPCION DEL PRODUCTO</th>
                                <th class="text-center">PRECIO</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $detalle)
                            <tr>
                                <td class="text-center">{{$detalle->cantidad}}</td>
                                <td class="text-center">
                                {{$detalle->producto->tabladetalle->simbolo.' - '.$detalle->producto->tabladetalle->descripcion}}
                                </td>
                                <td class="text-left">
                                    <div><strong>{{$detalle->producto->codigo.' - '.$detalle->producto->nombre}}</strong></div>
                                </td>
                                <td class="text-center">{{$detalle->precio}}</td>
                                <td class="text-center subtotal">{{$detalle->importe}}</td>

                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot  style="text-transform:uppercase">
                            <tr>
                                <td colspan="3" class="borde-title"></td>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>Sub Total</strong></th>
                                <th class="text-center"><span id="">{{'S/. '.$documento->sub_total}}</span></th>

                            </tr>
                            <tr>
                                <td colspan="3" class="borde-title"></td>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>IGV
                                        @if($documento->igv)
                                            {{$documento->igv}}%
                                        @else
                                            18%
                                        @endif </strong></th>
                                <th class="text-center"><span id="">{{'S/. '.$documento->total_igv}}</span></th>

                            </tr>
                            <tr>
                                <th colspan="3" class="borde-title">
                                    <strong> SON: {{$cadena_valor}}. </strong>
                                </th>
                                <th colspan="1" style="text-align:center" class="title-producto"><strong>Total </strong></th>
                                <th class="text-center"><span id="total">{{'S/. '.$documento->total}}</span></th>

                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="text-transform: uppercase;" id="tabla-adicional">

                                <tbody>
                                    <tr>
                                        <th class="text-left title-adicional" style="width:30%">
                                            TIPO DE DOCUMENTO:
                                        </th>
                                        <th class="text-left" style="width:70%">
                                            <span style="font-weight: normal">{{$documento->tipo_venta}}</span>
                                        </th>

                                    </tr>

                                    <tr>
                                        <th class="text-left title-adicional" style="width:30%">
                                            OBSERVACION:
                                        </th>
                                        <th class="text-left" style="width:70%">
                                            <span style="font-weight: normal"> {{$documento->observacion}}</span>
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
            window.location.href = "{{route('compras.documento.reporte', $documento->id)}}"
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



@endpush