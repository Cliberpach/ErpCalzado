@extends('layout') @section('content')
@section('almacenes-active', 'active')
@section('producto-active', 'active')
@include('almacenes.categorias.create')
@include('almacenes.marcas.create')
@include('almacenes.modelos.create')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVO PRODUCTO TERMINADO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.producto.index') }}">Productos Terminados</a>
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
                    <form action="{{ route('almacenes.producto.store') }}" method="POST" id="form_registrar_producto">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-xs-12 b-r">
                                <h4><b>Datos Generales</b></h4>
                                <input class="d-none" type="text" id="stocksJSON" name="stocksJSON">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12 d-none">
                                        <div class="form-group">
                                            <label class="required">Código ISO</label>
                                            <input type="text" id="codigo" name="codigo" class="form-control {{ $errors->has('codigo') ? ' is-invalid' : '' }}" value="{{old('codigo')}}" maxlength="50" onkeyup="return mayus(this)">
                                            @if ($errors->has('codigo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('codigo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-xs-12 d-none">
                                        <div class="form-group">
                                            <label class="required">Unidad de Medida</label>
                                            <select id="medida" name="medida" class="select2_form form-control {{ $errors->has('medida') ? ' is-invalid' : '' }}" required>
                                                <option></option>
                                                @foreach(unidad_medida() as $medida)
                                                    <option value="{{ $medida->id }}" {{ ($medida->simbolo== "NIU" ? "selected" : "") }}>{{ $medida->simbolo.' - '.$medida->descripcion }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('medida'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('medida') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-6 col-xs-12 ">
                                        <div class="form-group">
                                            <label>Peso (KG)</label>
                                            <input type="number" id="peso_producto" placeholder="0.00" step="0.001" min="0" onkeypress="return filterFloat(event, this);"  class="form-control {{ $errors->has('peso_producto') ? ' is-invalid' : '' }}" name="peso_producto" value="{{ old('peso_producto', 0.001)}}">
                                            @if ($errors->has('peso_producto'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('peso_producto') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div> --}}

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="required">Nombre del Producto</label>
                                            <input type="text" id="nombre" name="nombre" class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" value="{{old('nombre')}}" maxlength="191" onkeyup="return mayus(this)" required>
                                            @if ($errors->has('nombre'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('nombre') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row align-items-end">
                                            <div class="col-12 col-md-10">
                                                <div class="form-group">
                                                    <label class="">Código de Barra</label>
                                                    <input type="text" id="codigo_barra" class="form-control {{ $errors->has('codigo_barra') ? ' is-invalid' : '' }}" name="codigo_barra" value="{{ old('codigo_barra')}}" maxlength="20">
                                                    @if ($errors->has('codigo_barra'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('codigo_barra') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-block" title="Generar" onclick="generarCode()"><i class="fa fa-refresh"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2 d-none">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary btn-block" title="Imprimir"><i class="fa fa-file-excel-o"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="required">Categoria</label>  
                                            <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_categoria"  class="btn btn-primary" href="#">
                                                <i class="fas fa-plus"></i>    
                                            </a> 
                                            <select id="categoria" name="categoria" class="select2_form form-control {{ $errors->has('familia') ? ' is-invalid' : '' }}">
                                                <option></option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ (old('categoria') == $categoria->id ? "selected" : "") }} >{{ $categoria->descripcion }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('categoria'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('categoria') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="required">Marca</label>
                                            <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_marca"  class="btn btn-primary" href="#">
                                                <i class="fas fa-plus"></i>    
                                            </a> 
                                            <select id="marca" name="marca" class="select2_form form-control {{ $errors->has('marca') ? ' is-invalid' : '' }}" required value="{{old('marca')}}">
                                                <option></option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" {{ (old('marca') == $marca->id ? "selected" : "") }} >{{ $marca->marca }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('marca'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('marca') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-6 col-xs-12">

                                <div class="row">

                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="required">Modelo</label>
                                            <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_modelo"  class="btn btn-primary" href="#">
                                                <i class="fas fa-plus"></i>    
                                            </a> 
                                            <select id="modelo" name="modelo" class="select2_form form-control {{ $errors->has('familia') ? ' is-invalid' : '' }}">
                                                <option></option>
                                                @foreach($modelos as $modelo)
                                                    <option value="{{ $modelo->id }}" {{ (old('modelo') == $modelo->id ? "selected" : "") }} >{{ $modelo->descripcion }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('modelo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('modelo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12 d-none">
                                        <label class="required">Moneda</label>
                                        <select class="select2_form form-control" style="text-transform: uppercase; width:100%" name="moneda_cliente" id="moneda_cliente" disabled>
                                            <option></option>
                                            @foreach (tipos_moneda() as $tipo_moneda)
                                            <option value="{{$tipo_moneda->id}}" {{$tipo_moneda->descripcion === 'SOLES' ? 'selected' : ''}}>{{$tipo_moneda->simbolo.' - '.$tipo_moneda->descripcion}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"><b><span id="error-moneda"></span></b></div>
                                    </div>
                                    <div class="col-lg-6 col-12">

                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="required form-label">PRECIO 1</label>
                                            <input class="form-control" type="number" step="0.01" inputmode="decimal" id="precio1" name="precio1" />
                                            @if ($errors->has('precio1'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio1') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="required form-label">PRECIO 2</label>
                                            <input class="form-control" type="number" step="0.01" inputmode="decimal" id="precio2" name="precio2" />
                                            @if ($errors->has('precio2'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio2') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label class="required form-label">PRECIO 3</label>
                                            <input class="form-control" type="number" step="0.01" inputmode="decimal" id="precio3" name="precio3" />
                                            @if ($errors->has('precio3'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio3') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <h4><b>Cantidades y Precios</b></h4>
                                    <div class="form-group row">
                                        <div class="col-lg-6 col-xs-12">
                                            <label class="">Stock</label>
                                            <input type="text" id="stock" name="stock" readonly class="form-control {{ $errors->has('stock') ? ' is-invalid' : '' }}" value="{{old('stock')}}" maxlength="10" onkeypress="return isNumber(event);" required>
                                            @if ($errors->has('stock'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('stock') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-lg-6 col-xs-12">
                                            <label class="required">Stock mínimo</label>
                                            <input type="text" id="stock_minimo" name="stock_minimo" class="form-control {{ $errors->has('stock_minimo') ? ' is-invalid' : '' }}" value="{{old('stock_minimo')}}" maxlength="10" onkeypress="return isNumber(event);" required>
                                            @if ($errors->has('stock_minimo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('stock_minimo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div> --}}
                                    {{-- <div class="form-group row d-none">
                                        <div class="col-lg-6 col-xs-12">
                                            <label class="required">Precio venta mínimo</label>
                                            <input type="text" id="precio_venta_minimo" name="precio_venta_minimo" class="form-control {{ $errors->has('precio_venta_minimo') ? ' is-invalid' : '' }}" value="{{old('precio_venta_minimo')}}" maxlength="15" onkeypress="return filterFloat(event, this);">
                                            @if ($errors->has('precio_venta_minimo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio_venta_minimo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-lg-6 col-xs-12">
                                            <label class="required">Precio venta máximo</label>
                                            <input type="precio_venta_maximo" id="precio_venta_maximo" name="precio_venta_maximo" class="form-control {{ $errors->has('precio_venta_maximo') ? ' is-invalid' : '' }}" value="{{old('precio_venta_maximo')}}" maxlength="15" onkeypress="return filterFloat(event, this);">
                                            @if ($errors->has('precio_venta_maximo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio_venta_maximo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div> --}}
                                    {{-- <div class="form-group row">
                                        <div class="col-lg-12 col-xs-12">
                                            <label class="required">Incluye IGV</label>
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="row">
                                                        <div class="col-sm-6 col-xs-6">
                                                            <div class="radio">
                                                                <input type="radio" name="igv" id="igv_si" value="1" checked="">
                                                                <label for="igv_si">
                                                                    SI
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-xs-6">
                                                            <div class="radio">
                                                                <input type="radio" name="igv" id="igv_no" value="0">
                                                                <label for="igv_no">
                                                                    NO
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="row">
                                                        <div class="col-md-12 col-12">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <label> <input type="checkbox" checked class="i-checks"  value="SI"> <b class="text-danger">Facturado (SI/NO)</b> </label>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="facturacion_producto" id="facturacion_producto" value="SI">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="form-group row">
                                    
                                    
                                        {{-- ALMACENES --}}
                                        <div class="col-lg-6 col-12">
                                            <div class="form-group">
                                                {{-- <label class="required">Almacén</label> --}}
                                                @foreach($almacenes as $almacen)
                                                    @if ($almacen->descripcion =="CENTRAL")
                                                    <input type="hidden" value="{{ $almacen->id }}" id="almacen" name="almacen" >
                                                    @endif
                                                @endforeach
                                                {{-- <select id="almacen" name="almacen" class="select2_form form-control {{ $errors->has('sub_familia') ? ' is-invalid' : '' }}" required >
                                                    <option></option>
                                                    @foreach($almacenes as $almacen)
                                                        <option value="{{ $almacen->id }}" {{ (old('almacen') == $almacen->id ? "selected" : "") }} >{{ $almacen->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('almacen'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('almacen') }}</strong>
                                                    </span>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Stocks</b></h4>
                                    </div>
                                    <div class="panel-body">


                                        {{--<div class="row">

                                             <div class="col-md-4">
                                                <label class="required">Cliente</label>
                                                <select class="select2_form form-control"
                                                    style="text-transform: uppercase; width:100%" name="cliente"
                                                    id="cliente">
                                                    <option></option>
                                                    @foreach (tipo_clientes() as $cliente)
                                                    <option value="{{$cliente->descripcion}}">{{$cliente->descripcion}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"><b><span id="error-cliente"></span></b>
                                                </div>
                                            </div> --}}
                                            
                                            {{-- <div class="col-md-2">
                                                <label class="required">Porcentaje</label>
                                                <input type="text" id="porcentaje" name="porcentaje" class="form-control">
                                                <div class="invalid-feedback"><b><span id="error-porcentaje"></span></b></div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="">&nbsp;</label>
                                                <a class="btn btn-block btn-warning enviar_cliente" style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
                                            </div> 
                                        </div>--}}

                                        {{-- <input type="hidden" id="clientes_tabla" name="clientes_tabla[]"> --}}

                                        {{-- <hr> --}}

                                        {{-- <div class="table-responsive">
                                            <table
                                                class="table dataTables-clientes table-striped table-bordered table-hover"
                                                style="text-transform:uppercase">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">ACCIONES</th>
                                                        <th class="text-center">CLIENTE</th>
                                                        <th class="text-center">MONEDA</th>
                                                        <th class="text-center">PORCENTAJE</th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

                                            </table>
                                        </div> --}}
                                       @include('almacenes.productos.list-color-tallas')

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group row">
                                    <div class="col-md-6 text-left">
                                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco
                                            (<label class="required"></label>) son obligatorios.</small>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{route('almacenes.producto.index')}}" id="btn_cancelar"
                                           class="btn btn-w-m btn-default">
                                            <i class="fa fa-arrow-left"></i> Regresar
                                        </a>
                                        <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                            <i class="fa fa-save"></i> Grabar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('almacenes.productos.modal')
@stop

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    <link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/iCheck/custom.css' )}}" rel="stylesheet">

@endpush

@push('scripts')
    <script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <script>


        //Modal Eliminar
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        $(document).ready(function() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            //Controlar Error
            $.fn.DataTable.ext.errMode = 'throw';

            $("#codigo").on("change", validarCodigo);
            // $('#form_registrar_producto').submit(function(e) {
            //     e.preventDefault();
            //     Swal.fire({
            //         title: 'Opción Guardar',
            //         text: "¿Seguro que desea guardar cambios?",
            //         icon: 'question',
            //         showCancelButton: true,
            //         confirmButtonColor: "#1ab394",
            //         confirmButtonText: 'Si, Confirmar',
            //         cancelButtonText: "No, Cancelar",
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             this.submit();
            //             // var existe = buscarConsumidor()
            //             // if (existe == true) {
            //             //     cargarClientes();
            //             //     
            //             // }else{
            //             //     toastr.error('Es obligatorio el ingreso del Cliente Consumidor Normal y la moneda Soles.', 'Error');
            //             // }
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             swalWithBootstrapButtons.fire(
            //                 'Cancelado',
            //                 'La Solicitud se ha cancelado.',
            //                 'error'
            //             )
            //         }
            //     })
            // });

        });

        function validarCodigo() {
            // Consultamos nuestra BBDD
            $.ajax({
                dataType : 'json',
                type : 'post',
                url : '{{ route('almacenes.producto.getCodigo') }}',
                data : {
                    '_token' : $('input[name=_token]').val(),
                    'codigo' : $(this).val(),
                    'id': null
                }
            }).done(function (result){
                if (result.existe) {
                    toastr.error('El código ingresado ya se encuentra registrado para un producto','Error');
                    $(this).focus();
                }
            });
        }

        function generarCode() {
            // Consultamos nuestra BBDD
            $.ajax({
                dataType : 'json',
                type : 'get',
                url : '{{ route('generarCode') }}',
            }).done(function (result){
                $('#codigo_barra').val(result.code)
            });
        }

    </script>

    <script>

        $('#porcentaje').keyup(function() {
            var val = $(this).val();
            if (isNaN(val)) {
                val = val.replace(/[^0-9\.]/g, '');
                if (val.split('.').length > 2)
                    val = val.replace(/\.+$/, "");
            }
            $(this).val(val);
        });

        $('#porcentaje_editar').keyup(function() {
            var val = $(this).val();
            if (isNaN(val)) {
                val = val.replace(/[^0-9\.]/g, '');
                if (val.split('.').length > 2)
                    val = val.replace(/\.+$/, "");
            }
            $(this).val(val);
        });


        $(document).ready(function() {
            $('.i-checks').iCheck({
               checkboxClass: 'icheckbox_square-green',
               radioClass: 'iradio_square-green',
               cursor:true
            });
            $('.i-checks').on('ifChecked', function(event){
                $("#facturacion_producto").val("SI");
            });
            $('.i-checks').on('ifUnchecked', function(event){
                $("#facturacion_producto").val("NO");
            });
            // DataTables
            $('.dataTables-clientes').DataTable({
                "dom": 'lTfgitp',
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false,
                "language": {
                    "url": "{{asset('Spanish.json')}}"
                },

                "columnDefs": [
                    {
                        "targets": [0],
                        className: "text-center",
                        render: function(data, type, row) {
                            return "<div class='btn-group'>" +
                                "<a class='btn btn-warning btn-sm modificarDetalle' id='editar_cliente' style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                                "<a class='btn btn-danger btn-sm' id='borrar_cliente' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                                "</div>";
                        }
                    },
                    {
                        "targets": [1],
                        className: "text-left",
                    },
                    {
                        "targets": [2],
                        className: "text-center",
                    },
                    {
                        "targets": [3],
                        className: "text-center",
                    },
                    {
                        "targets": [4],
                        visible: false,
                        className: "text-center",
                    }

                ],

            });
        })


        //Editar Registro
        $(document).on('click', '#editar_cliente', function(event) {
            var table = $('.dataTables-clientes').DataTable();
            var data = table.row($(this).parents('tr')).data();
            $('#indice').val(table.row($(this).parents('tr')).index());
            $('#cliente_id_editar').val(data[1]).trigger('change');
            $('#moneda_id_editar').val(data[4]).trigger('change');
            $('#porcentaje_editar').val(data[3]);
            $('#modal_editar_cliente').modal('show');
        })

        //Borrar registro de articulos
        $(document).on('click', '#borrar_cliente', function(event) {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            })

            Swal.fire({
                title: 'Opción Eliminar',
                text: "¿Seguro que desea eliminar Artículo?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    var table = $('.dataTables-clientes').DataTable();
                    table.row($(this).parents('tr')).remove().draw();
                    // sumaTotal()

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



        });

        //Validacion al ingresar tablas
        $(".enviar_cliente").click(function() {
            limpiarErrores()
            var enviar = false;
            if ($('#cliente').val() == '') {
                toastr.error('Seleccione Cliente.', 'Error');
                enviar = true;
                $('#cliente').addClass("is-invalid")
                $('#error-cliente').text('El campo Cliente es obligatorio.')
            } else {
                var existe = buscarClientes($('#cliente').val() , $('#moneda_cliente').val() )
                if (existe == true) {
                    toastr.error('Tipo de Cliente y moneda ya se encuentra ingresado.', 'Error');
                    enviar = true;
                }
            }

            if ($('#porcentaje').val() == '') {

                toastr.error('Ingrese el porcentaje del tipo de cliente.', 'Error');
                enviar = true;

                $("#porcentaje").addClass("is-invalid");
                $('#error-porcentaje').text('El campo Porcentaje es obligatorio.')
            }

            if ($('#moneda_cliente').val() == '') {

                toastr.error('Seleccione la moneda del tipo de cliente.', 'Error');
                enviar = true;

                $("#moneda_cliente").addClass("is-invalid");
                $('#error-moneda').text('El campo Moneda es obligatorio.')
            }

            if (enviar != true) {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger',
                    },
                    buttonsStyling: false
                })

                Swal.fire({
                    title: 'Opción Agregar',
                    text: "¿Seguro que desea agregar Tipo de Cliente?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {

                        var detalle = {
                            cliente: $('#cliente').val(),
                            porcentaje: $('#porcentaje').val(),
                            moneda: $('#moneda_cliente').val(),
                            id_moneda: $('#moneda_cliente').val(),
                        }

                        limpiarDetalle()
                        agregarTabla(detalle);

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
        })

        function buscarClientes(cliente, moneda) {
            var tipo_moneda = cargarMoneda(moneda)
            var existe = false;
            var t = $('.dataTables-clientes').DataTable();
            t.rows().data().each(function(el, index) {
                if (el[1] == cliente && el[2] == tipo_moneda ) {
                    existe = true
                }
            });
            return existe
        }


        function agregarTabla(detalle) {
            var t = $('.dataTables-clientes').DataTable();
            t.row.add([
                '',
                detalle.cliente,
                cargarMoneda(detalle.moneda),
                Number(detalle.porcentaje).toFixed(2),
                detalle.moneda,


            ]).draw(false);

            cargarClientes()
        }

        function cargarMoneda(id) {

            var moneda = ""

            @foreach (tipos_moneda() as $tipo_moneda)
                if ("{{$tipo_moneda->id}}" == id ) {
                    moneda = "{{$tipo_moneda->descripcion}}"
                }
            @endforeach

            return moneda
        }

        function cargarClientes() {

            var clientes = [];
            var table = $('.dataTables-clientes').DataTable();
            var data = table.rows().data();

            data.each(function(value, index) {

                var url = '{{ route("mantenimiento.tabla.detalle.getDetail", ":descripcion")}}';
                url = url.replace(':descripcion', value[1]);

                $.ajax({
                    url: url,
                    type:'get',
                    success : function(tabladetalle){
                        let fila = {
                            cliente: tabladetalle.id,
                            monto_igv: value[3],
                            moneda: value[2],
                            id_moneda: value[4],
                        };
                        clientes.push(fila);
                        $('#clientes_tabla').val(JSON.stringify(clientes));
                    },
                })

            });
        }

        function limpiarDetalle() {
            $('#porcentaje').val('')
            $('#cliente').val($('#cliente option:first-child').val()).trigger('change');
            //$('#moneda_cliente').val($('#moneda_cliente option:first-child').val()).trigger('change');

        }

        function limpiarErrores() {
            $('#porcentaje').removeClass("is-invalid")
            $('#error-porcentaje').text('')

            $('#cliente').removeClass("is-invalid")
            $('#error-cliente').text('')

            $('#moneda_cliente').removeClass("is-invalid")
            $('#error-moneda').text('')
        }


        //CONSULTAR SI EXISTE EL CLIENTE TIPO CONSUMIDOR
        function buscarConsumidor() {
            var existe = false
            var table = $('.dataTables-clientes').DataTable();
            table.rows().data().each(function(el, index) {
                if (el[1] == 'NORMAL' && el[4] == '1' ) {
                    existe = true
                }
            });
            return existe
        }
    </script>
    <script src="{{asset('js/products-create.js')}}"></script>
@endpush
