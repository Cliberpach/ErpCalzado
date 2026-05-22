@extends('layout')

@section('caja-active', 'active')
@section('caja-chica-active', 'active')

@section('bread-module', 'Cajas')
@section('bread-submodule', 'Cajas')
@section('hero-title', 'Lista de Cajas')
@section('hero-subtitle', 'Cajas')

@section('btn-add')
    <a class="main-btn-add btn-modal" href="#">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('pos.Cajas.modalcreate')
    @include('pos.Cajas.modaledit')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table dataTables-cajas table-striped table-bordered table-hover"
                                style="text-transform:uppercase">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">CAJA</th>
                                        <th class="text-center">FECHA CREACIÓN</th>
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
@endsection

@push('styles')
    <style>
        .my-swal { z-index: 3000 !important; }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        window.dtCajas = new DataTable('.dataTables-cajas', {
            processing: true,
            serverSide: true,
            ajax: '{{ route('Caja.getCajas') }}',
            columns: [
                { data: 'id',         className: 'text-center' },
                { data: 'nombre',     className: 'text-center' },
                { data: 'created_at', className: 'text-center' },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function (data) {
                        return `<div class="btn-group">
                            <button class="btn btn-warning btn-sm"
                                onclick="abrirEditar(${data.id}, '${data.nombre}')"
                                title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm"
                                onclick="eliminar(${data.id})"
                                title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;
                    }
                }
            ],
            language: { url: '{{ asset('Spanish.json') }}' },
            order: [[0, 'desc']],
        });

        document.querySelector('.btn-modal').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('nombre_create').value = '';
            $('#modal_create_caja').modal('show');
        });
    });

    async function guardarCaja() {
        const nombre = document.getElementById('nombre_create').value.trim();
        if (!nombre) {
            toastr.warning('Ingrese el nombre de la caja.');
            return;
        }
        try {
            mostrarAnimacion();
            const res = await axios.post('{{ route('Caja.store') }}', { nombre });
            if (res.data.success) {
                toastr.success(res.data.message);
                $('#modal_create_caja').modal('hide');
                window.dtCajas.ajax.reload();
            } else {
                toastr.error(res.data.message);
            }
        } catch (e) {
            toastr.error('Error al guardar la caja.');
        } finally {
            ocultarAnimacion();
        }
    }

    function abrirEditar(id, nombre) {
        document.getElementById('edit_caja_id').value = id;
        document.getElementById('nombre_edit').value  = nombre;
        $('#modal_edit_caja').modal('show');
    }

    async function actualizarCaja() {
        const id     = document.getElementById('edit_caja_id').value;
        const nombre = document.getElementById('nombre_edit').value.trim();
        if (!nombre) {
            toastr.warning('Ingrese el nombre de la caja.');
            return;
        }
        const url = '{{ route('Caja.update', ':id') }}'.replace(':id', id);
        try {
            mostrarAnimacion();
            const res = await axios.put(url, { nombre });
            if (res.data.success) {
                toastr.success(res.data.message);
                $('#modal_edit_caja').modal('hide');
                window.dtCajas.ajax.reload();
            } else {
                toastr.error(res.data.message);
            }
        } catch (e) {
            toastr.error('Error al actualizar la caja.');
        } finally {
            ocultarAnimacion();
        }
    }

    async function eliminar(id) {
        const confirm = await Swal.fire({
            title: '¿Eliminar caja?',
            text: 'Se verificará que la caja no esté abierta. Esta acción no se puede revertir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { container: 'my-swal' }
        });
        if (!confirm.isConfirmed) return;

        const url = '{{ route('Caja.destroy', ':id') }}'.replace(':id', id);
        try {
            mostrarAnimacion();
            const res = await axios.delete(url);
            if (res.data.success) {
                toastr.success(res.data.message);
                window.dtCajas.ajax.reload();
            } else {
                toastr.error(res.data.message);
            }
        } catch (e) {
            toastr.error('Error al eliminar la caja.');
        } finally {
            ocultarAnimacion();
        }
    }
</script>
@endpush
