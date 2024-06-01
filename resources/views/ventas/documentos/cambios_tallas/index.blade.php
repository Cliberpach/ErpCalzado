@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')
@include('ventas.documentos.cambios_tallas.modal_cambio')
<style>
    .documento_titulo{
        font-weight: bold;
    }
</style>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Cambio de Tallas</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cambio de Tallas</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">

                    <div class="row">
                        <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                            <div class="list-group">
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action active" style="background-color:#15559a;border-color:#15559a;">
                                  <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><span style="font-weight: bold;">DOC: </span>{{$documento->serie.'-'.$documento->correlativo}}</h5>
                                    <small>{{$documento->created_at}}</small>
                                  </div>
                                 
                                </a>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                  <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><span style="font-weight: bold;">CLIENTE: </span>{{$documento->cliente}}</h5>
                                  </div>
                                </a>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                      <h5 class="mb-1"><span style="font-weight: bold;">SUBTOTAL: </span>{{$documento->total}}</h5>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                      <h5 class="mb-1"><span style="font-weight: bold;">IGV: </span>{{$documento->total_igv}}</h5>
                                    </div>
                                </a>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                      <h5 class="mb-1"><span style="font-weight: bold;">TOTAL: </span>{{$documento->total_pagar}}</h5>
                                    </div>
                                </a>
                              </div>
                        </div>
                        <div class="col-lg-9 col-md-7 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h4><b>Detalle Original</b></h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                      <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">PRODUCTO</th>
                                                        <th scope="col">COLOR</th>
                                                        <th scope="col">TALLA</th>
                                                        <th scope="col">CANT</th>
                                                        <th scope="col">PRECIO</th>
                                                        <th scope="col">CAMBIO</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($detalles as $detalle)
                                                            <tr>
                                                                <th>{{$loop->index +1}}</th>
                                                                <th scope="row">{{$detalle->nombre_producto}}</th>
                                                                <td>{{$detalle->nombre_color}}</td>
                                                                <td>{{$detalle->nombre_talla}}</td>
                                                                <td>{{$detalle->cantidad}}</td>
                                                                <td>{{$detalle->precio_unitario_nuevo}}</td>
                                                                <td>
                                                                    <i class="fas fa-exchange-alt btn btn-success btn-obtener-tallas" data-producto-id="{{$detalle->producto_id}}" data-color-id="{{$detalle->color_id}}"
                                                                        data-talla-id="{{$detalle->talla_id}}" data-producto-nombre="{{$detalle->nombre_producto}}"
                                                                        data-color-nombre="{{$detalle->nombre_color}}" data-talla-nombre="{{$detalle->nombre_talla}}"></i>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                      
                                                     
                                                    </tbody>
                                                  </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <h4><b>Cambios</b></h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tabla-cambio-tallas">
                                                    <thead>
                                                      <tr>
                                                        <th></th>
                                                        <th scope="col">PRODUCTO INICIAL</th>
                                                        <th scope="col">CAMBIO</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        
                                                      
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-end pr-3">
                                <button class="btn btn-success mr-3" id="btn-grabar-doc">GRABAR</button>
                                <button class="btn btn-danger" id="btn-regresar">REGRESAR</button>
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
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
@endpush
@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script>

    const producto_cambiado   =   {};
    let   closeSegurity       =     1;  //==== 1: NO DEVOLVER STOCK LÓGICO | 2:DEVOLVER STOCK LÓGICO ====

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        events();
        eventsModalCambios();
    })

    function events(){
        document.addEventListener('click',async (e)=>{
            if(e.target.classList.contains('btn-obtener-tallas')){
                e.target.classList.add('fa-spin');

                const producto_id   =   e.target.getAttribute('data-producto-id');
                const color_id      =   e.target.getAttribute('data-color-id');
                const talla_id      =   e.target.getAttribute('data-talla-id');
                const producto_nombre       =   e.target.getAttribute('data-producto-nombre');
                const color_nombre          =   e.target.getAttribute('data-color-nombre');
                const talla_nombre          =   e.target.getAttribute('data-talla-nombre');

                setProductoCambiado({producto_id,color_id,talla_id,producto_nombre,color_nombre,talla_nombre});

                document.querySelector('#talla').setAttribute('data-producto-id', producto_id);
                document.querySelector('#talla').setAttribute('data-color-id', color_id);
                document.querySelector('#talla').setAttribute('data-producto-nombre', producto_nombre);
                document.querySelector('#talla').setAttribute('data-color-nombre', color_nombre);

                await getTallas(producto_id,color_id);
                e.target.classList.remove('fa-spin');
                document.querySelector('#stock').value  =   '';
                $("#modal-cambio-talla").modal('show');
            }
        })

        document.querySelector('#btn-grabar-doc').addEventListener('click',async (e)=>{
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Desea realizar cambio de tallas?",
                text: "Se generará una nota de ingreso y salida!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {

                        const res = await axios.post(route('venta.cambiarTallas.cambiarTallasStore'), {
                            cambios: JSON.stringify(cambios),
                            documento_id: @json($documento->id)
                        });

                        if(res.data.success){
                            Swal.fire({
                                title: res.data.message,
                                text: 'Se generó una nota de ingreso y salida',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            });
                        }else{
                            Swal.fire({
                                title: res.data.message,
                                text: res.data.exception,
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            }); 
                        }

                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema con la solicitud',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                }
            });          
        })
    }


    function setProductoCambiado(producto){
        producto_cambiado.producto_id   =   producto.producto_id;
        producto_cambiado.color_id      =   producto.color_id
        producto_cambiado.talla_id      =   producto.talla_id;
        producto_cambiado.producto_nombre   =   producto.producto_nombre;
        producto_cambiado.color_nombre      =   producto.color_nombre;
        producto_cambiado.talla_nombre      =   producto.talla_nombre;
    }

    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

   
</script>
@endpush