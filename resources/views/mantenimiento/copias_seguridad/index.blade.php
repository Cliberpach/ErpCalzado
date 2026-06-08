@extends('layout')

@section('mantenimiento-active', 'active')
@section('copia_seguridad-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Copias Seguridad')
@section('hero-title', 'Copias Seguridad')
@section('hero-subtitle', 'Copias Seguridad')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="generarCopia()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="col-12">
                            <div class="table-responsive">
                                @include('mantenimiento.copias_seguridad.tables.tbl_list_copias')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    let dtCopias        = null;
    let pollingInterval = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDataTableCopias();
    });

    function iniciarPolling() {
        if (pollingInterval) return;
        pollingInterval = setInterval(() => dtCopias.draw(false), 4000);
    }

    function detenerPolling() {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }

    function iniciarDataTableCopias() {
        const urlGetBackups = '{{ route('mantenimiento.copias_seguridad.getBackups') }}';

        dtCopias = new DataTable('#tbl_list_copias', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetBackups,
                type: 'GET',
            },
            order: [
                [0, 'desc']
            ],
            columns: [ {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'usuario',
                    name: 'usuario',
                    orderable: false
                },
                {
                    data: 'tamano',
                    name: 'tamano',
                    orderable: false
                },
                {
                    data: 'estado_badge',
                    name: 'estado',
                    orderable: false
                },
                {
                    data: 'fecha',
                    name: 'created_at'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const urlDescarga =
                            `{{ route('mantenimiento.copias_seguridad.download', ['id' => ':id']) }}`
                            .replace(':id', row.id);
                        const esDescargable = row.estado === 'COMPLETADO';

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-th"></i>
                                </button>
                                <div class="dropdown-menu">
                                    ${esDescargable ? `
                                    <a class="dropdown-item" href="${urlDescarga}">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>` : ''}
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminarCopia(${row.id}, '${row.nombre ?? ''}')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            drawCallback: function() {
                const hayGenerando = document.querySelectorAll('#tbl_list_copias .badge-warning').length > 0;
                hayGenerando ? iniciarPolling() : detenerPolling();
            },
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No hay copias de seguridad generadas",
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
                "emptyTable": "No hay copias de seguridad generadas"
            }
        });
    }

    function generarCopia() {
        const swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swal.fire({
            title: '¿Generar nueva copia de seguridad?',
            text: 'Se generará un respaldo completo de la base de datos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Generando...',
                html: 'Por favor espere mientras se crea la copia de seguridad.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('{{ route('mantenimiento.copias_seguridad.generate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                const res = await response.json();
                Swal.close();

                if (res.success) {
                    dtCopias.draw(false);
                    toastr.info(res.message, 'BACKUP EN COLA');
                    iniciarPolling();
                } else {
                    toastr.error(res.message, 'ERROR AL GENERAR COPIA');
                }
            } catch (error) {
                Swal.close();
                toastr.error(error.toString(), 'ERROR EN LA PETICIÓN');
            }
        });
    }

    function eliminarCopia(id, nombre) {
        const swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swal.fire({
            title: '¿Eliminar copia de seguridad?',
            text: nombre || 'ID: ' + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const url = `{{ route('mantenimiento.copias_seguridad.destroy', ['id' => ':id']) }}`
                    .replace(':id', id);

                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                const res = await response.json();
                Swal.close();

                if (res.success) {
                    dtCopias.draw();
                    toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                } else {
                    toastr.error(res.message, 'ERROR AL ELIMINAR');
                }
            } catch (error) {
                Swal.close();
                toastr.error(error.toString(), 'ERROR EN LA PETICIÓN');
            }
        });
    }
</script>
