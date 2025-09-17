@extends('layout')
@section('content')

    @include('mantenimiento.cuentas.modals.mdl_cuenta_create')
    @include('mantenimiento.cuentas.modals.mdl_cuenta_edit')

@section('mantenimiento-active', 'active')
@section('cuentas_bancarias-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Cuentas</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Cuentas</strong>
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
                            @include('mantenimiento.cuentas.tables.tbl_cuentas')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    let dtCuentas = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDtCuentas();
        iniciarSelect2();
        events();
    })

    function events() {
        eventsMdlCreateCuenta();
        eventsMdlEditCuenta();
    }

    function iniciarSelect2() {
        $('.select2_mdl_cuenta_edit').select2({
            width: '100%',
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            dropdownParent: $('#mdlEditCuenta')
        });

    }

    function iniciarDtCuentas() {
        let urlGet = '{{ route('mantenimiento.cuentas.getCuentas') }}';

        dtCuentas = new DataTable('#tbl_cuentas', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGet,
                type: 'GET',
            },
            order: [
                [0, 'desc']
            ],
            columns: [
                {
                    data: 'id',
                    name: 'c.id'
                },
                {
                    data: 'banco_nombre',
                    name: 'c.banco_nombre'
                },
                {
                    data: 'banco_id',
                    name: 'c.banco_id',
                    visible: false
                },
                {
                    data: 'titular',
                    name: 'c.titular'
                },
                {
                    data: 'moneda',
                    name: 'c.moneda'
                },
                {
                    data: 'nro_cuenta',
                    name: 'c.nro_cuenta'
                },
                {
                    data: 'cci',
                    name: 'c.cci'
                },
                {
                    data: 'celular',
                    name: 'c.celular'
                },
                {
                    data: null,
                    render: function(data, type, row) {

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-th"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" data-id="${row.id}" data-action="ver">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a class="dropdown-item" href="#" data-id="${row.id}" data-action="editar" onclick="openMdlEditCuenta(${data.id})">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="dropdown-item text-danger" onclick="eliminarCuenta(${data.id})" data-id="${row.id}" data-action="eliminar">
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

    function eliminarCuenta(id) {
        toastr.clear();
        let row = getRowById(dtCuentas, id);
        let message = '';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: `DESEA ELIMINAR LA CUENTA BANCARIA?`,
            text: `${row.nro_cuenta}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Eliminando cuenta bancaria...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let urlDelete =
                        `{{ route('mantenimiento.cuentas.destroy', ['id' => ':id']) }}`;
                    urlDelete = urlDelete.replace(':id', id);
                    const token = document.querySelector('input[name="_token"]').value;

                    const response = await fetch(urlDelete, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    });

                    const res = await response.json();

                    if (res.success) {
                        dtCuentas.draw();
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR AL ELIMINAR CUENTA BANCARIA');
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ELIMINAR CUENTA BANCARIA');
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
