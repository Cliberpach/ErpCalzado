@extends('layout') 
@section('content')
@section('almacenes-active', 'active')
@section('conductores-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVO CONDUCTOR</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.conductores.index') }}">Productos Terminados</a>
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
                    @include('almacenes.conductores.forms.form_create_conductor')
                </div>
                <div class="ibox-footer d-flex justify-content-between align-items-center">
                    <span  style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son obligatorios</span>
                    
                    <div style="display:flex;">
                        <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                            <i class="fas fa-reply-all"></i> VOLVER
                        </button>
                        <button class="btn btn-primary" type="submit" form="formRegistrarConductor">
                            <i class="fas fa-save"></i> REGISTRAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('styles')
<link href="{{asset('Inspinia/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded',()=>{
        iniciarSelect2();
        events();
    })

    function events(){

        document.querySelector('#formRegistrarConductor').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarConductor();
        })

        document.addEventListener('click',(e)=>{
            if (e.target.closest('.btnVolver')) {
                const rutaIndex         =   '{{route('almacenes.conductores.index')}}';
                window.location.href    =   rutaIndex;
            }
        })

        //======= CONSULTAR API DOCUMENTO DNI ========
        document.querySelector('#btn_consultar_documento').addEventListener('click',()=>{
            const dni               =   document.querySelector('#nro_documento').value;
            const tipo_documento    =   document.querySelector('#tipo_documento').value;
            toastr.clear();

            if(tipo_documento != 6){
                toastr.error('SOLO SE PUEDE CONSULTAR TIPO DE DOCUMENTO DNI');
                return;
            }

            if(dni.length != 8){
                toastr.error('NRO DE DNI DEBE CONTAR CON 8 DÍGITOS');
                return;
            }

            consultarDocumento(dni);

        })

      
        //========== PERMITIR SOLO FORMATO DE CELULAR O TELEFONO ======
        document.querySelector('#telefono').addEventListener('input',(e)=>{
            const input = e.target;
            const maxLength = 20;
            
            // Expresión regular para validar números de teléfono internacionales
            const validPattern = /^\+?[0-9]*$/;

            // Reemplaza cualquier carácter que no sea un dígito o "+"
            let value = input.value.replace(/[^0-9+]/g, '');

            // Asegúrate de que el símbolo '+' esté al principio
            if (value.startsWith('+')) {
                value = '+' + value.slice(1).replace(/^\+/, '');
            } else {
                value = value.replace(/^\+/, '');
            }

            // Limita el valor a 20 caracteres
            if (value.length > maxLength) {
                value = value.slice(0, maxLength);
            }

            // Actualiza el valor del input
            input.value = value;
        })

        //===== PERMITIR SOLO NUMEROS ========
        document.querySelector('#nro_documento').addEventListener('input', (e) => {
            const input = e.target;

            input.value = input.value.replace(/\D/g, '');
        });
    }

    function iniciarSelect2(){
        $( '.select2_form' ).select2( {
            width:'100%',
            placeholder: $( this ).data( 'placeholder' ),
        } ); 
    }

    function registrarConductor(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "DESEA REGISTRAR EL CONDUCTOR?",
        text: "Se creará un nuevo conductor!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ, REGISTRAR!",
        cancelButtonText: "NO, CANCELAR!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            limpiarErroresValidacion('msgError');
            const token                     =   document.querySelector('input[name="_token"]').value;
            const formRegistrarConductor    =   document.querySelector('#formRegistrarConductor');
            const formData                  =   new FormData(formRegistrarConductor);
            const urlRegistrarConductor     =   @json(route('almacenes.conductores.store'));

            Swal.fire({
                title: 'Cargando...',
                html: 'Registrando nuevo conductor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                const response  =   await fetch(urlRegistrarConductor, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        },
                                        body: formData
                                    });

                const   res =   await response.json();
                
                console.log(res);
                
                if(response.status === 422){
                    if('errors' in res){
                        pintarErroresValidacion(res.errors);
                    }
                    Swal.close();
                    return;
                }
                
                if(res.success){
                    const conductor_index     =   @json(route('almacenes.conductores.index'));
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                    window.location.href    =   conductor_index;
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

              
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR CONDUCTOR');
                Swal.close();
            }
          

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
            title: "OPERACIÓN CANCELADA",
            text: "NO SE REALIZARON ACCIONES",
            icon: "error"
            });
        }
        });
    }

    function pintarErroresValidacion(objErroresValidacion){
        for (let clave in objErroresValidacion) {
            const pError        =   document.querySelector(`.${clave}_error`);
            pError.textContent  =   objErroresValidacion[clave][0];
        }
    }

    //======== CHANGE TIPO DOCUMENTO ======
    function changeTipoDoc(params) {
        const tipo_documento        =   document.querySelector('#tipo_documento').value;
        const inputNroDoc           =   document.querySelector('#nro_documento');
        const btnConsultarDocumento =   document.querySelector('#btn_consultar_documento');
        
        //======== DNI =======
        if(tipo_documento == 6){
            inputNroDoc.value               =   '';
            inputNroDoc.readOnly            =   false;
            inputNroDoc.maxLength           =   8;
            btnConsultarDocumento.disabled  =   false;
        }

        //====== CARNET EXTRANJERÍA =====
        if(tipo_documento == 7){
            inputNroDoc.value               =   '';
            inputNroDoc.readOnly            =   false;
            inputNroDoc.maxLength           =   20;
            btnConsultarDocumento.disabled  =   true;
        }
    }

    //======= CONSULTAR DOCUMENTO IDENTIDAD =====
    async function consultarDocumento(dni){
        mostrarAnimacion();
        try {
            const token     =   document.querySelector('input[name="_token"]').value;
            const urlApiDni = `{{ route('almacenes.conductores.consultarDni', ':dni') }}`.replace(':dni', encodeURIComponent(dni));

            const response  =   await fetch(urlApiDni, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': token 
                                    },
                                });

            const   res =   await response.json();

            if(res.success){
                setDatosDni(res.data.data);
                toastr.info(res.message);
            }else{
                toastr.error(res.message,'ERROR EN EL SERVIDOR AL CONSULTAR DNI');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN CONSULTAR DNI');
        }finally{
            ocultarAnimacion();
        }
    }

    function setDatosDni(data){
        const nombres           =   data.nombres;
        const apellidos         =   `${data.apellido_paterno} ${data.apellido_materno}`;
        //const nombre_completo   =   `${data.nombres} ${data.apellido_paterno} ${data.apellido_materno}`;

        document.querySelector('#nombre').value         =   nombres;
        document.querySelector('#apellido').value       =   apellidos;

    }

</script>

@endpush
