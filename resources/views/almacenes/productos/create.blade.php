@extends('layout') 
@section('content')
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
                    @include('almacenes.productos.forms.form_producto_create')
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('styles')
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

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
                registrarProducto(e.target);
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


    function registrarProducto(formRegistrarProducto){
        toastr.clear();
        Swal.fire({
            title: 'Opción Guardar',
            text: "¿Seguro que desea guardar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then(async (result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: "Registrando producto...",
                    text: "Por favor, espera mientras procesamos la solicitud.",
                    allowOutsideClick: false, 
                    allowEscapeKey: false,   
                    didOpen: () => {
                        Swal.showLoading(); 
                    },
                });

                try {

                    limpiarErroresValidacion('msgErrorProducto');
                    const formData  =   new FormData(formRegistrarProducto);
                    formData.append('coloresJSON',JSON.stringify(coloresAsignados));
                    const res       =   await axios.post(route('almacenes.producto.store'),formData);

                    if(res.data.success){
                        toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                        window.location =  route('almacenes.producto.index');
                    }else{
                        toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                    }
                    
                } catch (error) {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                        } else {
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.', 'ERROR DE CONEXIÓN');
                    } else {
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }           
                }finally{
                    Swal.close();
                }
                    
            } else if (result.dismiss === Swal.DismissReason.cancel) {
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
