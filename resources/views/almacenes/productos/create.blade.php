@extends('layout') @section('content')
@section('almacenes-active', 'active')
@section('producto-active', 'active')
@include('almacenes.categorias.create')
@include('almacenes.marcas.create')
@include('almacenes.modelos.create')
@include('almacenes.colores.create') 

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
                                <input class="d-none" type="text" id="coloresJSON" name="coloresJSON">
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
                                            <div class="col-12 col-md-10 d-none">
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
                                            <div class="col-12 col-md-2 d-none">
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
                                            <select required id="categoria" name="categoria" class="select2_form form-control {{ $errors->has('familia') ? ' is-invalid' : '' }}">
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
                                            <select required id="modelo" name="modelo" class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}">
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
                                            <input required class="form-control  {{ $errors->has('precio1') ? ' is-invalid' : '' }}" type="number" step="0.01" inputmode="decimal" id="precio1" name="precio1" value="{{old('precio1')}}" />
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
                                            <input required class="form-control  {{ $errors->has('precio2') ? ' is-invalid' : '' }}" type="number" step="0.01" inputmode="decimal" id="precio2" name="precio2" value="{{old('precio2')}}" />
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
                                            <input required class="form-control {{ $errors->has('precio3') ? ' is-invalid' : '' }}" type="number" step="0.01" inputmode="decimal" id="precio3" name="precio3" value="{{old('precio3')}}" />
                                            @if ($errors->has('precio3'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('precio3') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="required">COSTO</label>
                                            <input  class="form-control {{ $errors->has('costo') ? ' is-invalid' : '' }}" type="number" step="0.01" inputmode="decimal" id="costo" name="costo"  value="{{ old('costo')}}"/>
                                            @if ($errors->has('costo'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('costo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                  
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

                        @include('almacenes.productos.table-colores')

                       
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

<link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
<script>

        //====== VARIABLES ===============
        const formCrearCategoria        =   document.querySelector('#crear_categoria');
        const formCrearMarca            =   document.querySelector('#crear_marca');
        const formCrearModelo           =   document.querySelector('#crear_modelo');
        const formCrearColor            =   document.querySelector('#crear_color');
        const formRegProducto           =   document.querySelector('#form_registrar_producto');
        const tokenValue                =   document.querySelector('input[name="_token"]').value;
        const selectCategorias          =   document.querySelector('#categoria');
        const selectMarcas              =   document.querySelector('#marca');
        const selectModelos             =   document.querySelector('#modelo');
        const inputColoresJSON          =   document.querySelector('#coloresJSON');
        const tableColores              =   document.querySelector('#table-colores');

        let coloresAsignados    = [];
        let datatableColores = null;

        //=========== CUANDO SE CARGUE EL HTML Y CSS HACER ESTO =========
        document.addEventListener('DOMContentLoaded',()=>{
            loadSelect2();
            cargarDatatables();
            events();
        })

        function events(){

            //marcar check color 
            document.addEventListener('click',(e)=>{
                if(e.target.classList.contains('checkColor')){
                    const colorId = e.target.getAttribute('data-color-id');
                    if(e.target.checked){
                        addColor(colorId);
                    }else{
                        removeColor(colorId);
                    }
                }
            })

             //========== FORM REG PRODUCTO ==============
            formRegProducto.addEventListener('submit',(e)=>{
                e.preventDefault();
                Swal.fire({
                    title: 'Opción Guardar',
                    text: "¿Seguro que desea guardar cambios?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveColorsAssigned();
                        e.target.submit();
                    
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'La Solicitud se ha cancelado.',
                            'error'
                        )
                    }
                })
            })

            //============ FETCH CREAR CATEGORIA ==========================
            formCrearCategoria.addEventListener('submit',(e)=>{
                e.preventDefault();
                const url           =   '/almacenes/categorias/store';
                const formData      =   new FormData(e.target);
                formData.append('fetch', 'SI');
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': tokenValue,
                        },
                        body:   formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.message=='success'){
                            updateSelectCategorias(data.data);
                            $('#modal_crear_categoria').modal('hide');
                            toastr.success('Categoría creada.', 'Éxito');
                            formCrearCategoria.reset();
                        }else if(data.message=='error'){
                            toastr.error(pintarErroresCategoria(data.data.descripcion_guardar), 'Error');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            })


             //=================== FETCH CREAR MARCA =================================
            formCrearMarca.addEventListener('submit',(e)=>{
                e.preventDefault();
                const url           =   '/almacenes/marcas/store';
                const formData      =   new FormData(e.target);
                formData.append('fetch', 'SI');
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': tokenValue,
                        },
                        body:   formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.message=='success'){
                            updateSelectMarcas(data.data);
                            $('#modal_crear_marca').modal('hide');
                            toastr.success('Marca creada.', 'Éxito');
                            formCrearCategoria.reset();
                        }else if(data.message=='error'){
                            toastr.error(pintarErroresMarca(data.data.marca_guardar), 'Error');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            })

            //==================== FETCH CREAR MODELO ==========================
            formCrearModelo.addEventListener('submit',(e)=>{
                e.preventDefault();
                const url           =   '/almacenes/modelos/store';
                const formData      =   new FormData(e.target);
                formData.append('fetch', 'SI');
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': tokenValue,
                        },
                        body:   formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.message=='success'){
                            updateSelectModelos(data.data);
                            $('#modal_crear_modelo').modal('hide');
                            toastr.success('Modelo creado.', 'Éxito');
                            formCrearModelo.reset();
                        }else if(data.message=='error'){
                            toastr.error(pintarErroresModelo(data.data.descripcion_guardar), 'Error');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            })


             //==================== FETCH CREAR COLOR ==========================
             formCrearColor.addEventListener('submit',(e)=>{
                e.preventDefault();
                const url           =   '/almacenes/colores/store';
                const formData      =   new FormData(e.target);
                formData.append('fetch', 'SI');
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': tokenValue,
                        },
                        body:   formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.message=='success'){
                            //updateSelectModelos(data.data);
                            $('#modal_crear_color').modal('hide');
                            toastr.success('Color creado.', 'Éxito');
                            formCrearColor.reset();
                            addColorDataTable(data.data);

                        }else if(data.message=='error'){
                            toastr.error(pintarErroresColor(data.data.descripcion_guardar), 'Error');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            })
        }

        //====== CARGAR EXTENSIÓN SELECT2 ============
        const loadSelect2 = ()=>{
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
        }

         //===== PINTAR ERRORES AL CREAR COLOR =====
         const pintarErroresColor    =   (errores_color)=>{
            let message = '';
            errores_color.forEach((m, index) => {
                message += m;
                if (index < errores_color.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR CATEGORÍA =====
         const pintarErroresCategoria    =   (errores_marca)=>{
            let message = '';
            errores_marca.forEach((m, index) => {
                message += m;
                if (index < errores_marca.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR MARCA =====
        const pintarErroresMarca    =   (errores_marca)=>{
            let message = '';
            errores_marca.forEach((m, index) => {
                message += m;
                if (index < errores_marca.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }

        //===== PINTAR ERRORES AL CREAR MODELO =====
        const pintarErroresModelo    =   (errores_modelo)=>{
            let message = '';
            errores_modelo.forEach((m, index) => {
                message += m;
                if (index < errores_modelo.length - 1) {
                    message += '\n';
                }
            });
            return message;
        }


       //==== actualizar select de categorías ============
        const updateSelectCategorias = (categorias_actualizadas) => {
            let items = '<option></option>';
            categorias_actualizadas.forEach((c) => {
                const selected = "{{ (old('categoria') == '" + c.id + "') ? 'selected' : '' }}";
                items += `<option value="${c.id}" ${selected}>${c.descripcion}</option>`;
            });
            selectCategorias.innerHTML = items;
        };

        //====== actualizar select de marcas =========
        const updateSelectMarcas = (marcas_actualizadas) => {
            let items = '<option></option>';
            marcas_actualizadas.forEach((m) => {
                const selected = "{{ (old('marca') == '" + m.id + "') ? 'selected' : '' }}";
                items += `<option value="${m.id}" ${selected}>${m.marca}</option>`;
            });
            selectMarcas.innerHTML = items;
        };


       //========= actualizar select de modelos ===========
        const updateSelectModelos = (modelos_actualizados) => {
            let items = '<option></option>';
            modelos_actualizados.forEach((m) => {
                const selected = "{{ (old('marca') == '" + m.id + "') ? 'selected' : '' }}";
                items += `<option value="${m.id}" ${selected}>${m.descripcion}</option>`;
            });
            selectModelos.innerHTML = items;
        };


        //============ guardar colores asignados ============
        const saveColorsAssigned    = () =>{
            //======== guardamos el array en el inputJSON de colores asignados ========
            inputColoresJSON.value = JSON.stringify(coloresAsignados);
        }

        //========== cargar datatables =======
        const cargarDatatables = ()=>{
            datatableColores = new DataTable('#table-colores',
            {
                language: {
                    processing:     "Cargando...",
                    search:         "BUSCAR: ",
                    lengthMenu:    "MOSTRAR _MENU_ COLORES",
                    info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ COLORES",
                    infoEmpty:      "MOSTRANDO 0 ELEMENTOS",
                    infoFiltered:   "(FILTRADO de _MAX_ COLORES)",
                    infoPostFix:    "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable:     "NO HAY COLORES DISPONIBLES",
                    paginate: {
                        first:      "PRIMERO",
                        previous:   "ANTERIOR",
                        next:       "SIGUIENTE",
                        last:       "ÚLTIMO"
                    },
                    aria: {
                        sortAscending:  ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                }
            });
        }

        function addColorDataTable(color){
            datatableColores.row.add(
                [`<div style="text-align: left;font-weight:bold;">${color.id}</div>`,
                 `
                    <div class="form-check">
                        <input class="form-check-input checkColor" type="checkbox" value="" id="checkColor_${color.id}" 
                        data-color-id="${color.id}">
                        <label class="form-check-label" for="checkColor_${color.id}">
                            ${color.descripcion}
                        </label>
                    </div>
                 `
                ] 
            ).draw();
        }

        //agregar colores al array asignados 
        function addColor (idColor){
            if(!coloresAsignados.includes(idColor)){
                coloresAsignados.push(idColor);
            }
        }

        function removeColor(idColor){
            coloresAsignados = coloresAsignados.filter((c)=>{return c!=idColor })
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

@endpush
