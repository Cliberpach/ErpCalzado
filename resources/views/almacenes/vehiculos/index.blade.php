@extends('layout') 
@section('content')
@section('almacenes-active', 'active')
@section('vehiculos-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>LISTA DE VEHÍCULOS</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>VEHÍCULOS</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button onclick="crearVehiculo()" id="btn_añadir_producto" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Vehículos</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" id="div_productos">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('almacenes.vehiculos.tables.tbl_list_vehiculos')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<style>


</style>
@endpush

@push('scripts')
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    let dtVehiculos    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableVehiculos();
    })

    function iniciarDataTableVehiculos(){
        const urlGetVehiculos = '{{ route('almacenes.vehiculos.getVehiculos') }}';

        dtVehiculos  =   new DataTable('#table_list_vehiculos',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetVehiculos,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'placa', name: 'placa' },
                { data: 'modelo', name: 'tipo_gamodelosto_nombre' },
                { data: 'marca', name: 'marca' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_modificacion', name: 'fecha_modificacion' },
                {
                    data: null, 
                    render: function(data, type, row) {
                        const urlEdit   =   `{{ route('almacenes.vehiculos.edit', ['id' => ':id']) }}`.replace(':id', data.id);

                        const urlDelete = `{{ route('almacenes.vehiculos.destroy', ':id') }}`.replace(':id', data.id);

                        return `
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" style="max-height: 150px; overflow-y: auto;">
                                        <a class="dropdown-item" href="${urlEdit}">
                                            <i class="fa fa-pen"></i> Editar
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarVehiculo(${data.id})">
                                            <i class="fa fa-trash"></i> Eliminar
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


    function eliminarVehiculo(id){
        toastr.clear();
        let row             =   getRowById(dtVehiculos,id);
        let message         =   '';

        message =   `Desea eliminar el vehiculo: ${row.placa}`;

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: message,
        text: "Operación no reversible!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'Cargando...',
                html: 'Eliminando vehículo...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });

            try {
                let urlDeleteVehiculo     =   `{{ route('almacenes.vehiculos.destroy', ['id' => ':id']) }}`;
                urlDeleteVehiculo         =   urlDeleteVehiculo.replace(':id', id);
                const token               =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteVehiculo, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtVehiculos.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR VEHÍCULO');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR VEHÍCULO');
            }finally{
                Swal.close();
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
</script>

@endpush
