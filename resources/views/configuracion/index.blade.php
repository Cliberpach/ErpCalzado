    @extends('layout') @section('content')

@section('mantenimiento-active', 'active')
@section('configuracion-active', 'active')
<style>
#switch{
    top: 0;
    left: 20%;
    width: 170px;
    height: 40px;
}

.toggle{
    position: absolute;
    border: 2px solid #444249;
    border-radius: 20px;
    -webkit-transition: border-color .6s  ease-out;
    transition: border-color .6s  ease-out;
    box-sizing: border-box;
}

.toggle.toggle-on{
    border-color: rgba(137, 194, 217, .4);
    -webkit-transition: all .5s .15s ease-out;
    transition: all .5s .15s ease-out;
}

.toggle-button{
    position: absolute;
    top: 4px;
    width: 28px;
    bottom: 4px;
    right: 130px;
    background-color: #444249;
    border-radius: 19px; 
    cursor: pointer;
    -webkit-transition: all .3s .1s, width .1s, top .1s, bottom .1s;
    transition: all .3s .1s, width .1s, top .1s, bottom .1s;
}

.toggle-on .toggle-button{
    top: 3px;
    bottom: 3px;
    right: 3px;
    border-radius: 23px;
    background-color: #89c2da;
    box-shadow: 0 0 16px #4b7a8d;
    -webkit-transition: all .2s .1s, right .1s;
    transition: all .2s .1s, right .1s;
}


.toggle-text-on{
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0px;
    right: 50px;
    line-height: 36px;
    text-align: center;
    font-family: 'Quicksand', sans-serif;
    font-size: 11px;
    font-weight: normal;
    cursor: pointer;
    -webkit-user-select: none; /* Chrome/Safari */    
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* IE10+ */


    color: rgba(0,0,0,0);
}

.toggle-on .toggle-text-on{
    color: #3b6a7d;
    -webkit-transition: color .3s .15s ;
    transition: color .3s .15s ;
}

.toggle-text-off{
    position: absolute;
    top: 0;
    bottom: 0;
    right: 6px;
    line-height: 36px;
    text-align: center;
    font-family: 'Quicksand', sans-serif;
    font-size: 14px;
    font-weight: bold;
    -webkit-user-select: none; /* Chrome/Safari */        
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* IE10+ */

    cursor: pointer;

    color: #444249;
}

.toggle-on .toggle-text-off{
    color: rgba(0,0,0,0);
}

/* used for streak effect */
.glow-comp{
    position: absolute;
    opacity: 0;
    top: 10px;
    bottom: 10px;
    left: 10px;
    right: 10px;
    border-radius: 6px;
    background-color: rgba(75, 122, 141, .1);
    box-shadow: 0 0 12px rgba(75, 122, 141, .2);
    -webkit-transition: opacity 4.5s 1s;
    transition: opacity 4.5s 1s;
}

.toggle-on .glow-comp{
    opacity: 1;
    -webkit-transition: opacity 1s;
    transition: opacity 1s;
}


/*========= TOGGLE MOSTRAR CUENTAS BANCARIAS =======*/
/* From Uiverse.io by zanina-yassine */ 
/* Remove this container when use*/
.component-title {
  width: 100%;
  position: absolute;
  z-index: 999;
  top: 30px;
  left: 0;
  padding: 0;
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: #888;
  text-align: center;
}

/* The switch - the box around the slider */
.container {
  width: 100px;
  height: 31px;
  position: relative;
}

/* Hide default HTML checkbox */
.checkbox {
  opacity: 0;
  width: 0;
  height: 0;
  position: absolute;
}

.switch {
  width: 100%;
  height: 100%;
  display: block;
  background-color: #e9e9eb;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.2s ease-out;
}

/* The slider */
.slider {
  width: 27px;
  height: 27px;
  position: absolute;
  left: calc(40% - 27px/2 - 10px);
  top: calc(50% - 27px/2);
  border-radius: 50%;
  background: #FFFFFF;
  box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.15), 0px 3px 1px rgba(0, 0, 0, 0.06);
  transition: all 0.2s ease-out;
  cursor: pointer;
}

.checkbox:checked + .switch {
  background-color: #34C759;
}

.checkbox:checked + .switch .slider {
  left: calc(60% - 27px/2 + 10px);
  top: calc(50% - 27px/2);
}



</style>

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
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
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
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
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



        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>CLAVE MAESTRA NOTAS SALIDA</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="{{route('changePasswordMaster')}}" method="POST">
                        @csrf
                        @method('POST')
                       
                        <div class="row align-items-end">
                            <div class="col-7">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="clave_maestra"  placeholder="Contraseña">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label> <input type="checkbox" class="i-checks" name="estado_clave_maestra" > Activo </label>
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

    @foreach ($config as $item)
        @if ($item->slug == 'EARB')
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>ENVÍO AUTOMÁTICO RESÚMENES BOLETAS</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form id="form_resumenes_envio" action="{{route('configuracion.resumenes.envio')}}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="row align-items-end">
                                <div class="col-7">
                                    <div class="form-group">
                                        <input 
                                            @if ($item->nro_dias)
                                                value="{{$item->nro_dias}}"
                                            @endif
                                        type="text" class="form-control" name="nro_dias" id="nro_dias"  placeholder="NRO DÍAS">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label> <input 
                                            @if ($item->propiedad == "SI")
                                                checked
                                            @endif
                                            type="checkbox" class="i-checks" name="estado_resumenes_envio" id="estado_resumenes_envio" > Activo </label>
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
        @endif

        @php
            $greenter_mode  =   null;
        @endphp

        @if ($item->slug === "AG")
            @php
                $greenter_mode  =   $item->propiedad;
            @endphp
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>AMBIENTE GREENTER</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content" >
                        <div class="row">
                            <div class="col-12" style="height: 50px;">
                                
                                <div class='toggle' id='switch'>
                                    <div class='toggle-text-off'>BETA</div>
                                    <div class='glow-comp'></div>
                                    <div class='toggle-button'></div>
                                    <div class='toggle-text-on'>PRODUCCIÓN</div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($item->slug === 'MCB')
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>MOSTRAR CUENTAS BANCARIAS</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" >
                    <div class="row">
                        <div class="col-12" style="height: 50px;">
                            <div class="container">

                                <input
                                @if ($item->propiedad === "SI")
                                    checked
                                @endif
                                 type="checkbox" class="checkbox" id="checkMostrarCuentasBancarias">
                                <label class="switch" for="checkMostrarCuentasBancarias">
                                  <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

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
    const lstConfig =   @json($config);

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

    document.addEventListener('DOMContentLoaded',()=>{
        loadGreenterMode();
        events();
    })

    function events(){
        document.querySelector('#nro_dias').addEventListener('input',(e)=>{
            let valor = e.target.value;

            valor = valor.replace(/\D/g, '');

            let numero = parseInt(valor);
            if (numero == 0) {
                numero = 1;
            }
          
            if(isNaN(numero)){
                numero = "";
            }

            e.target.value = numero;
        })

        document.querySelector('#form_resumenes_envio').addEventListener('submit',(e)=>{
            e.preventDefault();
            const nro_dias  = document.querySelector('#nro_dias').value;
            const estado    =   document.querySelector('#estado_resumenes_envio').checked;

            if(nro_dias.trim().length == 0 && estado){
                toastr.error('NÚMERO DE DÍAS DEBE SER 1 COMO MÍNIMO','ERROR');
                return;
            }
            document.querySelector('#form_resumenes_envio').submit();
        })

        $('#switch').click(function(e){
            e.preventDefault(); 

            const switchToggle  =       document.querySelector('#switch');
            const modo_cambiar  =       switchToggle.classList.contains('toggle-on')? "BETA" : "PRODUCCIÓN" ;

            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
            title: `Desea cambiar el ambiente de greenter a ${modo_cambiar}?`,
            text: "Está acción afectará la facturación de la empresa!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
            }).then((result) => {
            if (result.isConfirmed) {

                $(this).toggleClass('toggle-on');
                const modo          =       switchToggle.classList.contains('toggle-on')? "PRODUCCION" : "BETA" ;
                setGreenterModo(modo);

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire({
                title: "Operación cancelada",
                text: "No se aplicaron cambios",
                icon: "error"
                });
            }
            });

        });

        //========= TOGGLE MOSTRAR CUENTAS BANCARIAS =======
        document.querySelector('#checkMostrarCuentasBancarias').addEventListener('change',(e)=>{
            //==== EVITAMOS QUE EL FRONTEND CAMBIE =====
            e.preventDefault();
            e.target.checked = !e.target.checked;
            //======= PREGUNTAMOS POR LA CONFIGURACION ACTUAL =====
            const configCuentasBancarias    =   lstConfig[3];
            console.log(configCuentasBancarias) 

            if(configCuentasBancarias.slug === "MCB"){
                const propiedadActual   =   configCuentasBancarias.propiedad;

                let propiedadNueva   =   '';
                propiedadActual === 'NO' ? propiedadNueva = "MOSTRAR" : propiedadNueva = "OCULTAR";
                let messageTitlte   =   `DESEA ${propiedadNueva} LAS CUENTAS BANCARIAS?`;   

                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                title: `${messageTitlte}`,
                text: "Esta acción afectará los pdf de cotización y doc venta!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {

                        try {
                            const res   =   await axios.post(route('configuracion.cuentasBancarias.modo'),{
                                propiedadNueva
                            });
                            
                            if(res.data.success){
                                if(propiedadNueva === "MOSTRAR"){
                                    lstConfig[3].propiedad    =   "SI";
                                    document.querySelector('#checkMostrarCuentasBancarias').checked =   true;
                                }
                                if(propiedadNueva === "OCULTAR"){
                                    lstConfig[3].propiedad    =   "NO";
                                    document.querySelector('#checkMostrarCuentasBancarias').checked =   false;
                                }
                                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                                console.log(configCuentasBancarias);
                            }else{
                                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                            }
                        } catch (error) {
                            toastr.error(error,'ERROR EN LA PETICIÓN');
                        }

                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se aplicaron cambios",
                        icon: "error"
                        });
                    }
                });

            }


           
        })

    }

    function loadGreenterMode(){
        const indexGreenterConfig   =   lstConfig.findIndex((lc)=>{
            return lc.slug === "AG";
        })

        if(indexGreenterConfig.length === -1){
            toastr.error('ERROR AL PINTAR EL ESTADO DEL AMBIENTE GREENTER','OPERACIÓN INCORRECTA');
            return;
        }

        const greenter_config =   lstConfig[indexGreenterConfig];
       
        if(greenter_config.propiedad    === "PRODUCCION"){
            $('.toggle').toggleClass('toggle-on');
        }
    }

    async function setGreenterModo(modo) {
        try {
            const res   =   await axios.post(route('configuracion.greenter.modo'),{modo});
            
            if(res.data.success){
                toastr.success(res.data.message,'CONFIGURACIÓN APLICADA');
            }else{
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN CAMBIAR AMBIENTE GREENTER');
        }
    }
</script>
@endpush
