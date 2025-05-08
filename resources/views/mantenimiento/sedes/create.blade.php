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
                <strong>Crear Sede</strong>
            </li>
        </ol>
    </div>
    
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox ">
                <div class="ibox-content">
                   
                    @include('mantenimiento.sedes.forms.form_create_sede')

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
        cargarDataTable();
        
    })

    function events(){
       
        document.querySelector('#formStoreSede').addEventListener('submit',(e)=>{
            e.preventDefault();
            registrarSede(e.target);
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

    async function registrarSede(formStoreSede){

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea registrar la sede?",
        text: "Se afiliará a la empresa!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí registrar!",
        cancelButtonText: "No!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
           
            limpiarErroresValidacion('msgError');

            Swal.fire({
                title: "Registrando...",
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
                
                const formData  =   new FormData(formStoreSede);

                const res   =   await axios.post(route('mantenimiento.sedes.store'),formData);

                if(res.data.success){
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    window.location.href = "{{ route('mantenimiento.sedes.index') }}";
                }else{
                    Swal.close();
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                
                Swal.close();

                if(error.response.status === 422){
                    toastr.error('VALIDACIÓN CON ERRORES!!!','ERROR EN EL SERVIDOR');
                    const lstErrors    =   error.response.data.errors;
                    pintarErroresValidacion(lstErrors,'error')
                    return;
                }

                toastr.error(error,'ERROR EN LA PETICIÓN REGISTRAR SEDE!!!');
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


    function cargarDataTable(){
        const getResumenesUrl = "{{ route('ventas.resumenes.getResumenes') }}";

        tableResumenes = new DataTable('#table-resumenes',
        {
            serverSide: true,
            ajax: {
                url: getResumenesUrl,
                type: 'GET' 
            },
            columns: [
                { data: 'id'},
                { data: 'created_at' },
                { data: 'fecha_comprobantes' },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        return data.serie + '-' + data.correlativo;
                    }
                },
                { 
                    data: null, 
                    render: function(data, type, row) {

                        //======= ENVIADO A SUNAT ======
                        if(data.send_sunat == 1){
                            //===== ACEPTADO POR SUNAT =====
                            if(data.code_estado == '0'){
                                return `<span class="badge badge-success">ACEPTADO</span>`;
                            }
                            //====== RESPUESTA DE SUNAT "ERRORES EN EL ARCHIVO" ====
                            if(data.code_estado == 99){
                                return `<span class="badge badge-danger">ENVIADO CON ERRORES</span>`;
                            }
                            //===== EN PROCESO =====
                            if(data.code_estado == 98){
                                return `<span class="badge badge-warning">EN PROCESO</span>`;
                            }
                            if(!data.code_estado){
                                return `<span class="badge badge-primary">ENVIADO</span>`;
                            }
                        }
                      
                        //====== AÚN NO ENVIADO A SUNAT =====
                        if(data.send_sunat == 0){
                            //======== ERRORES HTTP,ETC ======
                            if(!data.ticket){
                                return `<span class="badge badge-danger">ERROR AL ENVIAR</span>`;
                            }
                           
                        }

                    }
                },
                { data: 'ticket', title: 'ticket' },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        var html = `<td style="white-space: nowrap;"><div style="display: flex; justify-content: center;">`;
                        
                        if (data.ruta_xml) {
                            let urlGetXml       =   "{{ route('ventas.resumenes.getXml', ['resumen_id' => ':resumen_id']) }}";
                            urlGetXml           =   urlGetXml.replace(':resumen_id', data.id);

                            html += `<form action="${urlGetXml}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">XML</button>`;
                            html += `</form>`;
                        }
                                                
                        if (data.ruta_cdr) {
                            let urlGetCdr     = "{{ route('ventas.resumenes.getCdr', ['resumen_id' => ':resumen_id']) }}";
                            let url_getCdr    = urlGetCdr.replace(':resumen_id', data.id);


                            html += `<form style="margin-left:3px;" action="${url_getCdr}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">CDR</button>`;
                            html += `</form>`;
                        }
                                                
                        html += `</div></td>`;
                        
                        return html;
                    }
                },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        var html = '<td><div class="btn-group">';
                        
                            if (data.send_sunat == 1) {
                                if (data.code_estado == 98 || (data.ticket && !data.code_estado)) {
                                    html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-consultar-resumen">CONSULTAR</button>`;
                                }
                            }

                            if (data.send_sunat == 0 && !data.ticket) {
                                html += `<button type="button" data-resumen-id="${data.id}" class="btn btn-primary btn-reenviar-resumen">REENVIAR</button>`;
                            }

                            html += `<i class="fas fa-eye btn btn-success d-flex align-items-center btn-detalle-resumen" data-resumen-id="${data.id}"></i>`; 
                       
                        html += '</div></td>';
                        
                        return html;
                    }
                } 
            ],
            language: {
                processing:     "Cargando resúmenes",
                search:         "BUSCAR: ",
                lengthMenu:    "MOSTRAR _MENU_ RESÚMENES",
                info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ RESÚMENES",
                infoEmpty:      "MOSTRANDO 0 RESÚMENES",
                infoFiltered:   "(FILTRADO de _MAX_ RESÚMENES)",
                infoPostFix:    "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable:     "NO HAY RESÚMENES DISPONIBLES",
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
            },
            "order": [[ 0, "desc" ]]
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

            $('#province').empty().trigger('change');

            lstProvincesFiltered.forEach((province)=>{
                $('#province').append(new Option(province.nombre, province.id, false, false));
            })

            $('#province').select2({
                placeholder: 'Seleccione una provincia',
                width: '100%'
            });

            $('#province').trigger('change');
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

            $('#district').empty().trigger('change');

            lstDistrictsFiltered.forEach((district)=>{
                $('#district').append(new Option(district.nombre, district.id, false, false));
            })

            $('#district').select2({
                placeholder: 'Seleccione un distrito',
                width: '100%'
            });
        }

    }
   
</script>
@endpush
