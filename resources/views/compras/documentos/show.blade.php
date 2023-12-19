@extends('layout') @section('content')
@section('compras-active', 'active')
@section('documento-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
       <h2  style="text-transform:uppercase"><b>Detalle de Documento de Compra: # {{$documento->id}}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('compras.documento.index')}}">Documento de Compra</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detalle</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <div class="title-action">
            <button onclick="modificar({{ $documento->orden_compra ? 1 : 0 }},{{$documento->id}})" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Editar</button>
            <a href="{{route('compras.documento.reporte', $documento->id)}}" target="_blank" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf-o "></i> Reporte </a>
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
                            <div class="col-md-12 text-left" style="margin-left:80px;">
                                @if($documento->empresa->ruta_logo)
                                <img src="{{Storage::url($documento->empresa->ruta_logo)}}" class="img-fluid m-b"
                                    width="190px" height="190px">
                                @else
                                <img src="{{asset('img/default.png')}}" class="img-fluid m-b"
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
                                        <th colspan="2" class="text-center">DOCUMENTO DE COMPRA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-left">N°:</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">DC - {{ $documento->id }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA EMISION</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($documento->fecha_emision)->format('d/m/y') }}</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-left">FECHA ENTREGA</th>
                                        <th class="text-center">
                                            <span
                                                style="font-weight: normal">{{ Carbon\Carbon::parse($documento->fecha_entrega)->format('d/m/y') }}</span>
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
                        <span>{{$documento->usuario->user->persona->telefono_movil}}</span>
                        <br>
                        <a href="mailto:{{$documento->usuario->user->persona->correo_electronico}}">{{$documento->usuario->user->persona->correo_electronico}}</a>

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
                                                style="font-weight: normal"> {{$documento->proveedor->descripcion}}</span>
                                        </th>
                                    </tr>

                                    @if($documento->proveedor->ruc)
                                    <tr>
                                        <th class="text-left" style="width:20%">RUC:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal"> {{$documento->proveedor->ruc}}</span>
                                        </th>
                                    </tr>
                                    @else
                                    <tr>
                                        <th class="text-left" style="width:20%">DNI:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$documento->proveedor->dni}}</span>
                                        </th>
                                    </tr>
                                    @endif


                                    <tr>
                                        <th class="text-left" style="width:20%">DIRECCION:</th>
                                        <th class="text-left" style="width:80%">
                                            <span
                                                style="font-weight: normal">{{$documento->proveedor->direccion}}</span>
                                        </th>

                                    </tr>

                                    <tr>
                                        <th class="text-left" style="width:20%">CONTACTO:</th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">
                                            @if($documento->proveedor->contacto)
                                                {{$documento->proveedor->contacto}}
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
                                            @if($documento->proveedor->telefono)
                                            {{$documento->proveedor->telefono}}
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
                                            <a href="mailto:{{$documento->proveedor->correo}}">{{$documento->proveedor->correo}}</a>
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
                                <th class="text-center">PRODUCTO</th>
                                <th class="text-center">COSTO FLETE</th>
                                <th class="text-center">PRECIO</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $detalle)
                            <tr>
                                <td class="text-center">{{$detalle->cantidad}}</td>
                                <td class="text-left">
                                    <div><strong>{{$detalle->producto->nombre}}</strong></div>
                                </td>

                                <td class="text-center">{{$moneda.'  '.$detalle->costo_flete}}</td>
                                <td class="text-center">{{$moneda.'  '.$detalle->precio}}</td>
                                <td class="text-center subtotal">{{$moneda.'  '.$detalle->cantidad * $detalle->precio}}</td>

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
                                        @if($documento->igv)
                                            {{$documento->igv}}%
                                        @else
                                            18%
                                        @endif </strong></th>
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
                                            <span style="font-weight: normal">{{$documento->proveedor->ruc_transporte}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left" style="width:20%">
                                            EMPRESA:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$documento->proveedor->transporte}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left" style="width:20%">
                                            DIRECCION:
                                        </th>
                                        <th class="text-left" style="width:80%">
                                            <span style="font-weight: normal">{{$documento->proveedor->direccion_transporte}}</span>
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
                                        <th class="text-left title-adicional" style="width:30%">
                                            TIPO DE DOCUMENTO:
                                        </th>
                                        <th class="text-left" style="width:70%">
                                            <span style="font-weight: normal">{{$documento->tipo_compra}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left title-adicional" style="width:30%">
                                            NUMERO DEL DOCUMENTO:
                                        </th>
                                        <th class="text-left" style="width:70%">
                                            <span style="font-weight: normal">{{'N° '.$documento->numero_tipo}}</span>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th class="text-left title-adicional" style="width:30%">
                                            CONDICION DE DOCUMENTO:
                                        </th>
                                        <th class="text-left" style="width:70%">
                                            <span style="font-weight: normal">{{$documento->modo_compra}}</span>
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
    function modificar(orden,id) {
        if (orden === 1) {
            toastr.error('El documento de compra fue generado por una orden (Opción "Editar" en orden de compra).', 'Error');
        }else{
            Swal.fire({
                title: 'Opción Modificar',
                text: "¿Seguro que desea modificar registro?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {

                    //Ruta Modificar
                    var url_editar = '{{ route("compras.documento.edit", ":id")}}';
                    url_editar = url_editar.replace(':id', id);
                    location = url_editar;

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
    }

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
