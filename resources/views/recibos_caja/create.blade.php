@extends('layout') @section('content')
@include('ventas.cotizaciones.modal-cliente') 
    
@section('caja-chica-active', 'active')
@section('recibos_caja-active', 'active')

@csrf
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVO RECIBO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.cotizacion.index') }}">Recibos</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
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
                        <div class="col-12">
                            <form action="{{ route('recibos_caja.store') }}" method="POST"
                                id="form-recibos-caja">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h4><b>Datos Generales</b></h4>
                                    </div>
                                    <div class="col-12 d-none">
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="required">Fecha de Documento</label>
                                                <div class="input-group date">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="date" id="fecha_documento" name="fecha_documento"
                                                        class="form-control input-required {{ $errors->has('fecha_documento') ? ' is-invalid' : '' }}"
                                                        value="{{ old('fecha_documento', $fecha_hoy) }}"
                                                        autocomplete="off" required readonly>
                                                    @if ($errors->has('fecha_documento'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('fecha_documento') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="___class_+?31___">Moneda</label>
                                                <select id="moneda" name="moneda"
                                                    class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                                    disabled>
                                                    <option selected>SOLES</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-xs-12 select-required">
                                                <label class="required">Empresa</label>
                                                <select id="empresa" name="empresa"
                                                    class="select2_form form-control {{ $errors->has('empresa') ? ' is-invalid' : '' }}"
                                                    required>
                                                    <option></option>
                                                    @foreach ($empresas as $empresa)
                                                        <option value="{{ $empresa->id }}"
                                                            {{ old('empresa') == $empresa->id || $empresa->id === 1 ? 'selected' : '' }}>
                                                            {{ $empresa->razon_social }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('empresa'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('empresa') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="required">Fecha de Atención</label>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                        <input type="date" id="fecha_atencion" name="fecha_atencion"
                                                            class="form-control {{ $errors->has('fecha_atencion') ? ' is-invalid' : '' }}"
                                                            value="{{ old('fecha_atencion', $fecha_hoy) }}"
                                                            autocomplete="off" required readonly>
                                                        @if ($errors->has('fecha_atencion'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('fecha_atencion') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="">Vendedor</label>
                                                    <select id="vendedor" name="vendedor" class="select2_form form-control" disabled>
                                                        <option></option>
                                                        @foreach (vendedores() as $vendedor)
                                                            <option value="{{ $vendedor->id }}" {{ $vendedor->id === $vendedor_actual ? 'selected' : '' }}>
                                                                {{ $vendedor->persona->apellido_paterno . ' ' . $vendedor->persona->apellido_materno . ' ' . $vendedor->persona->nombres }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input hidden type="text" name="vendedor" value="{{$vendedor_actual}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <label for="metodo_pago">METOD. PAGO</label>
                                                <select required name="metodo_pago" id="metodo_pago" class="form-control select2_form">
                                                    <option value="EFECTIVO" {{ old('metodo_pago') == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                                                    <option value="TRANSFERENCIA" {{ old('metodo_pago') == 'TRANSFERENCIA' ? 'selected' : '' }}>TRANSFERENCIA</option>
                                                    <option value="YAPE/PLIN" {{ old('metodo_pago') == 'YAPE/PLIN' ? 'selected' : '' }}>YAPE/PLIN</option>
                                                </select>                                                
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <label for="monto_recibo">Monto</label>
                                                <input id="monto_recibo" type="text" name="monto" class="form-control" min="0" value="{{ old('monto') }}">
                                                @error('monto')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 select-required">
                                                <div class="form-group">
                                                    <label class="required">Cliente:
                                                        <button type="button" class="btn btn-outline btn-primary" onclick="openModalCliente()">
                                                            Registrar
                                                        </button>
                                                    </label>
                                                    <select id="cliente" name="cliente"
                                                        class="select2_form form-control {{ $errors->has('cliente') ? ' is-invalid' : '' }}"
                                                        required>
                                                        <option></option>
                                                        @foreach ($clientes as $cliente)
                                                            <option @if ($cliente->id == 1)
                                                                selected
                                                            @endif value="{{ $cliente->id }}"
                                                                {{ old('cliente') == $cliente->id ? 'selected' : '' }}>
                                                                {{ $cliente->getDocumento() }} - {{ $cliente->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('cliente'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('cliente') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OBTENER TIPO DE CLIENTE -->
                                    <input type="hidden" name="" id="tipo_cliente">
                                    <!-- OBTENER DATOS DEL PRODUCTO -->
                                    <input type="hidden" name="" id="presentacion_producto">
                                    <input type="hidden" name="" id="codigo_nombre_producto">

                                    <input type="hidden" name="movimiento_id" id="movimiento_id">
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                  

                  

                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('almacenes.producto.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>
                                
                                    <button type="submit" id="btn_grabar" form="form-recibos-caja" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                </div>
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
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">
@endpush

@push('scripts')

<script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>

<script>
    document.addEventListener('DOMContentLoaded',()=>{
        loadSelect2();
        events();
    })

    function events(){
        document.querySelector('#monto_recibo').addEventListener('input',(e)=>{
            e.target.value = e.target.value.replace(/[^\d.]/g, '');

            e.target.value = e.target.value.replace(/(\..*)\./g, '$1');
        })

        document.querySelector('#form-recibos-caja').addEventListener('submit',async(e)=>{
            e.preventDefault();
            const formData      =   new FormData(e.target);
            
            const res_caja_apert    =   await buscarCajaApertUsuario();

            if(res_caja_apert){
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                    });
                    swalWithBootstrapButtons.fire({
                    title: "Registrar recibo de caja?",
                    text: "Se creará un recibo nuevo!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí!",
                    cancelButtonText: "No, cancelar!",
                    reverseButtons: true
                    }).then((result) => {
                    if (result.isConfirmed) {
                       e.target.submit();
                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire({
                        title: "Operación cancelada",
                        text: "No se realizó ninguna operación",
                        icon: "error"
                        });
                    }
                });
            }
           
        })
    }



    function loadSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //============= ABRIR MODAL CLIENTE =============
    function openModalCliente(){
        $("#modal_cliente").modal("show");
    }

    //========= BUSCAR CAJA APERTURADA DE USUARIO =========== 
    async function buscarCajaApertUsuario() {
        try {
            const res   = await axios.get(route('recibos_caja.buscarCajaApertUsuario'));
            console.log(res);
            let validacion    =   true;

            if(res.data.success){
                document.querySelector('#movimiento_id').value    =   res.data.movimiento_id;
                toastr.success(res.data.message,'CAJA VERIFICADA');
            }

            if(!res.data.success){
                validacion  =   false;
                toastr.error(res.data.message,'CAJA VERIFICADA');
            }

            return validacion;

        } catch (error) {
            toastr.error('ERROR EN EL SERVIDOR','ERROR');
            return false;
        }
    }

</script>


@if(Session::has('recibo_caja_error'))
    <script>
        toastr.error('{{ Session::get('recibo_caja_error') }}','ERROR EN EL SERVIDOR');
    </script>
@endif
@endpush







