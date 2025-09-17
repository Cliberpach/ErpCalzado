@extends('layout')
@section('content')

    @include('mantenimiento.tipo_pago.modals.mdl_tipo_pago_create')
    @include('mantenimiento.tipo_pago.modals.mdl_tipo_pago_edit')

@section('mantenimiento-active', 'active')
@section('tipo_pago-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Tipos de Pago</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Tipos de Pago</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-success m-t-md" href="javascript:void(0);" onclick="openMdlNuevoMetodoPago()">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="col-12">
                        <div class="table-responsive">
                            @include('mantenimiento.tipo_pago.tables.tbl_list_metodos_pago')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
<script>
    let dtMetodosPago = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDataTableMetodosPago();
        iniciarSelect2();
        events();
    })

    function events() {
        eventsMdlCreateMetodoPago();
        eventsMdlEditMetodoPago();
    }

    function iniciarSelect2() {
        $('.select2_mdl').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: true,
        });

    }

    function iniciarDataTableMetodosPago() {
        const urlGetCargos = '{{ route('mantenimiento.tipo_pago.getTiposPago') }}';

        dtMetodosPago = new DataTable('#tbl_list_metodos_pago', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetCargos,
                type: 'GET',
            },
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'id',
                    name: 'tp.id'
                },
                {
                    data: 'nombre',
                    name: 'tp.descripcion'
                },
                {
                    data: 'fecha_registro',
                    name: 'tp.created_at'
                },
                {
                    data: 'fecha_modificacion',
                    name: 'tp.updated_at'
                },
                {
                    data: null,
                    render: function(data, type, row) {

                        const rutaCuentas =
                            `{{ route('mantenimiento.tipo_pago.asignarCuentasCreate', ['id' => ':id']) }}`;
                        const urlFinal = rutaCuentas.replace(':id', row.id);

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-th"></i>
                                </button>
                                <div class="dropdown-menu">
                                  
                                    <a class="dropdown-item" href="#" data-id="${row.id}" data-action="editar" onclick="openMdlEditMetodoPago(${data.id})">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="dropdown-item text-danger" onclick="eliminarMetodoPago(${data.id})" data-id="${row.id}" data-action="eliminar">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                    <a class="dropdown-item text-success" href="${urlFinal}" data-id="${row.id}" data-action="Asignar cuentas">
                                        <i class="fas fa-piggy-bank"></i> Cuentas
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

    function eliminarMetodoPago(id) {
        toastr.clear();
        let row = getRowById(dtMetodosPago, id);
        let message = '';
        let tipo_documento = '';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: `DESEA ELIMINAR EL TIPO DE PAGO?`,
            text: `${row.nombre}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Eliminando método de pago...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let urlDeleteMetodoPago =
                        `{{ route('mantenimiento.tipo_pago.destroy', ['id' => ':id']) }}`;
                    urlDeleteMetodoPago = urlDeleteMetodoPago.replace(':id', id);
                    const token = document.querySelector('input[name="_token"]').value;

                    const response = await fetch(urlDeleteMetodoPago, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    });

                    const res = await response.json();

                    if (res.success) {
                        dtMetodosPago.draw();
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR MÉTODO PAGO');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR MÉTODO PAGO');
                } finally {
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
