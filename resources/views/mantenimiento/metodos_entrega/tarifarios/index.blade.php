@extends('layout')

@section('mantenimiento-active', 'active')
@section('metodo_entrega-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Tarifarios de Envío')
@section('hero-title', 'Tarifarios de Envío por Provincia')
@section('hero-subtitle', 'Costo de envío según provincia destino')

@section('btn-add')
    <a class="main-btn-add" href="{{ route('mantenimiento.metodo_entrega.index') }}">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

    {{-- Modal editar costo --}}
    <div class="modal inmodal" id="modal_edit_costo" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="fas fa-dollar-sign modal-icon"></i>
                    <h4 class="modal-title">EDITAR COSTO</h4>
                    <small class="font-bold" id="modal-costo-provincia-nombre"></small>
                </div>
                <div class="modal-body">
                    <form id="frmEditCosto">
                        <input type="hidden" id="edit_provincia_id" name="provincia_id">
                        <div class="form-group">
                            <label style="font-weight:bold;">COSTO DE ENVÍO (S/) <span style="color:orange;font-weight:bold;">*</span></label>
                            <input type="number" id="edit_costo" name="costo" step="0.01" min="0"
                                   class="form-control" placeholder="0.00" required>
                            <small class="text-muted">Dejar vacío = sin cobertura</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <i class="fa fa-exclamation-circle leyenda-required"></i>
                        <small class="leyenda-required">(*) obligatorio</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm" form="frmEditCosto" style="color:white;">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">

                        {{-- Filtro por departamento --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label style="font-weight:bold;">FILTRAR POR DEPARTAMENTO</label>
                                <select id="filtro_departamento">
                                    <option value="">TODOS LOS DEPARTAMENTOS</option>
                                    @foreach($departamentos as $dep)
                                        <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <span class="badge badge-warning" style="font-size:0.85rem; padding:6px 10px;">
                                    <i class="fas fa-info-circle"></i>
                                    Provincias sin costo = sin cobertura de envío
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table dataTables-tarifarios table-striped table-bordered table-hover"
                                style="text-transform:uppercase; width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">DEPARTAMENTO</th>
                                        <th class="text-center">PROVINCIA</th>
                                        <th class="text-center">COSTO ENVÍO</th>
                                        <th class="text-center">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ mix('js/tomselect.js') }}"></script>
<script>
    let dtTarifarios = null;

    document.addEventListener('DOMContentLoaded', () => {
        initTomSelect();
        loadDtTarifarios();
        bindEvents();
    });

    function initTomSelect() {
        const el = document.getElementById('filtro_departamento');
        if (el && !el.tomselect) {
            window.tsFiltroDept = new TomSelect(el, {
                create: false,
                allowEmptyOption: true,
                plugins: ['clear_button'],
                onChange(val) {
                    dtTarifarios.ajax.url(
                        '{{ route('mantenimiento.metodo_entrega.tarifarios.getTable') }}' +
                        (val ? '?departamento_id=' + val : '')
                    ).load();
                }
            });
        }
    }

    function loadDtTarifarios() {
        dtTarifarios = new DataTable('.dataTables-tarifarios', {
            serverSide: true,
            processing: true,
            ajax: '{{ route('mantenimiento.metodo_entrega.tarifarios.getTable') }}',
            columns: [
                { data: 'departamento', className: 'text-center' },
                { data: 'nombre',       className: 'text-center' },
                {
                    data: 'costo',
                    className: 'text-center',
                    render(costo) {
                        if (costo === null || costo === '') {
                            return `<span class="badge badge-secondary">Sin cobertura</span>`;
                        }
                        return `<strong>S/ ${parseFloat(costo).toFixed(2)}</strong>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render(data) {
                        return `<button class="btn btn-primary btn-sm btn-edit-costo"
                                    data-id="${data.id}"
                                    data-nombre="${data.nombre}"
                                    data-costo="${data.costo ?? ''}">
                                    <i class="fa fa-edit"></i> Editar
                                </button>`;
                    }
                }
            ],
            order: [[0, 'asc'], [1, 'asc']],
            language: { url: "{{ asset('Spanish.json') }}" },
        });
    }

    function bindEvents() {
        // Abrir modal editar
        document.addEventListener('click', e => {
            if (e.target.closest('.btn-edit-costo')) {
                const btn = e.target.closest('.btn-edit-costo');
                document.getElementById('edit_provincia_id').value = btn.dataset.id;
                document.getElementById('edit_costo').value        = btn.dataset.costo;
                document.getElementById('modal-costo-provincia-nombre').textContent = btn.dataset.nombre;
                $('#modal_edit_costo').modal('show');
            }
        });

        // Guardar costo
        document.getElementById('frmEditCosto').addEventListener('submit', async e => {
            e.preventDefault();
            const provincia = document.getElementById('modal-costo-provincia-nombre').textContent;
            const costo     = document.getElementById('edit_costo').value;

            const confirm = await Swal.fire({
                title: '¿Confirmar cambio?',
                html: `Guardar costo de envío <strong>S/ ${parseFloat(costo).toFixed(2)}</strong> para la provincia <strong>${provincia}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
            });

            if (!confirm.isConfirmed) return;

            try {
                const formData = new FormData(e.target);
                const res = await axios.post('{{ route('mantenimiento.metodo_entrega.tarifarios.updateCosto') }}', formData);
                if (res.data.success) {
                    dtTarifarios.ajax.reload(null, false);
                    $('#modal_edit_costo').modal('hide');
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                } else {
                    toastr.error(`${res.data.message}`, 'ERROR');
                }
            } catch (err) {
                toastr.error('Error en el servidor', 'ERROR');
            }
        });
    }
</script>
@endpush
