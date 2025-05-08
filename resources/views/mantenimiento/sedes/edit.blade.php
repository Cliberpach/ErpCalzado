@extends('layout') 

@section('content')

@section('mantenimiento-active', 'active')
@section('sedes-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
        <h2  style="text-transform:uppercase">
            <b>Crear de Sede</b>
        </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Editar Sede</strong>
            </li>
        </ol>
    </div>
    
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox ">
                <div class="ibox-content">
                   
                    @include('mantenimiento.sedes.forms.form_edit_sede')

                </div>
            </div>
        </div>
    </div>
</div>


@stop
@push('styles')
<style>
    .swal2-container {
        z-index: 9999 !important; 
    }
</style>
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">   
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="{{asset('Inspinia/js/plugins/select2/select2.full.min.js')}}"></script>


<script>
    const btnGetComprobantes            =   document.querySelector('#btn-get-comprobantes');
    const bodyTableSearchComprobantes   =   document.querySelector('.table-search-comprobantes tbody');
    let tableResumenes  = null;

    let fecha_comprobantes              =   null;
    let listComprobantes                =   [];   

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        iniciarSelect2();  
        setUbigeoPrevio();
    })

    function events(){
       
        document.querySelector('#formActualizarSede').addEventListener('submit',(e)=>{
            e.preventDefault();
            actualizarSede(e.target);
        })

    }

    function iniciarSelect2(){
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('img_vista_previa');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result; 
            };

            reader.readAsDataURL(input.files[0]); 
        }
    }

    function resetImage() {
        const preview = document.getElementById('img_vista_previa');
        const input = document.getElementById('img_empresa');

        preview.src = '{{ asset("img/img_default.png") }}'; // Volver a la imagen por defecto
        input.value = ''; // Limpiar el campo de entrada
    }

    async function actualizarSede(formActualizarSede){

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea actualizar la sede?",
        text: "Se producirán cambios!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí actualizar!",
        cancelButtonText: "No!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
           
            limpiarErroresValidacion('msgError');

            Swal.fire({
                title: "Actualizando...",
                text: "Por favor, espere mientras procesamos la solicitud.",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                
                const formData  =   new FormData(formActualizarSede);
                const sede_id   =   @json($sede->id);

                const res   =   await axios.post(route('mantenimiento.sedes.update',sede_id),formData,
                                {
                                    headers: {
                                        "X-HTTP-Method-Override": "PUT"
                                    }
                                });

                if(res.data.success){
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    window.location.href = "{{ route('mantenimiento.sedes.index') }}";
                }else{
                    Swal.close();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                
                Swal.close();

                if (error.response) { // Verifica si error.response existe
                    if (error.response.status === 422) {
                        toastr.error('VALIDACIÓN CON ERRORES!!!', 'ERROR EN EL SERVIDOR');
                        const lstErrors = error.response.data.errors;
                        pintarErroresValidacion(lstErrors, 'error');
                        return;
                    }

                    toastr.error(error.response.data.message || 'Error desconocido', 'ERROR EN LA PETICIÓN ACTUALIZAR SEDE!!!');
                    return;
                }

                toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR SEDE!!!');
            }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
    }


   
    function changeDepartment(department_id){

        const lstProvinces     =   @json($provincias);
        const lstDistricts     =   @json($distritos);

        let lstProvincesFiltered      =   [];
            
        if(department_id){

            departamento_id = String(department_id).padStart(2, '0');

            lstProvincesFiltered      =   lstProvinces.filter((province)=>{
                return  province.departamento_id == department_id;
            })   

            $('#provincia').empty().trigger('change');

            lstProvincesFiltered.forEach((province)=>{
                $('#provincia').append(new Option(province.nombre, province.id, false, false));
            })

            $('#provincia').select2({
                placeholder: 'Seleccione una provincia',
                width: '100%'
            });

            $('#provincia').trigger('change');
        }

    }

    function changeProvince(province_id){

        const lstDistricts            =   @json($distritos);

        let lstDistrictsFiltered      =   [];

        if(province_id){

            province_id = String(province_id).padStart(4, '0');

            lstDistrictsFiltered      =   lstDistricts.filter((district)=>{
                return  district.provincia_id == province_id;
            })   

            $('#distrito').empty().trigger('change');

            lstDistrictsFiltered.forEach((district)=>{
                $('#distrito').append(new Option(district.nombre, district.id, false, false));
            })

            $('#distrito').select2({
                placeholder: 'Seleccione un distrito',
                width: '100%'
            });
        }

    }

    function setUbigeoPrevio(){
        const sede  =   @json($sede);
        
        $('#departamento').val(sede.departamento_id).trigger('change');
        $('#provincia').val(sede.provincia_id).trigger('change');
        $('#distrito').val(sede.distrito_id).trigger('change');

    }
   
</script>
@endpush