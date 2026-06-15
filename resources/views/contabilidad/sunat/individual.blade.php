@extends('layout')

@section('contabilidad-active', 'active')
@section('contabilidad-sunat-individual-active', 'active')

@section('bread-module', 'Contabilidad')
@section('bread-submodule', 'Consulta Individual SUNAT')
@section('hero-title', 'Consulta Individual')
@section('hero-subtitle', 'Consulta Individual SUNAT')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">

            {{-- FORMULARIO --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fas fa-search text-primary"></i> Consultar Comprobante en SUNAT</h5>
                </div>
                <div class="ibox-content">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <select id="tipo_comprobante" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <option value="01">01 - Factura</option>
                                    <option value="03">03 - Boleta</option>
                                    <option value="07">07 - Nota de Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Serie</label>
                                <input type="text" id="serie" class="form-control text-uppercase"
                                    placeholder="Ej: B001" maxlength="4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Número</label>
                                <input type="number" id="numero" class="form-control"
                                    placeholder="Ej: 11103" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha de Emisión</label>
                                <input type="date" id="fecha_emision" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Monto Total (S/)</label>
                                <input type="number" id="monto" class="form-control"
                                    placeholder="Ej: 85.00" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-2">
                        <button id="btn-consultar" class="btn btn-primary px-4" onclick="consultarIndividual()">
                            <i class="fas fa-search"></i> Consultar SUNAT
                        </button>
                    </div>

                </div>
            </div>

            {{-- RESULTADO --}}
            <div class="ibox" id="div-resultado" style="display:none;">
                <div class="ibox-title">
                    <h5><i class="fas fa-clipboard-check"></i> Resultado</h5>
                </div>
                <div class="ibox-content">

                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Comprobante</small>
                            <strong id="res-comprobante">—</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Fecha Emisión</small>
                            <strong id="res-fecha">—</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Monto</small>
                            <strong id="res-monto">—</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Estado SUNAT</small>
                            <div id="res-estado"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ESTADOS = {
        0: { label: 'No existe en SUNAT', cls: 'badge-danger'   },
        1: { label: 'Aceptado',           cls: 'badge-success'  },
        2: { label: 'Anulado',            cls: 'badge-warning'  },
        3: { label: 'Autorizado',         cls: 'badge-info'     },
        4: { label: 'No autorizado',      cls: 'badge-danger'   },
    };

    async function consultarIndividual() {
        const tipo  = document.getElementById('tipo_comprobante').value;
        const serie = document.getElementById('serie').value.trim().toUpperCase();
        const num   = document.getElementById('numero').value.trim();
        const fecha = document.getElementById('fecha_emision').value;
        const monto = document.getElementById('monto').value.trim();

        if (!tipo)  { toastr.warning('Seleccione el tipo de comprobante.', 'REQUERIDO'); return; }
        if (!serie) { toastr.warning('Ingrese la serie.',                  'REQUERIDO'); return; }
        if (!num)   { toastr.warning('Ingrese el número.',                 'REQUERIDO'); return; }
        if (!fecha) { toastr.warning('Ingrese la fecha de emisión.',       'REQUERIDO'); return; }
        if (!monto) { toastr.warning('Ingrese el monto total.',            'REQUERIDO'); return; }

        const btn = document.getElementById('btn-consultar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando...';
        document.getElementById('div-resultado').style.display = 'none';

        try {
            const res = await axios.post(route('contabilidad.sunat.validarIndividual'), {
                tipo_comprobante: tipo,
                serie:            serie,
                numero:           num,
                fecha_emision:    fecha,
                monto:            monto,
                _token:           document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            });

            if (res.data.success) {
                pintarResultado(res.data.data);
            } else {
                toastr.error(res.data.message, 'ERROR');
            }
        } catch (err) {
            const msg = err.response?.data?.message || err.message || 'Error desconocido';
            toastr.error(msg, 'ERROR');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Consultar SUNAT';
        }
    }

    function pintarResultado(data) {
        if (!data) return;

        document.getElementById('res-comprobante').textContent =
            (document.getElementById('tipo_comprobante').options[document.getElementById('tipo_comprobante').selectedIndex].text)
            + ' ' + data.serie + '-' + String(data.numero).padStart(8, '0');

        document.getElementById('res-fecha').textContent = data.fechaEmision ?? '—';
        document.getElementById('res-monto').textContent = data.monto !== undefined ? 'S/ ' + data.monto : '—';

        const estadoDiv = document.getElementById('res-estado');
        if (data.error) {
            estadoDiv.innerHTML = `<span class="badge badge-danger">${data.descripcion}</span>`;
        } else {
            const e = ESTADOS[data.estadoCp] ?? { label: 'Sin respuesta', cls: 'badge-secondary' };
            estadoDiv.innerHTML = `<span class="badge ${e.cls} badge-lg" style="font-size:0.9rem;padding:6px 12px;">${e.label}</span>`;
        }

        document.getElementById('div-resultado').style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['serie', 'numero', 'monto'].forEach(id => {
            document.getElementById(id).addEventListener('keydown', e => {
                if (e.key === 'Enter') consultarIndividual();
            });
        });
    });
</script>
@endpush
