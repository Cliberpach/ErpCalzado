@extends('layout') @section('content')

@section('recibos_caja-active', 'active')
@section('caja-chica-active', 'active')
@include('recibos_caja.modalImpreso')
@include('recibos_caja.modal_detalles')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Lista de Recibos Caja</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Egreso</strong>
            </li>

        </ol>
      
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_recibo" class="btn btn-block btn-w-m btn-primary m-t-md">
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
                        <table class="table dataTables-cajas table-striped table-bordered table-hover"
                            style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">USUARIO</th>
                                    <th class="text-center">MET. PAGO</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">SALDO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">ESTADO SERV</th>
                                    <th class="text-center">OBS</th>
                                    <th class="text-center">ACCIONES</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('styles')
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/textSpinners/spinners.css') }}" rel="stylesheet">
<style>
    .my-swal {
        z-index: 3000 !important;
    }

</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>

@if(Session::has('error_validacion_caja'))
    <script>
        toastr.error("{{ Session::get('error_validacion_caja') }}");
    </script>
@endif
@if(Session::has('recibo_caja_success'))
    <script>
        toastr.success('{{ Session::get('recibo_caja_success') }}','OPERACIÓN COMPLETADA');
    </script>
@endif
@if(Session::has('recibo_caja_error'))
    <script>
        toastr.error('{{ Session::get('recibo_caja_error') }}','ERROR');
    </script>
@endif

<script>

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTable();
        loadDataTableDetallesRecibo();
        events();

    })

    function events(){
        document.querySelector('#btn_añadir_recibo').addEventListener('click',()=>{
            registrarRecibo();
        })
    }

    function iniciarDataTable(){
        $('.dataTables-cajas').DataTable({
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
            "serverSide": true,
            "ajax": '{{ route('recibos_caja.getRecibosCaja') }}',
            "columns": [
                //Caja chica
                {
                    data: 'fecha_recibo',
                    className: "text-center"
                },
                {
                    data: 'cliente_nombre',
                    className: "text-center"
                },
                {
                    data: 'usuario_nombre',
                    className: "text-center"
                },
                {
                    data: 'metodo_pago',
                    className: "text-center"
                },
                {
                    data: 'monto',
                    className: "text-center"
                },
                {
                    data: 'saldo',
                    className: "text-center"
                },
                {
                    data: 'estado',
                    className: "text-center"
                },
                {
                    data: 'estado_servicio',
                    className: "text-center"
                },
                {
                    data: 'observacion',
                    className: "text-center"
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                    
                        //Ruta Modificar
                        var url_edit = '{{ route('clientes.tienda.edit', ':id') }}';
                        url_edit = url_edit.replace(':id', data.id);


                        return `<div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-bars"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="imprimir(${data.id})">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="editar(${data.id})">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="eliminar(${data.id})">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="detalleReciboCaja(${data.id})">
                                                <i class="fas fa-info-circle"></i> Detalle
                                            </a>
                                        </div>
                                    </div>
                                    <form id="form-eliminar-${data.id}" action="{{ route('recibos_caja.destroy', ':id') }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </div>`;
                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [
                [0, "desc"]
            ]

        });
    }
   

    async function registrarRecibo() {
        const res_apert_caja    =  await buscarCajaApertUsuario();

        if(res_apert_caja){
            setTimeout(() => {
                window.location = "{{ route('recibos_caja.create') }}";     
            }, 1000);
        }
    }

    function imprimir(id) {

        $("#recibo_caja_id").val(id)
        $("#modal_imprimir").modal("show");
        //var url = "{{ route('Egreso.recibo', ':id') }}"
        // window.location.href= url.replace(":id", id)
    }

    function editar(id) {
        var url = "{{ route('recibos_caja.edit', ['recibo_caja_id' => ':id']) }}";
    
        url = url.replace(':id', id);
    
        window.location.href = url;
    }

    function eliminar(id) {
        Swal.fire({
            title: 'Opción Eliminar',
            text: "¿Seguro que desea guardar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.getElementById(`form-eliminar-${id}`);
                var url_eliminar = form.action.replace(':id', id);
                form.action = url_eliminar;
                form.submit();

            } else if (
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
   

    //========= BUSCAR CAJA APERTURADA DE USUARIO =========== 
    async function buscarCajaApertUsuario() {
        try {
            const res   = await axios.get(route('recibos_caja.buscarCajaApertUsuario'));
            console.log(res);
            let validacion    =   true;

            if(res.data.success){
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

    async function detalleReciboCaja(recibo_caja_id){
        try {
            const res    =   await axios.get(route('recibos_caja.detalles',recibo_caja_id));
            if(res.data.success){
                pintarDetallesRecibo(res.data.detalles);
                $("#modal_detalles_recibos_caja").modal("show");
                
                //======== MOSTRAR DATA =====
                console.log(res.data.detalles);
            }else{
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA SOLICITUD DETALLES RECIBO DE CAJA');
        }
    }
</script>
@endpush
