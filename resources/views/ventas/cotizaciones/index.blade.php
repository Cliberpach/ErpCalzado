@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Listado de Cotizaciones</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cotizaciones</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button onclick="crearCotizacion()" id="btn_añadir_cotizacion" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> NUEVO
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('ventas.cotizaciones.tables.tbl_list_cotizacion')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop



@if(Session::has('error'))
    <script>    
        toastr.error('{{ Session::get('error') }}', 'Error');
    </script>
@endif
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>

    let dtCotizaciones =    null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableCotizaciones();
    })

    function iniciarDataTableCotizaciones(){
        const urlGetCotizaciones = '{{ route('ventas.cotizacion.getCotizaciones') }}';

        dtColaboradores  =   new DataTable('#tbl_list_cotizaciones',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetCotizaciones,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' ,visible:false},
                { data: 'simbolo', name: 'simbolo' },
                { data: 'documento', name: 'documento' },
                { data: 'pedido_id', name: 'pedido_id' },
                { data: 'almacen_nombre', name: 'almacen_nombre' },
                { data: 'cliente', name: 'cliente' },
                { data: 'registrador_nombre', name: 'registrador_nombre' },
                { data: 'created_at', name: 'created_at' },
                { data: 'total_pagar', name: 'total_pagar' },
                { data: 'estado', name: 'estado' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const baseUrlEdit   =   `{{ route('mantenimiento.colaborador.edit', ['id' => ':id']) }}`;
                        urlEdit             =   baseUrlEdit.replace(':id', data.id); 

                        const urlDelete = `{{ route('mantenimiento.colaborador.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-th"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" style="max-height: 150px; overflow-y: auto;">
                                            <a class="dropdown-item" href="${urlEdit}">
                                                <i class="fas fa-user-edit"></i> Editar
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarColaborador(${data.id})">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </a>
                                        </div>
                                    </div>
                                `;

                    },
                    name: 'actions', 
                    orderable: false, 
                    searchable: false 
                }
            ],
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function crearCotizacion() {
        window.location = "{{ route('ventas.cotizacion.create') }}";
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
                var url_eliminar = '{{ route('ventas.cotizacion.destroy', ':id') }}';
                url_eliminar = url_eliminar.replace(':id', id);
                $(location).attr('href', url_eliminar);

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

    function documento(id) {
        Swal.fire({
            title: 'Opción Documento de venta',
            text: "¿Seguro que desea crear un documento de venta?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                
                let url_concretar   = '{{ route('ventas.cotizacion.documento', ':id') }}';
                url_concretar       = url_concretar.replace(':id', id);
                $(location).attr('href', url_concretar);

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


    //======== CONVERTIR COTIZACIÓN A PEDIDO =======
    function pedido(cotizacion_id){
        Swal.fire({
            title: `Convertir Cotización N° ${cotizacion_id} a Pedido`,
            text: ``,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then(async (result) => {
            if (result.isConfirmed) {
                
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor, espere.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const res   =   await axios.post(route('ventas.cotizacion.pedido'),
                                        {cotizacion_id}
                                    );

                    if(res.data.success){
                        $('.dataTables-cotizacion').DataTable().ajax.reload();
                        toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    }
                } catch (error) {
                    toastr.error(res.data.message,'ERROR AL GENERAR EL PEDIDO');
                }finally{
                    Swal.close();
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



    @if (!empty($id))
        Swal.fire({
        title: 'Documento de Venta duplicado',
        text: "¿Desea anular el documento y crear uno nuevo?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        }).then((result) => {
        if (result.isConfirmed) {
        //Ruta Nuevo Documento
        var url_nuevo = '{{ route('ventas.cotizacion.nuevodocumento', ':id') }}';
        url_nuevo = url_nuevo.replace(':id', "{{ $id }}");
        $(location).attr('href', url_nuevo);


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
    @endif
</script>

