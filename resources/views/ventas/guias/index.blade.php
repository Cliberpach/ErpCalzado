@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Guias de Remision</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Guias de Remision</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{route('ventas.guiasremision.create')}}">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">

                        @include('ventas.guias.tables.tbl_guia_list')
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/dist/toastr.min.css">

@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>


<script>
$(document).ready(function() {

    showAlertas();

    // DataTables
    $('.dataTables-gui').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'Tablas Generales'
            },
            {
                titleAttr: 'Imprimir',
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimir',
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "processing": true,
        "ajax": "{{ route('ventas.getGuia')}}",
        "columns": [
            //Guias de remision
            {
                data: 'documento_afectado',
                className: "text-center"
            },
            {
                data: 'documento_fecha',
                className: "text-center"
            },
            {
                data: 'serie_guia',
                className: "text-center"
            },
            {
                data: 'cantidad',
                className: "text-center"
            },
            {
                data: 'peso',
                className: "text-center"
            },
            {
                data: 'ticket',
                className: "text-center"
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {
                    //====== ENVIADA A SUNAT ======
                    if(data.sunat == '1'){
                        //====== SI TIENE CDR ====
                        if(data.cdr_response_code){
                            if(data.cdr_response_code == 0){
                                return "<span class='badge badge-warning' d-block>ACEPTADO</span>";
                            }
                            if(data.cdr_response_code == 99){
                                return "<span class='badge badge-warning' d-block>ACEPTADO CON ERRORES</span>";
                            }
                            if(data.cdr_response_code == 98){
                                return "<span class='badge badge-warning' d-block>EN PROCESO</span>";
                            }
                        }else{
                        //=====  NO TIENE CDR =======
                            if(data.regularize == '1'){
                                return "<span class='badge badge-warning' d-block>ERROR EN EL ENVÍO</span>";
                            }
                            if(data.regularize == '0'){
                                return "<span class='badge badge-success' d-block>ENVIADO</span>";
                            }
                        }
                        
                    }

                    //======= NO ENVIADO ======
                    if(data.sunat == '0'){
                        if(data.regularize == '1'){
                            return "<span class='badge badge-danger' d-block>ERROR EN EL ENVÍO</span>"; 
                        }
                        if(data.regularize == '0'){
                            return "<span class='badge badge-success' d-block>REGISTRADO</span>";
                        }
                    }

                },
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {
                    var html = `<td style="white-space: nowrap;"><div style="display: flex; justify-content: center;">`;
                        
                        if (data.ruta_xml) {
                            let urlGetXml       =   "{{ route('ventas.guiasremision.getXml', ['guia_id' => ':guia_id']) }}";
                            urlGetXml           =   urlGetXml.replace(':guia_id', data.id);

                            html += `<form action="${urlGetXml}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">XML</button>`;
                            html += `</form>`;
                        }
                                                
                        if (data.ruta_cdr) {
                            let urlGetCdr     = "{{ route('ventas.guiasremision.getCdr', ['guia_id' => ':guia_id']) }}";
                            let url_getCdr    = urlGetCdr.replace(':guia_id', data.id);


                            html += `<form style="margin-left:3px;" action="${url_getCdr}" method="get">`;
                            html += `<button type="submit" class="btn btn-primary btn-xml">CDR</button>`;
                            html += `</form>`;
                        }
                                                
                        html += `</div></td>`;
                        
                        return html;

                },
            },

            {
                data: null,
                className: "text-center",
                render: function(data) {
                    
                    const route_delete_guia =   `{{route('ventas.guiasremision.delete')}}`;

                    let acciones    =   `<div class='btn-group' style='text-transform:capitalize;'>
                                            <button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'>
                                                <i class='fa fa-bars'></i>
                                            </button>
                                            <ul class='dropdown-menu'>
                                                <li>
                                                    <a class='dropdown-item' onclick='detalle(${data.id})' title='PDF'>
                                                        <b><i class='fa fa-eye'></i> PDF </b>
                                                    </a>
                                                </li>
                                                <li class='dropdown-divider'></li>`;  
                    
                    if(data.sunat != '1'){
                        acciones += `<li>
                                        <a class='dropdown-item' onclick='enviarSunat(${data.id})'  title='Enviar Sunat'>
                                            <b><i class='fa fa-file'></i> Enviar Sunat </b>
                                        </a>
                                    </li>`;
                    }

                    if (data.ticket && data.cdr_response_code != '0') {
                        acciones    +=  `<li>
                                            <a class='dropdown-item' onclick='consultarSunat(${data.id})'  title='Consultar Sunat'>
                                                <b><i class='fa fa-file'></i> Consultar Sunat </b>
                                            </a>
                                        </li>`;
                    }
                   

                    /*if(data.sunat != '1' && data.regularize != '1'){
                        acciones += `<li>
                                <form hidden method="POST" id="frm-delete-guia" action="${route_delete_guia}">
                                    @csrf
                                    <input value="${data.id}" name="guia_id"></input>
                                </form>
                                <a class='dropdown-item' onclick='eliminarGuia(" +data.id+ ")' href='javascript:void(0);'  title='Eliminar'>
                                    <b><i class='fa fa-trash'></i> Eliminar </b>
                                </a>
                            </li>`;
                    }*/

                    return acciones;
                            
                }
            }

        ],
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
    });

    tablaDatos = $('.dataTables-enviados').DataTable();

});

//Controlar Error
$.fn.DataTable.ext.errMode = 'throw';

const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})


function eliminarGuia(id) {

    Swal.fire({
        title: 'Opción Eliminar Guía de Remisión',
        text: "¿Seguro que desea guardar cambios?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            const formDeleteGuia    =   document.querySelector('#frm-delete-guia');
            formDeleteGuia.submit();
            
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


function detalle(id) {
    var url = '{{ route("ventas.guiasremision.show", ":id")}}';
    url = url.replace(':id',id);

    window.open(url, "Comprobante SISCOM", "width=900, height=600")
}


async function consultarSunat(id) {

    try {
        mostrarAnimacion();
        const res   =   await axios.post(route('ventas.guiasremision.consultar'),{id});

        if(res.data.success){
            toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
        }else{
            toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
        }

    } catch (error) {
        toastr.error(error,'ERROR EN LA PETICIÓN CONSULTAR GUÍA EN SUNAT');
    }finally{
        $('.dataTables-gui').DataTable().ajax.reload(null, false);
        ocultarAnimacion();
    }
}

function enviarSunat(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Enviar a Sunat",
        text: "¿Seguro que desea enviar documento de venta a Sunat?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then(async (result) => {
        if (result.value) {

          
            Swal.fire({
                title: '¡Cargando!',
                icon: 'info', 
                text: 'Enviando Guía de Remisión a Sunat',
                showConfirmButton: false,
                allowOutsideClick: false, 
                didOpen: () => { 
                    Swal.showLoading();
                }
            });


            try {
                const res   =   await axios.post(route("ventas.guiasremision.sunat"),{guia_id:id});

                if(res.data.success){
                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ENVIAR GUÍA A SUNAT');
            }finally{
                Swal.close();
                $('.dataTables-gui').DataTable().ajax.reload(null, false);
            }

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

function showAlertas(){
    const messageFlashError  = @json(session('error_guia_remision'));
    if(messageFlashError){
        toastr.error(messageFlashError,'OPERACIÓN INCORRECTA',{timeOut:5000});
    }

    const messageFlashSuccess   =   @json(session('guia_exito'));
    console.log(messageFlashSuccess);
    if(messageFlashSuccess){
        toastr.success(messageFlashSuccess, "OPERACIÓN COMPLETADA", { timeOut: 0 });
    }
}


</script>
@endpush
