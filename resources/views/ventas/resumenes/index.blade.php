@extends('layout') @section('content')
@section('ventas-active', 'active')
@section('resumenes-active', 'active')

@include('ventas.resumenes.modal_add')

<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Resúmenes</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Resúmenes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button type="button" class="btn btn-block btn-w-m btn-primary m-t-md" onclick="openModalResumenes()">
            <i class="fa fa-plus-square"></i> Añadir nuevo        
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-gui table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                             
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">FEC.EMISIÓN</th>
                                    <th class="text-center">FEC. REFERENCIA</th>
                                    <th class="text-center">IDENTIFICADOR</th>

                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">TICKET</th>
                                    <th class="text-center">DESCARGAS</th>
                                    <th class="text-center">ACCIONES</th>
                                    
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
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
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
<script src="https://kit.fontawesome.com/f9bb7aa434.js" crossorigin="anonymous"></script>
<script>
    const btnGetComprobantes            =   document.querySelector('#btn-get-comprobantes');
    const bodyTableSearchComprobantes   =   document.querySelector('.table-search-comprobantes tbody');
    let listComprobantes = [];

    document.addEventListener('DOMContentLoaded',()=>{
        events();
        
    })

    function events(){
        //======= GUARDAR RESUMEN ======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-guardar-resumen')){

               const valido   = validaciones();
               if(valido){
                saveSendResumen();
               }
   
            }
        })

        //===== ELIMINAR COMPROBANTE DEL CARRITO ======
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('btn-delete-document')){
                const documento_id  =   e.target.getAttribute('data-documento-id');
                eliminarComprobante(documento_id);
                pintarTableComprobantes();
            }
        })

        //====== OBTENER COMPROBANTES BOLETAS ======
        btnGetComprobantes.addEventListener('click',()=>{
            //===== OBTENIENDO FECHA DEL INPUT DATE =====
            const fechaComprobantes  =   document.querySelector('#fecha_comprobante').value;

            if(fechaComprobantes){
                getComprobantes(fechaComprobantes);
            }else{
                toastr.error('DEBE SELECCIONAR UNA FECHA');
            }

        })
    }

    //========= GUARDAR RESUMEN Y ENVIAR A SUNAT A LA VEZ ==========
    async function saveSendResumen(){
        try {
            const url       =   `/ventas/resumenes/store`;
            const response  =   await axios.post(url,{
                'comprobantes':listComprobantes
            });

            console.log(response);
        
        } catch (error) {
            console.error('Error al enviar y guardar resumen:', error);
        }
    }

    //==== ELIMINAR COMPROBANTE DEL CARRITO ======
    function eliminarComprobante(documento_id){
        listComprobantes    =   listComprobantes.filter((c)=>{
            return  c.documento_id != documento_id; 
        })
    }

    //============= ABRIR MODAL CLIENTE =============
    async function openModalResumenes(){
        $("#modal_resumenes").modal("show");

        await  isActive();
    }

    //===== OBTENER COMPROBANTES =====
    async function getComprobantes(fechaComprobantes){
        try {
            const url       =   `/ventas/resumenes/getComprobantes/${fechaComprobantes}`;
            const response  =   await axios.get(url);
            console.log(response);
            listComprobantes    =   response.data.success;
            pintarTableComprobantes();
        } catch (error) {
            console.error('Error al obtener comprobantes: ', error);
        }
    }

    //====== VERIFICAR SI ESTAN ACTIVOS LOS RESÚMENES =======
    async function isActive(){
        try {
            const url       =   `/ventas/resumenes/getStatus`;
            const response  =   await axios.get(url);
            
            if(response.status  === 200){
                const resumenActive =   response.data.resumenActive;
                //====== VERIFICANDO SI LOS RESUMENES ESTÁN ACTIVOS =====
                if(!resumenActive){
                    toastr.error('DEBE ACTIVAR LOS RESÚMENES','RESUMEN INACTIVO');
                    return false;
                }
                if(resumenActive){
                    toastr.success('RESÚMENES REGISTRADOS EN LA EMPRESA','RESUMEN ACTIVO');
                    return true;
                }
            }else{
                toastr.error(response.statusText,'ERROR EN LA SOLICITUD');
            }

        } catch (error) {
            console.error('Error al verificar estado: ', error);
        }
    }

    //====== PINTAR TABLE COMPROBANTES =====
    function pintarTableComprobantes(){
        clearTableComprobantes();
     
        let tbody = `<tr>`;
        listComprobantes.forEach((c)=>{
            tbody   +=  `
                                <th scope="row">${c.documento_id}</th>
                                <td>${c.documento_serie}-${c.documento_correlativo}</td>
                                <td>${c.documento_moneda}</td>
                                <td>${c.documento_subtotal}</td>
                                <td>${c.documento_igv}</td>
                                <td>${c.documento_total}</td>
                                <td>
                                    <i class="btn btn-danger fas fa-trash-alt btn-delete-document" data-documento-id=${c.documento_id}></i>    
                                </td>
                            </tr>
                        `;
        })

        bodyTableSearchComprobantes.innerHTML   =   tbody;
    }

    //======== LIMPIAR TABLE COMPROBANTES =========
    function clearTableComprobantes(){
        while (bodyTableSearchComprobantes.firstChild) {
            bodyTableSearchComprobantes.removeChild(bodyTableSearchComprobantes.firstChild)
        }
    }

    function validaciones(){
        let validacion  =   true;
        //======= VALIDAR DETALLE DEL RESUMEN =======
        if(listComprobantes.length==0){
            toastr.error('NO HAY COMPROBANTES EN EL RESUMEN','ERROR');
            validacion = false;
        }

        if(!isActive()){
            validacion = false;
        }
        
        return validacion;
    }


</script>
@endpush
