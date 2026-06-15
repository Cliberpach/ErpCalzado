@extends('layout')

@section('contabilidad-active', 'active')
@section('contabilidad-sunat-active', 'active')

@section('bread-module', 'Contabilidad')
@section('bread-submodule', 'Consulta Sunat')
@section('hero-title', 'Consulta Sunat')
@section('hero-subtitle', 'Consulta Sunat')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">

            {{-- PANEL FILTROS --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fas fa-search"></i> Validador de Comprobantes SUNAT</h5>
                </div>
                <div class="ibox-content">
                    <div class="row align-items-end">

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="tipo_comprobante">Tipo Comprobante</label>
                                <select id="tipo_comprobante" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <option value="01">Factura</option>
                                    <option value="03">Boleta</option>
                                    <option value="07">Nota de Crédito</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="serie">Serie</label>
                                <input type="text" id="serie" class="form-control text-uppercase"
                                    placeholder="Ej: F001" maxlength="4">
                            </div>
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="numero_desde">Nro. Desde</label>
                                <input type="number" id="numero_desde" class="form-control"
                                    placeholder="1" min="1">
                            </div>
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="numero_hasta">Nro. Hasta <small class="text-muted">(opcional)</small></label>
                                <input type="number" id="numero_hasta" class="form-control"
                                    placeholder="1" min="1">
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <button id="btn-consultar" class="btn btn-primary btn-block" onclick="consultarSunat()">
                                    <i class="fas fa-search"></i> Consultar SUNAT
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="alert alert-info mb-0" style="font-size:0.85rem;">
                        <i class="fas fa-info-circle"></i>
                        El sistema buscará los documentos en la base de datos local para obtener fecha y monto,
                        luego los validará contra SUNAT. Máximo <strong>50 comprobantes</strong> por consulta.
                    </div>
                </div>
            </div>

            {{-- TABLA RESULTADOS --}}
            <div class="ibox" id="div-resultados" style="display:none;">
                <div class="ibox-title d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list"></i> Resultados <span id="badge-total" class="badge badge-primary ml-2"></span></h5>
                    <div>
                        <span id="resumen-aceptados" class="badge badge-success mr-1"></span>
                        <span id="resumen-no-existe" class="badge badge-danger mr-1"></span>
                        <span id="resumen-anulados" class="badge badge-warning mr-1"></span>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="tbl-resultados">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Serie</th>
                                    <th class="text-center">Número</th>
                                    <th class="text-center">Fecha Emisión</th>
                                    <th class="text-center">Monto</th>
                                    <th class="text-center">Estado SUNAT</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-resultados">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    function consultarSunat() {
        const tipo     = document.getElementById('tipo_comprobante').value;
        const serie    = document.getElementById('serie').value.trim().toUpperCase();
        const desde    = document.getElementById('numero_desde').value;
        const hasta    = document.getElementById('numero_hasta').value;

        if (!tipo) {
            toastr.warning('Seleccione el tipo de comprobante.', 'CAMPO REQUERIDO');
            return;
        }
        if (!serie) {
            toastr.warning('Ingrese la serie.', 'CAMPO REQUERIDO');
            return;
        }
        if (!desde) {
            toastr.warning('Ingrese el número desde.', 'CAMPO REQUERIDO');
            return;
        }

        const btn = document.getElementById('btn-consultar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando SUNAT...';

        document.getElementById('div-resultados').style.display = 'none';

        axios.post(route('contabilidad.sunat.validar'), {
            tipo_comprobante: tipo,
            serie:            serie,
            numero_desde:     desde,
            numero_hasta:     hasta || desde,
            _token:           document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        })
        .then(res => {
            if (res.data.success) {
                pintarResultados(res.data.data);
            } else {
                toastr.error(res.data.message, 'ERROR');
            }
        })
        .catch(err => {
            const msg = err.response?.data?.message || err.message || 'Error desconocido';
            toastr.error(msg, 'ERROR');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Consultar SUNAT';
        });
    }

    function pintarResultados(data) {
        const tbody = document.getElementById('tbody-resultados');
        tbody.innerHTML = '';

        let aceptados = 0, noExiste = 0, anulados = 0;

        data.forEach((item, index) => {
            const badge  = badgeEstado(item);
            const fila   = `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">${item.serie}</td>
                    <td class="text-center"><strong>${item.numero}</strong></td>
                    <td class="text-center">${item.fechaEmision}</td>
                    <td class="text-center">${item.monto !== '-' ? 'S/ ' + item.monto : '-'}</td>
                    <td class="text-center">${badge}</td>
                </tr>`;
            tbody.innerHTML += fila;

            if (item.estadoCp === 1) aceptados++;
            if (item.estadoCp === 0) noExiste++;
            if (item.estadoCp === 2) anulados++;
        });

        document.getElementById('badge-total').textContent      = data.length + ' comprobante(s)';
        document.getElementById('resumen-aceptados').textContent = aceptados > 0 ? aceptados + ' Aceptado(s)' : '';
        document.getElementById('resumen-no-existe').textContent = noExiste > 0  ? noExiste  + ' No existe(n)' : '';
        document.getElementById('resumen-anulados').textContent  = anulados > 0  ? anulados  + ' Anulado(s)'  : '';

        document.getElementById('div-resultados').style.display = 'block';
    }

    function badgeEstado(item) {
        if (item.localNotFound) {
            return `<span class="badge badge-secondary">${item.descripcion}</span>`;
        }
        if (item.error) {
            return `<span class="badge badge-danger">${item.descripcion}</span>`;
        }

        const clases = {
            0: 'badge-danger',
            1: 'badge-success',
            2: 'badge-warning',
            3: 'badge-info',
            4: 'badge-danger',
        };

        const clase = clases[item.estadoCp] ?? 'badge-secondary';
        return `<span class="badge ${clase}">${item.descripcion}</span>`;
    }

    // Permite consultar con Enter en los inputs
    document.addEventListener('DOMContentLoaded', () => {
        ['serie', 'numero_desde', 'numero_hasta'].forEach(id => {
            document.getElementById(id).addEventListener('keydown', e => {
                if (e.key === 'Enter') consultarSunat();
            });
        });
    });

</script>
@endpush
