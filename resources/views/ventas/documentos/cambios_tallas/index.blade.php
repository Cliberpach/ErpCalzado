@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')
@include('ventas.documentos.cambios_tallas.modal_cambio')
@include('ventas.documentos.cambios_tallas.modal_historial_cambio')
<style>
    .documento_titulo{
        font-weight: bold;
    }


div.content-animacion {
    position: relative;
}

div.content-animacion.sk__loading::after {
    content: '';
    background-color: rgba(255, 255, 255, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 3000;
}

.content-animacion.sk__loading>.sk-spinner.sk-spinner-wave {
    margin: 0 auto;
    width: 50px;
    height: 30px;
    text-align: center;
    font-size: 10px;
}

.content-animacion.sk__loading>.sk-spinner {
    display: block;
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    z-index: 3500;
}

.content-animacion .sk-spinner.sk-spinner-wave.hide-animacion {
    display: none;
}


</style>

<div class="row wrapper border-bottom white-bg page-heading align-items-center">
    <div class="col-lg-6 col-md-6">
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
    <div class="col-6">
        <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
            <strong>NOTA: </strong> Solo permitido 1 cambio de talla por item.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row content-animacion">

        {{-- INICIO ANIMACION --}}
        <div class="sk-spinner sk-spinner-wave hide-animacion">
            <div class="sk-rect1"></div>
            <div class="sk-rect2"></div>
            <div class="sk-rect3"></div>
            <div class="sk-rect4"></div>
            <div class="sk-rect5"></div>
        </div>
        {{-- FIN ANIMACION --}}

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
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                      <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">PRODUCTO</th>
                                                        <th scope="col">COLOR</th>
                                                        <th scope="col">TALLA</th>
                                                        <th scope="col">CANT</th>
                                                        <th scope="col">CANT CAMBIADA</th>
                                                        <th scope="col">CANT SIN CAMBIO</th>
                                                        <th scope="col">PRECIO</th>
                                                        <th scope="col">CAMBIO</th>
                                                        <th scope="col">VER</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($detalles as $detalle)
                                                            <tr>
                                                                <th>{{$detalle->id}}</th>
                                                                <th scope="row">{{$detalle->nombre_producto}}</th>
                                                                <td>{{$detalle->nombre_color}}</td>
                                                                <td>{{$detalle->nombre_talla}}</td>
                                                                <td>{{$detalle->cantidad}}</td>
                                                                <td>{{$detalle->cantidad_cambiada}}</td>
                                                                <td>{{$detalle->cantidad_sin_cambio}}</td>
                                                                <td>{{$detalle->precio_unitario_nuevo}}</td>
                                                                <td>
                                                                    @if (!$detalle->estado_cambio_talla)
                                                                        <i class="fas fa-exchange-alt btn btn-success btn-obtener-tallas" data-id="{{$detalle->id}}" data-producto-id="{{$detalle->producto_id}}" data-color-id="{{$detalle->color_id}}"
                                                                            data-talla-id="{{$detalle->talla_id}}" data-producto-nombre="{{$detalle->nombre_producto}}"
                                                                            data-color-nombre="{{$detalle->nombre_color}}" data-talla-nombre="{{$detalle->nombre_talla}}"></i>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($detalle->estado_cambio_talla === "CON CAMBIOS")
                                                                        <i class="fas fa-eye btn btn-dark btn-historial-cambios" data-id="{{$detalle->id}}"></i>
                                                                    @endif
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
                                                        <th>#</th>
                                                        <th scope="col">PRODUCTO INICIAL</th>
                                                        <th scope="col">CAMBIO</th>
                                                        <th>CANT</th>
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
                                <button class="btn btn-danger mr-3" id="btn-regresar">REGRESAR</button>
                                <button class="btn btn-success" id="btn-grabar-doc">GRABAR</button>
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

    const producto_cambiado   =     {};
    let   closeSegurity       =     1;  //==== 1: NO DEVOLVER STOCK LÓGICO | 2:DEVOLVER STOCK LÓGICO ====

    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        events();
        eventsModalCambios();
        console.log(@json($documento));
    })

    function events(){
        document.addEventListener('click',async (e)=>{
            if(e.target.classList.contains('btn-obtener-tallas')){
                e.target.classList.add('fa-spin');
                document.querySelector('#btn-cambiar-talla').disabled   =   false;

                const detalles      =   @json($detalles);
                const detalle_id    =   e.target.getAttribute('data-id');
                const producto_id   =   e.target.getAttribute('data-producto-id');
                const color_id      =   e.target.getAttribute('data-color-id');
                const talla_id      =   e.target.getAttribute('data-talla-id');
                const producto_nombre       =   e.target.getAttribute('data-producto-nombre');
                const color_nombre          =   e.target.getAttribute('data-color-nombre');
                const talla_nombre          =   e.target.getAttribute('data-talla-nombre');
                const inputCantidadCambiar  =   document.querySelector('#cantidad_cambio');

                let cantidad_detalle    =   null;
                try {
                    const item  =  @json($detalles).filter((d)=>{
                        return d.id == detalle_id;
                    })
                    cantidad_detalle    =   item[0].cantidad;
                } catch (error) {
                    toastr.error('ITEM NO ENCONTRADO EN EL DETALLE DEL DOCUMENTO','RECARGAR LA VISTA E INTENTAR DE NUEVO');
                }
               
                setProductoCambiado({detalle_id,producto_id,color_id,talla_id,producto_nombre,color_nombre,talla_nombre,cantidad_detalle});

                document.querySelector('#talla').setAttribute('data-producto-id', producto_id);
                document.querySelector('#talla').setAttribute('data-color-id', color_id);
                document.querySelector('#talla').setAttribute('data-producto-nombre', producto_nombre);
                document.querySelector('#talla').setAttribute('data-color-nombre', color_nombre);
            

                await getTallas(producto_id,color_id);
                e.target.classList.remove('fa-spin');
                
                //====== COLOCANDO DATOS EN EL MODAL CAMBIO TALLA =========
                document.querySelector('#stock').value  =   '';

                const indexDetalle =   detalles.findIndex((d)=> d.id == detalle_id );
                
                indexDetalle !== -1?inputCantidadCambiar.value = parseInt(detalles[indexDetalle].cantidad_sin_cambio) :inputCantidadCambiar.value = '';  
                //===== ABRIR MODAL CAMBIO TALLA =======
                $("#modal-cambio-talla").modal('show');
            }

            //====== VER HISTORIAL CAMBIOS =====
            if(e.target.classList.contains('btn-historial-cambios')){
                //====== OBTENIENDO HISTORIAL DE CAMBIOS ======
                const detalle_id    =   e.target.getAttribute('data-id');
                e.target.classList.add('fa-spin');
                const res_getHistorialCambios   =   await getHistorialCambiosTallas(detalle_id,@json($documento->id));
                if(res_getHistorialCambios.success){
                    pintarTableHistorialCambios(res_getHistorialCambios.cambios_tallas);
                    
                    $("#modal-historial-cambios").modal('show');
                }else{
                    toastr.error(res_getHistorialCambios.message,'ERROR AL OBTENER HISTORIAL DE CAMBIOS DE TALLAS');
                }
                e.target.classList.remove('fa-spin');
            }
        })

        document.querySelector('#btn-grabar-doc').addEventListener('click',async (e)=>{
            e.target.disabled   =   true;

            if(cambios.length   === 0){
                e.target.disabled   =   false;
                toastr.error('EL DETALLE DE CAMBIO DE TALLAS ESTÁ VACÍO!!','OPERACIÓN INCORRECTA');
                return;
            }

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
                            //======= NO DEVOLVER STOCKS PORQUE SE GRABÓ TODO CORRECTAMENTE =======
                            closeSegurity   =   1;
                            //======= ALERTA DE CONFIRMACIÓN ======
                            Swal.fire({
                                title: res.data.message,
                                text: 'Se generó una nota de ingreso y salida',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            });
                            //====== REDIRECCIONAR ======
                            const url = "{{ route('ventas.documento.index') }}";
                            window.location.href = url;                        
                        }else{
                            //====== DEVOLVER STOCKS PORQUE HUBO ERRORES EN EL GRABADO ======
                            closeSegurity   =   2;
                            //======= ALERTA DE ERROR =====
                            Swal.fire({
                                title: res.data.message,
                                text: res.data.exception,
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            }); 
                        }

                    } catch (error) {
                        //====== DEVOLVER STOCKS PORQUE HUBO ERRORES EN EL GRABADO ======
                        closeSegurity   =   2;
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema con la solicitud',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }finally{
                        e.target.disabled   =   false;
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    //====== DEVOLVER STOCKS PORQUE SE CANCELÓ LA OPERACIÓN ======
                    closeSegurity   =   2;
                    swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizaron acciones",
                        icon: "error"
                    });
                    e.target.disabled   =   false;
                }
            });          
        })

        document.querySelector('#btn-regresar').addEventListener('click',(e)=>{
            e.target.disabled   =   true;
            const url = "{{ route('ventas.documento.index') }}";
            window.location.href = url;     
        })


        window.addEventListener("beforeunload", function(event) {
            if(closeSegurity === 2){
                if(cambios.length > 0){
                    devolverStockLogico(cambios);
                }
            }
        });
    }

    //====== CONTROL DE ANIMACIÓN =======
    function mostrarAnimacion(){
        document.querySelector('.content-animacion').classList.add('sk__loading');
        document.querySelector('.sk-spinner').classList.remove('hide-animacion');
    }
    function ocultarAnimacion(){
        document.querySelector('.content-animacion').classList.remove('sk__loading');
        document.querySelector('.sk-spinner').classList.add('hide-animacion');
    }

    function setProductoCambiado(producto){
        producto_cambiado.detalle_id    =   producto.detalle_id;  
        producto_cambiado.producto_id   =   producto.producto_id;
        producto_cambiado.color_id      =   producto.color_id
        producto_cambiado.talla_id      =   producto.talla_id;
        producto_cambiado.producto_nombre   =   producto.producto_nombre;
        producto_cambiado.color_nombre      =   producto.color_nombre;
        producto_cambiado.talla_nombre      =   producto.talla_nombre;
        producto_cambiado.cantidad_detalle  =   producto.cantidad_detalle;
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