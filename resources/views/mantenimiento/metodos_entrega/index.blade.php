@extends('layout') 

@section('content')
    @include('mantenimiento.metodos_entrega.modal_create')
    @include('mantenimiento.metodos_entrega.modal_edit')
    @include('mantenimiento.metodos_entrega.modal_sedes')

@section('mantenimiento-active', 'active')
@section('metodo_entrega-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Mantenimiento de Métodos entrega</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Vendedores</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a data-toggle="modal" data-target="#modal_create_metodo_entrega" class="btn btn-block btn-w-m btn-primary m-t-md" href="#" >
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
                        <table class="table dataTables-metodos_entrega table-striped table-bordered table-hover"  style="text-transform:uppercase">
                            <thead>
                            <tr>
                                <th class="text-center">EMPRESA</th>
                                <th class="text-center">TIPO_ENVIO</th>
                                <th class="text-center">FECHA</th>
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

@stop

@push('styles')
    <!-- DataTable -->
    <link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <style>
        .search-length-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.buttons-container{
    display: flex;
    justify-content:end;
}
    </style>
@endpush

@push('scripts')

    <!-- DataTable -->
    <script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

    <script>
        let sedes_data_table    =   null;

        document.addEventListener('DOMContentLoaded',()=>{

            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            sedes_data_table    =   dataTableSedes();

            // DataTables
            $('.dataTables-metodos_entrega').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'Tablas Generales'
                    },
                    {
                        titleAttr: 'Imprimir',
                        extend: 'print',
                        text:      '<i class="fa fa-print"></i> Imprimir',
                        customize: function (win){
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
                "serverSide":true,
                "processing":true,
                "ajax": "{{ route('mantenimiento.metodo_entrega.getTable')}}",
                "columns": [
                    {data: 'empresa', className:"text-center"},
                    {data: 'tipo_envio', className:"text-center"},
                    {data: 'fecha', className:"text-center"},
                    {
                        data: null,
                        className:"text-center",
                        render: function(data) {
                           
                            var url_detalle = '{{ route("mantenimiento.vendedor.show", ":id")}}';
                            url_detalle = url_detalle.replace(':id',data.id);

                           
              

                            var accionesHtml = "<div class='btn-group'>" +
                        "<button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Acciones</button>" +
                        "<div class='dropdown-menu'>" +
                            "<a class='dropdown-item' href='"+url_detalle+"' title='Detalle'><i class='fa fa-eye'></i> Detalle</a>" +
                            "<div class='dropdown-divider'></div>" + 
                            "<a class='dropdown-item modificarDetalle' onclick='editarMetodoEntrega("+data.id+")' href='#' title='Modificar'><i class='fa fa-edit'></i> Modificar</a>" +
                            "<div class='dropdown-divider'></div>"; 

                            if (data.tipo_envio === "AGENCIA") {
                                accionesHtml += "<a class='dropdown-item' href='#' onclick='sedes("+data.id+")' title='Sedes'><i class='fa fa-building'></i> Sedes</a>" +
                                                "<div class='dropdown-divider'></div>"; 
                            }

                            accionesHtml += "<a class='dropdown-item' href='#' onclick='eliminar("+data.id+")' title='Eliminar'><i class='fa fa-trash'></i> Eliminar</a>" +
                                            "</div>" +
                                            "</div>";

                            return accionesHtml;
                        }
                    }

                ],
                "language": {
                    "url": "{{asset('Spanish.json')}}"
                },
                "order": [[ 0, "desc" ]],
            });

            eventsSedes();
            eventsCreate();
            eventsUpdate();
        })
      

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

        //Modal Eliminar
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        // Funciones de Eventos
        function añadirVendedor() {
            window.location = "{{ route('mantenimiento.metodo_entrega.create')  }}";
        }

        function editarEmpleado(url) {
            window.location = url;
        }

        async function sedes(id){
            //===== OBTENEMOS LAS SEDES =====
            try {
                const res   =   await axios.get(route('mantenimiento.metodo_entrega.getSedes',id));
                console.log(res);
                if(res.data.success){
                    pintarSedes(res.data.sedes);
                    document.querySelector('#agencia_id').value =   id;
                    $('#modal_sedes').modal('show');
                }else{
                    toastr.error(`${res.data.message} - ${res.data.exception}`,'ERROR AL OBTENER LAS SEDES');
                }
            } catch (error) {
                
            }
        }

        async function editarMetodoEntrega(id){
            try {
                const res   =   await axios.get(route('mantenimiento.metodo_entrega.getMetodoEntrega',id));
                console.log(res);
                if(res.data.success){
                    document.querySelector('#empresa_envio_id').value   =   id;
                    setFormEdit(res.data.metodo_entrega);
                    $('#modal_edit_metodo_entrega').modal('show');
                }
            } catch (error) {
                
            }
        }

        function setFormEdit(metodo_entrega){
            const empresa       =   metodo_entrega.empresa;   
            const tipo_envio    =   metodo_entrega.tipo_envio;
            document.querySelector('#empresa_edit').value    =   empresa;

            var opcionSeleccionada = $('#tipo_envio').find('option').filter(function() {
                return $(this).text() == tipo_envio;
            });

            if (opcionSeleccionada.length > 0) {
                var valorSeleccionado = opcionSeleccionada.val();
                        
                $('#tipo_envio_edit').val(valorSeleccionado).trigger('change');
            } else {
                console.error("No se encontró ninguna opción con el texto:", textoSeleccionado);
            }
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
                    //Ruta Eliminar
                    var url_eliminar = '{{ route("mantenimiento.metodo_entrega.destroy", ":id")}}';
                    url_eliminar = url_eliminar.replace(':id',id);
                    $(location).attr('href',url_eliminar);

                }else if (
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

    </script>
@endpush
