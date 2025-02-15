@extends('layout')

@section('content')

@section('mantenimiento-active', 'active')
@section('colaboradores-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    @csrf
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>LISTA DE COLABORADORES</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Colaboradores</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <button id="btn_añadir_empleado" onclick="goToCrearColaborador()" class="btn btn-block btn-w-m btn-primary m-t-md">
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
                        @include('mantenimiento.colaboradores.tables.tbl_list_colaboradores')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


<script>
    let dtColaboradores    =   null;

    document.addEventListener('DOMContentLoaded',()=>{
        iniciarDataTableColaboradores();
    })

    function iniciarDataTableColaboradores(){
        const urlGetColaboradores = '{{ route('mantenimiento.colaborador.getColaboradores') }}';

        dtColaboradores  =   new DataTable('#tbl_list_colaboradores',{
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetColaboradores,
                type: 'GET',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'sede_nombre', name: 'sede_nombre' },
                { data: 'nombre', name: 'nombre' },
                { data: 'cargo_nombre', name: 'cargo_nombre' },
                { data: 'direccion', name: 'direccion' },
                { data: 'telefono', name: 'telefono' },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'dias_trabajo', name: 'dias_trabajo' },
                { data: 'dias_descanso', name: 'dias_descanso' },
                { data: 'pago_mensual', name: 'pago_mensual' },
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

    function goToCrearColaborador(){
        window.location.href = @json(route('mantenimiento.colaborador.create'));
    }


    function eliminarColaborador(id){
        toastr.clear();
        let row             =   getRowById(dtColaboradores,id);
        let message         =   '';
        let tipo_documento  =   '';

        tipo_documento  =   row.tipo_documento_nombre;
        
        message =   `Desea eliminar el colaborador: ${row.nombre}, ${tipo_documento}:${row.nro_documento}`;

        
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
                html: 'Eliminando colaborador...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); 
                }
            });
           

            try {
                let urlDeleteColaborador    =   `{{ route('mantenimiento.colaborador.destroy', ['id' => ':id']) }}`;
                urlDeleteColaborador        =   urlDeleteColaborador.replace(':id', id);
                const token                 =   document.querySelector('input[name="_token"]').value;

                const response  =   await fetch(urlDeleteColaborador, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': token 
                                        }
                                    });

                const   res =   await response.json();

                if(res.success){
                    dtColaboradores.draw();
                    toastr.success(res.message,'OPERACIÓN COMPLETADA');
                }else{
                    toastr.error(res.message,'ERROR EN EL SERVIDOR AL ELIMINAR COLABORADOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN ELIMINAR COLABORADOR');
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
